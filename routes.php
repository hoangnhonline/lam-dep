<?php
if(!isset($_SESSION)){
    session_start();
}
require_once 'backend/models/Frontend.php';
$model = new Frontend;
$mod = isset($_GET['mod']) ? $_GET['mod'] : "";
$arrText = $model->getListText();
function checkCat($uri) {
    require_once 'backend/models/Frontend.php';
    $model = new Frontend;
    $uri = str_replace("+", "", $uri);

    $mod = $seo = "";
    $uri = str_replace(".html", '', $uri);   


    $object_id = 0;
    $page_id = $cate_type_id = $cate_id = $object_id = -1;
    $arrTmp = explode('/',$uri);    
    unset($arrTmp[0]);
    // check if cate vs cate_type vs page    
    
    if(strpos($uri, 'tin-tuc')){
        $mod = "news";        
    }elseif(strpos($uri, 'lien-he')){
        $mod = "contact";        
    }elseif(strpos($uri, 'chi-tiet-tin')){
        $mod = "news-detail";
    }else{
		
        /*$detailUrl = $model->getDetailByAlias('url', $arrTmp[1]);
		$detailProduct = $model->getDetailByAliasProduct($arrTmp[1]);
		
		if(!empty($detailProduct)){
			$mod = "detail";
			$object_id = $detailProduct['id'];  
		}else{
			if(!empty($detailUrl)){
				if($detailUrl['type'] == 2 ){
					$mod = "cate";
					$cate_type_id = $detailUrl['object_id'];            
					$arrDetailCateType = $model->getDetail('cate_type',$cate_type_id);
					$seo = $arrDetailCateType;
				}elseif($detailUrl['type'] == 1){
					$mod = "cate";
					$cate_id = $detailUrl['object_id'];
					$detailCate = $model->getDetail('cate', $cate_id);
					$cate_type_id = $detailCate['cate_type_id'];
					$seo = $detailCate;
				}else{ // 3 : page	
					$mod = "content";                
					if($arrTmp[1] == "gioi-thieu"){
						$page_id = 1;
					}
				}
			}else{ // TH dac biet
				$tmp = $arrTmp[1];
				if($tmp == 'tin-tuc'){
					$mod = "news";
				}
			}
		}
        */   
    }
    $mod = "";

    return array("seo"=>$seo, "mod" =>$mod, 'cate_type_id' => $cate_type_id, 'cate_id' => $cate_id, 'page_id' => $page_id, 'object_id' => $object_id);
}

$uri = $_SERVER['REQUEST_URI'];

$arrRS = checkCat($uri);

$mod = $arrRS['mod'];
$cate_type_id = $arrRS['cate_type_id'];
$cate_id = $arrRS['cate_id'];
$page_id = $arrRS['page_id'];
$object_id = $arrRS['object_id'];

$uri = str_replace(".html", "", $uri);
$tmp_uri = explode("/", $uri);

switch ($mod) {
    case "cate":
        $seo = $arrDetailCateType = $model->getDetail('cate_type',$cate_type_id);
        if($cate_id > 0){
            $seo = $detailCate = $model->getDetail('cate', $cate_id);             
        }
        $arrProduct = $model->getListProductCate($cate_type_id, $cate_id);
                
        
        break;    
    case "news":		
        $seo = $model->getDetailSeo(4);        
        
        break;    
    case "contact": 
        $seo = $model->getDetailSeo(3);              
        break;
    case "info" : 
        $seo = $model->getDetailSeo(8);
        break;
    case "detail":                            
	    $id = $object_id;
        $seo = $detailProduct = $model->getDetail('product',$id);        
        $detailCateType = $model->getDetail('cate_type', $detailProduct['cate_type_id']);
        $detailCate = $model->getDetail('cate', $detailProduct['cate_id']);
        break;
     case "news-detail":        
        $article_alias = $tmp_uri[2];         
        $tmp = explode("-", $article_alias);        
        $id = (int) end($tmp);
        $detailNews = $model->getDetailNews($id);     
        $seo = $detailNews;
        break;  
    case "content":                
        $data = $seo = $model->getDetail('pages', $page_id);
        break;
    case "page":
        $rs_article = $model->getDetailPage($page_id);
        $arrDetailPage = mysql_fetch_assoc($rs_article);
        break;
    default :    
        $seo = $model->getDetailSeo(1);
        break;
}
?>
