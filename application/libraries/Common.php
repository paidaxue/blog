<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月13日
*/

class Common{
    
    //判断密码是否相等
    public static function hash_Validate($source,$target){
        return (self::do_hash($source,$target) == $target);
    }
    
    //对字符串进行hash加密
    public static function do_hash($string,$salt = NULL){
        //如果$salt参数为空,那么随机产生一个数字,md5加密,在截取1到10位数
        if (null == $salt){
            //参数定义constants.php	define('ST_SALT_LENGTH', 9);
            $salt = substr(md5(uniqid(rand(),true)),0,ST_SALT_LENGTH);
        }
        //如果$salt不为空,那么直接截取1到10位数
        else{
            $salt = substr($salt,0,ST_SALT_LENGTH);
        }
        return $salt.sha1($salt.$string);
    
    }
    
    //对slug格式处理
    public static function repair_slugName($str,$default = NULL,$marLength = 200,$charset = 'UTF-8'){
        
        $str = str_replace(array("'",":","\\","/"),"",$str);
        $str = str_replace(array("+", ",", " ", ".", "?", "=", "&", "!", "<", ">", "(", ")", "[", "]", "{", "}"), "_", $str);

        $str = empty($str) ? $default : $str;
        
        return function_exists('mb_get_info') ? mb_strimwidth($str, 0, 128, '', $charset) : substr($str, $maxLength);
    }
    
    //当创建文章中,传入的参数:$this->metas_mdl->metas['category'],'mid'
    public static  function array_flatten($value = array(),$key){
        
        $result = array();
        
        if ($value)
        {
            foreach ($value as $inval)
            {
                if (is_array($inval) && isset($inval[$key]))
                {
                    $result[] = $inval[$key];
                }else{
                    break;
                }
            }
        }
        return $result;        
    }
    
    //把时间变成文字格式
    public static function dateWord($from,$now){
        
        if(idate('Y',$now) != idate('Y',$from)){
            return date('Y年m月d日',$from);
        }
        
        $seconds = $now - $from;
        $days = idate('z',$now) - idate('z',$from);
        
        if($days == 0){
            if($seconds < 3600){
                
                if($seconds < 60){
                    
                    if(10 > $seconds){
                        return '刚刚';
                    }else {
                        return sprintf('%d秒前',$seconds);
                    }
                    return sprintf('%d分钟前',intval($seconds / 60));
                }
                return sprintf('%d小时前',idate('H',$now) - idate('H',$from));
            }
        }
        
        //如果是昨天
        if($days == 1){
            return sprintf('昨天%s',date('H:i',$from));
        }
        
        //如果是前天
        if($days == 2){
            return sprintf('前天%s',date('H:i',$from));
        }
        
        //如果是7天内
        if($days < 7){
            return sprintf('%d天前',$days);
        }
        
        //一周以前
        return date('n月j日',$from);

    }
}


/*
 End of file
 Location:Common.php
 */