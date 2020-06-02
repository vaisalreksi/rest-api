<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
  protected $table = "users";
  public $timestamps = true;
  protected $primaryKey = 'id';
  // protected $with = ['userRole'];

  protected $fillable = [
       'name',
       'username',
       'password',
       'role_id',
       'status',
  ];

  public function role()
  {
    return $this->belongsTo('App\Models\Setup\Role','role_id','id');
  }

  public function scopeBySearch($que,$input)
  {
    $que->where('status',0)->whereNull('deleted_at');
		$que->where(function($query) use ($input){
			if(isset($input['keyword_text'])){
				if(isset($input['filter_text']) && !empty($input['filter_text'])){
					$ex = explode(",",$input['filter_text']);
					for ($i=0; $i < count($ex); $i++) {
						if($ex[$i]=='role'){
							$query->orWhereHas("role",function($q) use ($input){
	        			$q->where('role.description','like','%'.$input['keyword_text'].'%');
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
        $query->orderBy('id','asc');
      }

      return $query;
  }

}
