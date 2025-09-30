<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TrainingList extends Model
{
	protected $fillable = [
		'description', 'company_id','trainer_id','training_type_id','start_date','end_date',
		'training_cost','status','remarks'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}
	public function trainer(){
		return $this->hasOne('App\Models\Trainer','id','trainer_id');
	}
	public function TrainingType(){
		return $this->hasOne('App\Models\TrainingType','id','training_type_id');
	}

	public function employees(){
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
