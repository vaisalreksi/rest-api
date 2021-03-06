<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class SpmkHeader extends Model
{
	protected $table = "spmk_header";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
      'no_ref',
			'sp_id',
			'penalty',
      'terms',
      'status'
  ];

	public function spmkDetail()
  {
    return $this->hasMany('App\Models\Module\SpmkDetail','spmk_header_id');
  }

	public function sp()
  {
    return $this->belongsTo('App\Models\Module\Sp','sp_id','id')->select('id','no_ref','spk_header_id')->with('spkHeader');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				$query->orWhereHas("sp",function($q) use ($input){
					$q->where('sp.no_ref','like','%'.$input['keyword_text'].'%');
					$q->orWhereHas("spkHeader",function($quer) use ($input){
						$quer->where('spk_header.name','like','%'.$input['keyword_text'].'%');
						$quer->orWhere('spk_header.no_ref','like','%'.$input['keyword_text'].'%');
					});
				});

				$query->orWhere('no_ref','like','%'.$input['keyword_text'].'%');
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
