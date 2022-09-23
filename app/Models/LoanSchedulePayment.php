<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSchedulePayment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * @return BelongsTo
     */
    public function userloans()
    {
        return $this->belongsTo(UserLoan::class, 'loan_reference_number', 'loan_reference_number');
    }
}
