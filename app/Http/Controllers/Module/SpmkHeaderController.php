<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\SpmkHeader;
Use App\Models\Module\SpmkDetail;
use App\Http\Controllers\ResourceController;
use Carbon\Carbon;

class SpmkHeaderController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',SpmkHeader::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'no_ref' =>$row->no_ref,
                'terms' =>$row->terms,
                'penalty' =>$row->penalty,
                'sp_id' =>$row->sp_id,
                'status' =>$row->status,
                'data' => []
            );

            if(!empty($row->spmkDetail)){
              $detail = [];
              $i=0;
              foreach ($row->spmkDetail as $value) {
                  $detail[$i] = $value;
                  $detail[$i]['file'] = array('name'=>$value->file);

                  $i++;
              }

              $data[$k]['data'] = $detail;
            }

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

              $data[$k]['time_working'] = "WAKTU PELAKSANAAN PEKERJAAN : ".$countDate." (".$terbilang.") hari kalender dan pekerjaan harus sudah selesai pada tanggal";

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
          'no_ref' => 'required|unique:spmk_header,no_ref,'.$input['no_ref'],
          'sp_id' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];
        if(!empty($detail)){
          $k = 0;
          foreach ($detail as $value) {
            $upload = $this->helper->uploadFile($value['file'],'spmk');

            if($upload['result']==false) return \Response::json(array('success'=>false,'message'=>$upload['message']),200);

            unset($detail[$k]['file']);
            if(!empty($upload['file_name'])) $detail[$k]['file'] = $upload['file_name'];
            $k++;

          }
        }
        $token = $input['token'];
        unset($input['token']);
        unset($input['data']);
        $data       = $this->masterStore($input,'',SpmkHeader::class);
        // if($data['success']==true) $this->auditTrail($token,'SPMK',$input['no_ref'],'SAVE');
        $data       = $this->masterStore($input,'',SpmkHeader::class,SpmkDetail::class,$detail);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'no_ref' => 'required',
          'sp_id' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $detail = $input['data'];
        if(!empty($detail)){
          $k = 0;
          foreach ($detail as $value) {
            if((isset($value['file']['file']) && !empty($value['file']['file'])) && (isset($value['file']['extention']) && !empty($value['file']['extention']))){
              if(isset($value['id'])){
                $fileName = SpmkDetail::find($value['id']);
                if(!empty($fileName->file)) $this->helper->deleteImage($fileName->file,'spmk');
              }

              $upload = $this->helper->uploadFile($value['file'],'spmk');
              if($upload['result']==false) return \Response::json(array('success'=>false,'message'=>$upload['message']),200);

              unset($detail[$k]['file']);
              if(!empty($upload['file_name'])) $detail[$k]['file'] = $upload['file_name'];

            }else{
              unset($detail[$k]['file']);
            }
            $k++;

          }
        }

        $token = $input['token'];
        unset($input['token']);
        unset($input['data']);
        // $data  = $this->masterUpdate($input,'',SpmkHeader::class,$input['id']);
        $data  = $this->masterUpdate($input,'',SpmkHeader::class,$input['id'],SpmkDetail::class,$detail);
        if($data['success']==true) $this->auditTrail($token,'SPMK',$input['no_ref'],'UPDATE');
        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $ref = SpmkHeader::find($input['id']);
        $data       = $this->masterDestroy($input['id'],SpmkHeader::class,'');
        if($data['success']==true) $this->auditTrail($input['token'],'SPMK',$ref->no_ref,'DELETE');
        return \Response::json($data,200);
    }
}
