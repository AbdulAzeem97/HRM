<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
	protected $fillable = [
		'company_id','account_id','amount','category_id','description','payment_method_id','payee_id',
		'expense_reference','expense_date','expense_file'
		,'category','payer_id',
		'deposit_reference','deposit_date','deposit_file'
	];

	public function company(){
		return $this->hasOne('App\Models\company','id','company_id');
	}

	public function Account(){
		return $this->hasOne('App\Models\FinanceBankCash','id','account_id');
	}


	public function setExpenseDateAttribute($value)
	{
		$this->attributes['expense_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getExpenseDateAttribute($value)
	{
		return $value;
	}

	public function setDepositDateAttribute($value)
	{
		$this->attributes['deposit_date'] = DateHelper::parseToddmmyyyy($value);
	}

	public function getDepositDateAttribute($value)
	{
		return $value;
	}





}
