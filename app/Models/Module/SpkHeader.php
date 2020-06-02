<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class SpkHeader extends Model
{
	protected $table = "spk_header";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
      'name',
      'unit',
			'no_ref',
      'origin',
      'date',
      'no_ref_doc',
			'date_doc',
			'desc_source',
			'start_date',
			'end_date',
			'instructions',
			'customer_commitment_id',
			'customer_supplier_id',
      'status'
  ];

	public function spkDetail()
  {
    return $this->hasMany('App\Models\Module\SpkDetail','spk_header_id')->with('masterItems');
  }

  public function customerCommitment()
  {
    return $this->belongsTo('App\Models\Master\Customer','customer_commitment_id','id')->select('id','name','master_company_id','nip','master_division_id','email')->with('masterDivision','masterCompany');
  }

	public function customerSupplier()
  {
    return $this->belongsTo('App\Models\Master\Customer','customer_supplier_id','id')->select('id','name','master_company_id','nip','master_division_id','email')->with('masterDivision','masterCompany');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				if(isset($input['filter_text']) && !empty($input['filter_text'])){
					$ex = explode(",",$input['filter_text']);
					for ($i=0; $i < count($ex); $i++) {
						if($ex[$i]=='customer_commitment_name'){
							$query->orWhereHas("customerCommitment",function($q) use ($input){
	        			$q->where('customer.name','like','%'.$input['keyword_text'].'%');
	        		});
						}elseif($ex[$i]=='customer_supplier_name'){
							$query->orWhereHas("customerSupplier",function($q) use ($input){
	        			$q->where('customer.name','like','%'.$input['keyword_text'].'%');
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

	public static function boot() {
			parent::boot();

			static::deleted(function($data){
					RoleDetail::where('role_Id',$data->id)->delete();
			});
	}

}
