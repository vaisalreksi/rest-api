<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResourceController;
use Illuminate\Http\Request;
Use App\Models\Module\ProspecFollowUp;
Use App\Models\Module\ProspectUnitDetail;
Use App\Models\Module\Prospectus;
Use App\Models\Module\Order;
Use App\Models\Master\News;
Use App\Models\Master\MasterUnit;
Use Carbon\Carbon;

class DashboardController extends ResourceController
{	

    public function getData(Request $request)
    {
        $input = $request->all();
        //get data count prospectus follow up

        $dataFollowUp = array(
            'agents_id' => $input['agents_id'],
            'type' => 1,
            'update_status' => 0
        );

        $resultFollowUp = $this->masterMobileIndex($dataFollowUp,ProspecFollowUp::class);

        //get data prospectus
        $dataProspectus = array(
            'agents_id' => $input['agents_id'],
            'filter' => ''
        );

        $resultProspec = $this->masterMobileIndex($dataProspectus,Prospectus::class);

        $order = Order::selectRaw('count(unit_colour.master_unit_id) as sum,unit_colour.master_unit_id');
        $order = $order->join('unit_colour','unit_colour.id','=','order.unit_colour_id');
        $order = $order->where('agents_id',$input['agents_id'])->groupBy('unit_colour.master_unit_id')->orderBy('sum','DESC')->first();
        //$order = Order::where('agents_id',$input['agents_id'])->selectRaw('SUM(unit_colour_id) as sum,unit_colour_id')->groupBy('unit_colour_id')->orderBy('sum','desc')->take(1)->get();

        $dataOrder = array();
        if (!empty($order)) {
            $unit = MasterUnit::find($order->master_unit_id);
            $dataOrder = array(
                'total' => $order->sum,
                'unit' => $unit->name
            );
            $dataOrder['data'] = array(
                'id' =>$unit->id,
                'code' =>$unit->code,
                'name' =>$unit->name,
                'description' =>$unit->description,
                'cc' =>$unit->cc,
                'price'=>round($unit->price),
                'default_picture' => "master_unit/".$unit->default_picture,
                'diameter' =>$unit->diameter,
                'kompresi' =>$unit->kompresi,
                'pelumasan' =>$unit->pelumasan,
                'mesin' =>$unit->mesin,
                'silinder' =>$unit->silinder,
                'volume' =>$unit->volume,
                'daya' =>$unit->daya,
                'torsi' =>$unit->torsi,
                'starter' =>$unit->starter,
                'oli' =>$unit->oli,
                'bahan_bakar' =>$unit->bahan_bakar
            );

            if(isset($unit->masterUnitCategory)){
                $dataOrder['data']['unit_category'] = $unit->masterUnitCategory->name;
            }
            if(isset($unit->masterBrand)){
                $dataOrder['data']['brand'] = $unit->masterBrand->name;
            }

            if(isset($unit->unitColour)){
                $detail = [];
                $no = 0;
                foreach ($unit->unitColour as $value) {
                    $detail[$no]['picture'] = "unit_colour/".$value->picture;
                    if(isset($value->masterColour)){
                        $detail[$no]['colour'] = $value->masterColour->colour_rgb;
                    } 
                    $no++;
                }
                $dataOrder['data']['unit_colour'] = $detail;
            }
        }


        //date follow up
        $follow_up = ProspecFollowUp::select('propec_follow_up.next_contact');
        $follow_up = $follow_up->join('prospect_unit_detail','prospect_unit_detail.id','=','propec_follow_up.prospect_unit_detail_id');
        $follow_up = $follow_up->join('prospectus','prospectus.id','=','prospect_unit_detail.prospectus_id');
        $follow_up = $follow_up->where('prospectus.agents_id',$input['agents_id']);
        $follow_up = $follow_up->where(function($que){
            $que = $que->where('propec_follow_up.update_status',0);
            $que = $que->orWhereNull('propec_follow_up.update_status');
        });
        $follow_up = $follow_up->where('propec_follow_up.next_contact','>=',Carbon::today()->format('Y-m-d'))->get();

        $dataProspectFollowUp = [];
        if(!$follow_up->isEmpty()){
            foreach ($follow_up as  $value) {
                $dataProspectFollowUp[] = $value->next_contact;
            }
        } 


        $dataDashboard = array(
            'follow_up' =>$resultFollowUp['total'],
            'prospectus' =>$resultProspec['total'],
            'order' =>$dataOrder,
            'date_follow_up' => $dataProspectFollowUp,
            'count' => $this->getCountProspect($input['agents_id'])
        );

        return \Response::json($dataDashboard,200); 

    }

