<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_reference_number', 20)->unique();
            $table->string('user_reference_number', 20);
            $table->decimal('amount', 9, 3);
            $table->integer('term')->default(1);
            $table->enum('loan_status',['pending', 'approved', 'in_progress', 'rejected', 'paid']);
            $table->decimal('pending_amount',9, 3)->default(0);
            $table->date('last_payment_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->foreign('user_reference_number')->references('user_reference_number')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->index('user_reference_number');
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
        Schema::dropIfExists('user_loans');
    }
}
