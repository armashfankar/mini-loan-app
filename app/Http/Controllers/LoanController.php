<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Repositories\LoanRepository;
use App\Repositories\RepaymentRepository;
use App\Traits\ResponseCodeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\UtilHelper as Util;

class LoanController extends Controller
{
    use ResponseCodeTrait;

    protected $loan_repository;

    /**
     * LoanController constructor.
     * @param LoanRepository $loan_repository
     * @param RepaymentRepository $repayment_repository
     * 
     */
    public function __construct(LoanRepository $loan_repository, RepaymentRepository $repayment_repository)
    {
        $this->loan_repository = $loan_repository;
        $this->repayment_repository = $repayment_repository;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function index(Request $request)
    {
        $request_data = $request->all();
    
        $loans = $this->loan_repository->all($request_data);
        
        $response = $this->getResponseCode(1);
        if (!empty($loans)) {
            $response['data']['loans'] = LoanResource::collection($loans);
        } else {
            $response = $this->getResponseCode(104);
        }

        return response($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function store(Request $request)
    {
        $request_data = $request->all();

        $rules = [
            'amount' => 'required|numeric',
            'term' => 'required|numeric',            
        ];

        $this->validate($request, $rules);

        $loan_params = [
            'user_reference_number' => auth()->user()->user_reference_number,
            'amount' => $request_data['amount'],
            'term' => $request_data['term'],            
            'loan_status' => 'pending',
            'pending_amount' => $request_data['amount'],
            'next_payment_date' => Carbon::today()->addDays(7)
        ];
        
        $loan = $this->loan_repository->create($loan_params);
        $response = $this->getResponseCode(1);
        if (!empty($loan)) {
            $this->repayment_repository->create($loan);
            $response['data']['loan'] = new LoanResource($loan);
        } else {
            $response = $this->getResponseCode(102);
        }

        return response($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $loan_reference_number
     * @return JsonResponse
     * @throws ValidationFailedException
     */
    public function update(Request $request, $loan_reference_number)
    {
        $request_data = $request->all();
        
        // check if auth user is admin then approve loan
        $is_admin = Util::checkIfAdmin();
        
        if($is_admin === 1){
            $approval = $this->loanApproval($loan_reference_number);
            
            $response = $this->getResponseCode(1);
            if ($approval === 1) {
                $response = $this->getResponseCode(3);
            } else {
                $response = $this->getResponseCode(104);
            }

            return response($response);
        }

        // fetch loan details
        $loan = $this->loan_repository->findByReferenceNumber($loan_reference_number);

        // Different Conditional Checks befor Loan Update
        $verify_loan = $this->verifyLoanConditions($request_data, $loan, $loan_reference_number);

        if($verify_loan !== 1){
            $response = $this->getResponseCode($verify_loan);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }

        // Repayment Logic (in repayment repository)
        $repayment_update = $this->repayment_repository->update($loan, $request_data);
        $loan_status = 'pending';

        if($repayment_update === 0){
            // if repayment amount is less than scheduled amount
            $response = $this->getResponseCode(110);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }
        elseif($repayment_update === 1){
            // if all scheduled repayments are completed and loan is fully paid
            $loan_status = 'paid';
            $next_payment_date = Null;
        }else{
            // if payment of a scheduled repayment is completed, gets next scheduled date
            $next_payment_date = $repayment_update;
        }
            
        $update_params = [
            'pending_amount' => $loan->pending_amount - $request_data['amount'],
            'last_payment_date' => Carbon::today(),
            'next_payment_date' => $next_payment_date,
            'loan_status' => $loan_status
        ];
        
        //update loan fields
        $update = $this->loan_repository->update($loan_reference_number, $update_params);
        
        $response = $this->getResponseCode(1);
        if (!empty($update)) {
            //fetch update loan data
            $loan = $this->loan_repository->findByReferenceNumber($loan_reference_number);
            $response['data']['loan'] = new LoanResource($loan);
        } else {
            $response = $this->getResponseCode(104);
        }

        return response($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $loan_reference_number
     * @return JsonResponse
     */
    public function destroy($loan_reference_number)
    {
        $message = '';
        
        // check if admin
        $is_admin = Util::checkIfAdmin();

        if($is_admin === 0){
            $response = $this->getResponseCode(113);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }
        
        $loan = $this->loan_repository->findByReferenceNumber($loan_reference_number);
        if(empty($loan)){   
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }
        
        $loan = $this->loan_repository->delete($loan_reference_number);

        $response = $this->getResponseCode(1);
        if (!empty($loan) && empty($message)) {
            $message = "Deleted Succesfully!";
            $response['message'] = $message;
        } else {
            $response = $this->getResponseCode(102);
            $response['message'] = $message;
        }

        return response($response);
    }

    public function verifyLoanConditions($request_data, $loan, $loan_reference_number)
    {  
        if(empty($loan)){            
            //if loan not found
            return 108;
        }elseif($loan->loan_status == 'paid'){
            // if loan is already paid
            return 2;
        }elseif($loan->loan_status != 'approved'){
            // if loan is not approved by admin user cannot repay
            return 112;
        }elseif(auth()->user()->user_reference_number != $loan->user_reference_number){
            return 114;
        }elseif($request_data['amount'] > $loan->amount || $request_data['amount'] > $loan->pending_amount){
            // if repay amount is greater than loan amount OR pending payment amount 
            return 111;
        }else{
            return 1;
        }
    }

    public function loanApproval($loan_reference_number)
    {
        $update_params = [
            'loan_status' => 'approved'
        ];
        
        // update loan status to approved
        $update = $this->loan_repository->update($loan_reference_number, $update_params);
        
        if (!empty($update)) {
            return 1;
        } else {
            return 104;
        }
    }
}