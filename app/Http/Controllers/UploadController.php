<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{	
	public function store(Request $request)
	{
		$input = $request->all();
		$date = Carbon::now();
		$message = __('dddddd');
		$success = true ;

        $image = "";
        if(isset($request->file)){
        	$image = $request->file;
        	$ekstensi = $request->file->getClientOriginalExtension();

	        $getiFileName = "smartSales_".$date->timestamp.'.'.$request->file->getClientOriginalExtension();
	        $this->uploadImage($request->file,$getiFileName);

	        $request->file->move(public_path('image/original'), $getiFileName);
        		return \Response::json(['success'=>$success,'message'=>$message],200);
        }

	}

	public function uploadImage($file,$name)
	{
		$ex = explode(".", $name);
		$fileName = end($ex);
		if($fileName == "jpg" || $fileName == "jpeg" || $fileName == "png"){
			//thumbnail
			$destThumbnail = public_path('image/thumbnail');
	        $img = Image::make($file->getRealPath());
	        $img->resize(150, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destThumbnail.'/'.$name);

			//medium
			$destMedium = public_path('image/medium');
	        $img = Image::make($file->getRealPath());
	        $img->resize(300, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destMedium.'/'.$name);

			//large
			$destLarge = public_path('image/large');
	        $img = Image::make($file->getRealPath());
	        $img->resize(625, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destLarge.'/'.$name);

			//full_size
			$destFull_size = public_path('image/full_size');
	        $img = Image::make($file->getRealPath());
	        $img->resize(1600, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destFull_size.'/'.$name);
		}
	}
}