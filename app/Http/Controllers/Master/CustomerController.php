<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
Use App\Models\Master\Customer;
use App\Http\Controllers\ResourceController;


class CustomerController extends ResourceController
{
    public function index(Request $request)
    {
        $input  = $request->all();
        $result = $this->masterIndex($input,'',Customer::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
                'master_division_id' =>$row->master_division_id,
								'master_company_id' =>$row->master_company_id,
                'name' =>$row->name,
								'flag' =>$row->flag,
								'nip' =>$row->nip,
								'phone' =>$row->phone,
								'email' =>$row->email,
								'address' =>$row->address,
                'status' =>$row->status,
            );

						if(isset($row->masterDivision)){
							$data[$k]['master_division'] = $row->masterDivision;
						}

						if(isset($row->masterCompany)){
							$data[$k]['master_company'] = $row->masterCompany;
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
                'master_division_id' => 'required'
            ]);

        if(!$result['success']){
            return \Response::json(array('data'=>$result),200);
        }

        unset($input['token']);
        $data       = $this->masterStore($input,'',Customer::class);
        return \Response::json($data,200);
    }

    public function update(Request $request)
    {

        $input      = $request->all();
         $result = $this->laravelValidation($input, [
					 		'master_division_id' => 'required'
            ]);

        if(!$result['success']){
          return \Response::json($result,200);
        }

        unset($input['token']);
        $data  = $this->masterUpdate($input,'',Customer::class,$input['id']);

        return \Response::json($data,200);
    }
    public function destroy(Request $request)
    {
        $input      = $request->all();
        $message    = __('message.success_delete');
        $success    = true;

        $data       = $this->masterDestroy($input['id'],Customer::class,'');
        return \Response::json($data,200);
    }

    public function getCustomer(Request $request)
    {
        $input = $request->all();

        $data   = new Customer;
        $data = $data->orderBy('name', 'asc')->select('id as value', 'name as display','master_company_id','master_division_id')->with('masterDivision','masterCompany');

        if( Input::get('q')){
            $data = $data->where('name', 'like', '%'.Input::get('q').'%');
        }

        if(isset($input['flag']) && $input['flag'] > 0){
          $data = $data->where('flag',1);
        }else{
          $data = $data->where('flag',0);
        }

				$data = $data->where('status',0);
        $data   = $data->get()->toArray();

        return \Response::json($data, 200);
    }
}
