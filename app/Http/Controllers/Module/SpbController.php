<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\Spb;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class SpbController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Spb::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'no_ref' =>$row->no_ref,
                'sp_id' =>$row->sp_id,
                'address' =>$row->address,
                'penalty' =>$row->penalty,
                'status' =>$row->status
            );

						if(isset($row->sp)){
							$data[$k]['sp'] = $row->sp;
						}

            if(isset($row->sp->spkHeader)){
              $countDate = 0;
              if(!empty($row->sp->spkHeader->start_date) && !empty($row->sp->spkHeader->end_date)){
                  $date = Carbon::parse($row->sp->spkHeader->start_date);
                  $diff = $date->diffInDays($row->sp->spkHeader->end_date);

                  $countDate = $diff + 1;
              }

              $terbilang = $this->helper->terbilang($countDate);

              $data[$k]['time_working'] = "selama ".$countDate." (".$terbilang.") hari kalender dan pekerjaan harus sudah selesai pada tanggal";

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
          'no_ref' => 'required|unique:spb,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }
        $token = $input['token'];
        unset($input['token']);
        $data       = $this->masterStore($input,'',Spb::class);
        if($data['success']==true) $this->auditTrail($token,'SPB',$input['no_ref'],'SAVE');
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
        $data  = $this->masterUpdate($input,'',Spb::class,$input['id']);
        if($data['success']==true) $this->auditTrail($token,'SPB',$input['no_ref'],'UPDATE');

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $ref = Spb::find($input['id']);
        $data       = $this->masterDestroy($input['id'],Spb::class,'');
        if($data['success']==true) $this->auditTrail($input['token'],'SPB',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }
}
