<?php
//ini_set('display_errors', '1');
if(!isset($_SESSION)){
    session_start();    
}
class Crawler {

    function __construct() {
        
       mysql_connect('localhost', 'the4f6df_palmy', 'UoHsv[akqMWz') or die("Can't connect to server");
                mysql_select_db('the4f6df_palmy') or die("Can't connect database");      
        mysql_query("SET NAMES 'utf8'") or die(mysql_error());
    }
    function processData($str) {
        $str = trim(strip_tags($str));
        if (get_magic_quotes_gpc() == false) {
            $str = mysql_real_escape_string($str);
        }
        return $str;
    }
    function getDetailArticle($arrUrl, $arrClass, $arrImgExpert, $folder_name, $arrPregReplace, $arrStrReplace, $domain = '', $classMore = '') {                
      
        $detailArr = array();
        $url = $arrUrl['url'];
        if($url){
           
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $result = curl_exec($ch);
            curl_close($ch);
             // Create a DOM object
            $html = new simple_html_dom();
            // Load HTML from a string
            $html->load($result);
            
            
            $detailArr = array();
            $detailArr['url'] = $url;
          
            if($html->find($arrClass['title'],0)){
                $detailArr['title'] = preg_replace('#<span (.*?)</span>#', ' ', $html->find($arrClass['title'],0)->innertext());    
            }else{
                return $detailArr;
            }
            
            $detailArr['title'] = strip_tags($detailArr['title']);  
            
            $content = $html->find($arrClass['content'],0);
           
            if ($content!=NULL)        
            foreach( $content->find('img') as $img) {
                $remove = strstr($img->src, '?');
                $img->src = str_replace($remove, "", $img->src);   
                $tenfile = basename($img->src); 
                
                $arrPartImage = explode('.', $tenfile);

                // Get image extension
                $imgExt = array_pop($arrPartImage);

                // Get image not extension
                $imgs = preg_replace('/(.*)(_\d+x\d+)/', '$1', implode('.', $arrPartImage));
                
                $imgs = $this->changeTitle($imgs);     
                $name = "{$imgs}.{$imgExt}";
                if(!is_dir("../uploads/".date('Y/m/d')."/".$folder_name."/")){                
                    mkdir("../uploads/".date('Y/m/d')."/".$folder_name."/", 0777, true);
                }
                $pathfile = "../uploads/".date('Y/m/d')."/".$folder_name."/".$tenfile;
                
                $img->src = $pathfile;

                if($urlHinh=='') $urlHinh=$img->src;

                $img->class = "aligncenter";
                
            }
            
            if($content){
                $contentHtml = $content->innertext();
            }
            if(!empty($arrPregReplace)){
                foreach ($arrPregReplace as $preg) {
                    $contentHtml = preg_replace($preg, ' ', $contentHtml);        
                }
            }
            if(!empty($arrStrReplace)){
                foreach ($arrStrReplace as $strre) {
                    $contentHtml = str_replace($strre, ' ', $contentHtml);        
                }
            }     
            
            $detailArr['content'] = $contentHtml;
            if($arrClass['description']==''){
                $tmp = strip_tags($contentHtml);
                $detailArr['description'] = $this->string_limit($tmp, 255);        
            }else{
                $detailArr['description'] = $html->find($arrClass['description'],0)->innertext();
            }
            if($arrUrl['thumbnailUrl']==''){
                $detailArr['thumbnailUrl'] = $urlHinh;    
            }else{
                $detailArr['thumbnailUrl'] = $arrUrl['thumbnailUrl'];
            }
            
           
            $html->clear();
            unset($html);
         }
        return $detailArr;

    }
    function grab_image($url,$saveto){      
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);       
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }
    function insertPost($arrLink, $arrUrl, $arrClass, $arrImgExpert, $folder_name, $arrPregReplace, $arrStrReplace, $domain = '', $classMore, $cate_id){
        echo $start = time();
        $i = 0;
        //var_dump($arrLink);
        
        foreach ($arrLink as $arrUrl){  
		$i++;
            $detailArr = $this->getDetailArticle($arrUrl, $arrClass, $arrImgExpert, $folder_name, $arrPregReplace, $arrStrReplace, $domain , $classMore);
            echo $start1 = time();
            var_dump("<pre>",$arrUrl);
            echo $i."--".$arrUrl['url']."<br>";
            
            if(!empty($detailArr)){
                $detailArr['status'] = 1;                
                $detailArr['cate_id'] = $cate_id;
                $detailArr['created_at'] = date('Y-m-d');       
                $i++;
                $idAr = $this->insert("post",$detailArr);
                echo $i."--".$idAr."--".$detailArr['title'];
                echo "<br/>";       
            }
            
            flush(); 
            //sleep(3);  
           $end1 = time();

            $t1 = $end1-$start1;
            var_dump(date('i:s', $t1));
        }
        
        echo "<br/>";
        $end = time();

        $t = $end-$start;
        var_dump("Total: ", date('i:s', $t));
    }
    function getAllLink($url, $arrElement, $domain = '', $cate_id, $folder_name) {   
     
        $linkarray=array();
        $html = file_get_html($url);
        /*
         $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);        
        curl_close($ch);  
         // Create a DOM object
        $html = new simple_html_dom();
        // Load HTML from a string
        $html->load($result);
        */
        //echo $html;die;
        //var_dump($arrElement);die;
        foreach ($arrElement as $element) {
           
            foreach ($html->find($element) as $div){
                
                // check giam can afamily
               // var_dump(count($linkarray));die;

					
					$link_url = $div->find('a', 0)->href;
					
					if($domain){
						$link_url = $domain.$link_url;
					}
					
					$arrTmpac = array('url' => $link_url, 'cate_id' => $cate_id);
				  
					if($this->checkUrl($arrTmpac)){
						$img = $div->find('img', 0);
						if($img){
							$thumbnail = $this->downloadImages($img, $folder_name);      
						}else{
							$thumbnail = "";
						}
						
						$linkarray[] = array(
							'thumbnailUrl' => $thumbnail,
							'url' => $link_url
						);
					}
				
            }
        }
        
        $html->clear(); //lenh xoa cache Dom, neu khong co ham nay thi bo nho ram se day`
        unset($html);
		//var_dump("<pre>", $linkarray);die;
        return $linkarray;
    }

    function checkUrl($detailArr){
        $url = $detailArr['url'];
        $cate_id = $detailArr['cate_id'];        
        $sql = "SELECT id FROM post WHERE url = '$url' AND cate_id = $cate_id";
        $rs = mysql_query($sql);
        return mysql_num_rows($rs) > 0 ? false : true;
    }
    function insert($table,$arrParams){
        $column = $values = "";

        foreach ($arrParams as $key => $value) {
            $value = addslashes($value);
            $column .= "$key".",";
            $values .= "'".$value."'".",";
        }
        $column = rtrim($column,",");
        $values = rtrim($values,",");
        $sql = "INSERT INTO ".$table."(".$column.") VALUES (".$values.")";
        mysql_query($sql) or die(mysql_error(). $sql);
        $id = mysql_insert_id();
        return $id;
    }

    function downloadImages($img, $folder_name){
        $pathfile = "";
        $tenfile = basename($img->src);
        if(!is_dir("../uploads/".date('Y/m/d')."/".$folder_name."/")){               
            mkdir("../uploads/".date('Y/m/d')."/".$folder_name."/", 0777, true);
        }
        $pathfile = "../uploads/".date('Y/m/d')."/".$folder_name."/".$tenfile;
        //if(!is_file($pathfile)){
            //$this->grab_image($img->src, $pathfile);
            //file_put_contents($pathfile, file_get_contents($img->src));         
        //}
        return $pathfile;
    }
    function changeTitle($str) {
        $str = $this->stripUnicode($str);
        $str = str_replace("?", "", $str);
        $str = str_replace("&", "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("  ", " ", $str);
        $str = trim($str);
        $str = mb_convert_case($str, MB_CASE_LOWER, 'utf-8'); // MB_CASE_UPPER/MB_CASE_TITLE/MB_CASE_LOWER
        $str = str_replace(" ", "-", $str);
        $str = str_replace("---", "-", $str);
        $str = str_replace("--", "-", $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('"', "", $str);
        $str = str_replace(":", "", $str);
        $str = str_replace("(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace(",", "", $str);
        $str = str_replace(".", "", $str);
        $str = str_replace("%", "", $str);
        return $str;
    }
    function stripUnicode($str) {
        if (!$str)
            return false;
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D' => 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            '' => '?',
            '-' => '/'
        );
        foreach ($unicode as $khongdau => $codau) {
            $arr = explode("|", $codau);
            $str = str_replace($arr, $khongdau, $str);
        }
        return $str;
    }
    function string_limit($str,$limit){
        $str = trim($str);
        if (strlen($str) <= $limit) { 
            $str = $str;
        } 
        else { 
            $str = wordwrap($str,$limit); 
            $str = substr($str, 0, strpos($str, "\n"));                 
        }       
        return $str;    
    }

}
