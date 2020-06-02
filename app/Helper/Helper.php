<?php

namespace App\Helper;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Helper
{

	public function convDateStr($paramString)
	{
		$arrStr = explode("-", $paramString);
		return $arrStr[2]."-".$arrStr[1]."-".$arrStr[0];
	}

	public function convNumStr($paramString)
	{
		return str_replace(',', '', $paramString);
	}

	public function executeQuery($str)
	{
		DB::statement($str);
	}

	public function getField($input,$class)
	{
		$data = new $class;
		foreach ($data->getFillable() as $value) {
			if(!isset($input[$value])){
				if(!array_key_exists($value,$input)){
					continue;
				}
			}
			$result[$value] = $input[$value];
		}

		return $result;
	}

	public function refNo($class,$value,$company)
	{

		$date = Carbon::today();
		setlocale(LC_TIME, 'id');

		$yy = $date->format('y');
		$mm = $date->format('m');
		if($company < 10){
			$company = "0".$company;
		}

		$param = $yy.$mm.$company;

		$ref = $param."0001";

		$table = $class::whereRaw('SUBSTRING(?, 0,  6) = '.$param,$value)->orderBy('id','DESC')->first();
		if(count($table) > 0){
			$ref_no = substr($table->$value, 6,4);
			$no = $ref_no + 1;
			$ref = $param.sprintf("%04d",$no);
		}

		return $ref;
	}

	public function getRandomWord() {
	    $word = array_merge(range('a', 'z'), range('A', 'Z'), range(0,9));
	    shuffle($word);
	    return substr(implode($word), 0, 10);
	}

	public function randCode($class,$value)
	{
		while (1) {
	    	$word = $this->getRandomWord();
	      	$check = $class::whereRaw("? = '".$word."'",$value)->get();
	      	if($check->isEmpty()){
	    		return $word;
	      	}
	    }
	}

	protected function refGenerator($model, $column, $format) {
		$refno_year = date('y');
		$refno_last = $model->select("$column")->where($column, 'like', $format.$refno_year.'%')->orderBy($column, 'desc')->first();

		if ($refno_last) {
			$refno_last->toArray();
			$refno_last = intval(substr($refno_last[$column], -5, 5))+1;
		}else{
			$refno_last = 1;
		}

		$number = $format.$refno_year.str_repeat("0", 5-strlen($refno_last)).$refno_last;

		return $number;
	}

