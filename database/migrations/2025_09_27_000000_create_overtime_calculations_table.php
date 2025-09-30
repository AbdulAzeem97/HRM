<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->time('clock_in');
            $table->time('clock_out');
            $table->time('shift_start_time');
            $table->time('shift_end_time');
            $table->integer('working_minutes'); // Total working time in minutes
            $table->integer('shift_minutes'); // Expected shift duration in minutes
            $table->integer('late_minutes')->default(0); // Late arrival in minutes
            $table->integer('overtime_minutes')->default(0); // Gross overtime before adjustments
            $table->integer('net_overtime_minutes')->default(0); // Net overtime after late deduction
            $table->decimal('hourly_rate', 10, 2); // Basic hourly rate
            $table->decimal('overtime_rate', 10, 2); // Overtime rate (usually 2x)
            $table->decimal('overtime_amount', 10, 2)->default(0); // Final overtime pay
            $table->boolean('overtime_eligible')->default(true); // Employee OT eligibility
            $table->integer('required_hours_per_day')->default(9); // Employee's required hours
            $table->decimal('basic_salary', 10, 2); // Employee's basic salary for calculation
            $table->string('calculation_notes')->nullable(); // Any special notes
            $table->string('shift_name')->nullable(); // Shift worked
            $table->enum('status', ['calculated', 'verified', 'paid'])->default('calculated');
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'attendance_date']);
            $table->index(['attendance_date']);
            $table->index(['status']);

            // Foreign key
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            // Unique constraint to prevent duplicate calculations
            $table->unique(['employee_id', 'attendance_date'], 'unique_employee_date_ot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime_calculations');
    }
};