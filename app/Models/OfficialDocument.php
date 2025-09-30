<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OfficialDocument extends Model
{
    //
	protected $guarded=[];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function DocumentType(){
		return $this->hasOne('App\Models\DocumentType','id','document_type_id');
	}

	public function AddedBy(){
		return $this->hasOne('App\Models\User','id','added_by');
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
