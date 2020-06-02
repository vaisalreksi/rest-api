<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Master\MasterBrand;
Use App\Models\Master\MasterUnit;
Use App\Models\Master\MasterUnitCategory;
Use App\Models\Master\Banner;
use App\Models\Master\MasterDealers;
use App\Models\Setup\Users;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use QrCode;

class HomeController extends Controller
{

    public function masterMobileIndex($input,$modelClass)
    {
        $model = new $modelClass;
        $getData = $model->bySearch($input)->byOrder($input)->get();

        return array('total'=>$model->bySearch($input)->count(),'data'=>$getData);
    }

    protected function masterIndex($input,$path,$modelClass)
    {
        $page = isset($input['page'])?$input['page']:1;
        $start = isset($input['start'])?$input['start']:0;
        $limit = isset($input['limit'])?$input['limit']:50;

        $model = new $modelClass;
        $getData = $model->bySearch($input)->byOrder($input)->skip(($limit*($page-1)))->take($limit)->get();

        return array('total'=>$model->bySearch($input)->count(),'data'=>$getData);
    }

    public function getComboBrand(Request $request)
    {
        $input = $request->all();

        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $result = $this->masterMobileIndex($input,MasterBrand::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {
            if(!$row->masterUnitCategory->isEmpty()){
                $data[$k] = array(
                    'id' => $row->id,
                    'display' => $row->name,
                    'default_view' => $row->default_view,
                    'logo' => "master_brand/".$row->logo,
                    );
                $k++;
            }
        }

        return \Response::json($data,200);
    }

    public function getComboUnitCategory(Request $request)
    {
        $input = $request->all();

        $id_header = 0;
        if(isset($input['brand']) && $input['brand'] > 0){
            $id_header = $input['brand'];
        }

        $array[] = array('operator'=>'brand','property'=>'master_brand_id','value'=>$id_header);
        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $result = $this->masterMobileIndex($input,MasterUnitCategory::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {
            if(!$row->masterUnit->isEmpty()){
                $data[$k] = array(
                    'id' => $row->id,
                    'display' => $row->name
                    );
                $k++;
            }
        }

        return \Response::json($data,200);
    }

    public function getComboUnit(Request $request)
    {
        $input = $request->all();

        $id_header = 0;
        if(isset($input['brand']) && $input['brand'] > 0){
            $id_header = $input['brand'];
        }

        $array[] = array('operator'=>'brand','property'=>'master_brand_id','value'=>$id_header);
        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $arraySort[] = array('property'=>'name','direction'=>'asc');
        $input['sort'] = json_encode($arraySort);

        $result = $this->masterMobileIndex($input,MasterUnit::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {

            $data[$k] = array(
                'id' => $row->id,
                'display' => $row->name
                );
            $k++;
        }

        return \Response::json($data,200);
    }

    public function getDataUnit(Request $request)
    {
        $input  = $request->all();

        if(isset($input['filter'])){
            $data = $input['filter'];

            if(isset($data['brand']) && $data['brand'] > 0){
                $array[] = array('operator'=>'brand','property'=>'master_brand_id','value'=>$data['brand']);
            }

            if(isset($data['unit_category']) && $data['unit_category'] > 0){
                $array[] = array('operator'=>'unit_category','property'=>'master_unit_category_id','value'=>$data['unit_category']);
            }

            if(isset($data['filter'])){
                $array[] = array('operator'=>'filter','property'=>'filter','value'=>$data['filter']);

            }
        }



        unset($input['filter']);
        $array[] = array('operator'=>'master_brand_id','property'=>'master_brand_id','value'=>1);
        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $result = $this->masterIndex($input,'',MasterUnit::class);
        $data   = array();
        $k      = 0;
        foreach ($result['data'] as $row)
        {
            $data[$k]   = array(
                'id' =>$row->id,
                'code' =>$row->code,
                'name' =>$row->name,
                'description' =>$row->description,
                'cc' =>$row->cc,
                'price'=>round($row->price),
                'default_picture' => "master_unit/".$row->default_picture,
                'diameter' =>$row->diameter,
                'kompresi' =>$row->kompresi,
                'pelumasan' =>$row->pelumasan,
                'mesin' =>$row->mesin,
                'silinder' =>$row->silinder,
                'volume' =>$row->volume,
                'daya' =>$row->daya,
                'torsi' =>$row->torsi,
                'starter' =>$row->starter,
                'oli' =>$row->oli,
                'bahan_bakar' =>$row->bahan_bakar
            );
            if(isset($row->masterUnitCategory)){
                $data[$k]['unit_category'] = $row->masterUnitCategory->name;
            }
            if(isset($row->masterBrand)){
                $data[$k]['brand'] = $row->masterBrand->name;
            }

            if(isset($row->unitColour)){
                $detail = [];
                $no = 0;
                foreach ($row->unitColour as $value) {
                    $detail[$no]['picture'] = "unit_colour/".$value->picture;
                    // $detail[$no]['picture'] = url('/')."/image/thumbnail/unit_colour/".$row->picture;
                    if(isset($value->masterColour)){
                        $detail[$no]['colour'] = $value->masterColour->colour_rgb;
                    }
                    $no++;
                }
                $data[$k]['unit_colour'] = $detail;
            }

            $k++;
        }
        return \Response::json(array('data'=>$data,'total'=>$result['total']),200);
    }

    public function getDataBanner(Request $request)
    {
        $input = $request->all();

        $array[] = array('operator'=>'gt','property'=>'end_date','value'=>Carbon::now()->format('Y-m-d H:i:s'));
        $array[] = array('operator'=>'lt','property'=>'start_date','value'=>Carbon::now()->format('Y-m-d H:i:s'));
        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $result = $this->masterMobileIndex($input,Banner::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {

            $data[$k] = array(
                'id' => $row->id,
                'display' => $row->description,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'picture' => "banner/".$row->banner_pic
                );
            $k++;
        }

        return \Response::json($data,200);
    }


    public function basic_email(){
        $data = array('verification_code'=>"Virat Gandhi");
        Mail::send('mail.mail', $data, function($message) {
            $message->to('vaisal@agapesoftware.co.id', 'Tutorials Point')->subject('Laravel Basic Testing Mail');
            $message->from('support.agapesoftware@gmail.com','Virat Gandhi');
        });

        return "Basic Email Sent. Check your inbox.";
    }

    public function getDataDealer(Request $request)
    {
        $input = $request->all();

        if(isset($input['filter'])){
            $array[] = array('operator'=>'filter','property'=>'name','value'=>$input['filter']);
            $array[] = array('operator'=>'filter','property'=>'city','value'=>$input['filter']);
            unset($input['filter']);
        }

        if(isset($input['id']) && $input['id'] > 0){
            $array[] = array('operator'=>'id','property'=>'id','value'=>$input['id']);
        }

        $array[] = array('operator'=>'status','property'=>'status','value'=>1);
        $input['filter'] = json_encode($array);

        $result = $this->masterMobileIndex($input,MasterDealers::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {

            $data[$k] = array(
                'id' => $row->id,
                'code'=>$row->code,
                'name'=>$row->name,
                'address'=>$row->address,
                'city'=>$row->city,
                'phone_no1'=>$row->phone_no1,
                'phone_no2'=>$row->phone_no2,
                'photo'=>'master_dealers/'.$row->photo,
                'location_map'=>$row->location_map
                );
            $k++;
        }

        return \Response::json(array('data'=>$data,'total'=>$result['total']),200);
    }


     public function getComboUser(Request $request){
       $input = $request->all();

        $array[] = array('operator'=>'','property'=>'level','value'=> 3);
        $input['filter'] = json_encode($array);

        $arraySort[] = array('property'=>'name','direction'=>'asc');
        $input['sort'] = json_encode($arraySort);

        $result = $this->masterMobileIndex($input,Users::class);

        $data = array();
        $k =0;

        foreach($result['data'] as $row) {

            //if ($data['level'] == 3) {
                $data[$k] = array(
                    'id' => $row->id,
                    'name' => $row->name,
                    );
                $k++;
            //}

        }

        //print_r($data)or die();
        return \Response::json($data,200);

    }

    public function setQrBarcode()
    {
        return QrCode::size(1000)->generate('https://play.google.com/store/apps/details?id=dev.smartsales');

    }

}
