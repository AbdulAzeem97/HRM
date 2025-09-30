<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\status;

class StatusOptionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['status_title' => 'Active', 'employee_id' => null],
            ['status_title' => 'Inactive', 'employee_id' => null],
            ['status_title' => 'On Leave', 'employee_id' => null],
            ['status_title' => 'Terminated', 'employee_id' => null],
            ['status_title' => 'Suspended', 'employee_id' => null],
        ];

        foreach ($statuses as $statusData) {
            // Check if status already exists to avoid duplicates
            $existingStatus = status::where('status_title', $statusData['status_title'])->first();

            if (!$existingStatus) {
                status::create([
                    'status_title' => $statusData['status_title'],
                    'employee_id' => $statusData['employee_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Created status: {$statusData['status_title']}\n";
            } else {
                echo "ℹ️ Status already exists: {$statusData['status_title']}\n";
            }
        }

        echo "🎉 Status options seeding completed!\n";
    }
}