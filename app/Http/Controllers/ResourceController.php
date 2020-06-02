<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Exception;
use Helper;
use Tymon\JWTAuth\Facades\JWTAuth;
Use App\Models\Setup\AuditTrail;
use Carbon\Carbon;

class ResourceController extends Security
{
	protected function laravelValidation($input,$rules)
	{
		$validator = Validator::make($input, $rules);

        if ($validator->fails()) {
        	$message = $validator->errors()->getMessages() ;
        	$psn = '';
        	foreach($message as $key => $val) {
        		$psn .= $val[0].', ';
        	}
        	$data = array(
				'success' => false,
				'message' => $psn
			);
            return $data;
        } else {
        	return array('success' => true);
        }
	}

	protected function masterIndex($input,$path,$modelClass)
	{

		$page = isset($input['page'])?$input['page']:1;
		$limit = isset($input['limit'])?$input['limit']:10;

		$model = new $modelClass;
		$getData = $model->bySearch($input)->byOrder($input)->skip(($limit*($page-1)))->take($limit)->get();
		$total = $model->bySearch($input)->count();
		$jumlah_data_page = $page * $limit;
		if($jumlah_data_page >= $total) $jumlah_data_page = $total;
		$total_page = ceil($total/$limit);

		$result = array(
				'total' => $total,
				'count_page' => $jumlah_data_page,
				'current_page' => $page,
				'data' => $getData,
				'sum_page' => $total_page
		);

		return $result;
	}

	protected function masterStore($input,$path,$modelClass,$childClass=null,$childInput=null)
	{

		$message =  __('messages.success_save');
		$success = true ;
		$detail = array();

		DB::beginTransaction();

		try {
			$model = new $modelClass;
			foreach($input as $key=>$data){
				$model->{$key} = $data;
			}

			$status = $model->save();

			if($status && !empty($childClass)) {
				foreach ($childInput as $row) {
					$child_model = new $childClass;
					$child_model->{$child_model['foreignKey']} = $model->id;
					foreach ($row as $key => $value) {
						if($key == "id"){
							continue;
						}
						$child_model->{$key}=$value;
					}
					$child_model->save();
				}

			}

			$detail['id'] = $model->id;
			foreach($input as $key=>$data){
				$detail[$key] = $model->{$key};
			}

			DB::commit();
		} catch (Exception $e) {
			$message = __('messages.error_save');
			// $message = $e->getMessage();
			$success = false;
			DB::rollback();
		}


		return array('data'=>$detail,'success' => $success,'message'=>$message);
	}

	protected function masterUpdate($input,$path,$modelClass,$id,$childClass=null,$childInput=null)
	{

		$message =  __('messages.success_update');
		$success = true ;

		DB::beginTransaction();

		try {

			$model = $modelClass::find($id);
			if(!empty($model))
			{
				foreach($input as $key=>$data){
					$model->{$key} = $data;
				}

				$status = $model->save();

				if($status && !empty($childClass)) {
					foreach ($childInput as $row) {
						if(isset($row['id'])) {
							$child_model = $childClass::find($row['id']);
						} else {
							$child_model = new $childClass;
							$child_model->{$child_model['foreignKey']} = $model->id;
						}
						foreach ($row as $key => $value) {
							if($key == "id"){
								continue;
							}
							$child_model->{$key}=$value;
						}
						$child_model->save();
					}
				}

				$id = $model->id;
				DB::commit();
			} else{
				$success = false;
				$message = __('messages.data_not_found');
			}

		} catch (Exception $e) {
			$message = __('messages.error_update');
			// $message = $e->getMessage();
			$success = false;
			DB::rollback();
		}

		return array('id'=>$id, 'success' => $success,'message'=>$message);
	}

	protected function masterDestroy($id,$model,$path)
	{

		$message =  __('messages.success_delete');
		$success = true ;

		DB::beginTransaction();

		try{
			$model = $model::find($id);
			$model->status = 1;
			$model->deleted_at = Carbon::now();
			$model->save();
			// $model->delete();
			DB::commit();
		}  catch (Exception $e){
			//$message = $e->getMessage();
			$message = __('messages.error_relasi');
			$success = false;
			DB::rollback();
		}

		return array('message'=>$message,'success'=>$success);
	}

	// protected function masterDestroyHeader($id,$model,$path,$detail)
	// {
	//
	// 	$message =  __('messages.success_delete');
	// 	$success = true ;
	//
	// 	DB::beginTransaction();
	//
	// 	try{
	// 		foreach ($detail as $value) {
	// 			$child_model = new $value;
	// 			$child_model = $child_model->where($child_model['foreignKey'],$id);
	// 			$child_model->delete();
	// 		}
	//
	// 		$model = $model::find($id);
	// 		$model->delete();
	// 		DB::commit();
	// 	}  catch (Exception $e){
	// 		$message = $e->getMessage();
	// 		$success = false;
	// 		DB::rollback();
	// 	}
	//
	// 	$data = array(
	// 		'success' => $success  ,
	// 		'message' => $message
	// 	);
	//
	// 	return array('data'=>$data);
	// }

	public function auditTrail($token,$module,$refno=null,$action)
	{
		$user = JWTAuth::toUser($token);
		if(!empty($user)){
			$save = new AuditTrail;
			$save->module = $module;
			$save->no_ref = $refno;
			$save->action = $action;
			$save->users_id = $user->id;
			$save->save();
		}
	}
}
