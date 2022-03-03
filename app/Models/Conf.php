<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Conf
{
	  private $mobile = null;
    private $conf = null;
    private $cashe = null;

    function __construct($cache = null) {
      $this->mobile = "N";
      $this->cache = $cache;

      $host = isset($_SERVER->HTTP_HOST) ? $_SERVER->HTTP_HOST : "";

      if($this->cache != null) {
          $cache_key_conf = sprintf("bz.%s.front.conf", $this->cache->site);
          $conf = $this->cache->get($cache_key_conf);

          if(isset($conf) && is_array($conf) && count($conf) > 0){
              $this->conf = $conf;
          } else {
              $conf = [];
              $sql = "select type,name,value,mvalue from conf";
              $rows = DB::select($sql);

              foreach($rows as $row) {
                  $conf[$row->type][$row->name] = array( "value" => $row->value, "mvalue" => $row->mvalue );
              }

              if(is_array($conf) && count($conf) > 0){
                  $this->conf = $conf;
                  $this->cache->set($cache_key_conf, $conf, 60*30);
              }
          }
      }

      if(in_array($host,array("m.netpx.co.kr","nm.netpx.co.kr","dm.netpx.co.kr"))){
          $this->mobile = "Y";
      }
    }

    /**
     * 배열 값 얻기
     * @param array $config 설정값
     * @param string $name 항목
     * @param string $default 기본값
     * @return string 배열 값
     */
    public function getValue($config, $name,$default = ""){
      return isset($config[$name]) ? $config[$name]: $default;
    }

    public function getConfig( $type , $name = "", $default = "") 
    {
        if($name == "") {
            return $this->getConfigTypeValue($type);
        } else {
            return $this->getConfigValue($type,$name,$default);
        }
    }

      /**
       * 설정값 얻기 - 구분 값
       * @param type $type
       * @return type
       */
      public function getConfigTypeValue( $type )
      {
        $config = [];

        if($this->conf != null && isset($this->conf[$type]) && count($this->conf[$type]) > 0){
            foreach($this->conf[$type] as $name => $row){
                $val = $row->value;
                $mval = $row->mvalue;
                $value = $this->getMValue($val, $mval);
                $config[$name] = $value;
            }
        } else {
            $sql = "select name, idx, value, mvalue, content from conf where type = '$type'";
            $rows = DB::select($sql);

            foreach($rows as $row) {
              $val = $row->value;
              $mval = $row->mvalue;
              $value = $this->getMValue($val, $mval);

              $config[$row->name] = $value;
            }

            $this->conf[$type] = $config;
        }

        return $config;
    }
    /**
     * 설정값 얻기
     * @param string $type 구분
     * @param string $name 항목
     * @param string $default = "" 기본값
     * @return string 설정값
     */
    public function getConfigValue( $type, $name , $default = "" ){
          if($this->conf != null && isset($this->conf[$type]) && count($this->conf[$type]) > 0){
              if(isset($this->conf[$type][$name])){
                  $row = $this->conf[$type][$name];
                  $val = $row['value'];
                  $mval = $row['mvalue'];
                  $value = $this->getMValue($val, $mval);
                  return $value;
              }
          } else {
              $sql = "select value, mvalue from conf where type = '$type' and name = '$name'";

              $row = DB::selectOne($sql);
              if($row){
                  $val = $row->value;
                  $mval = $row->mvalue;
  
                  $value = $this->getMValue($val, $mval);
                  return $value;
              }
          }

          return $default;
    }


	/**
     * 설정값 리스트 얻기
     * @param string $type 구분
     * @param string $name 항목
     * @param string $default = "" 기본값
     * @return string 설정값
     */
    public function getConfigValues( $type, $name , $default = "" ){
          if($this->conf != null && isset($this->conf[$type]) && count($this->conf[$type]) > 0){
              if(isset($this->conf[$type][$name])){
                  $row = $this->conf[$type][$name];
                  $val = $row['value'];
                  $mval = $row['mvalue'];
                  $value = $this->getMValue($val, $mval);
                  return $value;
              }
          } else {
              $sql = "select value, mvalue from conf where type = '$type' and name = '$name'";

              $result = DB::select($sql);
              if($result){
                  return $result;
              }
          }

          return $default;
    }

    /**
     * 모바일 설정값 얻기
     * 모바일 여부 따라 설정값 얻기
     *
     * @param string $value 설정값
     * @param string $mvalue 모바일 설정값
     * @return string 설정값
     */
    public function getMValue($value, $mvalue)
    {
      if($this->mobile == "Y" && $mvalue != ""){
        return $mvalue;
      } else {
        return $value;
      }
    }
  
}
