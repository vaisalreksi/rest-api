<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\Bahp;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class BahpController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Bahp::class);
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
                'sp_id' =>$row->sp_id,
                'result' =>$row->result,
                'checker_1' =>$row->checker_1,
                'checker_2' =>$row->checker_2,
								'checker_3' =>$row->checker_3,
                'status' =>$row->status
            );

						if(isset($row->sp)){
							$data[$k]['sp'] = $row->sp;
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
          'sp_id' => 'required',
          'no_ref' => 'required|unique:bahp,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }
        $token = $input['token'];
        unset($input['token']);
        $data       = $this->masterStore($input,'',Bahp::class);
        if($data['success']==true) $this->auditTrail($token,'BAHP',$input['no_ref'],'SAVE');
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'sp_id' => 'required',
          'no_ref' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $token = $input['token'];
        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Bahp::class,$input['id']);
        if($data['success']==true) $this->auditTrail($token,'BAHP',$input['no_ref'],'UPDATE');

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $ref = Bahp::find($input['id']);
        $data       = $this->masterDestroy($input['id'],Bahp::class,'');
        if($data['success']==true) $this->auditTrail($input['token'],'BAHP',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }

    public function getBahp(Request $request)
    {
        $input = $request->all();

        $data   = new Bahp;
        $data = $data->orderBy('no_ref', 'asc')->selectRaw('id as value ,no_ref as display');

        if( Input::get('q')){
            $data = $data->where('no_ref', 'like', '%'.Input::get('q').'%');
        }
				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
