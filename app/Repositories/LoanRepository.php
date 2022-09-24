<?php

namespace App\Repositories;

use App\Helpers\UtilHelper as Util;
use App\Models\UserLoan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LoanRepository
{
    protected $model;

    /**
     * LoanRepository constructor.
     * @param UserLoan $model
     * 
     */
    public function __construct(UserLoan $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $data['loan_reference_number'] = $this->generateReferenceNumber();
        return $this->model->create($data);
    }

    /**
     * @param $params
     * @param bool $first
     * @return UserLoan[]|Collection
     */
    public function all($params, $first = false)
    {
        if (!empty($params['page'])) {
            $limit = $params['limit'];
            $skip = ($params['page'] - 1) * $limit;
        }

        $loans = $this->model->where('user_reference_number', auth()->user()->user_reference_number);

        if (isset($limit) && isset($skip)) {
            $loans = $loans->take($limit)->skip($skip);
        }

        $loans = $loans->orderBy('next_payment_date', 'ASC')->with('user');

        if ($first) {
            $loans = $loans->first();
        } else {
            $loans = $loans->get();
        }

        return $loans;
    }

    /**
     * @param $loan_reference_number
     * @param $params
     * @return mixed
     */
    public function update($loan_reference_number, $params)
    {
        $loan = $this->model->where('loan_reference_number', $loan_reference_number)->first();

        if (!empty($params)) {
            $loan->update($params);
        }

        return $loan;
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
        return 'ASP' . Util::generateString(true);
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