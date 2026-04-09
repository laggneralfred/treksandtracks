<?php global $wp_theme_options; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<!--The Title-->
<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :: '; } ?><?php bloginfo('name'); ?></title>

<!--The Favicon-->
<?php
	require_once( $GLOBALS['ithemes_theme_path'] . '/lib/file-utility/file-utility.php' );
	
	$filename = false;
	$default_favicon = true;
	
	if ( ( 'custom_image' === $wp_theme_options['favicon_option'] ) && ( ! empty( $wp_theme_options['favicon_image'] ) ) ) {
		$filename = iThemesFileUtility::get_file_from_url( $wp_theme_options['favicon_image'] );
		$default_favicon = false;
		
		if ( is_wp_error( $filename ) ) {
			echo "<!-- Favicon image error: " . $filename->get_error_message() . "-->\n";
			
			$filename = false;
		}
	}
	
	if ( false === $filename )
		$default_favicon = true;
	
	if ( true === $default_favicon )
		echo "<link rel=\"shortcut icon\" href=\"${wp_theme_options['default_favicon_image']}\" type=\"image/x-icon\" />\n";
	else {
		if ( ! is_wp_error( $filename ) ) {
			$thumb = iThemesFileUtility::resize_image( $filename, 16, 16, true );
			$type = iThemesFileUtility::get_mime_type( $filename );
			
			if ( ! is_wp_error( $thumb ) )
				echo "<link rel=\"shortcut icon\" href=\"${thumb['url']}\" type=\"$type\" />\n";
			else
				echo "<!-- Favicon image generation error: " . $thumb->get_error_message() . "-->\n";
		}
		else {
			echo "<!-- Favicon image error: " . $filename->get_error_message() . "-->\n";
			echo "<link rel=\"shortcut icon\" href=\"${wp_theme_options['default_favicon_image']}\" type=\"image/x-icon\" />\n";
		}
	}
?>

<!--The Meta Info-->
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php do_action('ithemes_meta'); ?>

<!--The Stylesheets-->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<!--[if lte IE 7]>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/lte-ie7.css" type="text/css" media="screen" />
<![endif]-->
<!--[if lt IE 7]>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/lt-ie7.css" type="text/css" media="screen" />
    <script src="<?php bloginfo('template_url'); ?>/js/dropdown.js" type="text/javascript"></script>
<![endif]-->

<!--The RSS and Pingback-->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php
//A function that adds the necessary javascript for comment replying.
if (is_singular()) wp_enqueue_script( 'comment-reply' ); 
?>

<?php wp_head(); //we need this for plugins ?>
</head>
<body>
<div class="<?php do_action('container_style'); ?>" id="container">

	<?php do_action('above_header'); ?>

	<div id="header" style="position:relative;">
		<div id="title" style="position:relative;">
			<noscript>
				<?php if ( ! empty( $wp_theme_options['ithemes_featured_images']['link'] ) ) : ?>
					<a href="<?php echo $wp_theme_options['ithemes_featured_images']['link']; ?>">
						<img src="<?php echo $GLOBALS['iThemesFeaturedImages']->get_random_image(); ?>" alt="Header Image" />
					</a>
				<?php else : ?>
					<img src="<?php echo $GLOBALS['iThemesFeaturedImages']->get_random_image(); ?>" alt="Header Image" />
				<?php endif; ?>
			</noscript>
		</div>
		<?php if ( ! empty( $wp_theme_options['ithemes_featured_images']['enable_overlay'] ) ) : ?>
			<?php if ( ! empty( $wp_theme_options['ithemes_featured_images']['link'] ) ) : ?>
				<a href="<?php echo $wp_theme_options['ithemes_featured_images']['link']; ?>">
			<?php endif; ?>
			<span id="title_overlay">
				<?php echo $wp_theme_options['ithemes_featured_images']['overlay_header_text']; ?>
				
				<?php if ( ! empty( $wp_theme_options['ithemes_featured_images']['overlay_subheader_text'] ) ) : ?>
					<span><?php echo $wp_theme_options['ithemes_featured_images']['overlay_subheader_text']; ?></span>
				<?php endif; ?>
			</span>
			<?php if ( ! empty( $wp_theme_options['ithemes_featured_images']['link'] ) ) : ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	
	<?php do_action('below_header'); ?>
