<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\SpkHeader;
Use App\Models\Module\SpkDetail;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class SpkHeaderController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',SpkHeader::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
          $countDate = 0;
          if(!empty($row->start_date) && !empty($row->end_date)){
              $date = Carbon::parse($row->start_date);
              $diff = $date->diffInDays($row->end_date);

              $countDate = $diff + 1;
          }

          $terbilang = $this->helper->terbilang($countDate);

          $time_working = "WAKTU PELAKSANAAN PEKERJAAN : ".$countDate." (".$terbilang.") hari kalender dan pekerjaan harus sudah selesai pada tanggal";

            $data[$k]   = array(
                'id' =>$row->id,
								'name' =>$row->name,
								'unit' =>$row->unit,
                'no_ref' =>$row->no_ref,
								'origin' =>$row->origin,
								'date' =>$row->date,
								'no_ref_doc' =>$row->no_ref_doc,
                'date_doc' =>$row->date_doc,
                'desc_source' =>$row->desc_source,
                'start_date' =>$row->start_date,
                'end_date' =>$row->end_date,
                'time_working' => $time_working,
                'intructions' =>$row->intructions,
                'customer_commitment_id' =>$row->customer_commitment_id,
                'customer_supplier_id' =>$row->customer_supplier_id,
                'status' =>$row->status,
                'data' => null
            );

            if(!empty($row->spkDetail)){
              $i = 0;
              foreach ($row->spkDetail as $value) {
                  $data[$k]['data'][$i] = $value;
                  $data[$k]['data'][$i]['item_amount'] = round($value->item_amount);
                  $data[$k]['data'][$i]['total_amount'] = round($value->total_amount);
                  $i++;
              }
            }

						if(isset($row->customerCommitment)){
              $data[$k]['customer_commitment'] = $row->customerCommitment;
							$data[$k]['customer_commitment']['master_division'] = $row->customerCommitment->masterDivision->description;
						}

            if(isset($row->customerSupplier)){
              $data[$k]['customer_supplier'] = $row->customerSupplier;
              $data[$k]['customer_supplier']['master_division'] = $row->customerSupplier->masterDivision->description;
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
          'name' => 'required',
          'no_ref' => 'required|unique:spk_header,no_ref,'.$input['no_ref'],
          'customer_commitment_id' => 'required',
          'customer_supplier_id' => 'required',
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];
        $token = $input['token'];
        unset($input['token']);
        unset($input['data']);
        $data       = $this->masterStore($input,'',SpkHeader::class,SpkDetail::class,$detail);
        if($data['success']==true) $this->auditTrail($token,'SPK',$input['no_ref'],'SAVE');
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'name' => 'required',
          'no_ref' => 'required',
          'customer_commitment_id' => 'required',
          'customer_supplier_id' => 'required',
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];
        $token = $input['token'];
        unset($input['token']);
        unset($input['data']);
        $data  = $this->masterUpdate($input,'',SpkHeader::class,$input['id'],SpkDetail::class,$detail);
        if($data['success']==true) $this->auditTrail($token,'SPK',$input['no_ref'],'UPDATE');

        return \Response::json($data,200);
    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],SpkHeader::class,'');

        $ref = SpkHeader::find($input['id']);
        if($data['success']==true) $this->auditTrail($input['token'],'SPK',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }

    public function getSpkHeader(Request $request)
    {
        $input = $request->all();

        $data   = new SpkHeader;
        $data = $data->orderBy('name', 'asc')->selectRaw('id as value ,no_ref as display, name as title');

        if( Input::get('q')){
            $data = $data->where('name', 'like', '%'.Input::get('q').'%');
            $data = $data->orWhere('no_ref', 'like', '%'.Input::get('q').'%');
        }
				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
