<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Storage;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $loan = [
            'loan_reference_number' => $this->loan_reference_number,
            'amount' => $this->amount,
            'term' => $this->term,
            'is_approved' => $this->is_approved,
            'loan_status' => $this->loan_status,
            'pending_amount' => $this->amount,
            'last_payment_date' => $this->last_payment_date,
            'next_payment_date' => $this->next_payment_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];

        if ((!empty($this->user)) && $this->user->count()) {
            $loan['user'] = [
                'user_reference_number' => $this->user->user_reference_number,
                'name' => $this->user->name,
                'email' => $this->user->email
            ];
        } else {
            $loan['user'] = null;
        }

        return $loan;
    }
}