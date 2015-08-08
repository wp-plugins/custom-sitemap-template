<?php
/* Template Name: Sitemap */
get_header();
?>
<!--[start container]-->
<div class="container">

<?php $cst_settings_arr = get_option('cst_settings_arr', null);
if ($cst_settings_arr !==  null) { 
$cst_settings_val = unserialize($cst_settings_arr); ?>

	<!--[start sitemap-content]-->
	<div class="sitemap-content">

		<ul id="toggle-view">

		<?php $lists = $cst_settings_val['sitemap_post_list'];
		$postignore = $cst_settings_val['sitemap_hide_post'];

		$postex_arr = explode(",", $postignore);

		foreach($lists as $list) :
			global $wp_post_types;
			$obj = $wp_post_types[$list];
			$posttitle = $obj->labels->name; ?>
			<li>
				<h2><?php echo $posttitle;?></h2>
					<span>+</span>
				<div class="panel">
					<ul>
					<?php $args = array(
						'post_type'     => $list,
						'numberposts'   => -1,
						'orderby'	=> 'title',
						'order'		=> 'ASC',
						'post__not_in'  => $postex_arr
					);
					$query = new WP_Query( $args );

					// The Loop
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) :
							$query->the_post();?>
							<li class="sitemap">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</li>
								<?php endwhile;
							else :
								echo "No $posttitle Found";
							endif; wp_reset_postdata();?>
					</ul>
				</div>
			</li>
		<?php endforeach; 

		$catlists = $cst_settings_val['sitemap_cat_list'];
		$catignore = $cst_settings_val['sitemap_hide_cat'];

		$hidecat = explode(",", $catignore);
		foreach($catlists as $catlist) :
			$catresult = get_taxonomy( $catlist );
			$args = array(
		 		 'orderby' => 'name',
		  		'hide_empty' => 0,
		 		'exclude' =>  $hidecat
		  	);
			$categories = get_categories( $args );?>
			<li>
				<h2><?php echo $catresult->label;?></h2>
					<span>+</span>
				<div class="panel">
					<ul>

					<?php foreach ( $categories as $category ) {
						echo '<li class="sitemap"><a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a></li>';
					}
					?>

					</ul>
				</div>
			</li>
		<?php endforeach;?>

		</ul>

		<script type="text/javascript">
		jQuery(document).ready(function ($) {
	
			$('#toggle-view li').click(function () {

				var text = $(this).children('div.panel');

				if (text.is(':hidden')) {
					text.slideDown('200');
					$(this).children('span').css('color', '#FFA500');
					$(this).children('h2').css('color', '#FFA500');
					$(this).children('span').html('-');		
				} else {
					text.slideUp('200');
					$(this).children('span').css('color', '#000');
					$(this).children('h2').css('color', '#000');
					$(this).children('span').html('+');		
				}
		
			});

		});
		</script>
        </div>	
	<!--[end sitemap-content]-->

	<?php } ?>

	<!--[start sidebar-content]-->
	<div class="grid_4 sidebar-content">
		<?php get_sidebar(); ?>
	</div>
	<!--[end sidebar-content]-->
</div>
<!--[end container]-->

<?php get_footer(); ?>
