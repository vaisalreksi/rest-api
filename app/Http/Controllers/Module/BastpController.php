<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\Bastp;
use App\Http\Controllers\ResourceController;


class BastpController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Bastp::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'no_ref' =>$row->no_ref,
                'date' =>$row->date,
                'bast_header_id' =>$row->bast_header_id,
                'status' =>$row->status
            );

						if(isset($row->bastHeader)){
							$data[$k]['bast_header'] = $row->bastHeader;
						}

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
          'bast_header_id' => 'required',
          'no_ref' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        unset($input['token']);
        $data       = $this->masterStore($input,'',Bastp::class);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'bast_header_id' => 'required',
          'no_ref' => 'required|unique:bastp,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Bastp::class,$input['id']);

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],Bastp::class,'');
        return \Response::json($data,200);
    }
}
