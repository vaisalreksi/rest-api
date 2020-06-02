<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\Sp;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class SpController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Sp::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'no_ref' =>$row->no_ref,
                'date' =>$row->date,
                'contract_value' =>round($row->contract_value),
                'contract_value_terbilang' =>"",
                'payment' =>round($row->payment),
                'payment_terbilang' =>"",
                'dipa_no' =>$row->dipa_no,
                'dipa_date' =>$row->dipa_date,
                'deadline' =>$row->deadline,
                'bank_account' =>$row->bank_account,
                'bank_no' =>$row->bank_no,
                'spk_header_id' =>$row->spk_header_id,
                'status' =>$row->status
            );


            if($row->contract_value > 0) $data[$k]['contract_value_terbilang'] = $this->helper->terbilang($row->contract_value);
            if($row->payment > 0) $data[$k]['payment_terbilang'] = $this->helper->terbilang($row->payment);

						if(isset($row->spkHeader)){
              $countDate = 0;
              if(!empty($row->spkHeader->start_date) && !empty($row->spkHeader->end_date)){
                  $date = Carbon::parse($row->spkHeader->start_date);
                  $diff = $date->diffInDays($row->spkHeader->end_date);

                  $countDate = $diff + 1;
              }

              $terbilang = $this->helper->terbilang($countDate);

              $data[$k]['spk_header'] = $row->spkHeader;
							$data[$k]['spk_header']['time_working'] = "Jangka waktu penyelesaian pekerjaan pengadaan barang ini adalah selama : ".$countDate." (".$terbilang.") hari kalender.";
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
          'spk_header_id' => 'required',
          'no_ref' => 'required|unique:sp,no_ref,'.$input['no_ref']
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $token = $input['token'];
        unset($input['token']);
        $data       = $this->masterStore($input,'',Sp::class);
        if($data['success']==true) $this->auditTrail($token,'SP',$input['no_ref'],'SAVE');
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'spk_header_id' => 'required',
          'no_ref' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $token = $input['token'];
        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Sp::class,$input['id']);
        if($data['success']==true) $this->auditTrail($token,'SP',$input['no_ref'],'UPDATE');

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $ref = Sp::find($input['id']);
        $data       = $this->masterDestroy($input['id'],Sp::class,'');
        if($data['success']==true) $this->auditTrail($input['token'],'SP',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }

    public function getSp(Request $request)
    {
        $input = $request->all();

        $data   = new Sp;
        $data = $data->orderBy('no_ref', 'asc')->selectRaw('id as value ,no_ref as display');

        if( Input::get('q')){
            $data = $data->where('no_ref', 'like', '%'.Input::get('q').'%');
        }
				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
