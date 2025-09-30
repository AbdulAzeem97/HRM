<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
	protected $fillable = [
		'event_name','description','start_date','end_date','company_id','is_publish'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
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
