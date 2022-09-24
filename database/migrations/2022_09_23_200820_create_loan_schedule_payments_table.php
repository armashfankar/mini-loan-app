<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanSchedulePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_schedule_payments', function (Blueprint $table) {
            $table->id();
            $table->string('repayment_reference_number', 20);
            $table->string('loan_reference_number', 20);
            $table->decimal('scheduled_amount', 9, 3);
            $table->date('scheduled_date');
            $table->enum('payment_status',['pending','paid']);
            $table->decimal('paid_amount', 9, 3)->default(0);
            $table->date('paid_on')->nullable();
            $table->foreign('loan_reference_number')->references('loan_reference_number')->on('user_loans')->onDelete('cascade');
            $table->timestamps();
            $table->index('repayment_reference_number');
            $table->index('loan_reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_schedule_payments');
    }
}
