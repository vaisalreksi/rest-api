<?php

namespace App\Http\Controllers\Module;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Module\SpkDetail;
use App\Http\Controllers\ResourceController;


class SpkDetailController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();

				$id_header = (isset($input['spk_header_id']) && $input['spk_header_id']) ? $input['spk_header_id'] : 0;
				$input['spk_header_id'] = $id_header;

        $result = $this->masterIndex($input,'',SpkDetail::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
								'volume' =>$row->volume,
								'type' =>$row->type,
								'item_amount' =>$row->item_amount,
								'total_amount' =>$row->total_amount,
								'spk_header_id' =>$row->spk_header_id,
                'activity' =>$row->activity,
                'master_items_id' =>$row->master_items_id,
                'status' =>$row->status,
            );

						if(isset($row->spkHeader)){
							$data[$k]['spk_header'] = $row->spkHeader->name;
						}

						if(isset($row->masterItems)){
							$data[$k]['master_items'] = $row->masterItems->description;
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

        $data       = $this->masterDestroy($input['id'],SpkDetail::class,'');
        return \Response::json($data,200);
    }
}
