<?php
//Define the wp_content DIR for backward compatibility
if (!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('site_url').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
	define('WP_CONTENT_DIR', ABSPATH.'/wp-content');

//Define the content width for images
$max_width = 596;
$GLOBALS['content_width'] = 596;
    
//A function to include files throughout the theme
//It checks to see if the file exists first, so as to avoid error messages.
function get_template_file($filename) {
	if (file_exists(TEMPLATEPATH."/$filename"))
		include(TEMPLATEPATH."/$filename");
}

//A function to add the custom body background image
add_action('wp_head','flexx_body_bg');
function flexx_body_bg() {
	global $wp_theme_options;
	
	$options = array( 'background_image', 'background_color', 'background_position', 'background_attachment', 'background_repeat' );
	
?>
	<style type="text/css">
		body {
			<?php if ( 'custom_color' == $wp_theme_options['background_option'] ) : ?>
				background-color: <?php echo $wp_theme_options['background_color']; ?>;
				background-image: none;
			<?php else : ?>
				<?php foreach ( (array) $options as $option ) : ?>
					<?php if ( ! empty( $wp_theme_options[$option] ) ) : ?>
						<?php if ( 'background_image' == $option ) : ?>
							<?php echo str_replace( '_', '-', $option ); ?>: url(<?php echo $wp_theme_options[$option]; ?>);
						<?php else : ?>
							<?php echo str_replace( '_', '-', $option ); ?>: <?php echo $wp_theme_options[$option]; ?>;
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		}
	</style>
<?php
	
}


//Theme Options code
include(TEMPLATEPATH."/lib/theme-options/theme-options.php");

//FlexLayout Code
include(TEMPLATEPATH."/lib/flexlayout.php");

//Billboard Code
include(TEMPLATEPATH."/lib/billboard/billboard.php");

//Feedburner Widget Code
include(TEMPLATEPATH."/lib/feedburner-widget/feedburner-widget.php");


add_action( 'ithemes_load_plugins', 'ithemes_functions_after_init' );
function ithemes_functions_after_init() {
	//Include Tutorials Page
	include(TEMPLATEPATH."/lib/tutorials/tutorials.php");
	
	//FlexLayout Editor Code
	include(TEMPLATEPATH.'/lib/flexx-layout-editor/flexx-layout-editor.php');
	
	//Featured Image code
	include(TEMPLATEPATH."/lib/featured-images/featured-images.php");
	$GLOBALS['iThemesFeaturedImages'] =& new iThemesFeaturedImages( array( 'id_name' => 'title', 'width' => 'container_width', 'height' => '200', 'sleep' => 2, 'fade' => 1 ) );
	
	//Contact Page Template code
	include(TEMPLATEPATH."/lib/contact-page-plugin/contact-page-plugin.php");
}

//Unregister troublesome widgets
add_action('widgets_init','unregister_problem_widgets');
function unregister_problem_widgets() {
	unregister_sidebar_widget('Calendar');
	unregister_sidebar_widget('Search');
	unregister_sidebar_widget('Tag_Cloud');
}

//Register Page Template Widget Areas
register_sidebar(array('name'=>'Page Wide Sidebar Top','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
register_sidebar(array('name'=>'Page Wide Sidebar Bottom','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
register_sidebar(array('name'=>'Page Skinny Left Sidebar','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
register_sidebar(array('name'=>'Page Skinny Right Sidebar','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));

///Tracking/Analytics Code
function print_tracking() {
	global $wp_theme_options;
	echo stripslashes($wp_theme_options['tracking']);
}
if ($wp_theme_options['tracking_pos'] == "header")
	add_action('wp_head', 'print_tracking');
else //default
	add_action('flexx_footer_stats', 'print_tracking');


// Function to insert edit post/page link
function ithemes_insert_edit_link() {
	global $wp_query;
	edit_post_link('Edit this ' . ucwords( $wp_query->post->post_type ), '( ', ' )');
}
?>
