<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
	protected $fillable = [
		'company_id','employee_id','asset_name','assets_category_id','Asset_note','manufacturer','asset_code',
		'invoice_number','purchase_date','serial_number','asset_image','warranty_date','status'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function employee(){
		return $this->hasOne('App\Models\Employee','id','employee_id');
	}


	public function Category(){
		return $this->hasOne('App\Models\AssetCategory','id','assets_category_id');
	}

	public function setPurchaseDateAttribute($value)
	{
		$this->attributes['purchase_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getPurchaseDateAttribute($value)
	{
		return $value;
	}

	public function setWarrantyDateAttribute($value)
	{
		$this->attributes['warranty_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getWarrantyDateAttribute($value)
	{
		return $value;
	}
}
