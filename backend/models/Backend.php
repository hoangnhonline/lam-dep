<?php
//ini_set('display_errors', '1');
if(!isset($_SESSION)){
    session_start();    
}
class Backend {

    function __construct() {
        if($_SERVER['SERVER_NAME']=='dongho.dev'){
            mysql_connect('localhost', 'root', '') or die("Can't connect to server");
               mysql_select_db('vinawatch_dongho') or die("Can't connect database");
        }else{
			mysql_connect('localhost', 'vinawatch_dongho', 'donghodong') or die("Can't connect to server");
			mysql_select_db('vinawatch_dongho') or die("Can't connect database");  
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
    function getList($table,$offset = -1 , $limit = -1, $arrCustom = array()){
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
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";
            
            $rs = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_assoc($rs)){
               $arrResult['data'][$row['id']] = $row;
            }
            $arrResult['total'] = mysql_num_rows($rs);
            return $arrResult;
        }catch(Exception $ex){
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Post','function' => 'getListEstateType' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getDetail($table, $id){
        $sql = "SELECT * FROM $table WHERE id = $id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function feedbackTotal($type){
        $sql = "SELECT id FROM feedback WHERE type = $type GROUP BY product_id";
        $rs = mysql_query($sql);
        $total = mysql_num_rows($rs);
        return $total;
    }
    function getUrlId($object_id, $type){
        $id = 0;
        $sql = "SELECT id FROM url WHERE object_id = $object_id AND type = $type";
        $rs = mysql_query($sql);
        if(mysql_num_rows($rs) == 1){
            $row = mysql_fetch_assoc($rs);
            $id = $row['id'];
        }
        return $id;
    }    
    function getCateTypeId($cate_id){
        $sql = "SELECT cate_type_id FROM cate WHERE id = $cate_id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row['cate_type_id'];
    }
    function deleteProductCate($product_id){
        $sql = "DELETE FROM product_cate WHERE product_id = $product_id";
        mysql_query($sql);
    }
    function insertCateProduct($cate_id, $cate_type_id, $product_id){
        $sql = "INSERT INTO product_cate VALUES (NULL, $cate_id, $cate_type_id, $product_id)";
        mysql_query($sql);
    }
    function insertProductCate($arrCate, $product_id){
        $this->deleteProductCate($product_id);
        if(!empty($arrCate)){
            foreach ($arrCate as $cate_id) {
                $cate_type_id = $this->getCateTypeId($cate_id);
                $this->insertCateProduct($cate_id, $cate_type_id, $product_id);
            }
        }
    }
    function insertAlias($alias, $object_id, $type){
        $sql = "INSERT INTO url VALUES(NULL, '$alias', $object_id, $type)";
        mysql_query($sql);
    }
    function updateAlias($id, $alias){
        $sql = "UPDATE url SET alias = '$alias' WHERE id = $id";
        mysql_query($sql);
    }
    function insert($table,$arrParams){
        $column = $values = "";

        foreach ($arrParams as $key => $value) {
            $column .= "$key".",";
            $values .= "'".$value."'".",";
        }
        $column = rtrim($column,",");
        $values = rtrim($values,",");
        $sql = "INSERT INTO ".$table."(".$column.") VALUES (".$values.")";		
        mysql_query($sql) or die(mysql_error().$sql);
        $id = mysql_insert_id();
        return $id;
    }
    function getProductIdByCode($code){
        $id = 0;
        $sql = "SELECT id FROM product WHERE product_code = '$code'";
        $rs = mysql_query($sql);
        $arr = mysql_fetch_assoc($rs);
        if(!empty($arr)){
            $id = $arr['id'];
        }
        return $id;
    }
    function countStateByCity($city_id){
        $sql = "SELECT count(id) as total FROM state WHERE city_id = $city_id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        $total = $row['total'];
        return $total;
    }
    function getListStateByCity($city_id){
        $arrReturn = array();
        $sql = "SELECT *  FROM state WHERE city_id = $city_id  ORDER BY display_order ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[$row['id']] = $row;
        }        
        return $arrReturn;
    }
    function update($table,$arrParams){
        $str = "";
        foreach ($arrParams as $key => $value) {
            $str.= $key."= '".$value."',";
        }
        $str = rtrim($str,",");        
        $sql = "UPDATE ".$table." SET ".$str." WHERE id = ".$arrParams['id'];
        mysql_query($sql) or die(mysql_error().$sql);
    }
    function getListBanner(){
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM banner");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[$row['type_id']][] = $row;
        }
        return $arrReturn;
    }
    function getDetailBanner($id){
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM banner WHERE id = $id");
        $arrReturn = mysql_fetch_assoc($sql);        
        return $arrReturn;
    }
    function getListFeedbackByProduct($product_id, $limit=-1, $offset=-1){
        $arrReturn = array();
        $sql = "SELECT * FROM feedback WHERE product_id = $product_id ORDER BY id DESC ";
        if ($limit > 0 && $offset >= 0){
            $sql .= " LIMIT $offset,$limit";
        }
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function getListFeedbackByType($type, $limit=-1, $offset=-1){
        $arrReturn = array();
        $sql = "SELECT product_id FROM feedback WHERE type = $type ORDER BY id DESC ";
        if ($limit > 0 && $offset >= 0){
            $sql .= " LIMIT $offset,$limit";
        }
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            
            $arrProduct[$row['product_id']] = $row['product_id'];
            
        }
        if(!empty($arrProduct)){
            foreach ($arrProduct as $product_id) {
                $sql = "SELECT product_name, id FROM product WHERE id = $product_id";
                $rs = mysql_query($sql);
                $row = mysql_fetch_assoc($rs);
                $arrReturn[] = $row;
            }
        }
        return $arrReturn;
    }
    function countFeedBackByType($product_id, $type){
        $total = 0;
        $sql = "SELECT count(id) as total FROM feedback WHERE product_id = $product_id AND type = $type ";        
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        $total = $row['total'];
        return $total;
    }

    function getDetailText($id) {

        $sql = "SELECT * FROM text WHERE id = $id";

        $rs = mysql_query($sql) or die(mysql_error());

        $row = mysql_fetch_assoc($rs);

        return $row;

    }
    function getDetailState($id) {

        $sql = "SELECT * FROM state WHERE id = $id";

        $rs = mysql_query($sql) or die(mysql_error());

        $row = mysql_fetch_assoc($rs);

        return $row;

    }
    function getListSeo() {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM seo";                       
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;                           
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Page','function' => 'getListPage' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
    function getDetailSeo($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM seo WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }
    function updateSeo($id,$meta_title,$meta_keyword,$meta_description,$seo_title, $seo_text) {
       try{            
            $time = time();    
            $sql = "UPDATE seo
                        SET  meta_description = '$meta_description',
                            meta_title = '$meta_title',meta_keyword = '$meta_keyword',
                            seo_text = '$seo_text', seo_title ='$seo_title'
                        WHERE id = $id ";                      
            mysql_query($sql)  or $this->throw_ex(mysql_error());             

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Page','function' => 'updatePage' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getTextByID($id) {

        $sql = "SELECT text FROM text WHERE id = $id";

        $rs = mysql_query($sql) or die(mysql_error());

        $row = mysql_fetch_assoc($rs);

        return $row['text'];

    }



    function getListText($offset = -1, $limit = -1) {

        $sql = "SELECT * FROM text ";

        if ($limit > 0 && $offset >= 0)

            $sql .= " LIMIT $offset,$limit";

        $rs = mysql_query($sql) or die(mysql_error());

        return $rs;

    }



    function updateText($id,$text) {

        $time = time();

        $sql = "UPDATE text

                    SET text = '$text'

                    WHERE id = $id ";

        mysql_query($sql) or die(mysql_error() . $sql);

    }

    function insertText($text){

        try{

            $time = time();

            $sql = "INSERT INTO text VALUES(NULL,'$text')";

            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());

        }catch(Exception $ex){

            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Text','function' => 'insertText' , 'error'=>$ex->getMessage(),'sql'=>$sql);

            $this->logError($arrLog);

        }

    }
    function getListBannerByPosition($position_id){
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM banner WHERE position_id = $position_id");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function deletePrivi($user_id){
        mysql_query("DELETE FROM user_privilege WHERE user_id = $user_id");
    }
    function insertPrivi($user_id,$privi_id){
        mysql_query("INSERT INTO user_privilege VALUES($user_id,$privi_id)");
    }
    function getListPriviUser($user_id){
        $arrReturn = array();
        $sql = mysql_query("SELECT privilege_id FROM user_privilege WHERE user_id = $user_id");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[] = $row['privilege_id'];
        }
        return $arrReturn;
    }
    function checkprivilege($privilege_id){        
        $user_id = $_SESSION['user_id'];        
        if($user_id == 1){
            return true;
        }else{
            $priviArr = $_SESSION['privilege'];
            if(in_array($privilege_id, $priviArr)){
                return true;
            }else{
                return false;
            }            
        }
    }
    function getDetailUser($id) {

        $sql = "SELECT * FROM users WHERE user_id = $id";

        $rs = mysql_query($sql) or die(mysql_error());

        $row = mysql_fetch_assoc($rs);

        return $row;

    }
    function getDetailCustomer($id) {

        $sql = "SELECT * FROM customer WHERE id = $id";

        $rs = mysql_query($sql) or die(mysql_error());

        $row = mysql_fetch_assoc($rs);

        return $row;

    }

    function getListUser($status = -1 ,$full_name = '',$username = '',$offset = -1, $limit = -1) {

        $sql = "SELECT * FROM users WHERE (status = $status OR $status = -1) ";
        $sql.= "  AND (username = '$username' OR '$username' = '' ) ";
        if($full_name!=''){
            $sql.=' AND full_name LIKE "%'.$full_name.'%" ';
        }  
        if ($limit > 0 && $offset >= 0)

            $sql .= " LIMIT $offset,$limit";

        $rs = mysql_query($sql) or die(mysql_error().$sql);

        return $rs;
    }    
    function getListPrivilege($parent_id){        
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM privilege WHERE parent_id = $parent_id");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[$row['id']]= $row;
        }
        return $arrReturn;

    }

    function updateUser($id,$email,$full_name,$address,$city_id,$phone,$status) {        
        $user_id = $_SESSION['user_id'];
        $time = time();

        $sql = "UPDATE users
                    SET email = '$email',
                    full_name = '$full_name',
                    address = '$address',city_id = $city_id,phone = '$phone',
                    updated_by = $user_id,status = $status,
                    updated_at =  $time         
                    WHERE user_id = $id ";

        mysql_query($sql) or die(mysql_error() . $sql);

    }
    function updateCustomer($id,$full_name,$phone,$handphone,$address,$email,$username,$status){
        $user_id = $_SESSION['user_id'];
        $time = time();

        $sql = "UPDATE customer
                    SET email = '$email',username = '$username',
                    full_name = '$full_name',
                    address = '$address',handphone = '$handphone',phone = '$phone',
                    updated_by = $user_id,status = $status,
                    updated_at =  $time         
                    WHERE id = $id ";

        mysql_query($sql) or die(mysql_error() . $sql);
    }
    function getListCity(){
        $arrReturn = array();
        $sql = "SELECT * FROM city ORDER BY display_order ASC";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function insertUser($username,$full_name,$email,$phone,$address,$city_id,$status){

        try{
            $user_id = $_SESSION['user_id'];
            $time = time();
            $password = md5('12345@6');

            $sql = "INSERT INTO users VALUES(NULL,'$username','$password','$full_name','$email','$phone',
                '$address',$city_id,$status,$time,$time,$time,$user_id,$user_id)";

            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());       

        }catch(Exception $ex){            

            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'User','function' => 'insertUser' , 'error'=>$ex->getMessage(),'sql'=>$sql);

            $this->logError($arrLog);

        }

    }   

    function user_checkpass($idUser, $pass) {
        $chitiet = $this->user_chitiet($idUser);
        $row = mysql_fetch_assoc($chitiet);
        if (md5($pass) == $row['password'])
            return true;
        else
            return false;
    }

    function changePass($user_id,$password) {
        $sql = "UPDATE users SET password = '$password' WHERE user_id = $user_id";
        mysql_query($sql);
    } 
    function getListBannerByType($type_id){
        $arrReturn = array();
        $sql = mysql_query("SELECT * FROM banner WHERE type_id = $type_id");
        while($row = mysql_fetch_assoc($sql)){
            $arrReturn[] = $row;
        }
        return $arrReturn;   
    }    
    function insertOrderDetail($order_id,$product_id,$product_name,$amount,$price,$total){
        $time = time();
        $sql = "INSERT INTO order_detail VALUES(NULL,$order_id,$product_id,'$product_name',$amount,$price,$total,$time,1)";
        mysql_query($sql) or die(mysql_error());
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
            $str = str_replace("---", "-", $str);
            $str = str_replace("----", "-", $str);
        }

        return $str;
    }
    function throw_ex($e){
        throw new Exception($e);
    }
    /* category */
    function getDetailCate($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM cate WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }    
    function getListCate($cate_type_id=-1) {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM cate WHERE (cate_type_id = $cate_type_id OR $cate_type_id = -1) AND parent_id = 0  ";           
            $sql.="  ORDER BY display_order ASC ";
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;            
                $rs1 = mysql_query("SELECT * FROM cate WHERE parent_id = ".$row['id']);
                while($row1 = mysql_fetch_assoc($rs1)){
                    $arrReturn[$row['id']]['child'][$row1['id']] = $row1;                
                    $rs2 = mysql_query("SELECT * FROM cate WHERE parent_id = ".$row1['id']);
                    while($row2 = mysql_fetch_assoc($rs2)){                    
                        $arrReturn[$row['id']]['child'][$row1['id']]['child'][$row2['id']] = $row2;
                    }
                }
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Cate','function' => 'getListCate' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
    function getListCateByParent($parent=-1) {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM cate WHERE parent_id = $parent  ";           
            $sql.=" ORDER BY display_order ASC ";
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;                            
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Cate','function' => 'getListCateByParent' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
    function getListCateNoTree() {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM cate ";                      
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;                            
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Cate','function' => 'getListCateNoTree' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
   

    function insertCate($cate_name,$cate_alias,$parent_id,$cate_type_id,$image_url,$icon_url,$content,$is_hot,$display_order,$meta_title,$meta_description,$meta_keyword,$hidden,$seo_text, $seo_title) {
        try{         
            $user_id = $_SESSION['user_id'];
            $content = addslashes($content);   
            $time = time();            
            $sql = "INSERT INTO cate VALUES
                            (NULL,'$cate_name','$cate_alias','$parent_id',
                            $cate_type_id,'$image_url','$icon_url','$content',$is_hot,$display_order,
                            '$meta_title','$meta_description','$meta_keyword', '$seo_title','$seo_text',$time,$time,$user_id,$user_id,$hidden)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Cate','function' => 'insertCate' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   

    function updateCate($id,$cate_name,$cate_alias,$parent_id,$cate_type_id,$image_url,$icon_url,$content,$is_hot,$display_order,$meta_title,$meta_description,$meta_keyword,$hidden,$seo_text, $seo_title) {
       try{            
            $user_id = $_SESSION['user_id'];
            $time = time();    
            $content = addslashes($content);  
            $sql = "UPDATE cate
                        SET cate_name = '$cate_name', cate_alias = '$cate_alias',
                            parent_id = $parent_id,image_url = '$image_url',
                            cate_type_id = $cate_type_id,content = '$content',
                            icon_url = '$icon_url',hidden = $hidden,
                            updated_by = $user_id,seo_text = '$seo_text',
                            is_hot = $is_hot,display_order = $display_order,                    
                            cate_type_id = $cate_type_id, meta_description = '$meta_description',
                            meta_title = '$meta_title',meta_keyword = '$meta_keyword',                    
                            updated_at = $time, seo_title = '$seo_title'
                        WHERE id = $id ";
            mysql_query($sql)  or $this->throw_ex(mysql_error());             

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Cate','function' => 'updateCate' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  

    /* manufacture */
    function insertManu($manu_name,$manu_alias,$description,$image_url,$is_hot,$display_order,$meta_title,$meta_description,$meta_keyword,$hidden,$catetype_id) {
        try{    
            $user_id = $_SESSION['user_id'];
            $time = time();            
            $sql = "INSERT INTO manu VALUES
                            (NULL,'$manu_name','$manu_alias','$description','$image_url',
                            $is_hot,$display_order,
                            '$meta_title','$meta_description','$meta_keyword',$time,$time,$user_id,$user_id,$hidden)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());            
            $manu_id = mysql_insert_id();            
            if(!empty($catetype_id)){
                foreach ($catetype_id as $cate_id) {
                    mysql_query("INSERT INTO manu_catetype VALUES($manu_id,$cate_id)") or die(mysql_error());
                }
            }
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Manu','function' => 'insertManu' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   
    function getListCateTypeManu($manu_id){
        $arrReturn = array();
        $sql = "SELECT * FROM manu_catetype WHERE manu_id = $manu_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row['catetype_id'];
        }
        return $arrReturn;
    }
    function getListDetailCateTypeManu($manu_id){
        $arrReturn = array();
        $sql = "SELECT * FROM manu_catetype WHERE manu_id = $manu_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $cate_type_id = $row['catetype_id'];            
            $arrReturn[$cate_type_id][] = $this->getDetailCateType($cate_type_id);
        }
        return $arrReturn;
    }
    function updateManu($id,$manu_name,$manu_alias,$description,$image_url,$is_hot,$display_order,$meta_title,$meta_description,$meta_keyword,$hidden,$catetype_id) {
       try{              
            $user_id = $_SESSION['user_id'];   
            $time = time();    
            $sql = "UPDATE manu
                        SET manu_name = '$manu_name', manu_alias = '$manu_alias',
                            image_url = '$image_url',description = '$description',
                            is_hot = $is_hot,display_order = $display_order,                    
                            meta_description = '$meta_description',hidden = $hidden,
                            meta_title = '$meta_title',meta_keyword = '$meta_keyword',                    
                            updated_at = $time ,updated_by = $user_id
                        WHERE id = $id ";                      
            mysql_query($sql)  or $this->throw_ex(mysql_error());  
            mysql_query("DELETE FROM manu_catetype WHERE manu_id = $id");
            
            if(!empty($catetype_id)){
                foreach ($catetype_id as $cate_id) {
                    mysql_query("INSERT INTO manu_catetype VALUES($id,$cate_id)") or die(mysql_error());
                }
            }                      
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Manu','function' => 'updateManu' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  
    /* banner */
    function insertBanner($name_event,$name_en,$start_time,$end_time,$position_id,$description,$content,$image_url,$link_url,$type_id,$size_default,$status){
        try{    
            $user_id = $_SESSION['user_id'];
            $time = time();            
            $sql = "INSERT INTO banner VALUES
                            (NULL,'$name_event','$name_en',$start_time,$end_time,$position_id,'$description','$content','$image_url','$link_url',
                            $type_id,'$size_default',$status,$time,$time,$user_id,$user_id)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());            
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Banner','function' => 'insertBanner' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   

    function updateBanner($id,$name_event,$name_en,$start_time,$end_time,$position_id,$description,$content,$image_url,$link_url,$type_id,$size_default,$status){
       try{           

            $user_id = $_SESSION['user_id'];   
            $time = time();    
            $sql = "UPDATE banner
                        SET name_event = '$name_event', start_time = $start_time,end_time = $end_time,position_id = $position_id,
                            image_url = '$image_url',description = '$description',name_en = '$name_en',
                            content = '$content',type_id = $type_id, link_url = '$link_url',
                            status = '$status',updated_at = $time ,updated_by = $user_id
                        WHERE id = $id ";    

            mysql_query($sql)  or $this->throw_ex(mysql_error());                        
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Banner','function' => 'updateBanner' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  
    function getDetailManu($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM manu WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }
   
    function getListManu($is_hot=-1,$hidden=-1,$manu_name='',$offset=-1,$limit=-1) {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM manu WHERE (is_hot = $is_hot OR $is_hot = -1) AND (hidden = $hidden OR $hidden = -1) ";  
            if($manu_name!=''){
                $sql.=' AND manu_name LIKE "%'.$manu_name.'%" ';
            }  
            $sql.= " ORDER BY display_order ASC "; 
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";                               
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn['data'][] =  $row;                           
            } 
            $arrReturn['total'] = mysql_num_rows($rs);
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Manu','function' => 'getListManu' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
    /* age_range */
     function getListAge() {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM age_range ";                       
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;                           
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Age','function' => 'getListAge' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }
    function getDetailPage($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM pages WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }
    
    function getListPage($is_hot=-1) {
        try{
            $arrReturn = array();
            $sql = "SELECT * FROM pages WHERE (is_hot = $is_hot OR $is_hot = -1) ";                       
            $rs = mysql_query($sql) or die(mysql_error());            
            while($row =mysql_fetch_assoc($rs)){
                $arrReturn[$row['id']] =  $row;                           
            } 
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Page','function' => 'getListPage' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }

    /* product */   
    function updateTypeProduct($product_id,$col,$value){
        $sql = "UPDATE product SET $col = $value WHERE id = $product_id";
        mysql_query($sql);
    } 
    function updateHoteCate($cate_id,$value){
        $sql = "UPDATE cate SET is_hot = $value WHERE id = $cate_id";
        mysql_query($sql);
    } 
     function insertProduct($product_code,$product_name,$name_en,$product_alias,$cate_type_id,$cate_id,
        $parent_cate,$manu_id,$age_range,$image_url,$video_url,$description,$content,$guide_use,$guarantee,$is_new,
        $is_hot,$is_saleoff,$trangthai,$percent_deal,$display_order,$size,$color,
        $price,$price_saleoff,$meta_title,$meta_description,$meta_keyword,$str_image,$hidden) {
        try{            
		$user_id = $_SESSION['user_id'];
            $time = time();            
           $sql = "INSERT INTO product VALUES
                            (NULL,'$product_code','$product_name','$name_en','$product_alias',
                                $cate_type_id,$cate_id,$parent_cate,$manu_id,$age_range,
                                '$image_url','$video_url','$description','$content','$guide_use','$guarantee',
                                    $is_new,$is_hot,$is_saleoff,$trangthai,
                                '$percent_deal',
                                '$display_order','$size','$color','$price','$price_saleoff',
                                '$meta_title','$meta_description','$meta_keyword',$time,$time,$user_id,$user_id,$hidden)";        
            $rs = mysql_query($sql) or die(mysql_error().$sql);//or $this->throw_ex(mysql_error());
            $arrTmp = array();
            if($str_image){
                $arrTmp = explode(';', $str_image);
            }
            $product_id = mysql_insert_id();
            if(!empty($arrTmp)){
                foreach ($arrTmp as $url) {
                    if($url){                       
                        $url_1 =  str_replace('.', '_2.', $url);                        
                        mysql_query("INSERT INTO images VALUES(null,'$url','$url_1',$product_id,1,1)") or die(mysql_error());
                    }
                }
            }
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Product','function' => 'insertProduct' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   

    function updateProduct($id,$product_code,$product_name,$name_en,$product_alias,$cate_type_id,$cate_id,$parent_cate,$manu_id,$age_range,
        $image_url,$description,$content,$guide_use,$guarantee,$is_new,$is_hot,$is_saleoff,$trangthai,$percent_deal,$display_order,$size,$color,
        $price,$price_saleoff,$meta_title,$meta_description,$meta_keyword,$str_image,$video_url,$hidden) {
       try{            
            $user_id = $_SESSION['user_id'];
            $time = time();    
            $sql = "UPDATE product
                        SET product_code = '$product_code', product_name = '$product_name', product_alias = '$product_alias',
                            cate_type_id = $cate_type_id,age_range = $age_range,name_en = '$name_en',
                            cate_id = $cate_id, parent_cate = $parent_cate,manu_id = $manu_id,
                            image_url = '$image_url',description = '$description',guide_use = '$guide_use',is_new = $is_new,
                            is_hot = $is_hot,content = '$content',guarantee = '$guarantee',is_saleoff = $is_saleoff,
                            video_url = '$video_url',color = '$color',trangthai = $trangthai,
                            percent_deal = '$percent_deal', display_order = $display_order, size = '$size', 
                            price = '$price' , price_saleoff = '$price_saleoff',
                            meta_description = '$meta_description',
                            meta_title = '$meta_title',meta_keyword = '$meta_keyword',                    
                            updated_at = $time ,updated_by = $user_id,hidden = $hidden                 
                        WHERE id = $id ";                              
            mysql_query($sql)  or die(mysql_error().$sql);//$this->throw_ex(mysql_error());             
            $arrTmp = array();
            if($str_image){
                $arrTmp = explode(';', $str_image);
            }            
            if(!empty($arrTmp)){
                foreach ($arrTmp as $url) {
                    if($url){                       
                        $url_1 =  str_replace('.', '_2.', $url);                        
                        mysql_query("INSERT INTO images VALUES(null,'$url','$url_1',$id,1,1)") or die(mysql_error());
                    }
                }
            }
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Product','function' => 'updateProduct' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  
    function getDetailProduct($id) {
        $arrReturn = array();      
        $sql = "SELECT product.*, product_cate.cate_id as parent_cate, product_cate.cate_type_id as cate_type_id FROM product, product_cate WHERE product.id = $id AND product_cate.product_id = product.id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);        
        $arrReturn['data']= $row;

        $sql = "SELECT * FROM images WHERE object_id = $id AND object_type = 1";
        $rs = mysql_query($sql);
		$str_image = "";
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn['images'][] = $row;
            $str_image.= $row['url'].";";            
        }
        
        $arrReturn['str_image'] = $str_image;                  
        return $arrReturn;
    }
    function checkProductCode($product_code){       
        $sql = "SELECT id FROM product WHERE product_code = '$product_code'" ;
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_num_rows($rs);
        echo $row;
    }
    function checkSlug($alias, $object_id){       
        $sql = "SELECT id FROM url WHERE alias = '$alias'" ;
        if($object_id > 0){
            $sql.=" AND object_id <> $object_id";
        }
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_num_rows($rs);
        echo $row;
    }
    function getListProductByCateTypeId($cate_type_id = -1, $cate_id = -1){
        $str_product_id = '';
        $sql = "SELECT product_id FROM product_cate WHERE (cate_type_id = $cate_type_id OR $cate_type_id = -1) ";
        $sql .= " AND ( cate_id = $cate_id OR $cate_id = -1 ) GROUP BY product_id ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $str_product_id .= $row['product_id'].",";
        }
        $str_product_id = rtrim($str_product_id, ",");        
        return $str_product_id;
    }
    function getListCateProduct($id){
        $arrReturn['cate_type_id'] = array();
        $arrReturn['cate_id'] = array();
        $sql = "SELECT cate_type_id FROM product_cate WHERE product_id = $id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn['cate_type_id'][] = $row['cate_type_id'];
        }
        $sql = "SELECT cate_id FROM product_cate WHERE product_id = $id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn['cate_id'][] = $row['cate_id'];
        }
        return $arrReturn;
    }
    
    function getAllProduct(){
        $arrReturn = array();
        $sql = "SELECT id, product_name FROM product WHERE trangthai=1 and hidden=0 ORDER BY id DESC";
        $rs = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($rs)){
           $arrReturn['data'][] = $row;
        }
        //var_dump($arrReturn);die;
        return $arrReturn;    
    }
    
    function getListProduct($product_code,$product_name,$cate_type_id,$cate_id,$is_new,$is_saleoff,$is_hot,$trangthai,$offset,$limit) {
        try{            
            $arrReturn = array();
            $str_product_id = $this->getListProductByCateTypeId($cate_type_id, $cate_id);
            if($cate_type_id == -1 && $cate_id == -1){
                $sql = "SELECT * FROM product WHERE (is_hot = $is_hot OR $is_hot = -1) AND (is_new= $is_new OR $is_new = -1) AND (trangthai= $trangthai OR $trangthai = -1) ";                     
                $sql.=" AND (product_name LIKE '%".$product_name."%' OR '$product_name' = '') AND (product_code = '$product_code' OR '$product_code' = '') ";
                $sql.=" AND (is_saleoff = $is_saleoff OR $is_saleoff = -1) ";
            }else{
                $sql = "SELECT product.* , product_cate.cate_type_id, product_cate.cate_id as parent_cate FROM product, product_cate WHERE (is_hot = $is_hot OR $is_hot = -1) AND (is_new= $is_new OR $is_new = -1) AND (trangthai= $trangthai OR $trangthai = -1) ";                     
                $sql.=" AND (product_name LIKE '%".$product_name."%' OR '$product_name' = '') AND (product_code = '$product_code' OR '$product_code' = '') ";
                $sql.=" AND (is_saleoff = $is_saleoff OR $is_saleoff = -1) ";
                $sql.= " AND product.id = product_cate.product_id ";
                if($cate_type_id > 0){
                    $sql.= " AND product_cate.cate_type_id = $cate_type_id ";
                }
                if($cate_id > 0){
                    $sql.= " AND product_cate.cate_id = $cate_id ";
                }
                $sql.= " GROUP BY product_cate.product_id ";
            }            
         
            $sql.=" ORDER BY id DESC ";
            
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";                          
            $rs = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_assoc($rs)){
               $arrReturn['data'][] = $row;
            }
            $arrReturn['total'] = mysql_num_rows($rs);        
            

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Product','function' => 'getListProduct' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        return $arrReturn;
    }

    /* cate_type */
    function getListCateType(){
        $arrReturn = array();
        $sql = "SELECT * FROM cate_type ORDER BY display_order ASC ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[$row['id']] = $row;
        }
        return $arrReturn;
    }
    /* cate_type */
    function getListCateParent($cate_type_id){
        $arrReturn = array();
        $sql = "SELECT * FROM cate WHERE cate_type_id = $cate_type_id AND parent_id = 0 ORDER BY display_order ASC ";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[$row['id']] = $row;
        }
        return $arrReturn;
    }
    function getDetailCateType($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM cate_type WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }   

    function insertCateType($cate_type_name,$cate_type_alias,$description,$image_url,$icon_url,$is_menu,$display_order,$hidden,$meta_title,$meta_description,$meta_keyword) {
        try{            
            $time = time();        
            $user_id = $_SESSION['user_id'];    
            $sql = "INSERT INTO cate_type VALUES
                            (NULL,'$cate_type_name','$cate_type_alias','$description','$image_url','$icon_url',
                            $is_menu,$display_order,$time,$time,$hidden,$user_id,$user_id,'$meta_title','$meta_description','$meta_keyword')";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'CateType','function' => 'insertCateType' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   

    function updateCateType($id,$cate_type_name,$cate_type_alias,$description,$image_url,$icon_url,$is_menu,$display_order,$hidden,$meta_title,$meta_description,$meta_keyword) {
       try{            
            $time = time();    
            $user_id = $_SESSION['user_id'];    
            $sql = "UPDATE cate_type
                        SET cate_type_name = '$cate_type_name', cate_type_alias = '$cate_type_alias',
                        description = '$description',
                            hidden = $hidden, meta_title = '$meta_title' , meta_description = '$meta_description',
                            meta_keyword = '$meta_keyword',
                            is_menu = $is_menu,image_url = '$image_url',icon_url ='$icon_url',
                            display_order = $display_order,updated_at = $time ,
                            updated_by = $user_id                 
                        WHERE id = $id ";
            mysql_query($sql)  or $this->throw_ex(mysql_error());             

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'CateType','function' => 'updateCateType' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  
    function getCateByCateType($cate_type_id=-1,$type='form') {
        $arrReturn = array();        
        $sql = "SELECT * FROM cate WHERE (cate_type_id = $cate_type_id OR $cate_type_id = -1) AND parent_id = 0";
        $rs = mysql_query($sql) or die(mysql_error());
        while($row =mysql_fetch_assoc($rs)){
            $arrReturn[$row['id']] =  $row;           
        }      
        echo ($type == "list") ? "<option value='-1'>--Tất cả--</option>" :"";
        if(!empty($arrReturn)){
            foreach ($arrReturn as $cate) {
                echo "<option data-parent='0' value='".$cate['id']."' style='font-weight:bold'>".$cate['cate_name']."</option>";                          
            }
        }                
    }
    function getNumberChildCate($parent_id){
        $sql ="SELECT count(id) as num_cate FROM cate WHERE parent_id = $parent_id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['num_cate'];
    }
    function getNumberArticlesByCate($cate_id){
        $sql ="SELECT count(article_id) as num_article FROM articles WHERE cate_id = $cate_id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['num_article'];
    }
    function getNumberParentCate($cate_type_id){
        $sql ="SELECT count(id) as num_cate FROM cate WHERE cate_type_id = $cate_type_id AND parent_id = 0 ";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['num_cate'];
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
        echo "<div id='thanhphantrang'>";
        if ($page > 1) {
            ($page == 1) ? $class = " class='selected'" : $class = "";
            echo "<a" . $class . " href=" . $link . "&page=1>Đầu</a>";
        }
        for ($i = $dau; $i < $cuoi; $i++) {
            ($page == $i) ? $class = " class='selected'" : $class = "";
            echo "<a" . $class . " href=" . $link . "&page=$i>$i</a>";
        }
        if ($page < $total_page) {
            ($page == $total_page) ? $class = " class='selected'" : $class = "";
            echo "<a" . $class . " href=" . $link . "&page=$total_page>Cuối</a>";
        }
        echo "</div>";
    }



    public function Login(){

		$email = trim(strip_tags($_POST['email']));
        $password = trim(strip_tags($_POST['password']));
        if (get_magic_quotes_gpc() == false) {
            $email = trim(mysql_real_escape_string($email));
            $password = trim(mysql_real_escape_string($password));
        }
        $password = md5($password);
        $sql = "SELECT * FROM users WHERE username ='$email' AND password ='$password'";
		
        $user = mysql_query($sql) or die(mysql_error());

        $row = mysql_num_rows($user);
        if ($row == 1) {//success
            $chitiet = mysql_fetch_assoc($user);
            $_SESSION['user_id'] = $chitiet['user_id'];
            $_SESSION['email'] = $chitiet['email'];
            $_SESSION['phone'] = $chitiet['phone'];
            $_SESSION['address'] = $chitiet['address'];
            $_SESSION['full_name'] = $chitiet['full_name'];
            $_SESSION['privilege'] = $this->getListPriviUser($chitiet['user_id']);
            header("location:index.php");
        }
        else
            header("location:login.php"); //fail
	}


	function phantrang2($page,$page_show,$total_page,$link){
		$dau=1;
		$cuoi=0;
		$dau=$page - floor($page_show/2);
		if($dau<1) $dau=1;
		$cuoi=$dau+$page_show;
		if($cuoi>$total_page)
		{

			$cuoi=$total_page+1;
			$dau=$cuoi-$page_show;
			if($dau<1) $dau=1;
		}
		echo '<div class="pagination pagination__posts"><ul>';
		if($page > 1){
			($page==1) ? $class = " class='active'" : $class="first" ;
			echo "<li ".$class."><a href=".$link."-1.html>First</a></li>"	;
		}
		for($i=$dau; $i<$cuoi; $i++)
		{
			($page==$i) ? $class = " class='active'" : $class="inactive" ;
			echo "<li ".$class."><a href=".$link."-$i.html>$i</a></li>";
		}
		if($page < $total_page) {
			($page==$total_page) ? $class = "class='active'" : $class="last" ;
			echo "<li ".$class."><a href=".$link."-$total_page.html>Last</a></li>";
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
    function checkemailexist($email){
        $sql = "SELECT id FROM newsletter WHERE email = '$email' AND status = 1 ";
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_num_rows($rs);
        if($row==0){
            return "1";
        }else{
            return "0";
        }
    }

     function getDetailCode($id) {
        $sql = "SELECT * FROM promotion_code WHERE code_id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        return $row;
    }

    function getListCode($status = -1, $offset = -1, $limit = -1) {
        try{
            $arrResult = array();
            $sql = "SELECT * FROM promotion_code WHERE (status = $status OR $status = -1 )";            
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";        
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());  
            while($row = mysql_fetch_assoc($rs)){
                $arrResult['data'][] = $row; 
            }

            $arrResult['total'] = mysql_num_rows($rs);     
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Code','function' => 'getListCode' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        
        return $arrResult;
    }       

    function updateCode($id,$code,$amount,$type,$value){
        $time = time();
        $sql = "UPDATE promotion_code
                    SET  code= '$code',
                    type = '$type',
                    code_value  = '$value',
                    amount = $amount                    
                    WHERE code_id = $id ";
        mysql_query($sql) or die(mysql_error() . $sql);
    }
    function updateStatus($id,$status){
        $time = time();
        $sql = "UPDATE promotion_code
                    SET  status = $status                                   
                    WHERE code_id = $id ";
        mysql_query($sql) or die(mysql_error() . $sql);
    }
    function insertCode($code,$amount, $type,$value,$status){
        try{
            $time = time();
            $sql = "INSERT INTO promotion_code VALUES(NULL,'$code',$amount,$type,'$value',2)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());       
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Code','function' => 'insertCode' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function insertEmailNhanCode($code,$amount, $type,$value,$status){
        try{
            $time = time();
            $sql = "INSERT INTO promotion_code VALUES(NULL,'$code',$amount,$type,'$value',2)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());       
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Code','function' => 'insertCode' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }    

    function getDetailNewsletter($id) {
        $sql = "SELECT * FROM sendcontent WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        return $rs;
    }
   

    function getListContentByStatus($type, $status=-1,$offset = -1, $limit = -1) {
        $sql = "SELECT * FROM sendcontent WHERE type = $type AND (status = $status OR $status = -1) ORDER BY id DESC ";
        if ($limit > 0 && $offset >= 0)
            $sql .= " LIMIT $offset,$limit";
        $rs = mysql_query($sql) or die(mysql_error());
        return $rs;
    }
        
   

    function updateStatusNewsletter($id,$status) {        
        $sql = "UPDATE newsletter
                    SET status = $status           
                    WHERE id = $id ";
        mysql_query($sql) or die(mysql_error() . $sql);
    }

    function getDetailOrder($id) {
        $sql = "SELECT * FROM order_info WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function updateOrder($arrParams){

        $sql = "UPDATE orders SET "; 
        unset($arrParams['back_url']);
        $count = 0;
        $total = count($arrParams);
        foreach ($arrParams as $key => $value) {
            $count++;
            $sql.= " $key = '$value' ";
            if($count<$total) $sql.= " , ";
        }
        $sql.=" WHERE order_id = ".$arrParams['order_id'];                                
        mysql_query($sql) or die(mysql_error() . $sql);
    }
    function getTimeTicket($ticket_id){
        $arrResult = array();
        $sql = "SELECT * FROM time_ticket WHERE ticket_id = $ticket_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
                $arrResult[] = $row['time_id']; 
        }
        return $arrResult;
    }  
    function getServiceTicket($ticket_id){
        $arrResult = array();
        $sql = "SELECT * FROM service_ticket WHERE ticket_id = $ticket_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
                $arrResult[] = $row['service_id']; 
        }
        return $arrResult;
    } 
    function getProductName($id){
        $arrResult = array();
        $sql = "SELECT product_name FROM product WHERE id = $id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row['product_name'];
    } 
    
    function getListOrder($customer_name="",$phone_number="",$email="",$status=-1,$order_type=-1,$fromdate=-1,$todate=-1,$offset = -1, $limit = -1) {
        $arrResult = array();

        try{
            $sql = "SELECT * FROM order_info WHERE (status = $status OR $status = -1)  ";

            $sql.=" AND ( order_type = $order_type OR $order_type = -1 ) ";         
            
            if($customer_name !=''){
                $sql.=" AND customer_name LIKE '%$customer_name%'";
            }
            
            if($phone_number !=''){
                $sql.=" AND phone_number = '$phone_number' ";
            }
            if($email !=''){
                $sql.=" AND email = '$email' ";
            }            
            
            $nows = time();
            if($fromdate > 0 && $todate > 0){
                $sql.= " AND (created_at BETWEEN $fromdate AND $todate) " ;
            }
            if($fromdate > 0 && $todate < 0){
                $sql.= " AND created_at >= $fromdate "; 
            }
            if($fromdate < 0 && $todate > 0){
                $sql.= " AND created_at <= $todate "; 
            }
            $sql.=" ORDER BY id DESC ";
            //$sql.=" ORDER BY orders.created_at DESC ";            
              
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";              
     
            $rs = mysql_query($sql) or die(mysql_error());//$this->throw_ex(mysql_error());  
            while($row = mysql_fetch_assoc($rs)){
                $arrResult['data'][] = $row; 
            }
            $arrResult['total'] = mysql_num_rows($rs);

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Order','function' => 'getListOrder' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        
        return $arrResult;
    }    
    
    function getListOrderCustomer($customer_id,$status=-1,$method_id=-1,$fromdate=-1,$todate=-1,$offset = -1, $limit = -1){
        $arrResult = array();

        try{
            $sql = "SELECT orders.* FROM orders WHERE (orders.status = $status OR $status = -1)  ";

            $sql.=" AND ( method_id = $method_id OR $method_id = -1 ) AND ( customer_id = $customer_id ) ";         
            
            $nows = time();
            if($fromdate > 0 && $todate > 0){
                $sql.= " AND (orders.created_at BETWEEN $fromdate AND $todate) " ;
            }
            if($fromdate > 0 && $todate < 0){
                $sql.= " AND orders.created_at >= $fromdate "; 
            }
            if($fromdate < 0 && $todate > 0){
                $sql.= " AND orders.created_at <= $todate "; 
            }
            
            $sql.=" ORDER BY orders.created_at DESC ";            
              
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";              
            
            $rs = mysql_query($sql) or die(mysql_error());//$this->throw_ex(mysql_error());  
            while($row = mysql_fetch_assoc($rs)){
                $arrResult['data'][] = $row; 
            }
            $arrResult['total'] = mysql_num_rows($rs);

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Order','function' => 'getListOrderCustomer' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        
        return $arrResult;
    }
    function pagination($page, $page_show, $total_page,$r=1){        
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
        $str='<div class="pagination-page"><div class="left t-page"><p>Page<span> '.$page.'</span> of <span>'.$total_page.'</span></p></div><div class="right t-pagination"><ul>';
        if ($page > 1) {
            ($page == 1) ? $class = " class='active'" : $class = "";
            $str.='<li><a ' . $class . ' href="javascript:;" attr-value="1"><</a><li>';
            echo "";
        }
        for ($i = $dau; $i < $cuoi; $i++) {
            ($page == $i) ? $class = " class='active'" : $class = "";
            $str.='<li><a ' . $class . ' href="javascript:;" attr-value="'.$i.'">'.$i.'</a><li>';            
        }
        if ($page < $total_page) {
            ($page == $total_page) ? $class = " class='active end'" : $class = " class='end' ";
            $str.='<li><a ' . $class . ' href="javascript:;" attr-value="'.$total_page.'">></a><li>';            
        }
        $str.="</ul></div></div>";
        return $str;       
                            
    }
    function insertBlock($block_name,$arrText,$arrLink) {
        try{
            $sql = "INSERT INTO block VALUES
                            (NULL,'$block_name',1)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());
            $block_id = mysql_insert_id();
            if(!empty($arrText) && !empty($arrLink)){
                foreach ($arrText as $k=> $text) {
                    if($text){
                        $link = $arrLink[$k];
                        mysql_query("INSERT INTO link VALUES(null,'$text',$block_id,'$link',1)") or die(mysql_error());
                    }
                }
            }
        }catch(Exception $ex){
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Block','function' => 'insertBlock' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function updateBlock($block_id,$block_name,$arrText,$arrLink){
        try{
            $sql = "UPDATE block
                    SET block_name = '$block_name' WHERE block_id = $block_id";
            mysql_query($sql);

            mysql_query("DELETE FROM link WHERE block_id = $block_id");

            if(!empty($arrText) && !empty($arrLink)){
                foreach ($arrText as $k=> $text) {
                    if($text){
                        $link = $arrLink[$k];
                        mysql_query("INSERT INTO link VALUES(null,'$text',$block_id,'$link',1)") or die(mysql_error());
                    }
                }
            }
        }catch(Exception $ex){
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Block','function' => 'updateBlock' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }

    function getDetailBlock($block_id){
        $sql = "SELECT * FROM block WHERE block_id = $block_id";
        $rs = mysql_query($sql);
        $row = mysql_fetch_assoc($rs);
        return $row;
    }
    function getListLinkByBlock($block_id){
        $arrReturn = array();
        $sql = "SELECT * FROM link WHERE block_id = $block_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function getListBlock(){
        $arrReturn = array();
        $sql = "SELECT * FROM block WHERE status = 1";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function insertMailNhanCode($code_id,$list_email){
        try{            
            $time = time();
            $tmp = explode(";",$list_email);
            $code = $this->getCodeByID($code_id);
            foreach ($tmp as $email) {
                $email = trim($email); 
                $sql = "INSERT INTO email_code VALUES(NULL,'$email',$code_id,'$code',1)";
                $rs = mysql_query($sql) or $this->throw_ex(mysql_error()); 
            }
                  
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Rating','function' => 'insertMailNhanCode' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getCodeByID($code_id){
        $sql = "SELECT code FROM promotion_code WHERE code_id = $code_id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        return $row['code'];
    }
    function getCityById($city_id){
        $sql = "SELECT city_name FROM city WHERE city_id = $city_id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['city_name'];
    }
    function getMethodById($id){
        $sql = "SELECT name FROM method WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['name'];
    }
    function getListMethod(){
        $sql = "SELECT * FROM method";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[$row['id']] = $row['name'];
        }
        return $arrReturn;
    }
    function getImageById($id){
        $sql = "SELECT image_url FROM product WHERE id = $id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['image_url'];
    }
    function getUserById($id){
        $sql = "SELECT username FROM users WHERE user_id = $id";
        $rs = mysql_query($sql) or die(mysql_error().$sql);
        $row = mysql_fetch_assoc($rs);
        return $row['username'];
    }
    function getListEmailNhanCode($code_id=-1,$status = -1, $offset = -1, $limit = -1) {
        try{
            $arrResult = array();
            $sql = "SELECT * FROM email_code WHERE (status = $status OR $status = -1 )
            AND (code_id = $code_id OR $code_id = -1 )  ORDER BY id DESC ";            
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";        
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());  
            while($row = mysql_fetch_assoc($rs)){
                $arrResult['data'][] = $row; 
            }

            $arrResult['total'] = mysql_num_rows($rs);     
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Rating','function' => 'getListRating' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
        
        return $arrResult;
    }

    function getListCateArticles(){
        $arrReturn = array();
        $sql = "SELECT * FROM article_cate ORDER BY display_order";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[$row['cate_id']] = $row;
        }
        return $arrReturn;
    }
    function getDetailCateArticles($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM article_cate WHERE cate_id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }   
    function getDetailCity($id) {
        $arrReturn = array();      
        $sql = "SELECT * FROM city WHERE city_id = $id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn = $row;           
        return $arrReturn;
    }   

    function insertCateArticles($cate_name,$cate_alias,$image_url,$description,$meta_title,$meta_description,$meta_keyword,$is_hot,$hidden,$seo_text, $seo_title) {
        try{            
            $display_order = $this->getOrderMax("article_cate");
            $time = time();            
            $sql = "INSERT INTO article_cate VALUES
                            (NULL,'$cate_name','$cate_alias','$image_url','$description',
                            $display_order,'$meta_title','$meta_description','$meta_keyword','$seo_title', '$seo_text', $is_hot,$hidden)";
            $rs = mysql_query($sql) or $this->throw_ex(mysql_error());

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'CateType','function' => 'insertCateArticles' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }   

    function updateCateArticles($id,$cate_name,$cate_alias,$image_url,$description,$meta_title,$meta_description,$meta_keyword,$is_hot,$hidden,$seo_text, $seo_title) {
       try{            
            $time = time();    
            $sql = "UPDATE article_cate
                        SET cate_name = '$cate_name', cate_alias = '$cate_alias',
                            image_url = '$image_url',description = '$description',
                            is_hot = $is_hot ,hidden = $hidden,seo_text = '$seo_text',
                            meta_title = '$meta_title',meta_description = '$meta_description',
                            meta_keyword = '$meta_keyword'  ,seo_title = '$seo_title'               
                        WHERE cate_id = $id ";
            mysql_query($sql)  or $this->throw_ex(mysql_error());             

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'updateCateArticles','function' => 'updateCateArticles' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }  
    function getDetailArticle($article_id) {
        $arrReturn = array();
        $str_image = "";
        $sql = "SELECT * FROM articles WHERE article_id = $article_id";
        $rs = mysql_query($sql) or die(mysql_error());
        $row =mysql_fetch_assoc($rs);
        $arrReturn['data']= $row;

        $sql = "SELECT * FROM images WHERE object_id = $article_id AND object_type = 2";
        $rs = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn['images'][] = $row;
            $str_image.= $row['url'].";";            
        }
        $arrReturn['str_image'] = $str_image;        
        return $arrReturn;
    }
    function getListCustomer($full_name='',$handphone='',$address='',$email='',$username='',$status=-1,$offset=-1, $limit=-1){
        try{
            $arrResult = array();
            $sql = "SELECT * FROM customer WHERE (status = $status OR $status = -1) ";
            if(trim($full_name) != ''){
                $sql.= " AND full_name LIKE '%".$full_name."%' " ;
            } 
            if(trim($handphone) != ''){
                $sql.= " AND handphone = '$handphone' " ;
            }   
            if(trim($address) != ''){
                $sql.= " AND address LIKE '%".$address."%' " ;
            }   
            if(trim($email) != ''){
                $sql.= " AND email = '$email' " ;
            } 
            if(trim($username) != ''){
                $sql.= " AND username = '$username' " ;
            }     
            
            $sql.="  ORDER BY id DESC ";
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";            
            $rs = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_assoc($rs)){
               $arrResult['data'][] = $row; 
            }

            $arrResult['total'] = mysql_num_rows($rs);   
            return $arrResult;  
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Articles','function' => 'getListCustomer' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getListArticle($keyword='', $cate_id=-1,$tungay = '',$denngay='',$hidden=-1,$offset = -1, $limit = -1) {
        try{
            $arrResult = array();
            $sql = "SELECT * FROM articles WHERE (cate_id = $cate_id OR $cate_id = -1) AND (hidden = $hidden OR $hidden = -1) ";
            if(trim($keyword) != ''){
                $sql.= " AND article_title LIKE '%".$keyword."%' " ;
            }    
            if($tungay !=''){
                $tungay = strtotime($tungay);
                $sql.=" AND updated_at >= $tungay ";
            }
            if($denngay !=''){
                $denngay = strtotime($denngay);
                $sql.=" AND updated_at <= $denngay";
            }
            $sql.="  ORDER BY article_id DESC ";
            if ($limit > 0 && $offset >= 0)
                $sql .= " LIMIT $offset,$limit";            
            $rs = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_assoc($rs)){
               $arrResult['data'][] = $row; 
            }

            $arrResult['total'] = mysql_num_rows($rs);   
            return $arrResult;  
        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Articles','function' => 'getListArticle' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
   

    function insertArticles($article_title,$title_en,$article_alias,$image_url,$description,$content,$cate_id,$arrTag,$source,$is_hot,$meta_title,$meta_description,$meta_keyword,$hidden,$seo_text,$seo_title) {
        try{
            $user_id = $_SESSION['user_id'];
            $time = time();          
            $sql = "INSERT INTO articles VALUES
                            (NULL,'$article_title','$title_en','$article_alias','$image_url','$description','$content','$source',
                                $hidden,$cate_id,$is_hot,0,'$meta_title','$meta_description','$meta_keyword', '$seo_title','$seo_text', $time,$time,$user_id,$user_id)";
            $rs = mysql_query($sql) or die(mysql_error().$sql);//$this->throw_ex(mysql_error());
            $article_id = mysql_insert_id();
            
            if(!empty($arrTag)){
                foreach($arrTag as $tag){
                    $tag = trim($tag);
                    $tag_id = $this->checkTagTonTai($tag);
                    $this->addTagToArticle($article_id,$tag_id);
                }
            }       


        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Articles','function' => 'insertArticle' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }
    function getTagsByProductId($article_id){
        $sql = "SELECT tag_id FROM articles_tag WHERE article_id = $article_id";
        $rs = mysql_query($sql);
        return $rs;
    }
    function getTagsOfProductId($article_id){
        $arr = array();
        $sql = "SELECT tag_id FROM articles_tag WHERE article_id = $article_id";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $arr[] = $row['tag_id']; 
        }
        return $arr;
    }
    function getDetailTag($tag_id){
        $sql = "SELECT * FROM tag WHERE tag_id = $tag_id";
        $rs = mysql_query($sql);
        return $rs;
    }    

    function updateArticles($article_id,$article_title,$title_en,$article_alias,$image_url,$description,$content,$cate_id,$arrTag,$source,$is_hot,$meta_title,$meta_description,$meta_keyword,$hidden,$seo_text,$seo_title) {
       try{
            $user_id = $_SESSION['user_id'];
            $time = time();            
            $sql = "UPDATE articles
                        SET article_title = '$article_title',title_en = '$title_en' ,
                        article_alias = '$article_alias',seo_title = '$seo_title',
                        image_url = '$image_url',source = '$source',
                        description = '$description',content = '$content',                    
                        cate_id = $cate_id, is_hot = $is_hot,                   
                        updated_at = $time,hidden = $hidden,
                        meta_title='$meta_title',seo_text = '$seo_text',
                        meta_description = '$meta_description',
                        meta_keyword = '$meta_keyword',updated_by = $user_id              
                        WHERE article_id = $article_id ";
            mysql_query($sql)  or $this->throw_ex(mysql_error());  

            if(!empty($arrTag)){                  
                mysql_query("DELETE FROM articles_tag WHERE article_id = $article_id AND object_type = 2");
                foreach($arrTag as $tag){
                    $tag_id = $this->checkTagTonTai($tag);
                    $this->addTagToArticle($article_id,$tag_id);
                }
            }else{
                mysql_query("DELETE FROM articles_tag WHERE article_id = $article_id AND object_type = 2");
            }
           

        }catch(Exception $ex){            
            $arrLog = array('time'=>date('d-m-Y H:i:s'),'model'=> 'Articles','function' => 'updateArticle' , 'error'=>$ex->getMessage(),'sql'=>$sql);
            $this->logError($arrLog);
        }
    }

    function checkTagTonTai($tag){
        $sql = "SELECT tag_id FROM tag WHERE BINARY tag_name LIKE '%$tag%'";
        $rs = mysql_query($sql);
        $row = mysql_num_rows($rs);
        if($row == 1){
            $row = mysql_fetch_assoc($rs);
            $idTag = $row['tag_id'];
        }else{
            $tag_kd = $this->changeTitle($tag);
            $idTag = $this->insertTag($tag,$tag_kd);
        }
        return $idTag;
    }
    function insertTag($tag,$tag_kd){
        $sql = "INSERT INTO tag VALUES (NULL,'$tag','$tag_kd')";
        $rs = mysql_query($sql) or die(mysql_error());
        $id= mysql_insert_id();
        return $id;         
    }
     function insertAgeRange($range){
        $sql = "INSERT INTO age_range VALUES (NULL,'$range')";
        $rs = mysql_query($sql) or die(mysql_error());
    }   
    function updateAgeRange($id,$range){
        $sql ="UPDATE age_range SET `range` = '$range' WHERE id = $id"; 
        mysql_query($sql) or die(mysql_error().$sql);
    }
    function getDetailAgeRange($id){
        $rs = mysql_query("SELECT * FROM age_range WHERE id = $id");
        return $row = mysql_fetch_assoc($rs);
    }
    function getListAgeRange(){
        $rs = mysql_query("SELECT * FROM age_range");
        while($row = mysql_fetch_assoc($rs)){
            $arrReturn[] = $row;
        }
        return $arrReturn;
    }
    function addTagToArticle($article_id,$tag_id){
        $sql = "INSERT INTO articles_tag VALUES ($article_id,$tag_id,2)";
        mysql_query($sql) or die(mysql_error());
    }
    function updateOrderCateType($cate_type_id, $display_order){
        $sql = "UPDATE cate_type SET display_order  = $display_order WHERE id = $cate_type_id";
        mysql_query($sql) or die(mysql_error()); 
    }
    function updateOrderCate($cate_id, $display_order){
        $sql = "UPDATE cate SET display_order  = $display_order WHERE id = $cate_id";
        mysql_query($sql) or die(mysql_error()); 
    }
    function updateOrderCity($city_id, $display_order){
        $sql = "UPDATE city SET display_order  = $display_order WHERE city_id = $city_id";
        mysql_query($sql) or die(mysql_error()); 
    }
    function updateOrderManu($manu_id, $display_order){
        $sql = "UPDATE manu SET display_order  = $display_order WHERE id = $manu_id";
        mysql_query($sql) or die(mysql_error()); 
    }
    function updateOrderCateArticles($cate_id, $display_order){
        $sql = "UPDATE article_cate SET display_order  = $display_order WHERE cate_id = $cate_id";
        mysql_query($sql) or die(mysql_error()); 
    }
    function getOrderMax($table){
        $sql = "SELECT max(display_order) as order_max from $table";
        $rs = mysql_query($sql) or die(mysql_error()); 
        $row = mysql_fetch_assoc($rs);
        return $row['order_max'];
    }
    function getOrderMaxState($table, $city_id){
        $sql = "SELECT max(display_order) as order_max from $table WHERE city_id = $city_id";
        $rs = mysql_query($sql) or die(mysql_error()); 
        $row = mysql_fetch_assoc($rs);
        return $row['order_max'];
    }
    function uploadImages($file_upload){
        $allowedExts = array("jpg", "jpeg", "gif", "png");
        $arrResult = array();
        if(!is_dir("../../upload/images/".date('Y/m/d')."/"))
        mkdir("../../upload/images/".date('Y/m/d')."/",0777,true);

        $url = "../../upload/images/".date('Y/m/d')."/";   
            $extension = end(explode(".", $file_upload["name"]));
            if ((($file_upload["type"] == "image/gif") || ($file_upload["type"] == "image/jpeg") || ($file_upload["type"] == "image/png")
            || ($file_upload["type"] == "image/jpeg")) 
            && in_array($extension, $allowedExts))
              {
              if ($file_upload["error"] > 0)
                {
                //echo "Return Code: " . $file_upload["error"] . "<br />";
                }
              else
                {       
            
                if (file_exists($url. $file_upload["name"]))
                  {
                  //echo $file_upload["name"] . " đã tồn tại. "."<br />";       
                  }
                else
                  {

                    $arrPartImage = explode('.', $file_upload["name"]);

                    // Get image extension
                    $imgExt = array_pop($arrPartImage);

                    // Get image not extension
                    $img = preg_replace('/(.*)(_\d+x\d+)/', '$1', implode('.', $arrPartImage));
                    
                    $img = $this->changeTitle($img);
                    $img = $this->countImage($url,$img);                    
                    $name = "{$img}.{$imgExt}";

         
                   
                    if(move_uploaded_file($file_upload["tmp_name"],$url. $name)==true){                                    
                        $hinh = str_replace("../","",$url). $name;
                        $arrReturn['filename'] = $hinh;               
                    }
                  }
                }
                
              }
              
        return $arrReturn;      
   
    }
    function countImage($url,$img){
          $dh  = opendir($url);
            while (false !== ($filename = readdir($dh))) {
                $arrFiles[] = $filename;
            }
            sort($arrFiles);

            unset($arrFiles[0]);
            unset($arrFiles[1]);
            $nameReturn = $img.'-'.(count($arrFiles)+1);
           /* 
           if(!empty($arrFiles)){
            foreach ($arrFiles as $files) {
                $arrTmp = explode(".",$files);
                $arrName[] = $arrTmp[0];
            }
           }

           $nameReturn = $img.'-'.(count($arrFiles)+1);
           
           if(in_array($img, $arrName)){
                for($i = 0; $i<=9;$i++){             
                    if($i==0){
                        $newname =  $img;
                    }else{
                        $newname =  $img.'-'.$i;
                    }
                    if(in_array($newname, $arrName)){
                        $nameReturn = $img.'-'.($i+1);
                        
                    }else{
                        $nameReturn = $nameReturn;
                    }
                }
                
           }else{
             $nameReturn = $img;
           } 
           */          
            return $nameReturn;
    }   
}

?>