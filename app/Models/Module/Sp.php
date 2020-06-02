<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class Sp extends Model
{
	protected $table = "sp";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
      'no_ref',
      'date',
			'contract_value',
			'payment',
      'dipa_no',
			'dipa_date',
			'deadline',
			'bank_account',
			'bank_no',
			'spk_header_id',
			'status'
  ];

  public function spkHeader()
  {
    return $this->belongsTo('App\Models\Module\SpkHeader','spk_header_id','id')->select('id','no_ref','name','origin','date','customer_commitment_id','customer_supplier_id','start_date','end_date')->with('customerCommitment','customerSupplier','spkDetail');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				if(isset($input['filter_text']) && !empty($input['filter_text'])){
					$ex = explode(",",$input['filter_text']);
					for ($i=0; $i < count($ex); $i++) {
						if($ex[$i]=='spk_header_no_ref'){
							$query->orWhereHas("spkHeader",function($q) use ($input){
	        			$q->where('spk_header.no_ref','like','%'.$input['keyword_text'].'%');
	        		});
						}elseif($ex[$i]=='spk_header_name'){
							$query->orWhereHas("spkHeader",function($q) use ($input){
	        			$q->where('spk_header.name','like','%'.$input['keyword_text'].'%');
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
