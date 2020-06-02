<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
	protected $table = "bast";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
			'no_ref',
			'date',
			'sk_no',
			'sk_date',
			'customer_id',
			'bahp_id',
			'status'
  ];

  public function bahp()
  {
    return $this->belongsTo('App\Models\Module\Bahp','bahp_id','id')->select('id','no_ref','sp_id','date')->with('sp');
  }

	public function customer()
  {
    return $this->belongsTo('App\Models\Master\Customer','customer_id','id')->select('id','name','master_company_id','nip','master_division_id','email')->with('masterDivision','masterCompany');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				$query->Where('no_ref','like','%'.$input['keyword_text'].'%');
				$query->orWhere('sk_no','like','%'.$input['keyword_text'].'%');
				$query->orWhereHas("bahp",function($q) use ($input){
					$q->where('bahp.no_ref','like','%'.$input['keyword_text'].'%');
					$q->orWhereHas("sp",function($qu) use ($input){
						$qu->whereHas("spkHeader",function($quer) use ($input){
							$quer->where('spk_header.name','like','%'.$input['keyword_text'].'%');
							$quer->orWhere('spk_header.no_ref','like','%'.$input['keyword_text'].'%');
						});
					});
				});
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
