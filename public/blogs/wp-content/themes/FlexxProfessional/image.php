<?php get_header(); global $wp_theme_options; ?>
<?php do_action('before_content'); ?>

<!--index.php-->
<div class="<?php do_action('content_style'); ?>" id="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>

	<!--Post Wrapper Class-->
	<div <?php if (function_exists('post_class')) { post_class('post'); } else { echo 'class="post"'; } ?>>

	<!--Title/Date/Meta-->
	<div class="title wrap">
		<div class="post-title">
			<h1 id="post-<?php the_ID(); ?>">&raquo; <a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h1>
		</div>
	</div>

	<p class="attachment" style="text-align: center;"><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'large' ); ?></a></p>
	<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?></div>
	<?php the_content(); ?>
	<div class="photometa">
	    <div class="alignleft"><?php previous_image_link() ?></div>
		<div class="EXIF"><h2><?php _e('Image Data','flexx'); ?></h2>
		<?php if (is_attachment()) :
			$imgmeta = wp_get_attachment_metadata( $id );
			echo "Camera // " . $imgmeta['image_meta']['camera']."<br />\n";
			echo "Date Taken // " . date("m-d-Y H:i", $imgmeta['image_meta']['created_timestamp'])."<br />\n";
			echo "Dimensions // " . $imgmeta['width']." x ".$imgmeta['height']."<br />\n";
			echo "Aperture //  f/" . $imgmeta['image_meta']['aperture']."<br />\n";
			echo "Focal Length // " . $imgmeta['image_meta']['focal_length']."mm<br />\n";
			echo "ISO // " . $imgmeta['image_meta']['iso']."<br />\n";
			echo "Shutter Speed // " . number_format($imgmeta['image_meta']['shutter_speed'],2)." seconds<br />\n";
			echo "";
		endif; ?>
		</div>
	    <div class="alignright"><?php next_image_link() ?></div>
	</div>
	
	<!--post meta info-->
	<div class="meta-bottom wrap">
	</div>
    
	</div><!--end .post-->
	

	<?php endwhile; // end of one post ?>
	<?php else : // do not delete ?>

	<h3><?php _e("Page not Found"); ?></h3>
    <p><?php _e("We're sorry, but the page you're looking for isn't here."); ?></p>
    <p><?php _e("Try searching for the page you are looking for or using the navigation in the header or sidebar"); ?></p>

	<?php endif; // do not delete ?>
	
</div><!--end #content-->

<?php do_action('after_content'); ?>
<?php get_footer(); //Include the Footer ?>