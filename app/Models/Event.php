<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	protected $fillable = [
		'event_title','company_id','department_id','event_note','event_date','event_time',
		'status','is_notify'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function department(){
		return $this->hasOne('App\Models\department','id','department_id');
	}

	public function setEventDateAttribute($value)
	{
		$this->attributes['event_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getEventDateAttribute($value)
	{
		return $value;
	}

}
