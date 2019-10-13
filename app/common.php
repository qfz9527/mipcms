<?php
use think\Response;
use app\common\lib\ChinesePinyin;
    function compress_html($higrid_uncompress_html_source) {
        $chunks = preg_split( '/(<pre.*?\/pre>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE );
        $higrid_uncompress_html_source = '';
        foreach ( $chunks as $c )
        {
            if ( strpos( $c, '<pre' ) !== 0 )
            {
            $c = preg_replace( '/[\\n\\r\\t]+/', ' ', $c );
//              $c = preg_replace('/ {2,}/', '', $c);
//              $c = preg_replace('/> </', '><', $c);

                $c = preg_replace( '/ {2,}/', ' ', $c );
                $c = preg_replace( '/>\\s</', '><', $c );
                $c = preg_replace( '/\\/\\*.*?\\*\\//i', '', $c );
            }
            $higrid_uncompress_html_source .= $c;
        }
        $custom = preg_split( '/(<style mip-custom.*<\/style>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE );
        $higrid_uncompress_html_source = '';
        foreach ( $custom as $k => $c ) {
            if ($k == 1) {
                $c = str_replace(array("", "\r", "\n", "\t", '  ', '    ', '    '), '', $c);
                }
                $higrid_uncompress_html_source .= $c;
            }
            return $higrid_uncompress_html_source;
    }
      
    function create_salt($length=12) {
          return $salt = substr(uniqid(rand()),0,$length);
    }
    
    function create_md5($string,$salt)
    {
        return md5($string.$salt);
    }
    function uuid() {
        $charid = strtolower(md5(uniqid(rand(), true)));
        return substr($charid,0,24);
    }
    function unid() {
        $charid = strtolower(md5(uniqid(rand(), true)));
        return $charid;
    }
    function jsonError($message = '',$url=null) {
        $return['msg'] = $message;
        $return['code'] = -1;
        $return['url'] = $url;
        $res = json_encode($return,true);
        return Response::create($res)->contentType('text/json');;
        
    }
    function jsonSuccess($message = '',$data = '',$url=null) {
        $return['msg']  = $message;
        $return['data'] = $data;
        $return['code'] = 1;
        $return['url'] = $url;
        $res = json_encode($return,true);
        return Response::create($res)->contentType('text/json');;
    }
    
    function arrayError ($message = '', $data = '', $url = '') {
        $return['msg'] = $message;
        $return['code'] = -1;
        $return['url'] = $url;
        return $return;
    }
    function arraySuccess ($message = '', $data = '', $url = '') {
        $return['msg']  = $message;
        $return['data'] = $data;
        $return['code'] = 1;
        $return['url'] = $url;
        return $return;
    }
    
    function fetch_dir($dir, $file_type = null) {
        $base_dir = realpath($dir);
        if (!file_exists($base_dir)) {
            return false;
        }
        $dir_handle = opendir($base_dir);
        $files_list = array();
        while (($file = readdir($dir_handle)) !== false) {
            if (substr($file, 0, 1) != '.' AND is_dir($base_dir . DS . $file)) {
                $files_list[] = $base_dir . DS . $file;
            }
        }
        closedir($dir_handle);
        return $files_list;
    }
    
    function fetch_file_lists($dir, $file_type = null) {
       if ($file_type) {
    	    if (substr($file_type, 0, 1) == '.') {
                $file_type = substr($file_type, 1);
            }
        }
    
        $base_dir = realpath($dir);
    
        if (!file_exists($base_dir)) {
            return false;
        }
    
        $dir_handle = opendir($base_dir);
    
        $files_list = array();
    
        while (($file = readdir($dir_handle)) !== false) {
            if (substr($file, 0, 1) != '.' AND !is_dir($base_dir . DS . $file)) {
                if (($file_type AND end(explode('.', $file)) == $file_type) OR !$file_type) {
                    $files_list[] = $base_dir . DS . $file;
                }
            }
            else if (substr($file, 0, 1) != '.' AND is_dir($base_dir . DS . $file)) {
                if ($sub_dir_lists = fetch_file_lists($base_dir . DS . $file, $file_type)) {
                    $files_list = array_merge($files_list, $sub_dir_lists);
                }
            }
        }
        closedir($dir_handle);
        return $files_list;
    }
    
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                if ($data instanceof \think\Model) {
                    $list[$key] = $data->toArray();
                }
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                if (!isset($list[$key][$child])) {
                    $list[$key][$child] = [];
                }
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f' , (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
    function formatTime($value) {
        if(time() - $value < 60) {
            return (time() - $value).'秒前';
        }
        if(time() - $value > 60 && time() - $value < 3600) {
            return (int)((time() - $value)/60).'分钟前';
        }
        if(time() - $value > 3600 && time() - $value < 86400) {
            return (int)((time() - $value)/3600).'小时前';
        }
        if(time() - $value > 86400 && time() - $value < 172800) {
            return '1天前';
        }
        return date('m-d', $value);
    }
    
    function getAvatarUrl($uid) {
        if (SITE_HOST) {
            if (file_exists(ROOT_PATH . 'public'. DS .'uploads'. DS .'avatar' . DS . $uid . '.jpg')) {
                return '/public/uploads/avatar/' . $uid .'.jpg';
            } else {
                return '/public/assets/common/images/avatar.jpg';
            }
        } else {
            if (file_exists(ROOT_PATH . 'uploads'. DS .'avatar' . DS . $uid . '.jpg')) {
                return '/uploads/avatar/' . $uid .'.jpg';
            } else {
                return '/assets/common/images/avatar.jpg';
            }
        }
            
    }
        
    function getMipInfo() {
        return db('Settings')->select();
    }
        
    function getFile($url, $save_dir = '', $filename = '', $type = 0) {  
        if (trim($url) == '') {  
            return false;  
        }
        if (trim($save_dir) == '') {  
            $save_dir = './';  
        }
        if (0 !== strrpos($save_dir, '/')) {  
            $save_dir.= '/';  
        }
        //创建保存目录  
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {  
            return false;  
        }  
        //获取远程文件所采用的方法  
        if ($type) {  
            $ch = curl_init();  
            $timeout = 5;  
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
            $content = curl_exec($ch);  
            curl_close($ch);  
        } else {  
            ob_start();  
            readfile($url);  
            $content = ob_get_contents();  
            ob_end_clean();  
        }  
        //echo $content;  
        $size = strlen($content);  
        //文件大小  
        $fp2 = @fopen($save_dir . $filename, 'a');  
        fwrite($fp2, $content);  
        fclose($fp2);  
        unset($content, $url);  
        return array(  
            'file_name' => $filename,  
            'save_path' => $save_dir . $filename,  
            'file_size' => $size  
        );
    }
    function addFileToZip($path,$zip) {
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false){
            if($filename != "." && $filename != ".."){
                if(is_dir($path."/".$filename)){
                    addFileToZip($path."/".$filename, $zip);
                }else{
                    $zip->addFile($path."/".$filename);
                }
            }
        }
        @closedir($path);
    }
    
    function pushData($api,$urls) {
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
    function getData($api,$postData = '') {
        if (!$api) {
            return false;
        }
        if (empty($postData)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HEADER, 0);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 20); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
    
            curl_close($ch);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
    
    function curlData($url) {
        $header = array (
        //  "Host:www.baidu.com",
            "Content-Type:application/x-www-form-urlencoded",//post请求
            "Connection: keep-alive",
            'Referer:http://www.baidu.com',
            'User-Agent: Mozilla/5.0 (compatible; Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)'
        );
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
    
    function linkClient($url,$postData = '',$header = '') {
        if (!$url) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        if ($header) {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
    
    function getSHA1($strToken, $intTimeStamp, $strNonce, $strEncryptMsg = '') {
        $arrParams = array(
            $strToken, 
            $intTimeStamp, 
            $strNonce,
        );
        if (!empty($strEncryptMsg)) {
            array_unshift($arrParams, $strEncryptMsg);
        }
        sort($arrParams, SORT_STRING);
        $strParam = implode($arrParams);
        return sha1($strParam);
    }
    
    function deleteHtml($str) { 
        $str = preg_replace("/(\s|\r|\n|\t|\&nbsp\;|　| |   |\xc2\xa0)/","",trim(strip_tags($str)));
        return $str; //返回字符串
    }
    
    function deleteStyle($content)
    {
        $itemInfo['content'] = $content;
        $itemInfo['content'] =  preg_replace("/style=.+?['|\"]/i",'', $itemInfo['content']);
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        foreach($imagesArray[0] as $key => $val) {
            @preg_match("/alt=[\'|\"](.*?)[\'|\"]/",$val,$tempAlt);
            if ($tempAlt) {
                $alt = $tempAlt[1];
            }
            @preg_match("/width=[\'|\"](.*?)[\'|\"]/",$val,$tempWidth);
            @preg_match("/height=[\'|\"](.*?)[\'|\"]/",$val,$tempHeight);
            $src = $imagesArray[1][$key];
            if ($tempWidth && $tempHeight) {
                if ($tempWidth[1] > 500) {
                    $layout = '';
                    $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                } else {
                    $layout = 'layout="fixed"';
                    $tempImg = '<mip-img ' .$layout. ' ' . $tempWidth[0] . ' ' . $tempHeight[0] .' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
                }
            } else {
                $layout = '';
                $tempImg = '<mip-img '.$layout.' alt="'.$alt.'" src="'.$src.'" popup></mip-img>';
            }
            $itemInfo['content'] =  str_replace($val,$tempImg,$itemInfo['content']);
        }
        @preg_match_all('/<a[^>]*>[^>]+a>/',$itemInfo['content'],$tempLink);
        foreach($tempLink[0] as $k => $v) {
            if(strpos($v,"href")) {
                @preg_match('/href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))/',$v,$hrefRes);
                $matches = @preg_match($patern,$hrefRes[1]);
                if (!$matches) {
                    $itemInfo['content'] = str_replace($v,'',$itemInfo['content']);
                }
            } else {
                $itemInfo['content'] = str_replace($v,'',$itemInfo['content']);
            }
        }
        @preg_match_all('/<iframe.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/iframe>/', $itemInfo['content'], $iframeArray);
        if ($iframeArray) {
            foreach($iframeArray[0] as $key => $val) {
                $layout = 'layout="responsive"';
                $tempiframe = '<mip-iframe   width="320" height="200" '.$layout.' src="'.$iframeArray[1][$key].'"></mip-iframe>';
                $itemInfo['content'] =  str_replace($val,$tempiframe,$itemInfo['content']);
            }
        }
        @preg_match_all('/<embed.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $embedArray);
        if ($embedArray) {
            foreach($embedArray[0] as $key => $val) {
                $layout = '';
                $tempembed = '<mip-embed type="ad-comm" '.$layout.' src="'.$embedArray[1][$key].'"></mip-embed>';
                $itemInfo['content'] =  str_replace($val,$tempembed,$itemInfo['content']);
            }
        }
        @preg_match_all('/<video.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>*<\/video>/', $itemInfo['content'], $videoArray);
        if ($videoArray) {
            foreach($videoArray[0] as $key => $val) {
                $layout = '';
                $tempvideo = '<mip-video '.$layout.' src="'.$videoArray[1][$key].'"></mip-video>';
                $itemInfo['content'] =  str_replace($val,$tempvideo,$itemInfo['content']);
            }
        }
        return $itemInfo['content'];
        
    }

    
    
    
    
    function mipfilter($content) {
    if (strpos($content, '{MIPCMSCSS}') !== false) {
       
        $cssContent = [];
        $tempCssContent = '';
        if ($cssContent) {
            foreach ($cssContent as $key => $value) {
                $tempCssContent .= $key . '{' . $value . '}';
            }
        }
        preg_match_all("/<[a-zA-Z0-9]{1,}\s+.*?>/", $content, $contentHtmlArray);
        if ($contentHtmlArray) {
            foreach ($contentHtmlArray[0] as $key => $value) {
            	   if (strpos($value, 'style=') !== false) {
            	        preg_match_all("/style=[\'|\"](.*?)[\'|\"]/i", $value, $contentcssArray);
                    if ($contentcssArray) {
                        $cssName = 'mipmb-css-' . $key;
                        if (strpos($value, 'class=') !== false) {
                            preg_match_all("/class=[\'|\"](.*?)[\'|\"]/i", $value, $subClassArray);
                            if ($subClassArray && $subClassArray[1]) {
                                $tempClassName = $subClassArray[1];
                                $className = 'class="' . $tempClassName[0] . ($tempClassName[0] ? ' ' :'') . $cssName . '"';
                                $contentHtmlArray[0][$key] = str_replace($subClassArray[0][0], $className, $contentHtmlArray[0][$key]);
                                $cssBlock = '.' . $cssName . '{' . $contentcssArray[1][0] . '}';
                                $tempCssContent .= $cssBlock;
                                $contentHtmlArray[0][$key] = str_replace($contentcssArray[0][0], '', $contentHtmlArray[0][$key]);
                            }
                        } else {
                            $className = 'class="' . $cssName . '"';
                            $cssBlock = '.' . $cssName . '{' . $contentcssArray[1][0] . '}';
                            $tempCssContent .= $cssBlock;
                            $contentHtmlArray[0][$key] = str_replace($contentcssArray[0][0], $className, $contentHtmlArray[0][$key]);
                        }
                        $content = str_replace($value, $contentHtmlArray[0][$key], $content);
                    }
            	        
                   
                }
            }
        }
        
        preg_match_all("/<style type=\"text\/css\">(.*?)<\/style>/is", $content, $contentStyleTextArray);
        if ($contentStyleTextArray) {
            foreach ($contentStyleTextArray[1] as $key => $value) {
                $tempCssContent .= $value;
                $content = str_replace($contentStyleTextArray[0][0], '', $content);
            }
        }
        preg_match_all("/<style>(.*?)<\/style>/is", $content, $contentStyleArray);
        if ($contentStyleArray) {
            foreach ($contentStyleArray[1] as $key => $value) {
                $tempCssContent .= $value;
                $content = str_replace($contentStyleArray[0][0], '', $content);
            }
        }
        
        $content = str_replace('{MIPCMSCSS}', $tempCssContent, $content);
        }
        return $content;
    }

    function getPage($page = 1,$totalNum = 1,$url = '',$endUrl = '.html',$long = 8)
    {
        $oldUrl = $url;
        $url = str_replace('.html','',$url);
        $startUrl = $url . '_';
        $endUrl = $endUrl;
        $long = $long ? $long : 8;
        
        if ($totalNum == 1) {
            $upPage = '<li class="page-item disabled"><span class="page-link">上一页</span></li>';
            $html .= '<li class="page-item disabled"><span class="page-link">1</span></li>';
            $downPage = '<li class="page-item disabled"><span class="page-link">下一页</span></li>';
        } else {
            if ($page == 2) {
                $upPage = '<li class="page-item"><a class="page-link" href="'. $oldUrl . '">上一页</a></li>';
            } else {
                if ($page == 1) {
                    $upPage = '<li class="page-item disabled"><span class="page-link">上一页</span></li>';
                } else {
                    $upPage = '<li class="page-item"><a class="page-link" href="'.$startUrl. ($page - 1) . $endUrl . '">上一页</a></li>';
                }
            }
            for ($i = 1; $i <= intval($totalNum); $i++) {
                    if ($long == 1) {
                        if ($page == $i) {
                            if ($i == 1) {
                                if ($page == $i) {
                                     $html .= '<li class="page-item active"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                } else {
                                   $html .= '<li class="page-item"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                }
                            } else {
                                $html .= '<li class="page-item active"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                            }
                        }
                    } else {
                        if ($totalNum > 10) {
                            if ($page <= ceil($long / 2) && $i <= $long) {
                                if ($i == 1) {
                                    if ($page == $i) {
                                        $html .= '<li class="page-item active"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                    } else {
                                        $html .= '<li class="page-item"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                    }
                                } else {
                                    if ($page == $i) {
                                        $html .= '<li class="page-item active"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                    } else {
                                        $html .= '<li class="page-item"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                    }
                                }
                            } else {
                                if ($page + ceil($long / 2) > $totalNum && $i > $totalNum - $long) {
                                    if ($page == $i) {
                                        $html .= '<li class="page-item active"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                    } else {
                                        $html .= '<li class="page-item"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                    }
                                } else {
                                    if ($page - ceil($long / 2) <= $i  && $i <= $page + ceil($long / 2)) {
                                        if ($i == 1) {
                                            if ($page == $i) {
                                                $html .= '<li class="page-item active"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                            } else {
                                                $html .= '<li class="page-item"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                            }
                                        } else {
                                            if ($page == $i) {
                                                $html .= '<li class="page-item active"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                            } else {
                                                $html .= '<li class="page-item"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($i == 1) {
                                if ($page == $i) {
                                     $html .= '<li class="page-item active"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                } else {
                                   $html .= '<li class="page-item"><a class="page-link" href="'. $oldUrl . '">'.$i.'</a></li>';
                                }
                            } else {
                                if ($page == $i) {
                                    $html .= '<li class="page-item active"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                } else {
                                    $html .= '<li class="page-item"><a class="page-link" href="'.$startUrl. $i . $endUrl . '">'.$i.'</a></li>';
                                }
                            }
                        }
                    }
            }
            if ($page == $totalNum) {
                $downPage = '<li class="page-item disabled"><span class="page-link">下一页</span></li>';
            } else {
                $downPage = '<li class="page-item"><a class="page-link" href="'.$startUrl. ($page + 1) . $endUrl . '">下一页</a></li>';
            }
        }
        $html = '<ul class="pagination"><li class="page-item disabled"><span class="page-link">共'.$totalNum.'页</span></li> ' . $upPage . $html . $downPage . '</ul>';
        return $html;
    }

    
    
function format_url($srcurl, $baseurl) {  
  $srcinfo = parse_url($srcurl);  
  if(isset($srcinfo['scheme'])) {  
    return $srcurl;  
  }  
  $baseinfo = parse_url($baseurl);  
  $url = $baseinfo['scheme'].'://'.$baseinfo['host'];  
  
  if(substr($srcinfo['path'], 0, 1) == '/') {  
    $path = $srcinfo['path'];  
  }else{  
    $path = $baseinfo['path'].'/'.$srcinfo['path'];  
  }  
  $rst = array();  
  $path_array = explode('/', $path);  
  if(!$path_array[0]) {  
    $rst[] = '';  
  }  
  foreach ($path_array AS $key => $dir) {  
    if ($dir == '..') {  
      if (end($rst) == '..') {  
        $rst[] = '..';  
      }elseif(!array_pop($rst)) {  
        $rst[] = '..';  
      }  
    }elseif($dir && $dir != '.') {  
      $rst[] = $dir;  
    }  
   }  
  if(!end($path_array)) {  
    $rst[] = '';  
  }  
  $url .= implode('/', $rst);  
  return str_replace('\\', '/', $url);  
}  


    function replace_str_diy($fustr,$str1,$str2) {
        if (empty($fustr) || empty($str1)) {
          return FALSE;
        }
        $wz1 = 0;
        $arr= explode('(*)',$str1);
        $arr1 = array();
        $k = 0;
        for ($i=0;$i<count($arr);$i++) {
            if ($arr[$i]!=='') {
                $arr1[$k] = $arr[$i];
                $k++;
            }
        }
        $cishu=0;
        while($wz1 < strlen($fustr)) {
            $jishu=0;
            for ($i=0;$i<count($arr1);$i++) {
                if(($wz=strpos($fustr,$arr1[$i],$wz1))!==false) {
                    if ($i==0) $ks = $wz;
                    if ($i==count($arr1)-1) $js = $wz + strlen($arr1[$i]);
                    $wz1 = $wz + strlen($arr1[$i]);
                    $jishu++;
                } else break;
            }
            if ($jishu==count($arr1)) {
                $cishu++;
                $leftstr = substr($fustr,0,$ks);
                $rightstr = substr($fustr,$js);
                if (!$rightstr) $rightstr = '';
                $fustr = $leftstr . $str2 . $rightstr;
                $wz1 = $ks + strlen($str2);
            } else {
                break;
            }  
        }
        return $fustr;
    }

    function gbkToUTF8($html) {
        $arr = array( "UTF-8", "ASCII", "GBK", "GB2312", "gb2312","BIG5", "JIS", "eucjp-win", "sjis-win", "EUC-JP" );
        $encode  = mb_detect_encoding( $html, $arr );    
        $html = mb_convert_encoding(trim($html), "UTF-8", $encode);
        $html = str_replace('charset=GB2312','charset=UTF-8',$html);
        $html = str_replace('charset="GB2312"','charset="UTF-8"',$html);
        $html = str_replace("charset='GB2312'","charset='UTF-8'",$html);
        $html = str_replace('charset=gb2312','charset=UTF-8',$html);
        $html = str_replace('charset="gb2312"','charset="UTF-8"',$html);
        $html = str_replace("charset='gb2312'","charset='UTF-8'",$html);
        $html = str_replace('charset=GBK','charset=UTF-8',$html);
        $html = str_replace('charset="GBK"','charset="UTF-8"',$html);
        $html = str_replace("charset='GBK'","charset='UTF-8'",$html);
        $html = str_replace('charset=gbk','charset=UTF-8',$html);
        $html = str_replace('charset="gbk"','charset="UTF-8"',$html);
        $html = str_replace("charset='gbk'","charset='UTF-8'",$html);
        return $html;
    }
    
    
function getImage($url,$save_dir,$filename = '',$type=0) {
    if (!$url) {
         return false;
    }
    if (!$save_dir) {
         return false;
    }
    $ext = strrchr($url,'.');
    if ($ext != '.gif' && $ext != '.jpg' &&  $ext != '.png' && $ext != '.jpeg') {
        $ext = '.jpg';
    }
    $filename = uuid() . $ext;
    if (0 !== strrpos($save_dir, DS )){
        $save_dir.= DS;
    }
    if (!file_exists($save_dir)&&!mkdir($save_dir,0777,true)) {
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }
    if ($type) {  
        $ch = curl_init();  
        $timeout = 30;  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($ch);  
        curl_close($ch);  
    } else {  
        ob_start();  
        readfile($url);  
        $content = ob_get_contents();  
        ob_end_clean();
    }  
   
    $fp2 = @fopen($save_dir . $filename, 'a');  
    fwrite($fp2, $content);  
    fclose($fp2);  
    unset($content, $url);  
     
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}
   
    $template = APP_PATH;
    if (is_dir($template)) {
        $templateFile = opendir($template);
        if ($templateFile) {
            while (false !== ($file = readdir($templateFile))) {
                if (substr($file, 0, 1) != '.' AND strpos($file, 'function.php') !== false || strpos($file, 'Function.php')) {
                     include $file;
                }
            }
            closedir($templateFile);
        }
    }
    
    foreach (fetch_dir(ROOT_PATH . 'addons' . DS) as $key => $dir) {
        if (is_file($dir . DS . 'function.php')) {
            require $dir . DS . 'function.php';
        }
    }
    foreach (fetch_dir(ROOT_PATH . 'app' . DS) as $key => $dir) {
        if (is_file($dir . DS . 'function.php')) {
            require $dir . DS . 'function.php';
        }
    }
     
    
    function getPinyin($name) {
        if (!$name) {
           return false;
        }
        $Pinyin = new ChinesePinyin();
        $result = $Pinyin->TransformWithoutTone($name);
        return $result;
    }

	function toHex($N) {
	    if ($N==NULL) return "00";
	    if ($N==0) return "00";
	    $N=max(0,$N); 
	    $N=min($N,255); 
	    $N=round($N);
	    $string = "0123456789ABCDEF";
	    $val = (($N-$N%16)/16);
	    $s1 = $string{$val};
	    $val = ($N%16);
	    $s2 = $string{$val};
	    return $s1.$s2;
	}
	//颜色值转换为16进制数字
	function rgb2hex($r,$g,$b){
	    return toHex($r).toHex($g).toHex($b);
	}
	//16进制数字转换为颜色值
	function hex2rgb($N){
	    $dou = str_split($N,2);
	    return array(
	        "R" => hexdec($dou[0]), 
	        "G" => hexdec($dou[1]), 
	        "B" => hexdec($dou[2])
	    );
	}
	    
	function getImagetype($filename) {
	 $file = fopen($filename, 'rb');
	 $bin = fread($file, 2);
	 fclose($file);
	 $strInfo = @unpack('C2chars', $bin);
	 $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
	 $fileType = '';
	 switch ($typeCode) {
	  case 255216:
	   $fileType = 'jpg';
	   break;
	  case 7173:
	   $fileType = 'gif';
	   break;
	  case 6677:
	   $fileType = 'bmp';
	   break;
	  case 13780:
	   $fileType = 'png';
	   break;
	  default:
	   $fileType = '只能上传图片类型格式';
	 }
	 return $fileType;
	}