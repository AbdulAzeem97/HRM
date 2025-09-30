<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Database configuration
$capsule = new DB;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'u902429527_ttphrm',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "Starting database update for date format...\n";

    // Step 1: Create backup columns
    DB::statement("ALTER TABLE employees ADD COLUMN date_of_birth_new VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees ADD COLUMN joining_date_new VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees ADD COLUMN exit_date_new VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees ADD COLUMN nic_expiry_new VARCHAR(10) NULL");
    echo "Backup columns created.\n";

    // Step 2: Convert existing data from YYYY-MM-DD to DD-MM-YYYY
    $employees = DB::table('employees')->get();
    foreach ($employees as $employee) {
        $updates = [];

        if ($employee->date_of_birth && $employee->date_of_birth != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $employee->date_of_birth);
            if ($date) {
                $updates['date_of_birth_new'] = $date->format('d-m-Y');
            }
        }

        if ($employee->joining_date && $employee->joining_date != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $employee->joining_date);
            if ($date) {
                $updates['joining_date_new'] = $date->format('d-m-Y');
            }
        }

        if ($employee->exit_date && $employee->exit_date != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $employee->exit_date);
            if ($date) {
                $updates['exit_date_new'] = $date->format('d-m-Y');
            }
        }

        if ($employee->nic_expiry && $employee->nic_expiry != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $employee->nic_expiry);
            if ($date) {
                $updates['nic_expiry_new'] = $date->format('d-m-Y');
            }
        }

        if (!empty($updates)) {
            DB::table('employees')->where('id', $employee->id)->update($updates);
        }
    }
    echo "Employee data converted.\n";

    // Step 3: Drop old columns and rename new ones
    DB::statement("ALTER TABLE employees DROP COLUMN date_of_birth");
    DB::statement("ALTER TABLE employees DROP COLUMN joining_date");
    DB::statement("ALTER TABLE employees DROP COLUMN exit_date");
    DB::statement("ALTER TABLE employees DROP COLUMN nic_expiry");

    DB::statement("ALTER TABLE employees CHANGE date_of_birth_new date_of_birth VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees CHANGE joining_date_new joining_date VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees CHANGE exit_date_new exit_date VARCHAR(10) NULL");
    DB::statement("ALTER TABLE employees CHANGE nic_expiry_new nic_expiry VARCHAR(10) NULL");
    echo "Employee table structure updated.\n";

    // Step 4: Update salary_commissions table
    DB::statement("ALTER TABLE salary_commissions ADD COLUMN first_date_new VARCHAR(10) NULL");

    $commissions = DB::table('salary_commissions')->get();
    foreach ($commissions as $commission) {
        if ($commission->first_date && $commission->first_date != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $commission->first_date);
            if ($date) {
                DB::table('salary_commissions')
                    ->where('id', $commission->id)
                    ->update(['first_date_new' => $date->format('d-m-Y')]);
            }
        }
    }

    DB::statement("ALTER TABLE salary_commissions DROP COLUMN first_date");
    DB::statement("ALTER TABLE salary_commissions CHANGE first_date_new first_date VARCHAR(10) NULL");
    echo "Salary commissions table updated.\n";

    echo "Database update completed successfully!\n";
    echo "All dates are now stored in dd-mm-yyyy format.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>