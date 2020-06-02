<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;

use App\Http\Controllers\Security;
use App\Models\Setup\Menu;
use App\Models\Setup\RoleDetail;
use App\Models\Setup\Role;
use DB;

class MenuController extends Security
{
	private $return_data;

	public function index(Request $request)
	{
		// $check = $this->checkRole('setup.Menu','view');
		// if(!$check['status']) {
		// 	return \Response::json($check,200);
		// }

		$input = $request->all();


		$Menu = new Menu;
		if(isset($input['node']) && $input['node'] != "root") {
			$parent_id = $input['node'];
		} else {
			$parent_id = NULL;
		}
		$model = $Menu->where('parent_id','=',$parent_id)->orderBy('req_seq_no','ASC')->get();

		$role_id = isset($input['id_role']) ? $input['id_role'] : NULL;

		$result = $this->getChild($model,$role_id);

		return \Response::json(array('text'=>".",'children'=>$result),200);
	}

	public function getData(Request $request)
	{

		$input = $request->all();

		$Menu = new Menu;
		if(isset($input['node']) && $input['node'] != "root") {
			$parent_id = $input['node'];
		} else {
			$parent_id = NULL;
		}
		$model = $Menu->where('header_id','=',$parent_id)->orderBy('sort','ASC')->get();

		$role_id = isset($input['id_role']) ? $input['id_role'] : NULL;

		$result = $this->getChild($model,$role_id);

		return \Response::json(array('text'=>".",'children'=>$result),200);
	}

	public function addData($input)
	{
		// $check = $this->checkRole('setup.Menu','store');
		// if(!$check['status']) {
		// 	return \Response::json($check,200);
		// }

		// $input = $request->all();
		if(isset($input['parentId'])){
			unset($input['parentId']);
		}

		if(isset($input['index'])){
			unset($input['index']);
		}

		if(isset($input['leaf'])){
			unset($input['leaf']);
		}

		$message =  __('messages.success_save');
		$success = true ;
		$result = null;
		DB::beginTransaction();
		try{
			$Menu = new Menu;
			if(!empty($input['id']) && $input['id'] > 0) {
				$Menu = Menu::find($input['id']);
				$message =  __('messages.success_update');
			} else {
				if(is_numeric($input['parent_id']))
					$sequence = Menu::where('parent_id',$input['parent_id'])->max('req_seq_no');
				else
					$sequence = Menu::where('parent_id',null)->max('req_seq_no');
				$input['req_seq_no'] = $sequence + 1;
				unset($input["id"]);
			}
			unset($input["data"]);
			unset($input["_token"]);
			if(isset($input['parent_id']))  {
				if($input['parent_id'] == "id")
					$input['parent_id'] = null;
			}

			foreach($input as $key=>$data){
				if($key=="iconCls"){
					$key = "icon";
				}
				$Menu->{$key} = $data;
			}
			$Menu->save();

			$result = $this->getOneData($Menu->id);
			DB::commit();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;
			DB::rollback();
		}
		$data = array(
			'success' => $success  ,
			'message' => $message,
			'data' => $result
		);

		return \Response::json($data,200);
	}

	public function updateData(Request $request)
	{
		// $check = $this->checkRole('setup.Menu','update');
		// if(!$check['status']) {
		// 	return \Response::json($check,200);
		// }

		$input = $request->all();

		// return count($input);

		$message = __('messages.success_update');
		$success = true ;

		unset($input["_dc"]);
		unset($input["token"]);

		if(isset($input['id'])) {
			$update = $this->addData($input);
			return $update;
		}

		DB::beginTransaction();
		try{
			if(isset($input['id'])) {
				$this->setSequenceUpdate($input);
			} else {
				foreach ($input as $value) {
					$this->setSequenceUpdate($value);
				}
			}

		DB::commit();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;
			DB::rollback();
		}
		$data = array(
			'success' => $success  ,
			'message' => $message
		);

		return \Response::json($data,200);
	}

