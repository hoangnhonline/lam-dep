<?php
$cateArr = $model->getList('cate');
foreach ($cateArr as $cate) {	
?>
<section>
	<div class="row category-caption">
		<div class="col-lg-12">
			<h2 class="pull-left"><?php echo $cate['name']; ?></h2>
			<a href="<?php echo $cate['slug']; ?>/"><span class="pull-right"><i class="fa fa-plus"></i></span></a>
			
		</div>
	</div>
	<div class="row">		
		<?php $arr = $model->getListLimit('post', 0, 2, array('cate_id' => $cate['id'])); 
		foreach ($arr as $key => $value) {
		?>
		<article class="col-lg-6 col-md-6">
			<div class="picture">
				<div class="category-image">
					<a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>">
						<img src="<?php echo str_replace("../", "", $value['image_url']); ?>" class="img-responsive" alt="<?php echo $value['title']; ?>" >
					</a>
					<!--<div class="play-icon"><img src="images/icons/video-icon.png" width="40" height="40" alt="" ></div>-->
				</div>
			</div>
			<div class="detail">
				<div class="info">
					<!--<span class="date"><i class="fa fa-calendar-o"></i> 01/01/2015</span>-->
					<span class="comments pull-right"><i class="fa fa-comment-o"></i> 750</span>
					<span class="likes pull-right"><i class="fa fa-heart-o"></i> 500</span>
				</div>
				<div class="caption"><a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>"><?php echo $value['title']; ?></a></div>				
			</div>
		</article>
		<?php } ?>
		
	</div>
	<div class="row">
		<?php $arr = $model->getListLimit('post', 2, 3, array('cate_id' => $cate['id'])); 
		foreach ($arr as $key => $value) {
		?>
		<article class="col-lg-4 col-md-4">
			<div class="picture">
				<div class="category-image">
					<a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>">
						<img src="<?php echo str_replace("../", "", $value['image_url']); ?>" class="img-responsive" alt="<?php echo $value['title']; ?>" >
					</a>
					<!--<div class="play-icon"><img src="images/icons/video-icon.png" width="40" height="40" alt="" ></div>-->
				</div>
			</div>
			<div class="detail">
				<div class="info">
					<!--<span class="date"><i class="fa fa-calendar-o"></i> 01/01/2015</span>-->
					<span class="comments pull-right"><i class="fa fa-comment-o"></i> 750</span>
					<span class="likes pull-right"><i class="fa fa-heart-o"></i> 500</span>
				</div>
				<div class="small-caption">
					<a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>">
					<?php echo $value['title']; ?>
					</a>
				</div>
				
			</div>
		</article>
		<?php } ?>
	</div>
</section>
<!-- /. FASHION ENDS
	========================================================================= -->
<hr>
<?php } ?>