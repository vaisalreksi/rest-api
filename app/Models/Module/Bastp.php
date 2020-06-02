<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class Bastp extends Model
{
	protected $table = "bastp";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
      'no_ref',
			'date',
			'bast_header_id',
      'status'
  ];

  public function bastHeader()
  {
    return $this->belongsTo('App\Models\Module\BastHeader','bast_header_id','id');
  }

  public function scopeBySearch($que,$input)
  {
		$que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				if(isset($input['filter_text']) && !empty($input['filter_text'])){
					$ex = explode(",",$input['filter_text']);
					for ($i=0; $i < count($ex); $i++) {
						$query->orWhere($ex[$i],'like','%'.$input['keyword_text'].'%');
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
