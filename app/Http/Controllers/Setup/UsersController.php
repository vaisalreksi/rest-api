<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\ResourceController;
use App\Models\Setup\Users;
use Auth;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends ResourceController
{
	public function index(Request $request)
	{
			$input  = $request->all();
			$result = $this->masterIndex($input,'',Users::class);
			$data   = array();
			$k      = 0;
			foreach ($result['data'] as $row)
			{
					$data[$k]   = array(
							'id' =>$row->id,
							'role_id' =>$row->role_id,
							'name' =>$row->name,
							'username' =>$row->username,
							'status' =>$row->status,
					);

					if(isset($row->role)){
						$data[$k]['role'] = $row->role->description;
					}

					$k++;
			}
			return \Response::json(array('data'=>$data,'total'=>$result['total']),200);
	}

	public function store(Request $request)
	{
			$input      = $request->all();
			$result = $this->laravelValidation($input, [
				'username' => 'required',
				'password' => 'required',
				'role_id' => 'required',
			]);

			if(!$result['success']){
					return \Response::json(array('data'=>$result),200);
			}

			unset($input['token']);
			$input['password'] = \Hash::make($input['password']);
			$data       = $this->masterStore($input,'',Users::class);
			return \Response::json($data,200);
	}

	public function update(Request $request)
	{

			$input      = $request->all();
			$result = $this->laravelValidation($input, [
			 'username' => 'required',
			 'role_id' => 'required',
		 ]);

			if(!$result['success']){
					return \Response::json(array('data'=>$result),200);
			}

			unset($input['token']);
			if(!empty($input['password'])) $input['password'] = \Hash::make($input['password']);
			$data  = $this->masterUpdate($input,'',Users::class,$input['id']);

			return \Response::json($data,200);
	}
	public function destroy(Request $request)
	{
			$input      = $request->all();
			$message    = __('message.success_delete');
			$success    = true;

			$data       = $this->masterDestroy($input['id'],Users::class,'');
			return \Response::json($data,200);
	}

	public function changePassword(Request $request)
	{
		$input = $request->all();
		$user = JWTAuth::toUser($input['token']);

		$data = Users::find($user->id);
		if(!Hash::check($input['old_password'], $data->password)) {
			$result = array(
				'response' => array('success'=>false,'message'=>'your old password is wrong'),
				'data' => [],
				'success' => false
			);
			return \Response::json($result,503);
		}

		$encrypt = Hash::make($input['new_password']);
		$data->password = $encrypt;
		if(!$data->save()){
			$result = array(
				'response' => array('success'=>false,'message'=>'something is wrong'),
				'data' => [],
				'success' => false
			);
			return \Response::json($result,503);
		}

		$result = array(
			'response' => array('success'=>true,'message'=>'success change the password'),
			'data' => [],
			'success' => true
		);
		return \Response::json($result,200);
	}

}
