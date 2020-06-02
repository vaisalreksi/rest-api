<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class RoleDetail extends Model
{
 	protected $table = "role_detail";
	public $timestamps = true;
  protected $foreignKey = 'role_id';
	protected $primaryKey = 'id';

	protected $fillable = [
		    'role_id',
        'menu_id',
        'access',
        'status'
	];

    public function role()
    {
    	return $this->belongsTo('App\Models\Setup\Role','id','role_id');
    }
}
