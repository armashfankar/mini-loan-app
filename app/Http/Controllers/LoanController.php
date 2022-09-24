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
    
        $rules = [
            'loan_reference_number' => 'required'
        ];

        $this->validate($request, $rules);
        
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
        $loan_status = 'pending';

        $loan = $this->loan_repository->findByReferenceNumber($loan_reference_number);

        if(empty($loan)){            
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }elseif($loan->is_approved != 1){
            $response = $this->getResponseCode(113);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }elseif($loan->loan_status == 'paid'){
            $response = $this->getResponseCode(111);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }elseif($request_data['amount'] > $loan->amount || $request_data['amount'] > $loan->pending_amount){
            $response = $this->getResponseCode(112);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }

        //repayment logic
        $repay = $this->repayment_repository->update($loan, $request_data);
        if($repay === 0){
            $response = $this->getResponseCode(110);
            if (!empty($message)) {
                $response['message'] = $message;
            }
            return response($response);
        }elseif($repay === 1){
            $loan_status = 'paid';
            $next_payment_date = Null;
        }else{
            $next_payment_date = $repay;
        }
        
        if($loan->pending_amount - $request_data['amount'] <= 0){
            $loan_status = 'paid';
        }

        $update_params = [
            'pending_amount' => $loan->pending_amount - $request_data['amount'],
            'last_payment_date' => Carbon::today(),
            'next_payment_date' => $next_payment_date,
            'loan_status' => $loan_status
        ];
        
        $update = $this->loan_repository->update($loan_reference_number,$update_params);
        
        $response = $this->getResponseCode(1);
        if (!empty($update)) {
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
}