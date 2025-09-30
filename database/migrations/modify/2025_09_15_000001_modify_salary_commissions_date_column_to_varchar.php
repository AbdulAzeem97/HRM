<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySalaryCommissionsDateColumnToVarchar extends Migration
{
    public function up()
    {
        Schema::table('salary_commissions', function (Blueprint $table) {
            // Change first_date column to varchar to store dd-mm-yyyy format
            $table->string('first_date', 10)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('salary_commissions', function (Blueprint $table) {
            // Revert back to date column
            $table->date('first_date')->nullable()->change();
        });
    }
}