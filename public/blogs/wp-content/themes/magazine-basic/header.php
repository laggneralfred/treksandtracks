<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 * and the left sidebar conditional
 *
 * @since 3.0.0
 */
$bavotasan_theme_options = bavotasan_theme_options();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if IE]><script src="<?php echo BAVOTASAN_THEME_URL; ?>/library/js/html5.js"></script><![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="grid <?php echo ' ' . esc_attr( $bavotasan_theme_options['width'] ); ?>">
		<header id="header" class="row" role="banner">
			<div class="c12">
				<div id="mobile-menu">
					<a href="#" class="left-menu"><i class="fa fa-reorder"></i></a>
					<a href="#"><i class="fa fa-search"></i></a>
				</div>
				<div id="drop-down-search"><?php get_search_form(); ?></div>

				<?php
				$logo = $bavotasan_theme_options['logo'];
				$text_color = get_header_textcolor();
				$alignment = $bavotasan_theme_options['header_alignment'];
				$header_class = ( $alignment ) ? $alignment : '';
				$header_class2 = ( ! $logo && 'blank' == $text_color ) ? 'remove' : $header_class;
				$class = ( $logo ) ? ' class="remove"' : '';
				?>
				<div class="title-logo-wrapper <?php echo $header_class2; ?>">
					<?php
					if ( $logo ) {
						?>
						<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" id="site-logo"  rel="home"><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>
					<?php } ?>
					<div class="header-group">
						<?php $tag = ( is_front_page() && is_home() ) ? 'h1' : 'div'; ?>
						<<?php echo $tag; ?> id="site-title"<?php echo $class; ?>><a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></<?php echo $tag; ?>>
						<?php if ( $bavotasan_theme_options['tagline'] ) { ?><div id="site-description"><?php bloginfo( 'description' ); ?></div><?php } ?>
					</div>
				</div>

				<?php
				if ( is_active_sidebar( 'header-area' ) ) { ?>
					<div id="header-widgets" class="<?php echo $header_class; ?>">
						<?php dynamic_sidebar( 'header-area' ); ?>
					</div>
					<?php
				}

				$header_image = get_header_image();
				if ( $header_image ) {
					?>
					<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img id="header-img" src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
					<?php
				}
				?>

				<div id="nav-wrapper">
					<div class="nav-content">
						<nav id="site-navigation" class="menus clearfix" role="navigation">
							<h3 class="screen-reader-text"><?php _e( 'Main menu', 'magazine-basic' ); ?></h3>
							<a class="screen-reader-text" href="#primary" title="<?php esc_attr_e( 'Skip to content', 'magazine-basic' ); ?>"><?php _e( 'Skip to content', 'magazine-basic' ); ?></a>
							<?php echo str_replace( '</li>', '', wp_nav_menu( array( 'theme_location' => 'primary', 'echo' => false, 'container_id' => 'main-menu' ) ) ); ?>
						</nav><!-- #site-navigation -->

						<?php if ( has_nav_menu( 'secondary' ) ) { ?>
						<nav id="site-sub-navigation" class="menus" role="navigation">
							<h3 class="screen-reader-text"><?php _e( 'Sub menu', 'magazine-basic' ); ?></h3>
							<?php echo str_replace( '</li>', '', wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class' => 'secondary-menu', 'echo' => false, 'fallback_cb' => false ) ) ); ?>
						</nav><!-- #site-sub-navigation -->
						<?php } ?>
					</div>
				</div>

			</div><!-- .c12 -->
		</header><!-- #header.row -->

		<div id="main" class="row">