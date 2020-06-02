<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\BastHeader;
Use App\Models\Module\BastDetail;
use App\Http\Controllers\ResourceController;


class BastHeaderController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',BastHeader::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'no_ref' =>$row->no_ref,
                'date' =>$row->date,
                'status' =>$row->status,
                'data' => $row->spmkDetail
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
          'no_ref' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];

        unset($input['token']);
        unset($input['data']);
        $data       = $this->masterStore($input,'',BastHeader::class,BastDetail::class,$detail);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'no_ref' => 'required|unique:bast_header,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];

        unset($input['token']);
        unset($input['data']);
        $data  = $this->masterUpdate($input,'',BastHeader::class,$input['id'],BastDetail::class,$detail);

        return \Response::json($data,200);
    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],BastHeader::class,'');
        return \Response::json($data,200);
    }

    public function getBastHeader(Request $request)
    {
        $input = $request->all();

        $data   = new BastHeader;
        $data = $data->orderBy('name', 'asc')->selectRaw('id as value ,no_ref as display');

        if( Input::get('q')){
            $data = $data->where('no_ref', 'like', '%'.Input::get('q').'%');
        }
				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