    private function getCountProspect($id)
    {
        $hot=0;
        $warm=0;
        $cold=0;
        $total=0;

        $detailHot = [];
        $detailWarm = [];
        $detailCold = [];
        $detailToday = [];

        $today = 0;
        $count = ProspectUnitDetail::select('prospect_unit_detail.id','prospectus.name','prospectus.family_name','prospectus.remarks');
        $count = $count->join('prospectus','prospectus.id','=','prospect_unit_detail.prospectus_id');
        $count = $count->where('prospectus.agents_id',$id)->where('prospectus.priority_status',3)->get();

        if(!$count->isEmpty()){
            foreach ($count as $value) {
                $followUp = $this->checkFollowUp($value->id);

                if($followUp['type_today']==1){
                    $today++;
                    $detailToday[] = array(
                        'name' => $value->name,
                        'family_name' => $value->family_name,
                        'type' => $followUp['type'],
                        'contact_date'=>$followUp['contact_date'],
                        'next_contact'=>$followUp['next_contact'],
                        'final_option' => $followUp['final_option'],
                        'remarks' => $followUp['remarks'],
                    );
                } 

                switch ($followUp['result']) {
                    case "HOT":
                        $hot++;
                        $detailHot[] = array(
                            'name' => $value->name,
                            'family_name' => $value->family_name,
                            'type' => $followUp['type'],
                            'contact_date'=>$followUp['contact_date'],
                            'next_contact'=>$followUp['next_contact'],
                            'final_option' => $followUp['final_option'],
                            'remarks' => $followUp['remarks'],
                        );
                        break;
                    case "WARM":
                        $warm++;
                        $detailWarm[] = array(
                            'name' => $value->name,
                            'family_name' => $value->family_name,
                            'type' => $followUp['type'],
                            'contact_date'=>$followUp['contact_date'],
                            'next_contact'=>$followUp['next_contact'],
                            'final_option' => $followUp['final_option'],
                            'remarks' => $followUp['remarks'],

                        );
                        break;
                    case "COLD":
                        $cold++;
                        $detailCold[] = array(
                            'name' => $value->name,
                            'family_name' => $value->family_name,
                            'type' => $followUp['type'],
                            'contact_date'=>$followUp['contact_date'],
                            'next_contact'=>$followUp['next_contact'],
                            'final_option' => $followUp['final_option'],
                            'remarks' => $followUp['remarks'],
                        );
                        break;    
                    default:
                        break;
                }
            }
        }

        $total = $hot + $warm + $cold;
        $result = array(
            'hot'=>array('count'=>$hot,'detail'=>$detailHot),
            'warm'=>array('count'=>$warm,'detail'=>$detailWarm),
            'cold'=>array('count'=>$cold,'detail'=>$detailCold),
            'total'=>$total,
            'today' => array('count'=>$today,'detail'=>$detailToday),
        );

        return $result;
    }

    private function checkFollowUp($id)
    {
        $today = Carbon::today()->format('Y-m-d');
        $result = "FALSE";
        $type = "";
        $type_today = 0;
        $contact_date = "";
        $next_contact = "";
        $final_option = "";
        $remarks = "";
        $data = ProspecFollowUp::where('prospect_unit_detail_id',$id)->orderBy('id','DESC')->first();
        if(!empty($data)){
            $date = Carbon::parse($data->contact_date);
            $diff = $date->diffInDays($data->next_contact);

            if ($diff == 0 && $diff <= 31) {
                $result = "HOT";
            }elseif ($diff > 31 && $diff <= 90) {
                $result = "WARM";
            }elseif ($diff > 90){
                $result = "COLD";
            }    

            $type = $data->type;


            //cek date sekarang
            if($today == $data->contact_date) $type_today = 1;

            $next_contact = $data->next_contact;
            $contact_date = $data->contact_date;
            $final_option = $data->final_option;
            $remarks = $data->remarks;

        }

        return array('result'=>$result,'type'=>$type,'contact_date'=>$contact_date,'next_contact'=>$next_contact,'type_today'=>$type_today,'final_option' => $final_option,'remarks'=>$remarks);
    }

}