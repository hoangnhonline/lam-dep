<!-- LATEST ARTICLES STARTS
	========================================================================= -->  
<section class="latest-articles">
	<div class="row category-caption">
		<div class="col-lg-12">
			<h2 class="pull-left">LATEST ARTICLES</h2>
			<span class="pull-right"><a href="latest-articles.html"><i class="fa fa-plus"></i></a></span>
		</div>
	</div>
	<div class="row">
		<?php $arr = $model->getListLimit('post', 0, 9); 
		foreach ($arr as $key => $value) {
		?>
		<article class="col-lg-4 col-md-4">
			<div class="picture">
				<div class="category-image">
					<a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>">
					<img src="<?php echo str_replace("../", "", $value['image_url']); ?>" class="img-responsive" alt="<?php echo $value['title']; ?>" >
					</a>
					<h2 class="overlay-category"><?php echo $cateArr[$value['cate_id']]['name']; ?></h2>
				</div>
			</div>
			<div class="detail">
				<div class="info">					                       
					<span class="comments pull-right"><i class="fa fa-comment-o"></i> 750</span>
					<span class="likes pull-right"><i class="fa fa-heart-o"></i> 500</span>
				</div>
				<div class="caption">
					<a href="<?php echo $value['slug']; ?>.html" title="<?php echo $value['title']; ?>">
						<?php echo $value['title']; ?>
					</a>
				</div>				
			</div>
		</article>
		<?php } ?>
		
	</div>
	<!-- PAGGING STARTS -->
	<div class="row pagging">
		<div class="col-lg-12 col-md-12">
			<ul class="pagination pagination-lg">
				<li class="active"><a href="#">1</a></li>
				<li><a href="#">2</a></li>
				<li><a href="#">3</a></li>
				<li><a href="#">4</a></li>
				<li><a href="#">5</a></li>
				<li>
					<a href="#" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<!-- PAGGING ENDS -->
</section>
<!-- /. LATEST ARTICLES ENDS
	========================================================================= -->   