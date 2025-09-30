<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
	protected $fillable = [
		'employee_id', 'company_id', 'promotion_title','description','promotion_date'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}

	public function setPromotionDateAttribute($value)
	{
		$this->attributes['promotion_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getPromotionDateAttribute($value)
	{
		return $value;
	}
}
