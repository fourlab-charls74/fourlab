<?php
function auto_rotate_mobile_picture($filename){
    /*
      exif_read_data 가 작동되지 않는다면
      php.init 파일에서
      extension=exif와 extension=mbstring 주석 처리를 지워주세요.
    */
    $exif = @exif_read_data($filename);
    if(isset($exif['MimeType']) && $exif['MimeType'] == "image/jpeg"){
        if(!empty($exif['Orientation'])) {
            $image = imagecreatefromjpeg($filename);
            switch($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image,90,0);
                    break;
                case 3:
                    $image = imagerotate($image,180,0);
                    break;
                case 6:
                    $image = imagerotate($image,-90,0);
                    break;
            }
            imagejpeg($image,$filename);
        }

    }
}

function auto_image_resize($filename,$image_info,$new_filename,$width,$compression = 75){

    $tmp_width = $image_info[0];
    $tmp_height = $image_info[1];
    $image_type = $image_info[2];
    $height = ceil($width * $tmp_height / $tmp_width);

    if($image_type == IMAGETYPE_JPEG ) {
        $image = imagecreatefromjpeg($filename);
    } elseif( $image_type == IMAGETYPE_GIF ) {
        $image = imagecreatefromgif($filename);
    } elseif( $image_type == IMAGETYPE_PNG ) {
        $image = imagecreatefrompng($filename);
    }

    $new_image = imagecreatetruecolor($width, $height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, imagesx($image),imagesy($image));

    if( $image_type == IMAGETYPE_JPEG ) {
        imagejpeg($new_image,$new_filename,$compression);
    } elseif( $image_type == IMAGETYPE_GIF ) {
        imagegif($new_image,$new_filename);
    } elseif( $image_type == IMAGETYPE_PNG ) {
        imagepng($new_image,$new_filename);
    }
}

function AddImage($img_dir,$file)
{
    if ($file['name']) {
        if (!$file['error']){
            $size = getimagesize($file['tmp_name']);
            if (@is_array($size)) {
                $allow_file = array("jpg", "png", "bmp", "gif", "jpeg", "heif");
                $ext = explode('.', $file['name']);

                if(in_array(strtolower($ext[1]), $allow_file)){
                    $location = $_FILES["file"]["tmp_name"];
                    $filesize = filesize($location);
                    if($filesize < _MAX_UPLOAD_SIZE_*1024*1024){
                        auto_rotate_mobile_picture($location);
                        $img_info = getimagesize($location);
                        //$name = md5(rand(100, 200));
                        $name = sprintf("img_%s",md5(uniqid()));
                        $filename = $name . '.' . $ext[1];

                        $upload_path = $_SERVER['DOCUMENT_ROOT'] . $img_dir;
                        $destination = $upload_path . '/'. $filename; //change this directory

                        if(!file_exists($upload_path)){
                            mkdir($upload_path, 0755, true);
                            chmod($upload_path, 0777);
                        }

                        $tmp_width = $img_info[0];
                        if($tmp_width <= _MAX_UPLOAD_WIDTH_){
                            move_uploaded_file($location, $destination);
                        } else {
                            auto_image_resize($location,$img_info,$destination,_MAX_UPLOAD_WIDTH_);
                        }
                        //echo $img_dir . '/' . $filename;//change this URL

                        echo json_encode(array(
                            "url" => $img_dir . '/' . $filename,
                            "width" => $size[0],
                            "height" => $size[1],
                            "size" => $filesize
                        ));
                        
                    } else {
                        echo json_encode(array(
                            "errmsg" => 'Error - Over the max file size :  ' . $filesize
                        ));
                    }

                } else {
                    echo json_encode(array(
                        "errmsg" => 'Error - Not allowed image format :  ' . $file['name']
                    ));
                }

            } else {
                echo json_encode(array(
                    "errmsg" => 'Error - Not Imaeg file :  ' . $file['name']
                ));
            }
        } else {
            echo json_encode(array(
                "errmsg" => 'Error -  Your upload triggered the following error:  ' . $file['error']
            ));
        }
    }
}

function DelImage($img_dir,$img_url){
    //$file_name = str_replace(base_url(), '', $src); // striping host to get relative path
    $urls = parse_url($img_url);
    $file_name = $_SERVER['DOCUMENT_ROOT'] . $urls["path"];
    if(strpos($urls["path"],$img_dir) !== false){
        if(unlink($file_name))
        {
            echo json_encode(array(
                "msg" => 'File Delete Successfully'
            ));
        }
    }
}

define("_MAX_UPLOAD_SIZE_", 10); // MB
define("_MAX_UPLOAD_WIDTH_", 1280); // MB

$cmd = $_POST["c"];

if($cmd == "add"){
    if(isset($_POST["img_dir"])){
        AddImage($_POST["img_dir"],$_FILES['file']);
    } else {
        echo json_encode(array(
            "errmsg" => 'Error - Not defined img_dir : ' . $_POST["img_dir"]
        ));
    }
} else if($cmd == "del"){
    if(isset($_POST["img_dir"])){
        if(isset($_POST["img_url"])) {
            DelImage($_POST["img_dir"],$_POST["img_url"]);
        } else {
            echo json_encode(array(
                "errmsg" => 'Error - Not defined img_url : ' . $_POST["img_url"]
            ));
        }
    } else {
        echo json_encode(array(
            "errmsg" => 'Error - Not defined img_dir : ' . $_POST["img_dir"]
        ));
    }
} else {
    echo json_encode(array(
        "errmsg" => 'Error - Not defined command(c) - add or del : ' .  $cmd
    ));
}

?>