	public function deleteData(Request $request)
	{

		// $check = $this->checkRole('setup.Menu','delete');
		// if(!$check['status']) {
		// 	return \Response::json($check,200);
		// }

		$input = $request->all();
		unset($input['_token']);
		unset($input['_dc']);

		if(is_array($input)) {
			$data = [];
			foreach($input as $key => $value) {
				if(is_array($value)) {
					foreach($value as $row => $val) {
						$data[] = $val;
					}
				} else {
					$data[] = $value;
				}
			}
		} else {
			$data = array($input['id']);
		}

		$message =  __('messages.success_delete');
		$success = true ;

		DB::beginTransaction();
		//cek ke user role detail
		$role_detail = UserRoleDetail::whereIn('menu_id',$data)->delete();

		try{
			$id = Menu::whereIn('id',$data)->delete();

			DB::commit();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;
			DB::rollback();
		}
		$data = array(
			'success' => $success  ,
			'message' => $message
		);

		return \Response::json($data,200);
	}

	private function getChild($model,$role_id)
	{
		$return_data = [];
		if(!$model->isEmpty()) {
			$i = 0;
			foreach ($model as $row) {
				$return_data[$i] = [
						'id' => $row->id,
						'description' => $row->description,
						'status' => $row->status,
						'url' => $row->url,
						'header_id' => $row->header_id,
						'header' => $row->header,
						'sort' => $row->sort,
						'icon' => $row->icon,
						'access' => false
				];

				//get role from usre role detail
				if(!empty($role_id)) {
					$role = new RoleDetail;
					$model_role = $role->where(['role_id' => $role_id,
						'menu_id' => $row->id])->get();

					if(!$model_role->isEmpty()) {
						foreach($model_role as $baris) {
							if($baris->access == 1)
								$return_data[$i]['access'] = true;
						}
					}
				}

					$Menu = new Menu;
					$model_child = $Menu->where('header_id','=',$row->id)->orderBy('sort','ASC')->get();

					$result = $this->getChild($model_child,$role_id);
					if(!empty($result)) {
						$return_data[$i]['children'] = $result;
					} else {
						if($row->header == "2")
							$return_data[$i]['leaf'] = TRUE;
					}
				$i++;
			}
		}

		return $return_data;
	}

	private function setSequenceUpdate($value) {
		$Menu = Menu::find($value['id']);

		if(isset($value['index'])) {
			$sequence = $value['index']+1;
		} else {
			$sequence = 1;
		}

		if(isset($value['parentId'])) {
			if($value['parentId'] == "root") {
				$Menu->parent_id = NULL;
				$Menu->header = 1;
				$Menu->req_seq_no = $sequence;
			} else {
				$Menu->parent_id = $value['parentId'];
				// $Menu->header = 2;
				$Menu->req_seq_no = $sequence;
			}
		} else {
			$Menu->req_seq_no = $sequence;
		}

		$Menu->save();
	}

	private function getOneData($id)
	{
		$get_data = Menu::where('id',$id)->get()->toArray();
			foreach ($get_data as $value) {
				$result[0] = [
					'id' => $value['id'],
					'description' => $value['description'],
					'en_description' => $value['en_description'],
					'ch_description' => $value['ch_description'],
					'req_seq_no' => $value['req_seq_no'],
					'parent_id' => $value['parent_id'],
					'header' => $value['header'],
					'url' => $value['url'],
					'status' => $value['status'],
				];

				if($value['header'] == "2"){
					$result[0]['leaf'] = true;
				} else {
					$result[0]['expanded'] = true;
				}
			}
		return $result;
	}

	public function menuTree()
	// public function dataMenu(Request $request)
	{
		$Menu = new Menu;
		$model = $Menu->whereNull('parent_id')->where('header',1)->orderBy('req_seq_no','ASC')->get();
		$this->return_data = [];
		$this->getChildMenu($model);
		return \Response::json($this->return_data,200);
	}



	private function getChildMenu($model,$desc=null)
	{
		if(!$model->isEmpty()) {
			$i = 0;
			foreach ($model as $row) {
				$paramDesc = $row->description;
				if($desc != ""){
					$paramDesc = $desc." > ".$row->description;
				}
				$this->return_data[] = [
						'id' => $row->id,
						'description' => $paramDesc,
						'en_description' => $row->en_description,
						'ch_description' => $row->ch_description,
						'req_seq_no' => $row->req_seq_no,
						'icon' => $row->icon,
						'expanded' => true,
				];

				$Menu = new Menu;
				$model_child = $Menu->where('parent_id','=',$row->id)->where('header',1)->orderBy('req_seq_no','ASC')->get();
				if(!empty($model_child)){
					$description = "";
					if($paramDesc != ""){
						$description = $paramDesc;
					}
					$this->getChildMenu($model_child, $description);
				}

				$i++;
			}
		}
	}


}
