<?php
class Frontend {

    function __construct() {

    	if($_SERVER['SERVER_NAME']=='lamdep.dev'){
    		mysql_connect('localhost', 'root', '') or die("Can't connect to server");
    	    mysql_select_db('lamdep') or die("Can't connect database");
    	}else{
    	mysql_connect('localhost', 'ikkkggke_aga', 'agavietnam@@!@') or die("Can't connect to server");
            mysql_select_db('ikkkggke_aga') or die("Can't connect database"); 
    	}
        mysql_query("SET NAMES 'utf8'") or die(mysql_error());
    }
    function processData($str) {
        $str = trim(strip_tags($str));
        if (get_magic_quotes_gpc() == false) {
            $str = mysql_real_escape_string($str);
        }
        return $str;
    }
    function getDetailSeo($id) {
            $arrReturn = array();      
            $sql = "SELECT * FROM seo WHERE id = $id";
            $rs = mysql_query($sql) or die(mysql_error());
            $row =mysql_fetch_assoc($rs);
            $arrReturn = $row;           
            return $arrReturn;
    }
    function getNameById($table,$column, $id){
        $sql = "SELECT $column FROM $table WHERE id = $id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row[$column];
    }
    function getListBannerByPosition($position_id){
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM banner WHERE position_id = $position_id");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function getDetailByAlias($table, $alias){
        $sql = "SELECT * FROM $table WHERE alias = '$alias'";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
	function getListText(){
        $arrResult = array();
        $sql = "SELECT * FROM text";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrResult[$row['id']] = $row['text'];
        }
        return $arrResult;
    }
	function getDetailByAliasProduct($alias){
        $sql = "SELECT * FROM product WHERE product_alias = '$alias'";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function getListAll_CateType(){
        $arr = array();
        $sql = "SELECT * FROM cate_type LIMIT 0,5 ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListORDERBY($table){
        
        $arr = array();
        $sql = "SELECT * FROM $table WHERE 1 = 1 ";
        $sql .= " ORDER BY display_order";
    
        $rs = mysql_query($sql);
        while ($row = mysql_fetch_assoc($rs)) {
            $arr[$row['id']] = $row;
        }
        return $arr;
    }
    function getList($table, $arrCustom = array()){
        
        $arr = array();
        $sql = "SELECT * FROM $table WHERE 1 = 1 ";

        if(!empty($arrCustom)){
            foreach ($arrCustom as $key => $value) {
                $sql.= " AND $key = '$value' ";
            }
        }
        //echo $sql;die;
        $rs = mysql_query($sql);
        while ($row = mysql_fetch_assoc($rs)) {
            $arr[] = $row;
        }
        return $arr;
    }
    function getListLimit($table,$offset = -1 , $limit = -1, $arrCustom = array()){
        try{
            $arrResult = array();
            $sql = "SELECT * FROM $table";

            if(!empty($arrCustom)){
                $sql.= " WHERE 1 = 1 ";                
                foreach ($arrCustom as $column => $value) {
                    if($value > 0 || ($value != '' && $value != '-1')){
                        $sql.= " AND $column = '$value' ";
                    }
                }
            }
            $sql .= " ORDER BY id DESC ";
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";
            
            $rs = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_assoc($rs)){
               $arrResult[$row['id']] = $row;
            }
            
            return $arrResult;
        }catch(Exception $ex){
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Post','function' => 'getListEstateType' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getListProduct_New(){
        $arr = array();
        $sql = "SELECT * FROM product ORDER BY id DESC LIMIT 0 ,4 ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListNewsLimit(){
        $arr = array();
        $sql = "SELECT * FROM articles ORDER BY article_id DESC LIMIT 0 ,5 ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListHotProduct(){
        $arr = array();
        $sql = "SELECT * FROM product WHERE is_hot = 1 ORDER BY id DESC LIMIT 0,8";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListNewProduct(){
        $arr = array();
        $sql = "SELECT * FROM product ORDER BY id DESC LIMIT 0,8";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListProductLike($table, $arrCustom = array(), $id){
        $arr = array();
        $sql = "SELECT * FROM $table WHERE 1 = 1";
        if(!empty($arrCustom)){
            foreach($arrCustom as $key => $value){
                $sql .= " AND $key = $value AND id <> $id";
            }
        }
        $rs = mysql_query($sql);
        while ($row = mysql_fetch_assoc($rs)) {
            $arr[] = $row;
        }
        return $arr;
    }
    function getDetail($table, $id){
        $sql = "SELECT * FROM $table WHERE id = $id";        
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function getDetailNews($id){
        $sql = "SELECT * FROM articles WHERE article_id = $id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function getDetailProduct($id) {
        $arrReturn = array();
        $str_image = "";    
        $sql = "SELECT * FROM product WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);        
        $arrReturn['data']= $row;

        $sql = "SELECT * FROM images WHERE object_id = $id AND object_type = 1";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn['images'][] = $row;
            $str_image.= $row['url'].";";            
        }
        
        $arrReturn['str_image'] = $str_image;                  
        return $arrReturn;
    }
    function getListProductCate($cate_type_id = -1, $cate_id = -1, $id = -1){
        $arr = array();
        if($cate_type_id > 0){
            $sql = "SELECT * FROM product WHERE cate_type_id = $cate_type_id";
        }
        if($cate_id > 0){
            $sql = "SELECT * FROM product WHERE cate_id = $cate_id";
        }
        if($cate_type_id > 0 && $cate_id > 0){
            $sql = "SELECT * FROM product WHERE cate_type_id = $cate_type_id AND cate_id = $cate_id";
        }
        if($id > 0){
            $sql = "SELECT * FROM product WHERE cate_type_id = $id";
        }
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row;
        }
        return $arr;
    }
    function getListProductNoiBat($offset=-1,$limit=-1){
        $arrReturn = array();
        $sql = "SELECT * FROM product WHERE is_hot = 1  ORDER BY id DESC ";
        if ($limit > 0 && $offset >= 0){
            $sql .= " LIMIT $offset,$limit";
        }        
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
               $arrReturn['data'][] = $row;
        }
        $arrReturn['total'] = mysql_num_rows($rs);    
        return $arrReturn;
    } 
    function getListProduct($cate_type_id = -1, $cate_id = -1, $is_new = -1, $is_hot = -1, $offset = -1, $limit = -1){
        $arrReturn = array();
        
        $sql = "SELECT id, image_url, price, cate_type_id, cate_id, product_name FROM product WHERE (is_hot = -1 OR $is_hot = -1) ";
        $sql.= " AND (cate_type_id= $cate_type_id OR $cate_type_id = -1) ";
        $sql.= " AND (cate_id= $cate_id OR $cate_id = -1) ";
        $sql.= " AND (is_new = $is_new OR $is_new = -1) ";
     
        $sql.=" ORDER BY created_at DESC ";        
        if ($limit > 0 && $offset >= 0)
            $sql .= " LIMIT $offset,$limit";                
        $rs = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($rs)){
           $arrReturn['data'][] = $row;
        }
        $arrReturn['total'] = mysql_num_rows($rs);        
        return $arrReturn;
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
        $str = str_replace("?", "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace('"', "", $str);
        $str = str_replace("%", "", $str);
        for($i = 0;$i<=strlen($str);$i++){
            $str = str_replace(" ", "-", $str);
            $str = str_replace("--", "-", $str);
        }

        return $str;
    }
    function throw_ex($e){
        throw new Exception($e);
    }
    
   
    function logError($arrLog){
        $time = date('d-m-Y H:i:s');
         ////put content to file
        $createdTime = date('Y/m/d');

        // path to log folder
        $logFolder = "../logs/errors/$createdTime";

        // If not existed => create it
        if (!is_dir($logFolder)) mkdir($logFolder, 0777, true);
        // path to log file
        $logFile = $logFolder . "/error_model.log";
        // Put content in it
        $fp   = fopen($logFile, 'a');
        fwrite($fp, json_encode($arrLog)."\r\n");
        fclose($fp);
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
    function phantrang($page, $page_show, $total_page, $link) {
        $dau = 1;
        $cuoi = 0;
        $dau = $page - floor($page_show / 2);
        if ($dau < 1)
            $dau = 1;
        $cuoi = $dau + $page_show;
        if ($cuoi > $total_page) {

            $cuoi = $total_page + 1;
            $dau = $cuoi - $page_show;
            if ($dau < 1)
                $dau = 1;
        }
        if(strpos($link,"?") >0 ){
          $pc = '&';  
        }else{
            $pc = '?';
        }
        echo '<div class="pagination pagination__posts"><ul class="pags">';
        if($page > 1){
            ($page==1) ? $class = " class='active'" : $class="class='first'" ;
            echo "<li ".$class."><a href=" . $link ."><<</a>" ;
        }
        for($i=$dau; $i<$cuoi; $i++)
        {
            ($page==$i) ? $class = " class='active'" : $class="class='inactive'" ;
            if($i>1){
            echo "<li ".$class."><a href=" . $link . $pc . "trang=$i>$i</a></li>";
            }else{
                echo "<li ".$class."><a href=" . $link . ">1</a></li>";
            }   
        }
        if($page < $total_page) {
            ($page==$total_page) ? $class = "class='active'" : $class="class='last'" ;
            echo "<li ".$class."><a href=" . $link . $pc . "trang=$total_page>>></a></li>";
        }
        echo "</ul></div>";
    }
    function smtpmailer($to, $from, $from_name, $subject, $body) {

		//ini_set('display_errors',1);
        global $error;
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->Username = GUSER;
        $mail->Password = GPWD;
        $mail->SetFrom($from, $from_name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->CharSet="utf-8";
        $mail->IsHTML(true);
        $mail->AddAddress($to);
		//var_dump($mail->ErrorInfo);
        if(!$mail->Send()) {
            $error = 'Gởi mail bị lỗi : '.$mail->ErrorInfo;
            return false;
        } else {
            $error = 'Thư của bạn đã được gởi đi !';
            return true;
        }
    } 
    
}

?>
