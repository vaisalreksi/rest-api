<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\QueryException;
use App\Http\Requests;

use App\Http\Controllers\Security;
use App\Models\Setup\UserRoleDetail;

class UsersRoleDetailController extends Security
{
	
	public function index(Request $request)
	{
		$input = $request->all();		
		$page = isset($input['page'])?$input['page']:1;
		$start = isset($input['start'])?$input['start']:0;
		$limit = isset($input['limit'])?$input['limit']:50;

		$role = new UserRoleDetail;	
		$model = $role->byUserRole($request)->skip(($limit*($page-1)))->take($limit);				
	
		return \Response::json($model->toArray(),200);
	}

	public function store(Request $request) {
		$input = $request->all();
		
		$message =  'Ok';
		$success = true ;
		try{
			$role = new UserRole;			
			unset($input["_dc"]);
			unset($input["_token"]);
			foreach($input as $key=>$data){
				$role->{$key} = $data;
			}
			$role->save();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;		
		}
		$data = array(
			'success' => $success  ,
			'message' => $message
		);

		return \Response::json($data,200);		
	}

	public function update(Request $request) {
		$input = $request->all();
		
		$message =  'Ok';
		$success = true ;
		try{
			$id = $input["id"];
			$role = UserRole::find($id);
			unset($input["id"]);
			unset($input["_dc"]);
			unset($input["_token"]);
			foreach($input as $key=>$data){
				$role->{$key} = $data;
			}
			$role->save();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;		
		}
		$data = array(
			'success' => $success  ,
			'message' => $message
		);
	}

	public function delete(Request $request)
	{
		$input = $request->all();
		
		$message =  'Ok';
		$success = true ;
		try{
			$id = UserRole::find($input['id']);
			$id->delete();
		}catch(QueryException $e){
			$message = $e->getMessage();
			$success = false ;		
		}		
		$data = array(
			'success' => $success  ,
			'message' => $message
		);
		
		return \Response::json($data,200);
	}
}