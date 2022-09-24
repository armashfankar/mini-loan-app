<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Repositories\LoanRepository;
use App\Traits\ResponseCodeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    use ResponseCodeTrait;

    protected $loan_repository;

    /**
     * LoanController constructor.
     * @param LoanRepository $loan_repository
     * 
     */
    public function __construct(LoanRepository $loan_repository)
    {
        $this->loan_repository = $loan_repository;
        
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
            'pending_amount' => $request_data['amount']
        ];
        
        $loan = $this->loan_repository->create($loan_params);

        $response = $this->getResponseCode(1);
        if (!empty($loan)) {
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
        
        $loan = $this->loan_repository->findByReferenceNumber($loan_reference_number);
        if(empty($loan)){
            
            $response = $this->getResponseCode(108);
            if (!empty($message)) {
                $response['message'] = $message;
            }

            return response($response);
        }
        
        $update_params = [];
        if ($request->has('loan_status')) {
            $update_params['loan_status'] = $request_data['loan_status'];
        }
        if ($request->has('pending_amount')) {
            $update_params['pending_amount'] = $request_data['pending_amount'];
        }
        if ($request->has('last_payment_date')) {
            $update_params['last_payment_date'] = $request_data['last_payment_date'];
        }
        if ($request->has('next_payment_date')) {
            $update_params['next_payment_date'] = $request_data['next_payment_date'];
        }
        
        $update = $this->loan_repository->update($loan_reference_number,$update_params);
        
        $response = $this->getResponseCode(1);
        if (!empty($update)) {
            $response['data']['loan'] = new LoanResource($update);
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