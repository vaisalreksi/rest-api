<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\SpmkDetail;
use App\Http\Controllers\ResourceController;


class SpmkDetailController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();

				$id_header = (isset($input['spmk_header_id']) && $input['spmk_header_id']) ? $input['spmk_header_id'] : 0;
				$input['spmk_header_id'] = $id_header;

        $result = $this->masterIndex($input,'',SpmkDetail::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'file' =>$row->file,
								'master_letter_type_id' =>$row->master_letter_type_id,
								'spmk_header_id' =>$row->spmk_header_id,
                'status' =>$row->status,
            );

						if(isset($row->spmkHeader)){
							$data[$k]['spmk_header'] = $row->spmkHeader;
						}

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

    }

    public function update(Request $request)
    {

    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $fileName = SpmkDetail::find($input['id']);
        if(!empty($fileName->file)) $this->helper->deleteImage($fileName->file,'spmk');

        $data       = $this->masterDestroy($input['id'],SpmkDetail::class,'');
        return \Response::json($data,200);
    }
}
