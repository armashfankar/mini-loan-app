<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserLoan extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_reference_number', 'user_reference_number');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedulepayments()
    {
        return $this->hasMany(LoanSchedulePayment::class, 'loan_reference_number',  'loan_reference_number');
    }
}
