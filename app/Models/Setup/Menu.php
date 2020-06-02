<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
 	protected $table = "menu";
	public $timestamps = true;
	protected $primaryKey = 'id';

	protected $fillable = [
		'description',
		'header_id',
		'header',
		'sort',
		'url',
		'status'
	];

    public function parent()
    {
    	return $this->hasMany('App\Models\Setup\Menu','header_id','id');
    }

    public function roleDetail() {
        // return $this->hasMany('App\Models\Setup\RoleDetail','menu_id','id')->where('role_id',\Session::get('data')['role']);
    }

}
