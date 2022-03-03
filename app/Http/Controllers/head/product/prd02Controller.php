<?php

namespace App\Http\Controllers\head\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class prd02Controller extends Controller
{
	var $img_type = array(1 => "b","c","d","e","f","g","h");

    public function index($goods_no, Request $req)
    {

        $img_ta		= $req->input("img_ta", "a");
		$img_urls	= [];

        $sql = /** @lang text */
            "
            select goods_nm, date_format(reg_dm,'%Y%m%d') as reg_dm, img, goods_cont, opt_kind_cd
            from goods
            where goods_no = :goods_no
         ";
        $goods = DB::selectOne($sql, array("goods_no" => $goods_no));
        $regdm = $goods->reg_dm;
        $dir = sprintf("/images/goods_img/%s/%s", $regdm, $goods_no);

		//이미지가 등록된경우 목록 이미지 리스트 구하기
		if( $goods->img != "" ){
			$img_urls["a"]["kind"]	= "a";
			$img_urls["a"]["title"]	= "기본이미지";
			$img_urls["a"]["img"]	= $goods->img . "?" . mt_rand();

			$sql_list	= " select img from goods_image where goods_no = :goods_no and goods_sub = '0' ";
			$rows		= DB::select($sql_list, array("goods_no" => $goods_no));

			foreach($rows as $row) {
				if(preg_match("/_([a-z]+)_/i",$row->img,$m)){
					$img_urls[$m[1]]["kind"]	= $m[1];
					$img_urls[$m[1]]["title"]	=  "추가이미지:" . $m[1];
					$img_urls[$m[1]]["img"]		= $row->img . "?" . mt_rand();
				}
			}
		}

        $file_list = Storage::disk('public')->files($dir);

		$files_ta	= [];
        $files		= [];
        $sortArr	= [];

        foreach ($file_list as $file) {
            $names		= explode('/', $file);
            $name		= array_pop($names);
            $filesize	= floor(Storage::disk('public')->size($file) / 1000);
            $img_size	= explode('.', explode('_', $name)[2])[0];

			//추가 이미지 구분자
            $img_sep    = explode('.', explode('_', $name)[1])[0];

			if($img_sep == "s")	$img_sep	= "a";	//추가 크기 이미지는 기본이미지에서 출력되게 설정

            $files[$img_sep][] = [
                'src'		=> "/" . $file,
                'filesize'	=> $filesize,
                //'size' => $img_size	//이미지명으로 대처
                'size'		=> $name
            ];

            $sortArr[$img_sep][] = $img_size;
        }

		if(isset($files[$img_ta])){
			$files_ta	= $files[$img_ta];
			$sortArr_ta	= $sortArr[$img_ta];

			array_multisort($sortArr_ta, SORT_ASC, $files_ta);
		}

        //array_multisort($sortArr, SORT_ASC, $files);

        //print_r($files);

        $values = [
            'goods_no'			=> $goods_no,
			'img_ta'			=> $img_ta,
			"img_type_alias"	=> $this->img_type,
            'files'				=> $files_ta,
            'goods'				=> $goods,
			'img_urls'			=> $img_urls
        ];

        return view(Config::get('shop.head.view') . '/product/prd02', $values);
    }