	public function refGen($model, $column, $format)
	{
		return $this->refGenerator($model, $column, $format);
	}
	protected function kekata($x) {
	    $x = abs($x);
	    $angka = array("", "satu", "dua", "tiga", "empat", "lima",
	    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	    $temp = "";
	    if ($x <12) {
	        $temp = " ". $angka[$x];
	    } else if ($x <20) {
	        $temp = self::kekata($x - 10). " belas";
	    } else if ($x <100) {
	        $temp = self::kekata($x/10)." puluh". self::kekata($x % 10);
	    } else if ($x <200) {
	        $temp = " seratus" . self::kekata($x - 100);
	    } else if ($x <1000) {
	        $temp = self::kekata($x/100) . " ratus" . self::kekata($x % 100);
	    } else if ($x <2000) {
	        $temp = " seribu" . self::kekata($x - 1000);
	    } else if ($x <1000000) {
	        $temp = self::kekata($x/1000) . " ribu" . self::kekata($x % 1000);
	    } else if ($x <1000000000) {
	        $temp = self::kekata($x/1000000) . " juta" . self::kekata($x % 1000000);
	    } else if ($x <1000000000000) {
	        $temp = self::kekata($x/1000000000) . " milyar" . self::kekata(fmod($x,1000000000));
	    } else if ($x <1000000000000000) {
	        $temp = self::kekata($x/1000000000000) . " trilyun" . self::kekata(fmod($x,1000000000000));
	    }
	        return $temp;
	}

	public function terbilang($x, $style=4) {
	    if($x<0) {
	        $hasil = "minus ". trim(self::kekata($x));
	    } else {
	        $hasil = trim(self::kekata($x));
	    }
	    switch ($style) {
	        case 1:
	            $hasil = strtoupper($hasil);
	            break;
	        case 2:
	            $hasil = strtolower($hasil);
	            break;
	        case 3:
	            $hasil = ucwords($hasil);
	            break;
	        default:
	            $hasil = ucfirst($hasil);
	            break;
	    }
	    return $hasil;
	}

	protected function replaceKoma($content){
        $content = str_replace(',', '', $content);
        return $content;
    }

    protected function getMonthName($monthNumber)
	{
	    return date("F", mktime(0, 0, 0, $monthNumber, 1));
	}

	public static function convDateStrGaring($paramString)
	{
	    $arrStr = explode("/", $paramString);
	    return $arrStr[2]."-".$arrStr[0]."-".$arrStr[1];
	}

	public function uploadImage($file,$folder)
	{
		$date = Carbon::now();

		$image = "";
		if(isset($file)){
        	$ekstensi = $file->getClientOriginalExtension();

	        $getiFileName = $folder."_".$date->timestamp.'.'.$file->getClientOriginalExtension();
	        $this->uploadSizeImage($file,$getiFileName,$folder);

	        $file->move(public_path('image/original/'.$folder), $getiFileName);

	        $files = File::exists(public_path('image/original/'.$folder.'/'.$getiFileName));
	        if($files){
	        	$image = $getiFileName;
	        }
        }

        return $image;
	}

	public function uploadFile($file,$folder)
	{
		$result = true;
		$file_name = "";
		$message = "";

		if(isset($file['extention'])){
			if($file['extention'] != 'jpg' && $file['extention'] != 'png' && $file['extention'] != 'jpeg' && $file['extention'] != 'docx' && $file['extention'] != 'doc' && $file['extention'] != 'xls' && $file['extention'] != 'xlsx' && $file['extention'] != 'pdf'){
				$result = false;
				$message = "tipe file harus xls,xlsx,doc,docx,pdf,jpg,jpeg,png";
			}else{
				if(isset($file['file'])){
					$this->createFolder($folder);

					$file_data = $file['file'];
					$file_name = $folder.'_'.time().'.'.$file['extention'];
					$path = public_path().'/file'.'/'.$folder.'/'.$file_name;
					@list($type, $file_data) = explode(';', $file_data);
					@list(, $file_data) = explode(',', $file_data);
					$decode = base64_decode($file_data);

					$upload = File::put($path, $decode);

					if(!$upload){
						$result = false;
						$message = "terjadi kesalahan ketika upload file";
					}
				}
			}
		}
    return ['result'=>$result,'file_name'=>$file_name,'message'=>$message];
	}

	public function createFolder($folder="default",$param="file")
	{

		if($param=="file"){
			if(!File::exists(public_path('file/'.$folder))){
				$path = public_path('file/' . $folder);
				File::makeDirectory($path, $mode = 0777, true, true);
			}
		}else{
			if(!File::exists(public_path('image\thumbnail\\'.$folder))){
				$path = public_path('image\thumbnail\\' . $folder);
				File::makeDirectory($path, $mode = 0777, true, true);
			}
			if(!File::exists(public_path('image\medium\\'.$folder))){
				$path = public_path('image\medium\\' . $folder);
				File::makeDirectory($path, $mode = 0777, true, true);
			}

			if(!File::exists(public_path('image\large\\'.$folder))){
				$path = public_path('image\large\\' . $folder);
				File::makeDirectory($path, $mode = 0777, true, true);
			}

			if(!File::exists(public_path('image\full_size\\'.$folder))){
				$path = public_path('image\full_size\\' . $folder);
				File::makeDirectory($path, $mode = 0777, true, true);
			}
		}

	}

	public function deleteImage($fileName,$folder,$param="file")
	{
		if($param=="file"){
			File::delete(public_path('file/'.$folder.'/'.$fileName));
		}else{
			File::delete(public_path('image/original/'.$folder.'/'.$fileName));
			File::delete(public_path('image/thumbnail/'.$folder.'/'.$fileName));
			File::delete(public_path('image/medium/'.$folder.'/'.$fileName));
			File::delete(public_path('image/large/'.$folder.'/'.$fileName));
			File::delete(public_path('image/full_size/'.$folder.'/'.$fileName));
		}

	}

	public function uploadSizeImage($file,$name,$folder=null)
	{

		$this->createFolder($folder);

		if(empty($folder)){
			$folder = "default";
		}

		$ex = explode(".", $name);
		$fileName = end($ex);
		if($fileName == "jpg" || $fileName == "jpeg" || $fileName == "png"){
			//thumbnail
			$destThumbnail = public_path('image/thumbnail/'.$folder);
	        $img = Image::make($file->getRealPath());
	        $img->resize(150, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destThumbnail.'/'.$name);

			//medium
			$destMedium = public_path('image/medium/'.$folder);
	        $img = Image::make($file->getRealPath());
	        $img->resize(300, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destMedium.'/'.$name);

			//large
			$destLarge = public_path('image/large/'.$folder);
	        $img = Image::make($file->getRealPath());
	        $img->resize(625, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destLarge.'/'.$name);

			//full_size
			$destFull_size = public_path('image/full_size/'.$folder);
	        $img = Image::make($file->getRealPath());
	        $img->resize(1600, null, function ($constraint) {
			    $constraint->aspectRatio();
			})->save($destFull_size.'/'.$name);
		}
	}

	public function getUser($token)
	{
        $user = JWTAuth::toUser($token);
		// return $user->
	}

	public function sendEmail($data,$email,$subject,$view)
	{
        Mail::send($view, $data,
            function($mail) use ($email, $subject){
                $mail->from('support@kontrak.com');
                $mail->to($email);
                $mail->subject($subject);
            });
	}
}
