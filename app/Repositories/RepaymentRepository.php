<?php

namespace App\Repositories;

use App\Helpers\UtilHelper as Util;
use App\Models\LoanSchedulePayment;
use Carbon\Carbon;

class RepaymentRepository
{
    protected $model;

    /**
     * RepaymentRepository constructor.
     * @param LoanSchedulePayment $model
     * 
     */
    public function __construct(LoanSchedulePayment $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create($data)
    {
        for($i=1; $i <= $data['term']; $i++){
            $repayment_params = [
                'repayment_reference_number' => $this->generateReferenceNumber(),
                'loan_reference_number' => $data['loan_reference_number'],
                'scheduled_amount' => $data['amount']/$data['term'],
                'scheduled_date' => Carbon::today()->addDays(7*$i),            
                'payment_status' => 'pending'
            ];

            $this->model->create($repayment_params);
        }
        
        return true;
    }

    /**
     * @param $loan_reference_number
     * @param $params
     * @return mixed
     */
    public function update($loan, $request_data)
    {   
        //fetch next scheduled payment
        $scheduled_payment = $this->model->where('loan_reference_number', $loan->loan_reference_number)
        ->where('payment_status','pending')
        ->orderBy('scheduled_date','asc')
        ->first();

        if (!empty($request_data) && !empty($scheduled_payment)) {

            //if amount not greater than or equal to scheduled amount than return 0
            if(!($request_data['amount'] >= $scheduled_payment->scheduled_amount)){
                return 0;
            }
            
            //parameters list to update
            $repayment_params = [
                'paid_amount' => $request_data['amount'],
                'paid_on' => Carbon::today(),            
                'payment_status' => 'paid'
            ];

            $scheduled_payment->update($repayment_params);

            //find next pending scheduled repayment list
            $pending_repayment = $this->model->where('loan_reference_number',$loan->loan_reference_number)
            ->where('payment_status','pending')->get();

            if(count($pending_repayment) > 0){

                //calculate new scheduled amount for remaining/pending scheduled repayments
                $new_scheduled_amount = ($loan->pending_amount - $request_data['amount']) / count($pending_repayment);
                
                $pending_repayment_params = [
                    'scheduled_amount' => $new_scheduled_amount
                ];

                if($new_scheduled_amount <= 0){
                    $pending_repayment_params['payment_status'] = 'paid';
                }

                $this->model->where('loan_reference_number',$loan->loan_reference_number)
                ->where('payment_status','pending')->update($pending_repayment_params);

                return $pending_repayment[0]['scheduled_date'];
            }
            
        }

        return 1;
    }

    /**
     * @param $loan_reference_number
     * @return mixed
     */
    public function delete($loan_reference_number)
    {
        return $this->model->where('loan_reference_number', $loan_reference_number)->delete();
    }

    /**
     * @return string
     */
    private function generateReferenceNumber()
    {
        return 'REP' . Util::generateString(true);
    }


    /**
     * @param $loan_reference_number
     * @return mixed
     */
    public function findByReferenceNumber($loan_reference_number)
    {
        return $this->model->where('loan_reference_number', $loan_reference_number)->first();
    }
}