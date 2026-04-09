<?php get_header(); global $wp_theme_options; ?>
<?php do_action('before_content'); ?>

<!--404.php-->
<div class="<?php do_action('content_style'); ?>" id="content">

	<!--Post Wrapper Class-->
	<div class="post">

	<h3><?php _e("Page Not Found"); ?></h3>
    <p><?php _e("We're sorry, but the page you are looking for isn't here."); ?></p>
    <p><?php _e("Try searching for the page you are looking for or using the navigation in the header or sidebar") ?></p>

	</div>

</div><!--end #content-->

<?php do_action('after_content'); ?>
<?php get_footer(); //Include the Footer ?>