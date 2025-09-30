<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class BulkAttendanceInsert extends Command
{
    protected $signature = 'attendance:bulk-insert {staff_ids*} {--days=30} {--start-date=}';
    
    protected $description = 'Insert bulk attendance records for specified staff IDs';

    public function handle()
    {
        $staffIds = $this->argument('staff_ids');
        $days = $this->option('days') ?? 30;
        $startDate = $this->option('start-date') ?? Carbon::now()->subDays($days)->format('Y-m-d');
        
        $this->info("Inserting {$days} days of attendance for staff IDs: " . implode(', ', $staffIds));
        $this->info("Starting from: {$startDate}");

        // Get employees
        $employees = Employee::whereIn('staff_id', $staffIds)->get();
        
        if ($employees->count() !== count($staffIds)) {
            $this->error('Some staff IDs not found in database');
            return 1;
        }

        $attendanceData = [];
        $bar = $this->output->createProgressBar($employees->count() * $days);

        foreach ($employees as $employee) {
            for ($i = 0; $i < $days; $i++) {
                $date = Carbon::parse($startDate)->addDays($i);
                
                // Skip weekends (Saturday & Sunday)
                if ($date->isWeekend()) {
                    continue;
                }

                // Generate realistic attendance times with variation
                $clockIn = $this->generateClockInTime();
                $clockOut = $this->generateClockOutTime($clockIn);
                
                $attendanceData[] = [
                    'employee_id' => $employee->id,
                    'attendance_date' => $date->format('Y-m-d'),
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'clock_in_ip' => '192.168.1.' . (100 + $employee->id),
                    'clock_out_ip' => '192.168.1.' . (100 + $employee->id),
                    'clock_in_out' => 0,
                    'time_late' => $this->calculateLateness($clockIn),
                    'early_leaving' => $this->calculateEarlyLeaving($clockOut),
                    'overtime' => $this->calculateOvertime($clockIn, $clockOut),
                    'total_work' => $this->calculateTotalWork($clockIn, $clockOut),
                    'total_rest' => '00:00',
                    'attendance_status' => 'present'
                ];
                
                $bar->advance();
            }
        }

        // Insert in chunks for better performance
        $chunks = array_chunk($attendanceData, 100);
        foreach ($chunks as $chunk) {
            Attendance::insert($chunk);
        }

        $bar->finish();
        $this->newLine();
        $this->info('Bulk attendance insertion completed successfully!');
        $this->info('Total records inserted: ' . count($attendanceData));
        
        return 0;
    }

    private function generateClockInTime()
    {
        // Generate time between 8:00 AM to 8:45 AM
        $baseMinutes = 8 * 60; // 8:00 AM in minutes
        $variationMinutes = rand(0, 45); // 0 to 45 minutes variation
        
        $totalMinutes = $baseMinutes + $variationMinutes;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function generateClockOutTime($clockIn)
    {
        // Standard work: 9 hours 15 minutes from clock in
        list($inHour, $inMinute) = explode(':', $clockIn);
        $inTotalMinutes = ($inHour * 60) + $inMinute;
        
        // Add 9 hours 15 minutes (555 minutes) + random variation
        $workMinutes = 555 + rand(-30, 30); // ±30 minutes variation
        $outTotalMinutes = $inTotalMinutes + $workMinutes;
        
        $outHour = floor($outTotalMinutes / 60);
        $outMinute = $outTotalMinutes % 60;
        
        return sprintf('%02d:%02d', $outHour, $outMinute);
    }

    private function calculateLateness($clockIn)
    {
        list($hour, $minute) = explode(':', $clockIn);
        $inMinutes = ($hour * 60) + $minute;
        $standardStart = 8 * 60; // 8:00 AM
        
        if ($inMinutes > $standardStart) {
            $lateMinutes = $inMinutes - $standardStart;
            return sprintf('%02d:%02d', floor($lateMinutes / 60), $lateMinutes % 60);
        }
        
        return '00:00';
    }

    private function calculateEarlyLeaving($clockOut)
    {
        list($hour, $minute) = explode(':', $clockOut);
        $outMinutes = ($hour * 60) + $minute;
        $standardEnd = 17 * 60 + 15; // 5:15 PM
        
        if ($outMinutes < $standardEnd) {
            $earlyMinutes = $standardEnd - $outMinutes;
            return sprintf('%02d:%02d', floor($earlyMinutes / 60), $earlyMinutes % 60);
        }
        
        return '00:00';
    }

    private function calculateOvertime($clockIn, $clockOut)
    {
        list($inHour, $inMinute) = explode(':', $clockIn);
        list($outHour, $outMinute) = explode(':', $clockOut);
        
        $inMinutes = ($inHour * 60) + $inMinute;
        $outMinutes = ($outHour * 60) + $outMinute;
        $workMinutes = $outMinutes - $inMinutes;
        
        $standardWork = 9 * 60 + 15; // 9 hours 15 minutes
        
        if ($workMinutes > $standardWork) {
            $overtimeMinutes = $workMinutes - $standardWork;
            return sprintf('%02d:%02d', floor($overtimeMinutes / 60), $overtimeMinutes % 60);
        }
        
        return '00:00';
    }

    private function calculateTotalWork($clockIn, $clockOut)
    {
        list($inHour, $inMinute) = explode(':', $clockIn);
        list($outHour, $outMinute) = explode(':', $clockOut);
        
        $inMinutes = ($inHour * 60) + $inMinute;
        $outMinutes = ($outHour * 60) + $outMinute;
        $workMinutes = $outMinutes - $inMinutes;
        
        return sprintf('%02d:%02d', floor($workMinutes / 60), $workMinutes % 60);
    }
}