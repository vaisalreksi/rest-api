<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Master\MasterLetterType;
use App\Http\Controllers\ResourceController;


class MasterLetterTypeController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',MasterLetterType::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
                'description' =>$row->description,
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
        $result = $this->laravelValidation($input, [
                'description' => 'required|max:100'
            ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        unset($input['token']);
        $data       = $this->masterStore($input,'',MasterLetterType::class);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
         $result = $this->laravelValidation($input, [
								'description' => 'required|max:100'
            ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        unset($input['token']);
        $data  = $this->masterUpdate($input,'',MasterLetterType::class,$input['id']);

        return \Response::json($data,200);
    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],MasterLetterType::class,'');
        return \Response::json($data,200);
    }

    public function getMasterLetterType(Request $request)
    {
        $input = $request->all();

        $data   = new MasterLetterType;
        $data = $data->orderBy('description', 'asc')->select('id as value', 'description as display');

        if( Input::get('q')){
            $data = $data->where('description', 'like', '%'.Input::get('q').'%');
        }
        $data = $data->where('status',0);

        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
