<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Http\Requests;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResourceController;
use App\Models\Master\Agents as Agents;
use App\Models\Setup\Users;
use Illuminate\Support\Facades\Mail;
use App\User;
use Auth;
use Hash;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserController extends ResourceController
{

    public function updateData(Request $request)
    {

    	$input = $request->all();
		$result = $this->laravelValidation($input, [
	            'photo' => 'max:20000|image|mimes:jpg,png,jpeg',
	        ]);
        
        if(!$result['success']){
        	return \Response::json(array('data'=>$result['message']),200);
        }
    	// return $request->photo;

    	$file = "";
        if(isset($request->photo)){
        	$file = Agents::find($input['id']);
        	$this->helper->deleteImage($file->photo,'agents');

        	$file = $this->helper->uploadImage($request->photo,'agents');
        	$input['photo'] = $file;
        }

        unset($input['token']);
        unset($input['_dc']);

        $data = $this->masterUpdate($input,'',Agents::class,$input['id']);
        return \Response::json(['success'=>$data['data']['success'],'message'=>$data['data']['message'], 'photo'=> url('/')."/image/thumbnail/agents/".$file,'id'=>$data['id']],200);  
		// return \Response::json(['success'=>$data['data']['success'],'message'=>$data['data']['message'], 'photo'=>"http://192.168.11.17:81/smartSales/public/image/thumbnail/agents/".$file],200);	
    }

    public function getCombo(Request $request){
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

}
