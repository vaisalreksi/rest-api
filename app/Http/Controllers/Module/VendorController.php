<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
use Image;
Use App\Models\Module\Vendor;
use App\Http\Controllers\ResourceController;


class VendorController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Vendor::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'file' =>$row->file,
								'master_letter_type_id' =>$row->master_letter_type_id,
                'status' =>$row->status,
            );

            if(isset($row->masterLetterType)){
							$data[$k]['master_letter_type'] = $row->masterLetterType->description;
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
          'file' => 'required',
          'master_letter_type_id' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        $upload = $this->helper->uploadFile($input['file'],'vendor');

        if($upload['result']==false) return \Response::json(array('success'=>false,'message'=>$upload['message']),200);

        $paramData = array(
            'file' => $upload['file_name'],
            'master_letter_type_id' => $input['master_letter_type_id'],
            'status' => $input['status']
        );

        $data       = $this->masterStore($paramData,'',Vendor::class);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
        $result = $this->laravelValidation($input, [
          'master_letter_type_id' => 'required'
        ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        if(isset($input['file'])){
          if((isset($input['file']['file']) && !empty($input['file']['file'])) && (isset($input['file']['extention']) && !empty($input['file']['extention']))){
            $fileName = Vendor::find($input['id']);
            if(!empty($fileName->file)) $this->helper->deleteImage($fileName->file,'vendor');

            $upload = $this->helper->uploadFile($input['file'],'vendor');
            if($upload['result']==false) return \Response::json(array('success'=>false,'message'=>$upload['message']),200);
            $input['file'] = $upload['file_name'];
          }
        }

        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Vendor::class,$input['id']);

        return \Response::json($data,200);
    }

    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $fileName = Vendor::find($input['id']);
        if(!empty($fileName->file)) $this->helper->deleteImage($fileName->file,'vendor');

        $data       = $this->masterDestroy($input['id'],Vendor::class,'');
        return \Response::json($data,200);
    }
}
