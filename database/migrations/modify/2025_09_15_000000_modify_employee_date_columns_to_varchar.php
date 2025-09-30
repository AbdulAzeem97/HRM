<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEmployeeDateColumnsToVarchar extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Change date columns to varchar to store dd-mm-yyyy format
            $table->string('date_of_birth', 10)->nullable()->change();
            $table->string('joining_date', 10)->nullable()->change();
            $table->string('exit_date', 10)->nullable()->change();
            $table->string('nic_expiry', 10)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert back to date columns
            $table->date('date_of_birth')->nullable()->change();
            $table->date('joining_date')->nullable()->change();
            $table->date('exit_date')->nullable()->change();
            $table->date('nic_expiry')->nullable()->change();
        });
    }
}