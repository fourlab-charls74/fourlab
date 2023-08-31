<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Thumbnail {

	var $quality = 95;
	var $amount = 50;
	var $radius = 0.5;
	var $threshold = 0;
	var $srcFile = "";

	var $ftp_yn = "N";
	var $image_info = array();
	var $db;


	/**
	 * 생성자 ( PHP5 )
	 * @param Resource $db
	 */
	function __construct($db = null)
	{
		/*if($db != null){
			$this->db = $db;
			// FTP 환경 설정
			$this->setFTP();
		}*/
	}

	/**
	 * 생성자 ( PHP4 )
	 * php5 로 정의된 생성자 호출
	 */
	function Thumbnail()
	{
		$this->__construct();
	}


	/**
	 * FTP 환경 설정
	 * @param Resouce $this->db
	 */
	function setFTP()
	{
		// 설정 값 얻기
		$conf = new Config($this->db);
		$this->image_info = $conf->getConfigTypeValue("image");
		$this->ftp_yn = $conf->getConfigValue("image","ftp_yn");
		if($this->ftp_yn == "Y")
		{
			$this->img_server = (strpos($this->image_info["domain"],"http://") !== false) ? $this->image_info["domain"]:sprintf("http://%s",$this->image_info["domain"]);
		}
	}

	/*
		Function: imgFilesResize
			이미지 리사이징 사이즈 별 설정 전달 <imgFileResize>, 실제 처리는 <imgResize>에서 진행

		Parameters:
			$srcFile - 이미지 소스 파일
			$arrDstFile - 이미지 리사이징 사이즈 정보 ( array)
			$isprint = true - 파일명 출력 여부

		Returns:
			None
	*/

	function imgFilesResize($srcFile, $arrDstFile, $isprint = true) {

		if(file_exists($srcFile)){

			if(preg_match("/jpg/", $srcFile) || preg_match("/gif/", $srcFile) || preg_match("/jpeg/", $srcFile) || preg_match("/png/", $srcFile)) {

				$this->srcFile = $srcFile;

				$img_info = getimagesize($srcFile);
				$type = $img_info[2];
				if ($type == 1) {
					$src_img = imagecreatefromgif($srcFile);
				} else if ($type == 2) {
					$src_img = imagecreatefromjpeg($srcFile);
				} else if ($type == 3) {
					$src_img = imagecreatefrompng($srcFile);
				} else {
					return false;
				}

				$srcWidth = $img_info[0];
				$srcHeight = $img_info[1];

				for($i=0;$i<count($arrDstFile);$i++){
					$dstFile = $arrDstFile[$i]["file"];
					$width = $arrDstFile[$i]["width"];
					$height = $arrDstFile[$i]["height"];

					if(file_exists($dstFile)){
						if(Storage::disk('public')->exists($dstFile)) {
							if ($src_img != "") {
								$image      = preg_replace('/data:image\/(.*?);base64,/', '', $src_img);
								$save_file  = sprintf("%s", $dstFile);

								Storage::disk('public')->put($save_file, base64_decode($image));
							}
						}
					}

					$this->imgResize($src_img,$dstFile,$srcWidth,$srcHeight,$width,$height);
				}

				imagedestroy($src_img);
			}
		} else {
			return false;
		}
	}

	/*
		Function: imgFileResize
			이미지 리사이징 사이즈 설정 전달 <imgResize>

		Parameters:
			$srcFile - 이미지 소스 파일
			$dstFile - 리사이징 후 저장할 파일
			$width - 가로 사이즈
			$height - 세로 사이즈

		Returns:
			None
	*/

	function imgFileResize($srcFile,$dstFile,$width,$height) {

		if (preg_match("/jpg/", $srcFile) || preg_match("/gif/", $srcFile) || preg_match("/jpeg/", $srcFile) || preg_match("/png/", $srcFile)) {

			$this->srcFile = $srcFile;

			$img_info = getimagesize($srcFile);
			$type = $img_info[2];
			if ($type == 1) {
				$src_img = imagecreatefromgif($srcFile);
			} else if ($type == 2) {
				$src_img = imagecreatefromjpeg($srcFile);
			} else if ($type == 3) {
				$src_img = imagecreatefrompng($srcFile);
			} else {
				return false;
			}

			$srcWidth = $img_info[0];
			$srcHeight = $img_info[1];

			if(file_exists($dstFile)){
				if(Storage::disk('public')->exists($dstFile)) {
					if ($src_img != "") {
						$image      = preg_replace('/data:image\/(.*?);base64,/', '', $src_img);
						$save_file  = sprintf("%s", $dstFile);

						Storage::disk('public')->put($save_file, base64_decode($image));
					}
				}
			}

			$this->imgResize($src_img, $dstFile, $srcWidth, $srcHeight, $width, $height);

			imagedestroy($src_img);
		}
	}

	/*
		Function: imgResize
			이미지 리사이즈 처리

		Parameters:
			$srcFile - 이미지 소스 파일
			$dstFile - 리사이징 후 저장할 파일
			$width - 가로 사이즈
			$height - 세로 사이즈

		Returns:
			None
	*/

	function imgResize($src_img,$dstFile,$sw,$sh,$dw,$dh){

		if ($sw <= $dw) {
			$dw = $sw;
			$dh = $sh;
		}

		if ($sw > $dw) {
			$dh = ceil(($dw/$sw)*$sh);
		}

		$dst_img = imagecreatetruecolor($dw,$dh);
		imagecolorallocate($dst_img, 255, 255, 255);

		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0,$dw,$dh,$sw,$sh);

		$dst_img = UnsharpMask($dst_img, 50, 0.5, 0);
		//$dst_img = WaterMark($dst_img);

		imageinterlace($dst_img);


		$img_info = getimagesize($this->srcFile);
		$type = $img_info[2];

		if ($type == 1) {
			imagegif($dst_img,$dstFile);
		} else if ($type == 2) {
			imagejpeg($dst_img,$dstFile,$this->quality);
		} else if ($type == 3) {
			imagepng($dst_img,$dstFile);
		}
		imagedestroy($dst_img);
	}

	/*
		Function: imgFileSquareResize
			이미지 정사각형으로 만들기

		Parameters:
			$srcFile - 이미지 소스 파일
			$dstFile - 리사이징 후 저장할 파일
			$width - 가로 사이즈
			$height - 세로 사이즈

		Returns:
			None
	*/

	function imgFileSquareResize($srcFile,$dstFile){

		$img_info = getimagesize($srcFile);

		$width = $img_info[0];
		$height = $img_info[1];
		$type = $img_info[2];

		if ($type == 1) {
			$src_img = imagecreatefromgif($srcFile);
		} else if ($type == 2) {
			$src_img = imagecreatefromjpeg($srcFile);
		} else if ($type == 3) {
			$src_img = imagecreatefrompng($srcFile);
		} else {
			return false;
		}

		// 정사작형 만들기
		if( $width > $height){
			$thumb_size = $height;
		} else if($width < $height){
			$thumb_size = $width;
		} else {
			return false;
		}

		if( $height > $width ) {
			// For landscape images
			$x_offset = 0;
			$y_offset = ($height - $thumb_size )/ 2;
			$square_size = $thumb_size;
		} else if( $height < $width ) {
			// For portrait and square images
			$y_offset = 0;
			$x_offset = ($width - $thumb_size )/ 2;
			$square_size = $thumb_size;
		}

		$dst_img = imagecreatetruecolor($square_size,$square_size);
		imagecolorallocate($dst_img, 255, 255, 255);

		imagecopyresampled($dst_img, $src_img, 0, 0, $x_offset, $y_offset,$square_size,$square_size,$square_size,$square_size);

		$dst_img = UnsharpMask($dst_img, 50, 0.5, 0);
		//$dst_img = WaterMark($dst_img);

		imageinterlace($dst_img);

		if ($type == 1) {
			imagegif($dst_img,$dstFile);
		} else if ($type == 2) {
			imagejpeg($dst_img,$dstFile,$this->quality);
		} else if ($type == 3) {
			imagepng($dst_img,$dstFile);
		}
		imagedestroy($dst_img);

		return true;
	}
}


