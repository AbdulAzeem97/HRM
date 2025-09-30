<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeShiftChange extends Model
{
    protected $fillable = [
        'employee_id',
        'old_shift_id',
        'new_shift_id',
        'effective_date',
        'changed_by',
        'reason'
    ];

    protected $dates = [
        'effective_date'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function oldShift()
    {
        return $this->belongsTo(OfficeShift::class, 'old_shift_id');
    }

    public function newShift()
    {
        return $this->belongsTo(OfficeShift::class, 'new_shift_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeEffectiveFromDate($query, $date)
    {
        return $query->where('effective_date', '>=', $date);
    }

    public function scopeEffectiveBeforeDate($query, $date)
    {
        return $query->where('effective_date', '<=', $date);
    }

    // Accessors
    public function getFormattedEffectiveDateAttribute()
    {
        return $this->effective_date ? $this->effective_date->format('d-m-Y') : '';
    }

    public function getShiftChangeDescriptionAttribute()
    {
        $oldShiftName = $this->oldShift ? $this->oldShift->shift_name : 'None';
        $newShiftName = $this->newShift ? $this->newShift->shift_name : 'Unknown';

        return "Changed from {$oldShiftName} to {$newShiftName}";
    }
}