    //업로드 후 파일이 안보일 경우.
    //php artisan storage:link 실행 부탁드립니다.
    public function upload($goods_no, Request $req)
    {

        $user	= Auth('head')->user();

        $img_type	= $req->input('img_type', 'a');
        $goods_sub	= $req->input('goods_sub', 0);
        $sizes		= $req->input('sizes');
        $effect		= $req->input('effect');

		// 추가 이미지 종류
		$img_type_alias	= $req->input("img_type_alias");

		$image		= preg_replace('/data:image\/(.*?);base64,/', '', $req->img);
        preg_match('/data:image\/(.*?);base64,/', $req->img, $matches, PREG_OFFSET_CAPTURE);
        //print_r($matches);
        $ext = $matches[1][0];

        if ($ext == "jpeg" || $ext == "jpg") {
            $ext = "jpg";
        } else if ($ext == "png" || $ext == "gif") {
        }
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

        $sql = /** @lang text */
            "
            select date_format(reg_dm,'%Y%m%d') as reg_dm, img
            from goods
            where goods_no = :goods_no
         ";
        $goods = DB::selectOne($sql, array("goods_no" => $goods_no));
        $regdm = $goods->reg_dm;
        $save_path = sprintf("/images/goods_img/%s/%s", $regdm, $goods_no);

        if( $img_type == "a")
			$file_name = sprintf("%s_%s_%s.%s", $goods_no, $img_type, $req->size, $ext);
		else
			$file_name = sprintf("%s_%s_%s.%s", $goods_no, $img_type_alias, $req->size, $ext);
        
		$save_file = sprintf("%s/%s", $save_path, $file_name);

        try {

            /* 이미지를 저장할 경로 폴더가 없다면 생성 */
            if (!Storage::disk('public')->exists($save_path)) {
                Storage::disk('public')->makeDirectory($save_path);
            }

            //저장
            Storage::disk('public')->put($save_file, base64_decode($image));

            $src_file = public_path($save_file);

            if (file_exists($src_file)) {
                $img_info = getimagesize($src_file);

                $type = $img_info[2];
                if ($type == 1) {
                    $src_img = imagecreatefromgif($src_file);
                } else if ($type == 2) {
                    $src_img = imagecreatefromjpeg($src_file);
                } else if ($type == 3) {
                    $src_img = imagecreatefrompng($src_file);
                } else {
                    return false;
                }

                $dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type, $req->size));
                $this->resize($type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $req->size);

                for ($i = 0; $i < count($sizes); $i++) {

					// 임시수정 김용남 21-08-12
                    // 추가 이미지들은 img_type이 s임.
                    switch ($sizes[$i]) {
                        case '50':
                        case '62':
                        case '70':
                        case '100':
                        case '129':
                            $img_type_chk = "s";
                            break;
                        default:
                            $img_type_chk = "a";
                            break;
                    }

					if( $img_type == "f" && $img_type_alias != "" && $img_type_chk == "s"){
						//추가이미지의 추가사이즈는 등록안함
					}else{
						//추가이미지의 경우
						if( $img_type == "f" && $img_type_alias != "" ){
							$img_type_chk	= $img_type_alias;
						}

						//$dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type, $sizes[$i]));
						$dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type_chk, $sizes[$i]));
						$this->resize($type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $sizes[$i]);
					}
                }

				// 추가이미지 500일때 데이터 저장
				if( $img_type != "a" ){
					$sql	= " select count(*) as cnt from goods_image where goods_no = :goods_no and goods_sub = '0' and type = :type ";
					$selectarr = array(
						"goods_no"	=> $goods_no,
						"type"		=> $img_type_alias
					);
					$row	= DB::selectOne($sql, $selectarr);
					if( $row->cnt > 0 ){
						$sql	= " delete from goods_image where goods_no = :goods_no and goods_sub = '0' and type = :type ";
						DB::delete($sql, $selectarr);
					}

					$sql	= "
						insert into goods_image (
							goods_no, goods_sub, type, img, admin_id, admin_nm, rt, ut
						) values (
							:goods_no, '0', :type, :img, :admin_id, :admin_nm, now(), now()
						)
					";
					DB::insert($sql, array("goods_no" => $goods_no, "type" => $img_type_alias, "img" => $save_file, "admin_id" => $user->id, "admin_nm" => $user->name));
				}
				
				if( $img_type == "a"){
					DB::table('goods')
						->where('goods_no', $goods_no)
						->where('goods_sub', $goods_sub)
						->update([
							'img' => $save_file,
							'img_update' => now()
						]);
				}else{
					DB::table('goods')
						->where('goods_no', $goods_no)
						->where('goods_sub', $goods_sub)
						->update([
							'img_update' => now()
						]);
				}
            }
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg, 'img_type' => $img_type, 'img_type_alias' => $img_type_alias]);
    }

    public function resize($type, $effect, $src_img, $dstFile, $sw, $sh, $dw, $dh = 0)
    {

        if ($sw < $dw) {
            $dw = $sw;
            $dh = $sh;
        }

        if ($sw >= $dw) {
            $dh = ceil(($dw / $sw) * $sh);
        }

        $dst_img = imagecreatetruecolor($dw, $dh);
        imagecolorallocate($dst_img, 255, 255, 255);

        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dw, $dh, $sw, $sh);

        $dst_img = $this->UnsharpMask($dst_img, 50, 0.5, 0);
        //$dst_img = WaterMark($dst_img);

        imageinterlace($dst_img);

        //echo $dstFile;

        if ($type == 1) {
            imagegif($dst_img, $dstFile);
        } else if ($type == 2) {
            imagejpeg($dst_img, $dstFile, $effect["quality"]);
        } else if ($type == 3) {
            imagepng($dst_img, $dstFile);
        }
        imagedestroy($dst_img);
    }

    /*
	Function: UnsharpMask
		UnsharpMask 처리

	Parameters:
		$img - 이미지 원본
		$amount - 적용 양 설정
		$radius - 화소 반경 설정
		$threshold - 화소 밀도 설정

	Returns:
		None

	New:
		- In version 2.1 (February 26 2007) Tom Bishop has done some important speed enhancements.
		- From version 2 (July 17 2006) the script uses the imageconvolution function in PHP
		version >= 5.1, which improves the performance considerably.


		Unsharp masking is a traditional darkroom technique that has proven very suitable for
		digital imaging. The principle of unsharp masking is to create a blurred copy of the image
		and compare it to the underlying original. The difference in colour values
		between the two images is greatest for the pixels near sharp edges. When this
		difference is subtracted from the original image, the edges will be
		accentuated.

		The Amount parameter simply says how much of the effect you want. 100 is 'normal'.
		Radius is the radius of the blurring circle of the mask. 'Threshold' is the least
		difference in colour values that is allowed between the original and the mask. In practice
		this means that low-contrast areas of the picture are left unrendered whereas edges
		are treated normally. This is good for pictures of e.g. skin or blue skies.

		Any suggenstions for improvement of the algorithm, expecially regarding the speed
		and the roundoff errors in the Gaussian blur process, are welcome.
    */
    public function UnsharpMask($img, $amount, $radius, $threshold)
    {

        ////////////////////////////////////////////////////////////////////////////////////////////////
        ////
        ////                  Unsharp Mask for PHP - version 2.1.1
        ////
        ////    Unsharp mask algorithm by Torstein Hønsi 2003-07.
        ////             thoensi_at_netcom_dot_no.
        ////               Please leave this notice.
        ////
        ///////////////////////////////////////////////////////////////////////////////////////////////

        // $img is an image that is already created within php using
        // imgcreatetruecolor. No url! $img must be a truecolor image.

        // Attempt to calibrate the parameters to Photoshop:
        if ($amount > 500) $amount = 500;
        $amount = $amount * 0.016;
        if ($radius > 50) $radius = 50;
        $radius = $radius * 2;
        if ($threshold > 255) $threshold = 255;

        $radius = abs(round($radius));     // Only integers make sense.
        if ($radius == 0) {
            return $img;
            imagedestroy($img);
        }
        $w = imagesx($img);
        $h = imagesy($img);
        $imgCanvas = imagecreatetruecolor($w, $h);
        $imgBlur = imagecreatetruecolor($w, $h);


        // Gaussian blur matrix:
        //
        //    1    2    1
        //    2    4    2
        //    1    2    1
        //
        //////////////////////////////////////////////////


        if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(
                array(1, 2, 1),
                array(2, 4, 2),
                array(1, 2, 1)
            );
            imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
            imageconvolution($imgBlur, $matrix, 16, 0);
        } else {

            // Move copies of the image around one pixel at the time and merge them with weight
            // according to the matrix. The same matrix is simply repeated for higher radii.
            for ($i = 0; $i < $radius; $i++) {
                imagecopy($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
                imagecopymerge($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
                imagecopymerge($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
                imagecopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

                imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333); // up
                imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
            }
        }

        if ($threshold > 0) {
            // Calculate the difference between the blurred pixels and the original
            // and set the pixels
            for ($x = 0; $x < $w - 1; $x++) { // each row
                for ($y = 0; $y < $h; $y++) { // each pixel

                    $rgbOrig = ImageColorAt($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    // When the masked pixels differ less from the original
                    // than the threshold specifies, they are set to their original value.
                    $rNew = (abs($rOrig - $rBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
                        : $rOrig;
                    $gNew = (abs($gOrig - $gBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
                        : $gOrig;
                    $bNew = (abs($bOrig - $bBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
                        : $bOrig;


                    if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                        $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
                        ImageSetPixel($img, $x, $y, $pixCol);
                    }
                }
            }
        } else {
            for ($x = 0; $x < $w; $x++) { // each row
                for ($y = 0; $y < $h; $y++) { // each pixel
                    $rgbOrig = ImageColorAt($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if ($rNew > 255) {
                        $rNew = 255;
                    } elseif ($rNew < 0) {
                        $rNew = 0;
                    }
                    $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if ($gNew > 255) {
                        $gNew = 255;
                    } elseif ($gNew < 0) {
                        $gNew = 0;
                    }
                    $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if ($bNew > 255) {
                        $bNew = 255;
                    } elseif ($bNew < 0) {
                        $bNew = 0;
                    }
                    $rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
                    ImageSetPixel($img, $x, $y, $rgbNew);
                }
            }
        }
        imagedestroy($imgCanvas);
        imagedestroy($imgBlur);

        return $img;
    }
    
	/*
	***
	상품슬라이더
	***
	*/

	public function index_slider(Request $request) {

        $where = '';
        foreach(explode(",", $request->goods_nos) as $goods_no) {
            $where .= " or goods_no = $goods_no";
        }

		$sql = "
            select goods_no, goods_sub, goods_nm, date_format(reg_dm,'%Y%m%d') as reg_dm, img, goods_cont
            from goods
            where 1!=1 $where
		";
        $goods_list = DB::select($sql);

        foreach($goods_list as $goods) {
            $reg_dm = $goods->reg_dm;
            $dir = sprintf("/images/goods_img/%s/%s", $reg_dm, $goods->goods_no);
            $file_list = Storage::disk('public')->files($dir);

            $files = [];
            $sortArr = [];

            foreach ($file_list as $file) {
                $names = explode('/', $file);
                $name = array_pop($names);
                $file_size = floor(Storage::disk('public')->size($file) / 1000);
                $img_size = explode('.', explode('_', $name)[2])[0];
        
                $files[] = [
                    'src' => "/" . $file,
                    'filesize' => $file_size,
                    'size' => $img_size
                ];
        
                $sortArr[] = $img_size;
            }
            array_multisort($sortArr, SORT_ASC, $files);
            $goods->files = $files;
        }
        // error_log(var_export($goods_list, 1));

		$values = [
			'goods_list' => $goods_list,
		];
		return view( Config::get('shop.head.view') . '/product/prd02_slider', $values);
	}
}
