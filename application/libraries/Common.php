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
    
    /**
     * 格式化metas输出
     *
     * @access public
     * @param array - $metas metas内容数组
     * @param string - $split 分割符
     * @param boolean - $link 是否输出连接
     * @return string - 格式化输出
     */
    public static function format_metas($metas = array(), $split = ',', $link = true)
    {
    
        $format = '';
        	
        if ($metas)
        {
            $result = array();
    
            foreach ($metas as $meta)
            {
                $result[] = $link ? '<a href="' . site_url($meta['type'].'/'.$meta['slug']) . '">'
                    . $meta['name'] . '</a>' : $meta['name'];
            }
    
            $format = implode($split, $result);
        }
    
        return $format;
    }
    
    /**
     * 根据分割符的位置获取摘要
     * @access string $string 输入串
     * @param unknown $string
     */
    public static function get_excerpt($string)
    {
        /** 检查是否存在分割符标记 */
        list($excerpt) = explode(ST_CONTENT_BREAK,$string);
        
        $excerpt = (empty($excerpt)) ? $string : $excerpt;
        
        $CI = &get_intance();
        
        /** 如果没有安装任何编辑器插件，则需程序自动分段 */
        if(!$CI->plugin->check_hook_exist(ST_CORE_HOOK_EDITOR))
        {
            $excerpt = self::remove_paragraph($excerpt);
            $excerpt = self::cut_paragraph($excerpt);
        }
        
        return self::fix_html($excerpt);
    }
    
    
    /**
     * 去掉html中的分段
     *
     * @access public
     * @param string $html 输入串
     * @return string
     */
    public static function remove_paragraph($html)
    {
        return trim(preg_replace(
            array("/\s*<p>(.*?)<\/p>\s*/is", "/\s*<br\s*\/>\s*/is",
                "/\s*<(" . self::PARAGRAPH_HTML_TAG . ")([^>]*)>/is", "/<\/(" . self::PARAGRAPH_HTML_TAG . ")>\s*/is", "/\s*\[\-\-break\-\-\]\s*/is"),
            array("\n\\1\n", "\n", "\n\n<\\1\\2>", "</\\1>\n\n", "\n\n[--break--]\n\n"),
            $html));
    }
    
    public static function fix_html($string)
    {
        $starPos = strrpos($string,"<");
        /**strrpos — 计算指定字符串在目标字符串中最后一次出现的位置*/
        
        if(false == $starPos)
        {
            return $string;
        }
        
        $trimString = substr($string,$starPos);
        /**substr — 返回字符串的子串*/
        
        if(false === strpos($trimString,">"))
        {
            $string = substr($string,0,$starPos);
        }
        
        //非自闭合html标签列表
	   preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
	   preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);
	   
       if(!empty($startTags[1]) && is_array($closeTags[1]));
       {
           /**krsort — 对数组按照键名逆向排序*/
           krsort($startTags[1]);
           $closeTagsIsArray = is_array($closeTags[1]);
           foreach ($startTags[1] as $key=> $tag)
           {
               $attrLength = strlen($startTags[2][$key]);
               if($attrLength >0 && "/" == trim($strartTags[2][$key][$attrLength-1]))
               {
                   continue;
               }
               if(!empty($closeTags[1]) && $closeTagsIsArray)
               {
                   if(false !== ($index = array_search($tag,$closeTags[1])))
                   {
                       unset($closeTags[1][$index]);
                       continue;
                   }
               }
               
               $string .="</{$tag}>";
               
           }
       }
       return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string); 
    }
    
}



/*
 End of file
 Location:Common.php
 */