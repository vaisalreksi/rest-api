<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Master\MasterCompany;
use App\Http\Controllers\ResourceController;


class MasterCompanyController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',MasterCompany::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
                'name' =>$row->name,
                'email' =>$row->email,
								'phone' =>$row->phone,
								'address' =>$row->address,
								'faximile' =>$row->faximile,
                'no_kp' =>$row->no_kp,
								'date_kp' =>$row->date_kp,
                'notaris_date' =>$row->notaris_date,
                'notaris_name' =>$row->notaris_name,
								'notaris_no' =>$row->notaris_no,
                'status' =>$row->status,
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

        unset($input['token']);
        $data       = $this->masterStore($input,'',MasterCompany::class);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();

        unset($input['token']);
        $data  = $this->masterUpdate($input,'',MasterCompany::class,$input['id']);

        return \Response::json($data,200);
    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],MasterCompany::class,'');
        return \Response::json($data,200);
    }

    public function getMasterCompany(Request $request)
    {
        $input = $request->all();

        $data   = new MasterCompany;
        $data = $data->orderBy('name', 'asc')->select('id', 'name');

        if( Input::get('q')){
            $data = $data->where('name', 'like', '%'.Input::get('q').'%');
        }

				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
