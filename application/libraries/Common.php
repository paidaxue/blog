<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月13日
*/

class Common{
    
    /** 默认不解析的标签列表 */
    const LOCKED_HTML_TAG = 'code|pre|script';
    
    /** 需要去除内部换行的标签 */
    const ESCAPE_HTML_TAG = 'div|blockquote|object|pre|table|fieldset|tr|th|td|li|ol|ul|h[1-6]';
    
    /** 元素标签 */
    const ELEMENT_HTML_TAG = 'div|blockquote|pre|td|li';
    
    /** 布局标签 */
    const GRID_HTML_TAG = 'div|blockquote|pre|code|script|table|ol|ul';
    
    /** 独立段落标签 */
    const PARAGRAPH_HTML_TAG = 'div|blockquote|pre|code|script|table|fieldset|ol|ul|h[1-6]';
    
    /**
     * 锁定的代码块
     *
     * @access private
     * @var array
     */
    private static $_lockedBlocks = array('<p></p>' => '');
    
    /**
     * 锁定标签回调函数
     *
     * @access private
     * @param array $matches 匹配的值
     * @return string
    */
    public static function __lock_html(array $matches)
    {
        $guid = '<code>' . uniqid(time()) . '</code>';
        self::$_lockedBlocks[$guid] = $matches[0];
    
        return $guid;
    }
    
    
    /**
     * 根据count数目来输出字符
     * <code>
     * echo Common::split_by_count(20, 10, 20, 30, 40, 50);
     * </code>
     *
     * @access public
     * @return string
     */
    public static function split_by_count($count)
    {
        $sizes = func_get_args();
        array_shift($sizes);
    
        foreach ($sizes as $size)
        {
            if ($count < $size)
            {
                return $size;
            }
        }
    
        return 0;
    }
    
    
    /**
     * 按分割数输出字符串
     *
     * @access public
     * @param string $param 需要输出的值
     * @return integer
     */
    public function split($count)
    {
        $args = func_get_args();
        array_unshift($args, $count);
    
        return call_user_func_array(array('Common', 'split_by_count'), $args);
    }
    
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
    
    public static function cut_paragraph($string,$pargraph = true)
    {
        $string = trim($string);
        
        if(empty($string))
        {
            return '';
        }
        
        		/** 锁定标签 */
		$string = preg_replace_callback("/<(" . self::LOCKED_HTML_TAG . ")[^>]*>.*?<\/\\1>/is", array('Common', '__lock_html'), $string);
	
		$string = @preg_replace("/\s*<(" . self::ELEMENT_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>\s*/ise",
				"str_replace('\\\"', '\"', '<\\1\\2>' . Common::cut_paragraph(trim('\\3'), false) . '</\\1>')", $string);
		$string = @preg_replace("/<(" . self::ESCAPE_HTML_TAG . '|' . self::LOCKED_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>/ise",
				"str_replace('\\\"', '\"', '<\\1\\2>' . str_replace(array(\"\r\", \"\n\"), '', '\\3') . '</\\1>')", $string);
		$string = @preg_replace("/<(" . self::GRID_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>/is", "\n\n<\\1\\2>\\3</\\1>\n\n", $string);
	
		/** fix issue 197 */
		$string = preg_replace("/\s*<p ([^>]*)>(.*?)<\/p>\s*/is", "\n\n<p \\1>\\2</p>\n\n", $string);
	
		/** 区分段落 */
		$string = preg_replace("/\r*\n\r*/", "\n", $string);
		
		if($paragraph || false !== strpos($string, "\n\n"))
		{
		    $string = '<p>' . preg_replace("/\n{2,}/", "</p><p>", $string) . '</p>';
		}
        
		$string = str_replace("\n", '<br />', $string);
		
		/** 去掉不需要的 */
		$string = preg_replace("/<p><(" . self::ESCAPE_HTML_TAG . '|p|' . self::LOCKED_HTML_TAG
		    . ")([^>]*)>(.*?)<\/\\1><\/p>/is", "<\\1\\2>\\3</\\1>", $string);
		
		return str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $string);    
        
    }
    
    /**
     * 是否存在分割符标记
     *
     * @access public
     * @param string $content 输入串
     * @return bool
     */
    public static function has_break($content)
    {
        if(strpos($content,ST_CONTENT_BREAK) !== FALSE)
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * 输出头部feed meta信息
     *
     * @access public
     * @param string $type 类型
     * @param mixed $slug slug
     * @param string $alt_title
     * @return string
     */
    public static function render_feed_meta($type = 'default', $slug = NULL , $alt_title = '')
    {
        if(empty($type) || !in_array($type, array('default', 'post', 'category', 'tag')))
        {
            return;
        }
    
        /** 初始化默认值 */
        $feed_rss_url = site_url('feed');
        $feed_atom_url = site_url('feed/atom');
        $alt_title = empty($alt_title) ? setting_item('blog_title') : htmlspecialchars($alt_title);
    
        $parsed_feed = <<<EOT
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}" title="订阅 {$alt_title} 所有文章" />\r\n
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}/comments" title="订阅 {$alt_title} 所有评论" />\r\n
EOT;
    
        if('default' === $type)
        {
            return $parsed_feed;
        }
        else
        {
            $title = '订阅';
            	
            switch($type)
            {
                case 'post':
                    $title .= $alt_title . '下的评论';
                    break;
                case 'category':
                    $title .= $alt_title . '分类下的文章';
                    break;
                case 'tag':
                    $title .= $alt_title . '标签下的文章';
                    break;
            }
            	
            return <<<EOT
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}/{$type}/{$slug}" title="{$title}" />\r\n
EOT;
    
        }
    }
    
    /**
     * 获取一个选项
     *
     * @access	public
     * @return	mixed
     */
    function setting_item($item)
    {
        static $setting_item = array();
    
        if (!isset($setting_item[$item]))
        {
            $settings = &get_settings();
    
            if (!isset($settings[$item]))
            {
                return FALSE;
            }
    
            $setting_item[$item] = $settings[$item];
        }
    
        return $setting_item[$item];
    }
    
/**
 * 获取用户配置
 *
 * @access	public
 * @return	array
 */
function &get_settings()
{
	static $user_settings;

	if(!isset($user_settings))
	{
		$CI = &get_instance();

		$CI->load->library('stcache');
		//得到关于setting的缓存
		$settings = $CI->stcache->get('settings');
		//如果缓存里面没有,就从数据库中取
		if(FALSE == $settings)
		{
			$query = $CI->db->get('settings');

			foreach($query->result() as $row)
			{
				$settings[$row->name] = $row->value;
			}

			$query->free_result();

			//把数据库中得到的,放入缓存中
			$CI->stcache->set('settings', $settings);
		}

		$user_settings[0] = &$settings;
	}
	//返回缓存
	return $user_settings[0];
}
    
}



/*
 End of file
 Location:Common.php
 */