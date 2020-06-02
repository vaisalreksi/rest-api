<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Setup\Role;
Use App\Models\Setup\RoleDetail;
use App\Http\Controllers\ResourceController;


class RoleController extends ResourceController
{
    public function getRole(Request $request)
    {
        $input = $request->all();

        $data   = new Role;
        $data = $data->orderBy('description', 'asc')->select('id as value', 'description as display');

        if( Input::get('q')){
            $data = $data->where('description', 'like', '%'.Input::get('q').'%');
        }
				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }

    public function index(Request $request)
  	{

      $input  = $request->all();
      $result = $this->masterIndex($input,'',Role::class);
      $data   = array();
      $k      = 0;
      foreach ($result['data'] as $row)
      {
          $data[$k]   = array(
              'id' =>$row->id,
              'description' =>$row->description,
              'status' =>$row->status,
              'data' => $row->roleDetail
          );

          $k++;
      }

      $result = array(
          'total' => $result['total'],
          'count_page' => $result['count_page'],
          'current_page' => $result['current_page'],
          'data' => $data,
          'sum_page' => $result['sum_page']
      );

      return \Response::json($result,200);
  	}

  	public function store(Request $request)
    {

      $input      = $request->all();

      $detail = $input['data'];
      unset($input['token']);
      unset($input['data']);

      $data       = $this->masterStore($input,'',Role::class);
      if($data['success']==true){
        if(!empty($detail)) $this->setMenuRole($detail, $data['data']['id']);
      }
      return \Response::json($data,200);

  	}

  	public function update($dataId,Request $request)
    {

      $input      = $request->all();

      $detail = $input['data'];
      unset($input['token']);
      unset($input['data']);
      $data  = $this->masterUpdate($input,'',Role::class,$input['id']);
      if($data['success']==true){
        if(!empty($detail)) $this->setMenuRole($detail, $input['id']);
      }
      return \Response::json($data,200);

  	}

  	public function destroy(Request $request)
  	{

  		$input = $request->all();
  		//cek ke user role detail
  		// $role_detail = RoleDetail::where('role_id',$input['id'])->update(['status'=>1,'deleted_at'=>\Carbon\Carbon::now()]);
  		$data = $this->masterDestroy($input['id'],Role::class,'');
  		return \Response::json($data,200);
  	}

  	private function setMenuRole($menu_data, $role_id)
  	{
  		$data_input = array();
  		$k=1;
  		foreach($menu_data as $row) {
  			$data_input[$k] = array(
  					'role_id' => $role_id,
  					'menu_id' => $row['menu_id'],
  				);
  			if(isset($row['access'])) {
  				$data_input[$k]['access'] = ($row['access'] > 0) ? 1 : 0;
  			} else {
  				$data_input[$k]['access'] = 0;
  			}

  			$getData = RoleDetail::where('role_id',$role_id)->where('menu_id',$row['menu_id'])->get();

  			if(!$getData->isEmpty())
  			{
  				$data_input[$k]['updated_at'] = \Carbon\Carbon::now();
  				RoleDetail::where('role_id',$role_id)->where('menu_id',$row['menu_id'])->update($data_input[$k]);
  			} else {
  				$data_input[$k]['created_at'] = \Carbon\Carbon::now();
  				RoleDetail::insert($data_input[$k]);
  			}
  			$k++;
  		}
  	}
}
