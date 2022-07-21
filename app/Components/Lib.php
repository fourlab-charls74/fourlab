<?php

namespace App\Components;

class Lib
{
    /**
     * Object 에 name 에 대한 value 값
     *
     * @param   $object
     * @param   $name
     * @param   $default
     * @return  $object value
     */
    public static function getValue($obj,$name, $default = ""){
        $ret = null;
        if( $default != "" ) $ret = $default;
        return isset($obj[$name])? $obj[$name]:$ret;
    }

    /**
     * Quote 처리
     *
     * @param   $string - 변경할 문자열
     * @return  String
     */
    public static function quote($str){
        return addCslashes($str, '\'');
    }

    /**
     * 콤마
     *
     * @param   $integer - 숫자
     * @param   $integer - 소수점
     *
     * @return  String
     */
    public static function cm($num,$dec = -1){
        $num = (float)$num;
        if($dec == -1){
            return preg_replace("/(\d+)/",number_format(floor(abs($num))),$num,1);
        } else {
            return number_format($num,$dec);
        }
    }

    /*
        Function: uncm
            숫자에 콤마 제거

        Parameters:
            num - 숫자

        Returns:
            콤마 제거한 숫자

    */

    public static function uncm($num){
        return str_replace(",","",$num);
    }



    /**
     * 문자열 자르기
     *
     * @param   $string - 문자열
     * @param   $integer - 문자열 길이
     * @param   $boolean - checkmb 여부
     * @param   $string - 꼬리말
     *
     * @return  String
     */
    public static function cutString($str, $divpnt, $checkmb=false, $tail='...') {

        if(mb_check_encoding($str)){
            $encoding = mb_detect_encoding($str);
            if(mb_strlen($str,$encoding) > $divpnt){
                $div_str = mb_substr($str,0,$divpnt - mb_strlen($tail,$encoding),$encoding);
            } else {
                return $str;
            }
        } else {

            if ( strlen($str) <= $divpnt ) {
                return $str;
            }

            if(preg_match( '/[\x80-\xff]/', $str) && @iconv("UTF-8","UTF-8",$str) == $str){	// UTF8

                $checkmb = false;
                /**
                 * UTF-8 Format
                 * 0xxxxxxx = ASCII, 110xxxxx 10xxxxxx or 1110xxxx 10xxxxxx 10xxxxxx
                 * latin, greek, cyrillic, coptic, armenian, hebrew, arab characters consist of 2bytes
                 * BMP(Basic Mulitilingual Plane) including Hangul, Japanese consist of 3bytes
                 **/
                preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match); // target for BMP

                $m = $match[0];
                $tlen = strlen($tail); // length of tail string
                $mlen = count($m); // length of matched characters

                if (!$checkmb && $mlen <= $divpnt) return $str;

                $ret = array();
                $count = 0;
                for ($i=0; $i < $divpnt; $i++) {
                    $count += ($checkmb && strlen($m[$i]) > 1)?2:1;
                    if ($count + $tlen > $divpnt) break;
                    $ret[] = $m[$i];
                }
                $div_str = join('', $ret);

            } else {

                $str = substr($str, 0, $divpnt);
                for ($i = $divpnt - 1; $i > 1; $i--){
                    if (ord(substr($str,$i,1)) < 128) break;
                }
                $div_str = substr($str, 0, $divpnt - ($divpnt - $i + 1) % 2);
            }
        }
        return $div_str . $tail;
    }

    public static function get_enc_hash($pw, $mode = "password", $enc_key = "" ){
        if( $mode == "password" ){
           $pw_hash = Lib::enc_password($pw);
        } elseif( $mode == "old_password" ){
           $pw_hash = Lib::enc_old_password($pw);
        } elseif( $mode == "md5" ){
           $pw_hash = Lib::enc_md5($pw);
        } elseif( $mode == "mhash" ){
           $pw_hash = Lib::enc_mhash($enc_key.$pw);
        }
        return $pw_hash;
     }

     /*
         function : enc_password
     
         MySQL password() 함수 구현
     
         Parameters:
             $pw : 비밀번호
     
         Returns:
             $pw : 암호화된 hash 문자열
     */
     public static function enc_password($pw) {
         if (strlen($pw) > 0) {
             if (substr($pw,0,1) == "*") {
                 return $pw;
             } else {
                 return strtoupper('*'.sha1(sha1($pw,true)));
             }
         } else {
             return null;
         }
     }
     
    /*
        function : enc_old_password

        MySQL old_password() 함수 구현

        Parameters:
            $pw : 비밀번호

        Returns:
            $pw : 암호화된 hash 문자열
    */
    public static function enc_old_password($pw) {
        if( $pw === null )
            return null;
        if( strlen($pw) == 0 )
            return '';

        $nr  = 1345345333;
        $add = 7;
        $nr2 = 0x12345671;

        $chs = preg_split("//", $pw);
        foreach ($chs as $ch) {
            // skip space in password
            if (($ch == '') || ($ch == ' ') || ($ch == '\t'))
                continue;
            $tmp = ord($ch);
            $nr  ^= ((($nr & 63) + $add) * $tmp) + ($nr << 8);
            $nr2 += ($nr2 << 8) ^ $nr;
            $add += $tmp;
        }

        // Don't use sign bit (str2int)
        $nr  &= 0x7fffffff;
        $nr2 &= 0x7fffffff;

        return sprintf( "%08x%08x", $nr, $nr2 );
    }

    /*
        function : enc_md5
    
        MySQL md5() 함수 구현
    
        Parameters:
            $pw : 비밀번호
    
        Returns:
            $pw : 암호화된 hash 문자열
    */
    public static function enc_md5( $pw ) {
        if( $pw === null )
            return null;
    
        return md5($pw);
    }

    /*
        function : enc_mhash

        PHP mhash() 함수 구현

        Parameters:
            $pw : 비밀번호

        Returns:
            $pw : 암호화된 hash 문자열
    */
    public static function enc_mhash( $pw ){
        if( $pw == null )
            return null;

        $hash = mhash(MHASH_SHA256, md5( $pw ));
        return bin2hex( $hash );
    }

    /**
     * HTML 태그 제거
     *
     * @param string $str 문자열
     * @return string HTML태그 제거된 문자열
     */
    public static function RHtml($str)
    {
        if(($pos = strpos(phpversion(),'5.4')) !== false && $pos == 0)
        {
            $default_charset = strtoupper(ini_get("default_charset"));
            if($default_charset == "UTF-8")
            {
                $str = htmlspecialchars($str,ENT_COMPAT|ENT_HTML401,"ISO-8859-1");
            }
            else
            {
                $str = htmlspecialchars($str);
            }
        }
        else
        {
            $str = htmlspecialchars($str);
        }

        return $str;
    }

    /*
        Function: CheckInt
            지정한 정수얻기

        Parameters:
            p_name - 변수
            default - 값이 없을 경우 기본값

        Returns:
            int
    */
    public static function CheckInt($p_name,$default=0)
    {
        $p_name = str_replace(",", "", $p_name);
        return is_numeric($p_name) ? (int)$p_name : $default;
    }

    /**
     * pretty var_dump data
     * 
     * @param Any ...$arguments
     * @return void
     */
    public static function dd() {
        $count = 0;
        $collection = func_get_args();
        while($count < func_num_args()) {
            echo "<pre style='font-size:1.5rem'>";
            var_dump($collection);
            echo "</pre>";
            $count++;
        }
        exit;
    }

    /**
     * 화면에 메세지 출력하기
     * 
     * @param string $msg 출력할 메세지
     * @param null|string $action
     * * 'close' - 메세지 출력 후 창닫기
     * * 'go' - 메시지 출력 후 링크 이동
     * * 'back' - 메시지 출력후 이전페이지로 이동
     * @param null|string $url 이동할 url 링크 전달 ( $action='go'인 경우 사용 )
     * @return void
     */
    public static function printMsg($msg, $action = null, $url = null) {
        $script = "<script type='text/javascript'>\n";
        $script .= "alert('".$msg."')\n";
        switch ($action) {
            case 'close':
                $script .= "window.close()\n";
                break;
            case 'go':
                $script .= "location.href = '" . $url . "';\n";
            case 'back':
                $script .= "history.back();\n";
            default:
                break;
        }
        $script .= "</script>";
        echo $script;
    }

    /*
    Function: Rq
        DB 입력을 위한 quote 처리 ( 김대진 이름 만듬 )
    Parameters:
        str - 변경할 문자열
        flag - stripslashes 여부 ( 기본값 : 1 )
    Returns:
        String
	*/
	public static function Rq($str, $flag = "1"){
		if($flag != "1"){
			return str_replace("'","''",$str);
		} else {
			return str_replace("'","''",stripslashes($str));
		}
	}

    /**
     * 작성된 쿼리 확인
     */
    public static function q($sql = "") {
        echo "<pre>" . $sql. "</pre>";
        exit;
    }
   
}