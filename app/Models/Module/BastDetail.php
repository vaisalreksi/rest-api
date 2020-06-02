<?php

namespace App\Models\Module;

use Illuminate\Database\Eloquent\Model;

class BastDetail extends Model
{
	protected $table = "bast_detail";
	public $timestamps = true;
	protected $primaryKey = 'id';
	protected $foreignKey = 'bast_header_id';

	protected $fillable = [
      'file',
      'bast_header_id',
      'master_letter_type_id',
      'status'
  ];

  public function bastHeader()
  {
    return $this->belongsTo('App\Models\Module\BastHeader','bast_header_id','id');
  }

	public function masterLetterType()
  {
    return $this->belongsTo('App\Models\Master\MasterLetterType','master_letter_type_id','id')->select('id','description');
  }

  public function scopeBySearch($que,$input)
  {
			$que->where('spk_header_id',$input['spk_header_id']);
			$que->where('status',0)->whereNull('deleted_at');
			$que->where(function($query) use ($input){
				if(isset($input['keyword_text'])){
					if(isset($input['filter_text']) && !empty($input['filter_text'])){
						$ex = explode(",",$input['filter_text']);
						for ($i=0; $i < count($ex); $i++) {
							if($ex[$i]=='master_letter_type'){
								$query->orWhereHas("masterLetterType",function($q) use ($input){
		        			$q->where('master_letter_type.description','like','%'.$input['keyword_text'].'%');
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
