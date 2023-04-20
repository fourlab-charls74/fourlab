<?php

namespace App\Http\Controllers\partner\product;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class prd08Controller extends Controller
{
    public function index(Request $req)
    {
        $values = [
            'com_nm' => Auth('partner')->user()-> com_nm,
            'com_id' => Auth('partner')->user()-> com_id
        ];

		return view(Config::get('shop.partner.view') . '/product/prd08', $values);
    }

    // 상품번호로 상품정보 조회
    public function get_goods_info_by_goodsno(Request $req) 
    {
        $code = 200;
        $msg = '상품정보가 정상적으로 조회되었습니다.';

        $req_list = $req->data;
        $result = [];
        $failed_ids = [];

        foreach($req_list as $item) {
            $sql = "
                select goods_no, goods_sub, style_no, goods_nm
                from goods
                where goods_no = :goods_no
            ";
            $row = DB::selectOne($sql, ['goods_no' => $item['goods_no']]);
            if($row == null || $row->goods_no == 0) {
                array_push($failed_ids, $item['id']);
            } else {
                $row->id = $item['id'];
            }
            array_push($result, $row);
        }

        return response()->json(['code' => $code, 'msg' => $msg, 'body' => ['data' => ['all' => $result, 'failed' => $failed_ids]]]);
    }

    // 스타일넘버로 상품정보 조회
    public function get_goods_info_by_styleno(Request $req) 
    {
        $code = 200;
        $msg = '상품정보가 정상적으로 조회되었습니다.';
        
        $req_list = $req->data;
        $com_id = $req->com_id ?? '';
        $result = [];
        $failed_ids = [];

        foreach($req_list as $item) {
            $where = '';
            if($com_id != '') $where += ' and com_id = ' . $com_id;

            $sql = "
                select goods_no, goods_sub, style_no, goods_nm
                from goods
                where style_no = :style_no
            " . $where;
            $row = DB::selectOne($sql, ['style_no' => $item['style_no']]);

            if($row == null || $row->goods_no == 0) {
                array_push($failed_ids, $item['id']);
            } else {
                $row->id = $item['id'];
            }
            array_push($result, $row);
        }

        return response()->json(['code' => $code, 'msg' => $msg, 'body' => ['data' => ['all' => $result, 'failed' => $failed_ids]]]);
    }

