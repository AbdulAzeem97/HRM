<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
	protected $fillable = [
		'document_title','document_type_id','employee_id','expiry_date','document_file','description',
		'is_notify'
	];

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}

	public function DocumentType(){
		return $this->hasOne('App\Models\DocumentType','id','document_type_id');
	}

	public function setExpiryDateAttribute($value)
	{
		$this->attributes['expiry_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getExpiryDateAttribute($value)
	{
		return $value;
	}
}
