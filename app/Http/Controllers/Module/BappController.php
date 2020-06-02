<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\Bapp;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class BappController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Bapp::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
                'no_ref' =>$row->no_ref,
                'date' =>$row->date,
                'sk_no' =>$row->sk_no,
                'sk_date' =>$row->sk_date,
                'bahp_id' =>$row->bahp_id,
                'customer_id' =>$row->customer_id,
                'status' =>$row->status
            );

						if(isset($row->bahp)){
							$data[$k]['bahp'] = $row->bahp;
						}

            if(isset($row->customer)){
							$data[$k]['customer'] = $row->customer;
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
          'bahp_id' => 'required',
          'customer_id' => 'required',
          'no_ref' => 'required|unique:bapp,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }
        $token = $input['token'];
        unset($input['token']);
        $data       = $this->masterStore($input,'',Bapp::class);
        if($data['success']==true) $this->auditTrail($token,'BAPP',$input['no_ref'],'SAVE');
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'bahp_id' => 'required',
          'customer_id' => 'required',
          'no_ref' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $token = $input['token'];
        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Bapp::class,$input['id']);
        if($data['success']==true) $this->auditTrail($token,'BAPP',$input['no_ref'],'UPDATE');

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $ref = Bapp::find($input['id']);
        $data       = $this->masterDestroy($input['id'],Bapp::class,'');
        if($data['success']==true) $this->auditTrail($input['token'],'BAPP',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }
}
