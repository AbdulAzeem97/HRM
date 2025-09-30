<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Check if nic_expiry column exists
            if (!Schema::hasColumn('employees', 'nic_expiry')) {
                // Add the column if it doesn't exist
                $table->date('nic_expiry')->nullable()->after('nic');
            } else {
                // Modify existing column to be DATE type
                // First, backup any existing data and convert format if needed
                DB::statement("UPDATE employees SET nic_expiry = NULL WHERE nic_expiry = '' OR nic_expiry = '0000-00-00'");

                // Convert existing data from dd-mm-yyyy or dd/mm/yyyy to yyyy-mm-dd format
                DB::statement("UPDATE employees SET nic_expiry = STR_TO_DATE(nic_expiry, '%d-%m-%Y') WHERE nic_expiry IS NOT NULL AND nic_expiry REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$'");
                DB::statement("UPDATE employees SET nic_expiry = STR_TO_DATE(nic_expiry, '%d/%m/%Y') WHERE nic_expiry IS NOT NULL AND nic_expiry REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'");

                // Now modify the column type
                $table->date('nic_expiry')->nullable()->change();
            }
        });

        // Also ensure nic column exists if it doesn't
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'nic')) {
                $table->string('nic', 50)->nullable()->after('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Convert back to string if needed (optional rollback)
            if (Schema::hasColumn('employees', 'nic_expiry')) {
                $table->string('nic_expiry', 20)->nullable()->change();
            }
        });
    }
};