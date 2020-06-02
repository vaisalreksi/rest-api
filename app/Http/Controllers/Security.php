<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setup\Menu as Menu;
use App\Models\Setup\RoleDetail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helper\Helper;
use Illuminate\Http\Request;
use App;

class Security extends Controller
{
	protected $helper;

	private $dataRole = "";

    public function __construct()
    {
        // $this->middleware('jwt.auth');
        // $this->middleware('jwt.user');
        $this->middleware('updatedb');
        $this->middleware('cors');
        $this->helper = new Helper;

				App::setLocale("id");
    }

  public function getMenuSidebar(Request $request){
		$model = new Menu;
		$lang = "description";
		$lang_id = 1;
		if($lang_id == 2){
			$lang = "description";
		}elseif($lang_id==3){
			$lang = "description";
		}

		if($request->has('node') && $request->node != "root") {
			$id= $request->node;
		} else {
			$id= NULL;
		}
		$user = JWTAuth::toUser($request->input('token'));
		$this->dataRole = $user->role_id;

		$data = $model->where('status',0)->where('header_id',$id)->orderBy('sort','ASC')->get();
		$result['data_menu'] = $this->getChild($data,$this->dataRole,$lang);

		$id_menu = RoleDetail::select('menu_id as id','access','description');
		$id_menu = $id_menu->join('menu','menu.id','=','role_detail.menu_id');
		$id_menu = $id_menu->where('role_id',$this->dataRole)->get();
		$result['id_menu'] = $id_menu->toArray();
		// $result = $this->getChild($data,\Session::get('data')['role'],$lang);
		return \Response::json($result,200);
	}

	private function getChild($model,$role_id=null,$lang)
	{
		$return_data = [];
		if(!$model->isEmpty()) {
			$i = 0;
			foreach ($model as $row) {
				$model_role = [];
				if(!empty($role_id)) {
					$role = new RoleDetail;
					$data_model_role = $role->where(['role_id' => $role_id,
						'menu_id' => $row->id]);
					$model_role = $data_model_role->get();

					$count_model_role = $data_model_role->count();
					if($count_model_role > 0){
						if($model_role[0]->access == 0){
							continue;
						}
					}else{
						$findDetail = Menu::find($row->id);
						if($findDetail->header == 1){
							continue;
						}
					}

				}

				$return_data[$i] = [
						'id' => $row->id,
						'text' => $row->$lang,
						'url' => $row->url,
						'iconCls' => $row->icon,
						'header' => $row->header,
						'sort' => $row->sort
				];

				$leaf = false;
				$cekLeaf = Menu::where('header_id',$row->id)->where('status',0)->where('header',1)->count();
				if($cekLeaf > 0){
					$cekLeaf = new Menu;
					$cekLeaf = $cekLeaf->join('role_detail','role_detail.menu_id','=','menu.id');
					$cekLeaf = $cekLeaf->where('menu.header_id',$row->id)->where('role_detail.access',1);
					$cekLeaf = $cekLeaf->count();
					if($cekLeaf < 1){
						$leaf = true;
					}
				}else{
					$leaf = true;
				}

				if($leaf == true){
					$return_data[$i]['leaf'] = true;
				}

				//get role from usre role detail
				if(!empty($model_role)) {
					foreach($model_role as $baris) {
						$return_data[$i]['access'] = $baris->access;
					}
				}

				$Menu = new Menu;
				$model_child = $Menu->where('status','0')->where('header_id','=',$row->id)->orderBy('sort','ASC')->get();

				$result = $this->getChild($model_child,$this->dataRole,$lang);
				// $result = $this->getChild($model_child,$role_id,$lang);
				if(!empty($result)) {
					$return_data[$i]['children'] = $result;
				} else {
					if($row->header == "1")
						$return_data[$i]['leaf'] = TRUE;

					if($row->header == "0")	{
						$return_data[$i]['expanded'] = FALSE;
					}
				}
				$i++;
			}
		}

		return $return_data;
	}
}
