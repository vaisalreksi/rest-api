<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\BastDetail;
use App\Http\Controllers\ResourceController;


class BastDetailController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();

				$id_header = (isset($input['bast_header_id']) && $input['bast_header_id']) ? $input['bast_header_id'] : 0;
				$input['bast_header_id'] = $id_header;

        $result = $this->masterIndex($input,'',BastDetail::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'file' =>$row->file,
								'master_letter_type_id' =>$row->master_letter_type_id,
								'bast_header_id' =>$row->bast_header_id,
                'status' =>$row->status,
            );

						if(isset($row->bastHeader)){
							$data[$k]['bast_header'] = $row->bastHeader;
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

        $data       = $this->masterDestroy($input['id'],BastDetail::class,'');
        return \Response::json($data,200);
    }
}
