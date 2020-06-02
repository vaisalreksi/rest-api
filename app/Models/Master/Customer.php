<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = "customer";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
			'master_division_id',
      'master_company_id',
			'name',
      'flag',
      'nip',
      'phone',
      'email',
      'address',
      'status'
  ];

  public function masterDivision()
  {
    return $this->belongsTo('App\Models\Master\MasterDivision','master_division_id','id')->select('id','description');
  }

	public function masterCompany()
  {
    return $this->belongsTo('App\Models\Master\MasterCompany','master_company_id','id')->select('id','name','address','no_kp','date_kp','notaris_no','notaris_date','notaris_name','phone','faximile','email');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				if(isset($input['filter_text']) && !empty($input['filter_text'])){
					$ex = explode(",",$input['filter_text']);
					for ($i=0; $i < count($ex); $i++) {
						if($ex[$i]=='master_division'){
							$query->orWhereHas("masterDivision",function($q) use ($input){
	        			$q->where('master_division.description','like','%'.$input['keyword_text'].'%');
	        		});
						}elseif($ex[$i]=='master_company'){
							$query->orWhereHas("masterCompany",function($q) use ($input){
	        			$q->where('master_company.name','like','%'.$input['keyword_text'].'%');
	        		});
						}else{
							$query->orWhere($ex[$i],'like','%'.$input['keyword_text'].'%');
						}
					}
				}
			}

			if(isset($input['keyword_date'])){
				if(isset($input['filter_date']) && !empty($input['filter_date'])){
					$ex = explode(",",$input['filter_date']);
					for ($i=0; $i < count($ex); $i++) {
						$query->orWhere($ex[$i],$input['keyword_date']);
					}
				}
			}
		});
    return $que;
  }

  public function scopeByOrder($query,$input)
  {
      if(isset($input['sort']))
      {
          $sort = json_decode($input['sort']);
          foreach ($sort as $sorter) {
              $query->orderBy($sorter->property,$sorter->direction);
          }
       } else {
          $query->orderBy('id','DESC');
      }

      return $query;
  }
}
