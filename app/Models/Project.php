<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	protected $fillable = [
		'title','client_id','company_id','start_date','end_date','project_priority','description','summary',
		'project_status','project_note','is_notify','added_by','project_progress'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}
	public function client(){
		return $this->hasOne('App\Models\Client','id','client_id');
	}
	public function addedBy(){
		return $this->hasOne('App\Models\User','id','added_by');
	}
	public function assignedEmployees(){
		return $this->belongsToMany(Employee::class);
	}

	public function setStartDateAttribute($value)
	{
		$this->attributes['start_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getStartDateAttribute($value)
	{
		return $value;
	}

	public function setEndDateAttribute($value)
	{
		$this->attributes['end_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getEndDateAttribute($value)
	{
		return $value;
	}

}