    // 이미지 일괄등록
    public function upload_images(Request $req)
    {
        $code = 200;
        $msg = '';

        $user = Auth('head')->user();
        $list = $req->data;
        $type = $req->type; // 목록이미지 OR 상세이미지
        $size = $req->size; // 이미지 사이즈 (2022-05 기준 700)
        $sizes = $req->sizes; // 저장할 이미지 사이즈 목록 (2022-05 기준 [50, 62, 70, 100, 129, 55, 120, 160, 180, 270, 280, 320, 500])
        $effect = $req->effect;
        $goods_cont = $req->goods_cont; // 상품 상세설명 (상세이미지일 경우)

        $success = [];

        if($type === 'a') { // 목록이미지일 경우
            if(!empty($list)) {
                foreach($list as $item) {
                    try {
                        DB::beginTransaction();

                        $img_type = 'a';
                        $goods_no = $item['goods_no'] ?? '';
                        $goods_sub = $item['goods_sub'] ?? '';
                        $src = $item['src'] ?? '';

                        if($goods_no == '' || $goods_sub == '' || $src == '') throw new Exception("PRODUCT::NOT_FOUND");
            
                        $image = preg_replace('/data:image\/(.*?);base64,/', '', $src);
                        preg_match('/data:image\/(.*?);base64,/', $src, $matches, PREG_OFFSET_CAPTURE);
                        $ext = $matches[1][0];
            
                        if ($ext == "jpeg" || $ext == "jpg") {
                            $ext = "jpg";
                        // } else if ($ext == "png" || $ext == "gif") {
                        }
                        // $cfg_img_size_real = SLib::getCodesValue("G_IMG_SIZE", "real");
            
                        $sql = "
                            select date_format(reg_dm,'%Y%m%d') as reg_dm, img
                            from goods
                            where goods_no = :goods_no and goods_sub = :goods_sub
                        ";
                        $goods = DB::selectOne($sql, ["goods_no" => $goods_no, "goods_sub" => $goods_sub]);
                        $regdm = $goods->reg_dm;
                        $save_path = sprintf("/images/goods_img/%s/%s", $regdm, $goods_no);
                        $file_name = sprintf("%s_%s_%s.%s", $goods_no, $img_type, $size, $ext);
                        $save_file = sprintf("%s/%s", $save_path, $file_name);
                        
                        if (!Storage::disk('public')->exists($save_path)) {
                            Storage::disk('public')->makeDirectory($save_path);
                        }
        
                        Storage::disk('public')->put($save_file, base64_decode($image));
        
                        $src_file = public_path($save_file);
        
                        if(file_exists($src_file)) {
                            $img_info = getimagesize($src_file);
            
                            $ext_type = $img_info[2];
                            if ($ext_type == 1) {
                                $src_img = imagecreatefromgif($src_file);
                            } else if ($ext_type == 2) {
                                $src_img = imagecreatefromjpeg($src_file);
                            } else if ($ext_type == 3) {
                                $src_img = imagecreatefrompng($src_file);
                            } else {
                                return false;
                            }
            
                            $dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type, $size));
                            $this->resize($ext_type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $size);
        
                            for ($i = 0; $i < count($sizes); $i++) {
                                $img_type_chk = 'a';
        
                                switch ($sizes[$i]) {
                                    case '50':
                                    case '62':
                                    case '70':
                                    case '100':
                                    case '129':
                                        $img_type_chk = "s";
                                        break;
                                    default:
                                        break;
                                }
                                $dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type_chk, $sizes[$i]));
                                $this->resize($ext_type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $sizes[$i]);
                            }
        
                            DB::table('goods')
                                ->where('goods_no', $goods_no)
                                ->where('goods_sub', $goods_sub)
                                ->update([
                                    'img' => $save_file,
                                    'img_update' => now()
                                ]
                            );
                        }
                        DB::commit();
                        array_push($success, $item);
                    } catch (Exception $e) {
                        return response()->json(['code' => 500, 'msg' => $e->getMessage(), 'body' => ['fail' => 'image upload fail']]);
                    }
                }
            }
        } else if($type === 'd') { // 상세이미지일 경우
            if(!empty($list)) {
                define("_MAX_UPLOAD_WIDTH_", 1280);
                define("_IMG_DIR_", "/images/goods_cont");

                foreach($list as $item) {
                    try {
                        DB::beginTransaction();

                        $goods_no = $item['goods_no'] ?? '';
                        $goods_sub = $item['goods_sub'] ?? '';
                        $goods_nm = $item['goods_nm'] ?? '';
                        $src = $item['src'] ?? '';

                        if($goods_no == '' || $goods_sub == '' || $src == '') throw new Exception("PRODUCT::NOT_FOUND");

                        $image = preg_replace('/data:image\/(.*?);base64,/', '', $src);
                        preg_match('/data:image\/(.*?);base64,/', $src, $matches, PREG_OFFSET_CAPTURE);
                        $ext = $matches[1][0];

                        if ($ext == "jpeg" || $ext == "jpg") {
                            $ext = "jpg";
                        // } else if ($ext == "png" || $ext == "gif") {
                        }

                        $img_name = sprintf("img_%s", md5(uniqid()));
                        $file_name = $img_name . '.' . $ext;
                        $save_file = sprintf("%s/%s", _IMG_DIR_, $file_name);

                        // if(!file_exists(_IMG_DIR_)) {
                        //     mkdir(_IMG_DIR_, 0755, true);
                        //     chmod(_IMG_DIR_, 0777);
                        // }

                        if (!Storage::disk('public')->exists(_IMG_DIR_)) {
                            Storage::disk('public')->makeDirectory(_IMG_DIR_);
                        }
                        Storage::disk('public')->put($save_file, base64_decode($image));
                        
                        $src_file = public_path($save_file);
                        if(file_exists($src_file)) {
                            $img_info = getimagesize($src_file);
            
                            $ext_type = $img_info[2];
                            if ($ext_type == 1) {
                                $src_img = imagecreatefromgif($src_file);
                            } else if ($ext_type == 2) {
                                $src_img = imagecreatefromjpeg($src_file);
                            } else if ($ext_type == 3) {
                                $src_img = imagecreatefrompng($src_file);
                            } else {
                                return false;
                            }
            
                            $dst_file = public_path($save_file);
                            if($item['width'] > _MAX_UPLOAD_WIDTH_) {
                                $this->resize($ext_type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], _MAX_UPLOAD_WIDTH_);
                            } else {
                                $this->resize($ext_type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $item['width']);
                            }
                        }
            
                        $conf = new Conf();
                        $cfg_domain	= $conf->getConfigValue("shop", "domain");
                        $cont = Lib::Rq(str_replace($cfg_domain, "", $goods_cont));
                        $cont = str_replace("***이미지영역***", "<img src=\"$save_file\" style=\"width: auto;\" />", $cont);

                        DB::table('goods')
                            ->where('goods_no', $goods_no)
                            ->where('goods_sub', $goods_sub)
                            ->limit(1)
                            ->update([
                                'goods_cont' => $cont,
                                'upd_dm' => now()
                            ]
                        );
                        DB::commit();
                        array_push($success, $item);
                    } catch (Exception $e) {
                        return response()->json(['code' => 500, 'msg' => $e->getMessage(), 'body' => ['fail' => 'image upload fail']]);
                    }
                }
            }
        }
        $msg = '이미지업로드가 완료되었습니다.';
        return response()->json(['code' => $code, 'msg' => $msg, 'body' => ['success' => $success]]);
    }

    public function resize($type, $effect, $src_img, $dstFile, $sw, $sh, $dw, $dh = 0)
    {
        if ($sw < $dw) { // 이미지가 작은 값이 들어오는 경우 - 프론트 단에서 처리하여 해당 사항 없음.
            $dw = $sw;
            $dh = $sh;
        }

        if ($sw >= $dw) { // 이미지가 리사이징 되어야할 이미지 너비보다 큰 경우
            // $dh = ceil(($dw / $sw) * $sh);
            $dh = round(($dw / $sw) * $sh); // 500px 사이즈에서 리사이징된 이미지 높이가 500px 에서 501px로 올림되고 있어 수식을 round로 변경
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
}