/*



*/


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
function UnsharpMask($img, $amount, $radius, $threshold)    {

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
	if ($amount > 500)    $amount = 500;
	$amount = $amount * 0.016;
	if ($radius > 50)    $radius = 50;
	$radius = $radius * 2;
	if ($threshold > 255)    $threshold = 255;

	$radius = abs(round($radius));     // Only integers make sense.
	if ($radius == 0) {
		return $img; imagedestroy($img);
	}
	$w = imagesx($img); $h = imagesy($img);
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
			array( 1, 2, 1 ),
			array( 2, 4, 2 ),
			array( 1, 2, 1 )
		);
		imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h);
		imageconvolution($imgBlur, $matrix, 16, 0);
	}
	else {

		// Move copies of the image around one pixel at the time and merge them with weight
		// according to the matrix. The same matrix is simply repeated for higher radii.
		for ($i = 0; $i < $radius; $i++)    {
			imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
			imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
			imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
			imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
		}
	}

	if($threshold>0){
		// Calculate the difference between the blurred pixels and the original
		// and set the pixels
		for ($x = 0; $x < $w-1; $x++)    { // each row
			for ($y = 0; $y < $h; $y++)    { // each pixel

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
	}
	else{
		for ($x = 0; $x < $w; $x++)    { // each row
			for ($y = 0; $y < $h; $y++)    { // each pixel
				$rgbOrig = ImageColorAt($img, $x, $y);
				$rOrig = (($rgbOrig >> 16) & 0xFF);
				$gOrig = (($rgbOrig >> 8) & 0xFF);
				$bOrig = ($rgbOrig & 0xFF);

				$rgbBlur = ImageColorAt($imgBlur, $x, $y);

				$rBlur = (($rgbBlur >> 16) & 0xFF);
				$gBlur = (($rgbBlur >> 8) & 0xFF);
				$bBlur = ($rgbBlur & 0xFF);

				$rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
				if($rNew>255){$rNew=255;}
				elseif($rNew<0){$rNew=0;}
				$gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
				if($gNew>255){$gNew=255;}
				elseif($gNew<0){$gNew=0;}
				$bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
				if($bNew>255){$bNew=255;}
				elseif($bNew<0){$bNew=0;}
				$rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
				ImageSetPixel($img, $x, $y, $rgbNew);
			}
		}
	}
	imagedestroy($imgCanvas);
	imagedestroy($imgBlur);

	return $img;
}

function watermark($dir, $watermark_nm, $image_nm) {
	/*
	// Setting
	$watermarkPath = $dir.$watermark_nm.'.png';
	$target = $dir."wm/".$image_nm;
	$quality = "100";
	$imagesource = $dir.$image_nm;

	$filetype = substr($imagesource,strlen($imagesource)-4,4);
	$filetype = strtolower($filetype);

	$watermarkType = substr($watermarkPath,strlen($watermarkPath)-4,4);
	$watermarkType = strtolower($watermarkType);

	if($filetype == ".gif") {
		$image = @imagecreatefromgif($imagesource);
	} else if($filetype == ".jpg" || $filetype == "jpeg") {
		$image = @imagecreatefromjpeg($imagesource);
	} else	if($filetype == ".png") {
		$image = @imagecreatefrompng($imagesource);
	} else {
		die("a murit nu am scos imaginea");
	}

	if(!$image) {
		die("a murit nu avem imagine");
	}

	if($watermarkType == ".gif") {
		$watermark = @imagecreatefromgif($watermarkPath);
	} else if($watermarkType == ".png") {
		$watermark = @imagecreatefrompng($watermarkPath);
	} else {
		die("a murit $watermark nu e");
	}

	if(!$watermark) {
		die("a murit lipsa watermark");
	}

	$imagewidth = imagesx($image);
	$imageheight = imagesy($image);
	$watermarkwidth = imagesx($watermark);
	$watermarkheight = imagesy($watermark);
	$startwidth = (($imagewidth - $watermarkwidth)/to);
	$startheight = (($imageheight - $watermarkheight)/to);

	imagecopy($image, $watermark, $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight);
	imagejpeg($image,$target,$quality);
	imagedestroy($image);
	imagedestroy($watermark);
	*/
}

?>
