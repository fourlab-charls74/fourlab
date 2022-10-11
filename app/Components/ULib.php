<?php

namespace App\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ULib
{
    /**
     * 
     * $save_path :: 데이터를 저장할 경로
     * $src :: base64로 데이터
     * 
     * return 
     *     업로드된 파일 경로 반환
     * */ 
    public static function uploadBase64img($save_path, $src) {
        $image = preg_replace('/data:image\/(.*?);base64,/', '', $src);
        
        $file_name = sprintf("%s.jpg", date('YmdHis'));
        $save_file = sprintf("%s/%s", $save_path, $file_name);

        /* 이미지를 저장할 경로 폴더가 없다면 생성 */
        if(!Storage::disk('public')->exists($save_path)){
            Storage::disk('public')->makeDirectory($save_path);
        }
  
        //저장
        Storage::disk('public')->put($save_file, base64_decode($image));
        
        return sprintf('%s', $save_file);
    }

    public static function deleteFile($path) {
        Storage::disk('public')->delete($path);
    }
}