<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeShift extends Model
{
    protected $table = 'office_shifts';

    protected $fillable = [
        'shift_name', 'company_id', 'default_shift',
        'monday_in', 'monday_out', 'tuesday_in', 'tuesday_out',
        'wednesday_in', 'wednesday_out', 'thursday_in', 'thursday_out',
        'friday_in', 'friday_out', 'saturday_in', 'saturday_out',
        'sunday_in', 'sunday_out'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'office_shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'office_shift_id');
    }

    public function shiftChanges()
    {
        return $this->hasMany(EmployeeShiftChange::class, 'new_shift_id');
    }

    // Helper methods to get shift times for specific days
    public function getShiftTimeForDay($day)
    {
        $inColumn = strtolower($day) . '_in';
        $outColumn = strtolower($day) . '_out';

        return [
            'start' => $this->{$inColumn},
            'end' => $this->{$outColumn}
        ];
    }

    public function getShiftStartTime($day)
    {
        $column = strtolower($day) . '_in';
        return $this->{$column};
    }

    public function getShiftEndTime($day)
    {
        $column = strtolower($day) . '_out';
        return $this->{$column};
    }

    // Check if shift has times defined for a specific day
    public function hasShiftForDay($day)
    {
        $inColumn = strtolower($day) . '_in';
        $outColumn = strtolower($day) . '_out';

        return !empty($this->{$inColumn}) && !empty($this->{$outColumn});
    }

    // Get all working days for this shift
    public function getWorkingDays()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $workingDays = [];

        foreach ($days as $day) {
            if ($this->hasShiftForDay($day)) {
                $workingDays[] = $day;
            }
        }

        return $workingDays;
    }

    // Calculate shift duration in minutes for a specific day
    public function getShiftDurationMinutes($day)
    {
        if (!$this->hasShiftForDay($day)) {
            return 0;
        }

        $start = $this->getShiftStartTime($day);
        $end = $this->getShiftEndTime($day);

        $startTime = \Carbon\Carbon::parse($start);
        $endTime = \Carbon\Carbon::parse($end);

        // Handle overnight shifts
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        return $startTime->diffInMinutes($endTime);
    }

    // Scope for active shifts
    public function scopeActive($query)
    {
        return $query->whereNotNull('shift_name');
    }

    // Scope for shifts by company
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}