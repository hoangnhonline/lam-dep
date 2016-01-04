<?php 
session_start();
date_default_timezone_set ('Asia/Saigon');
require_once 'routes.php';
require_once "backend/models/Frontend.php";
$model = new Frontend;
$cateArr = $model->getList('cate');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $seo['meta_title']; ?></title>
        <base href="http://<?php echo $_SERVER["SERVER_NAME"]; ?>">   
        <meta name="description" content="<?php echo $seo['meta_description']; ?>">
        <meta name="keyword" content="<?php echo $seo['meta_keyword']; ?>">
		<meta charset="utf-8">
		<!--[if IE]>
		<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
		<![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- All Stylesheets -->
		<link href="css/all-stylesheets.css" rel="stylesheet">
		<!-- Header & Nav Center Align -->
		<link href="css/header-center-align.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->		
		<!-- Favicons -->
		<link rel="shortcut icon" href="images/icons/favicon/favicon.png">
		<link rel="apple-touch-icon" href="images/icons/favicon/apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="72x72" href="images/icons/favicon/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="114x114" href="images/icons/favicon/apple-touch-icon-114x114.png">
	</head>
	<body>
		<div class="container content-bg">
			<?php include "blocks/home/header.php"; ?>
			<?php include "blocks/home/mobile-header.php"; ?>
			<?php include "blocks/home/nav.php"; ?>
			<?php include "blocks/home/breaking-news.php"; ?>
			<?php include "blocks/home/slider.php"; ?>
			
			<!-- PAGE CONTENTS STARTS
				========================================================================= -->
			<section class="page-contents">
				<div class="row">
					<!-- LEFT COLUMN STARTS
						========================================================================= -->
					<div class="col-lg-8">
						<?php include "blocks/home/category.php"; ?>						
					</div>
					<!-- /. LEFT COLUMN ENDS
						========================================================================= --> 
					<?php include "blocks/home/right.php"; ?>	
				</div>
				<?php include "blocks/home/lastest.php"; ?>
			</section>
			<!-- /. PAGE CONTENTS ENDS
				========================================================================= -->
			<?php include "blocks/home/footer.php"; ?>
		</div>
		<!-- TO TOP STARTS
			========================================================================= -->
		<a href="#" class="scrollup">Scroll</a>      
		<!-- /. TO TOP ENDS
			========================================================================= --> 
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
		<script src="js/jquery-1.11.1/jquery.min.js"></script> 
		<!-- Include all compiled plugins (below), or include individual files as needed --> 
		<script src="js/bootstrap/bootstrap.min.js"></script>		 
		<!-- Hover Dropdown Menu -->  
		<script src="js/bootstrap-hover/twitter-bootstrap-hover-dropdown.min.js"></script> 
		<!-- Sidr JS Menu -->
		<script src="js/sidr/jquery.sidr.min.js"></script>
		<!-- Owl Carousel --> 
		<script type="text/javascript" src="owl-carousel/owl-carousel/owl.carousel.js"></script>
		<!-- AJAX Contact Form --> 			
		<script type="text/javascript" src="js/contact/contact-form.js"></script>
		<!-- Retina --> 
		<script type="text/javascript" src="js/retina/retina.js"></script> 
		<!-- FitVids --> 
		<script type="text/javascript" src="js/fitvids/jquery.fitvids.js"></script>
		<!-- Custom --> 
		<script type="text/javascript" src="js/custom/custom.js"></script>
	</body>
</html>