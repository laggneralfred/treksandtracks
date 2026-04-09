<?php
/*
Template Name: Contact Page
*/
?>
<?php get_header(); global $wp_theme_options; ?>
<?php do_action('before_content'); ?>

<!--index.php-->
<div class="<?php do_action('content_style'); ?>" id="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>
		
	<!--Post Wrapper Class-->
	<div class="post">
	
	<!--Title-->
	<h3 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h3>
	<?php the_content(); ?>
	<?php if ( ! empty( $GLOBALS['ithemes_contact_page'] ) ) : ?>
		<?php $GLOBALS['ithemes_contact_page']->render(); ?>
	<?php endif; ?>
	
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