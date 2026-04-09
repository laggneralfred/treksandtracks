<?php
/*
Template Name: Search Template
*/
?>
<?php get_header(); global $wp_theme_options; ?>
<?php do_action('before_content'); ?>

<!--page.php-->
<div class="<?php do_action('content_style'); ?>" id="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>
		
	<!--Post Wrapper Class-->
	<div class="post">
	
	<!--Title-->
	<h3 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h3>

	<!--post text with the read more link-->
	<?php the_content(); ?>
    
    <p>Find what you're looking for ... enter keywords below:</p>	
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
<label class="hidden" for="s"><?php _e(''); ?></label>
<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="Search" />
</div>
</form>

	<!--post meta info-->
	<div class="meta-bottom wrap">
	</div>
    
	</div><!--end .post-->
	
	<?php endwhile; // end of one post ?>  
	<?php else : // do not delete ?>

	<div class="post">
	<h3><?php _e("Page not Found"); ?></h3>
    <p><?php _e("We're sorry, but the page you're looking for isn't here."); ?></p>
    <p><?php _e("Try searching for the page you are looking for or using the navigation in the header or sidebar"); ?></p>
    </div>

	<?php endif; // do not delete ?>
	
</div><!--end #content-->

<?php do_action('after_content'); ?>
<?php get_footer(); //Include the Footer ?>