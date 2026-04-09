<?php
$bavotasan_theme_data = wp_get_theme( 'magazine-basic' );
define( 'BAVOTASAN_THEME_URL', get_template_directory_uri() );
define( 'BAVOTASAN_THEME_TEMPLATE', get_template_directory() );
define( 'BAVOTASAN_THEME_VERSION', trim( $bavotasan_theme_data->Version ) );
define( 'BAVOTASAN_THEME_NAME', $bavotasan_theme_data->Name );
define( 'BAVOTASAN_PRO_UPGRADE_NAME', 'Magazine Premium' );

/**
 * Includes
 *
 * @since 3.0.0
 */
require( BAVOTASAN_THEME_TEMPLATE . '/library/customizer.php' ); // Functions for theme options page
require( BAVOTASAN_THEME_TEMPLATE . '/library/preview-pro.php' ); // Functions for preview pro page
require( BAVOTASAN_THEME_TEMPLATE . '/library/about.php' ); // Functions for about page

/**
 * Prepare the content width
 *
 * @since 3.0.3
 */
function bavotasan_content_width() {
	$bavotasan_theme_options = bavotasan_theme_options();

	$array_width = array( '' => 1200, 'w960' => 960, 'w640' => 640, 'w320' => 320, 'wfull' => 1200 );
	$array_content = array( 'c2' => .17, 'c3' => .25, 'c4' => .34, 'c5' => .42, 'c6' => .5, 'c7' => .58, 'c8' => .66, 'c9' => .75, 'c10' => .83, 'c12' => 1 );

	return round( $array_content[$bavotasan_theme_options['primary']] * $array_width[$bavotasan_theme_options['width']] - 40 );
}

if ( ! isset( $content_width ) )
	$content_width = bavotasan_content_width();

add_action( 'after_setup_theme', 'bavotasan_setup' );
if ( ! function_exists( 'bavotasan_setup' ) ) :
/**
 * Initial setup for Magazine Basic theme
 *
 * This function is attached to the 'after_setup_theme' action hook.
 *
 * @uses	load_theme_textdomain()
 * @uses	get_locale()
 * @uses	add_theme_support()
 * @uses	add_editor_style()
 * @uses	register_default_headers()
 *
 * @since 3.0.0
 */
function bavotasan_setup() {
	$bavotasan_theme_options = bavotasan_theme_options();
	load_theme_textdomain( 'magazine-basic', BAVOTASAN_THEME_TEMPLATE . '/library/languages' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( array( 'library/css/admin/editor-style.css', bavotasan_font_url() ) );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menu( 'primary', __( 'Primary Menu', 'magazine-basic' ) );
	register_nav_menu( 'secondary', __( 'Secondary Menu', 'magazine-basic' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'gallery', 'image', 'video', 'audio', 'quote', 'link', 'status', 'aside' ) );

	// This theme uses Featured Images (also known as post thumbnails) for archive pages
	add_theme_support( 'post-thumbnails' );
	add_image_size( '1_column', $bavotasan_theme_options['1_image_width'], 999 );
	add_image_size( '2_column', $bavotasan_theme_options['2_image_width'], 999 );
	add_image_size( '3_column', $bavotasan_theme_options['3_image_width'], 999 );

	// Add a filter to bavotasan_header_image_width and bavotasan_header_image_height to change the width and height of your custom header.
	add_theme_support( 'custom-header', array(
		'random-default' => true,
		'default-text-color' => '333',
		'flex-width' => true,
		'flex-height' => true,
		'width' => apply_filters( 'bavotasan_header_image_width', 1200 ),
		'height' => apply_filters( 'bavotasan_header_image_height', 288 ),
		'admin-head-callback' => 'bavotasan_admin_header_style',
		'admin-preview-callback' => 'bavotasan_admin_header_image'
	) );

	add_theme_support( 'custom-background', array(
		'default-image' => BAVOTASAN_THEME_URL . '/library/images/solid.png',
	) );

	// Add HTML5 elements
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );

	// Add title tag support
	add_theme_support( 'title-tag' );

	// Remove default gallery styles
	add_filter( 'use_default_gallery_style', '__return_false' );
}
endif; // bavotasan_setup

add_action( 'wp_head', 'bavotasan_styles' );
/**
 * Add a style block to the theme for the current link color.
 *
 * This function is attached to the 'wp_head' action hook.
 *
 * @since 3.0.0
 */
function bavotasan_styles() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$text_color = get_header_textcolor();
	$styles = ( 'blank' == $text_color ) ? 'position:absolute !important;clip:rect(1px 1px 1px 1px);clip:rect(1px, 1px, 1px, 1px)' : 'color:#' . $text_color . ' !important';
	?>
<style>
#site-title a,#site-description{<?php echo $styles; ?>}
#page{background-color:<?php echo esc_attr( $bavotasan_theme_options['page_background'] ); ?>}
.entry-meta a,.entry-content a,.widget a{color:<?php echo esc_attr( $bavotasan_theme_options['link_color'] ); ?>}
</style>
	<?php
}

if ( ! function_exists( 'bavotasan_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in bavotasan_setup().
 *
 * @since 3.0.0
 */
function bavotasan_admin_header_style() {
	$text_color = get_header_textcolor();
	$styles = ( 'blank' == $text_color ) ? 'display:none' : 'color:#' . $text_color . ' !important';
	?>
<style>
.appearance_page_custom-header #headimg {
	border: none;
	}

#site-title {
	margin: 0;
	font-family: Georgia, sans-serif;
	font-size: 50px;
	line-height: 1.2;
	}

#site-description {
	font-family: Arial, sans-serif;
	margin: 0 0 30px;
	font-size: 20px;
	line-height: 1.2;
	font-weight: normal;
	padding: 0;
	}

#headimg img {
	max-width: 1200px;
	height: auto;
	width: 100%;
	}

#site-title,#site-description{<?php echo $styles; ?>}
</style>
	<?php
}
endif; // bavotasan_admin_header_style

if ( ! function_exists( 'bavotasan_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in bavotasan_setup().
 *
 * @uses	bloginfo()
 * @uses	get_header_image()
 *
 * @since 3.0.0
 */
function bavotasan_admin_header_image() {
	?>
	<div id="headimg">
		<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>
		<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
		<?php if ( $header_image = get_header_image() ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
	<?php
}
endif; // bavotasan_admin_header_image

add_action( 'pre_get_posts', 'bavotasan_home_query' );
if ( ! function_exists( 'bavotasan_home_query' ) ) :
/**
 * Remove sticky posts from home page query
 *
 * This function is attached to the 'pre_get_posts' action hook.
 *
 * @param	array $query
 *
 * @since 3.0.0
 */
function bavotasan_home_query( $query = '' ) {
	$bavotasan_theme_options = bavotasan_theme_options();
	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() )
		return;

	$query->set( 'post__not_in', (array) get_option( 'sticky_posts' ) );
	$query->set( 'posts_per_page', (int) $bavotasan_theme_options['number'] );
}
endif;

add_action( 'wp_enqueue_scripts', 'bavotasan_add_js' );
if ( ! function_exists( 'bavotasan_add_js' ) ) :
/**
 * Load all JavaScript to header
 *
 * This function is attached to the 'wp_enqueue_scripts' action hook.
 *
 * @uses	is_admin()
 * @uses	is_singular()
 * @uses	get_option()
 * @uses	wp_enqueue_script()
 * @uses	BAVOTASAN_THEME_URL
 *
 * @since 3.0.0
 */
function bavotasan_add_js() {
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_enqueue_script( 'theme_js', BAVOTASAN_THEME_URL .'/library/js/theme.js', array( 'jquery' ), '', true );

	wp_enqueue_style( 'google_fonts', bavotasan_font_url(), false, null, 'all' );
	wp_enqueue_style( 'theme_stylesheet', get_stylesheet_uri() );
	wp_enqueue_style( 'font_awesome', BAVOTASAN_THEME_URL .'/library/css/font-awesome.css', false, '4.3.0', 'all' );
}
endif; // bavotasan_add_js

/**
 * Register Open Sans and Raleway Google font.
 *
 * @since 1.0.2
 */
function bavotasan_font_url() {
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Open Sans or Raleway, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	return ( 'off' !== _x( 'on', 'Google Fonts: on or off', 'magazine-basic' ) ) ? add_query_arg( 'family', 'Cantata+One|Lato:300,700', '//fonts.googleapis.com/css' ) : '';
}

add_action( 'widgets_init', 'bavotasan_widgets_init' );
if ( ! function_exists( 'bavotasan_widgets_init' ) ) :
/**
 * Creating the two sidebars
 *
 * This function is attached to the 'widgets_init' action hook.
 *
 * @uses	register_sidebar()
 *
 * @since 3.0.0
 */
function bavotasan_widgets_init() {
	// include the widgets
	include( BAVOTASAN_THEME_TEMPLATE . '/library/widgets/widget_feature.php' );

	register_sidebar( array(
		'name' => __( 'First Sidebar', 'magazine-basic' ),
		'id' => 'sidebar',
		'description' => __( 'This is the first sidebar area. All defaults widgets work great here.', 'magazine-basic' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Second Sidebar', 'magazine-basic' ),
		'id' => 'second-sidebar',
		'description' => __( 'This is the second sidebar area. All defaults widgets work great here. You must select one of the "2 sidebar" layout options in order to view this area on the front end.', 'magazine-basic' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Header Area', 'magazine-basic' ),
		'id' => 'header-area',
		'description' => __( 'Widgetized area in the header to the right of the site name. Great place for a search box or a banner ad.', 'magazine-basic' ),
		'before_widget' => '<aside id="%1$s" class="header-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="header-widget-title">',
		'after_title' => '</h3>',
	) );
}
endif; // bavotasan_widgets_init

if ( ! function_exists( 'bavotasan_pagination' ) ) :
/**
 * Add pagination
 *
 * @uses	paginate_links()
 * @uses	add_query_arg()
 *
 * @since 3.0.0
 */
function bavotasan_pagination() {
	global $wp_query;

	$current = max( 1, get_query_var('paged') );
	$big = 999999999; // need an unlikely integer

	$pagination_return = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => $current,
		'total' => $wp_query->max_num_pages,
		'next_text' => '&raquo;',
		'prev_text' => '&laquo;'
	) );

	if ( ! empty( $pagination_return ) ) {
		echo '<div id="pagination">';
		echo '<div class="total-pages">';
		printf( __( 'Page %1$s of %2$s', 'magazine-basic' ), $current, $wp_query->max_num_pages );
		echo '</div>';
		echo $pagination_return;
		echo '</div>';
	}
}
endif; // bavotasan_pagination

if ( ! function_exists( 'bavotasan_comment' ) ) :
/**
 * Callback function for comments
 *
 * Referenced via wp_list_comments() in comments.php.
 *
 * @uses	get_avatar()
 * @uses	get_comment_author_link()
 * @uses	get_comment_date()
 * @uses	get_comment_time()
 * @uses	edit_comment_link()
 * @uses	comment_text()
 * @uses	comments_open()
 * @uses	comment_reply_link()
 *
 * @since 3.0.0
 */
function bavotasan_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :
		case '' :
		?>
		<li <?php comment_class(); ?>>
			<div id="comment-<?php comment_ID(); ?>" class="comment-body">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 60 ); ?>
				</div>
				<div class="comment-content">
					<div class="comment-author">
						<?php echo get_comment_author_link() . ' '; ?>
					</div>
					<div class="comment-meta">
						<?php
						printf( __( '%1$s at %2$s', 'magazine-basic' ), get_comment_date(), get_comment_time() );
						edit_comment_link( __( '(edit)', 'magazine-basic' ), '  ', '' );
						?>
					</div>
					<div class="comment-text">
						<?php if ( '0' == $comment->comment_approved ) { echo '<em>' . __( 'Your comment is awaiting moderation.', 'magazine-basic' ) . '</em>'; } ?>
						<?php comment_text() ?>
					</div>
					<?php if ( $args['max_depth'] != $depth && comments_open() && 'pingback' != $comment->comment_type ) { ?>
					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php
			break;

		case 'pingback'  :
		case 'trackback' :
		?>
		<li id="comment-<?php comment_ID(); ?>" class="pingback">
			<div class="comment-body">
				<?php _e( 'Pingback:', 'magazine-basic' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(edit)', 'magazine-basic' ), ' ' ); ?>
			</div>
			<?php
			break;
	endswitch;
}
endif; // bavotasan_comment

add_filter( 'excerpt_more', 'bavotasan_excerpt' );
if ( ! function_exists( 'bavotasan_excerpt' ) ) :
/**
 * Adds a read more link to all excerpts
 *
 * This function is attached to the 'excerpt_more' filter hook.
 *
 * @param	int $more
 *
 * @return	Custom excerpt ending
 *
 * @since 3.0.0
 */
function bavotasan_excerpt( $more ) {
	return '&hellip;';
}
endif; // bavotasan_excerpt

add_filter( 'wp_trim_excerpt', 'bavotasan_excerpt_more' );
if ( ! function_exists( 'bavotasan_excerpt_more' ) ) :
/**
 * Adds a read more link to all excerpts
 *
 * This function is attached to the 'wp_trim_excerpt' filter hook.
 *
 * @param	string $text
 *
 * @return	Custom read more link
 *
 * @since 3.0.0
 */
function bavotasan_excerpt_more( $text ) {
	return $text . '<p class="more-link-p"><a class="more-link" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Read more &rarr;', 'magazine-basic' ) . '</a></p>';
}
endif; // bavotasan_excerpt_more

add_filter( 'the_content_more_link', 'bavotasan_content_more_link', 10, 2 );
if ( ! function_exists( 'bavotasan_content_more_link' ) ) :
/**
 * Customize read more link for content
 *
 * This function is attached to the 'the_content_more_link' filter hook.
 *
 * @param	string $link
 * @param	string $text
 *
 * @return	Custom read more link
 *
 * @since 3.0.0
 */
function bavotasan_content_more_link( $link, $text ) {
	return '<p class="more-link-p"><a class="more-link" href="' . get_permalink( get_the_ID() ) . '">' . $text . '</a></p>';
}
endif; // bavotasan_content_more_link

add_filter( 'excerpt_length', 'bavotasan_excerpt_length', 999 );
if ( ! function_exists( 'bavotasan_excerpt_length' ) ) :
/**
 * Custom excerpt length
 *
 * This function is attached to the 'excerpt_length' filter hook.
 *
 * @param	int $length
 *
 * @return	Custom excerpt length
 *
 * @since 3.0.0
 */
function bavotasan_excerpt_length( $length ) {
	return 40;
}
endif; // bavotasan_excerpt_length

/**
 * Create the required attributes for the #primary container
 *
 * @since 3.0.5
 */
function bavotasan_primary_attr() {
	$push = '';

	$bavotasan_theme_options = bavotasan_theme_options();
	$primary = str_replace( 'c', '', $bavotasan_theme_options['primary'] );
	$secondary = ( is_active_sidebar( 'second-sidebar' ) ) ? str_replace( 'c', '', $bavotasan_theme_options['secondary'] ) : 12 - $primary;
	$tertiary = 12 - $primary - $secondary;
	$class = $bavotasan_theme_options['primary'];

	if ( is_active_sidebar( 'second-sidebar' ) ) {
		$push = ( 'left' == $bavotasan_theme_options['layout'] ) ? ' push' . ( $secondary + $tertiary ) : '';
		$push = ( 'separate' == $bavotasan_theme_options['layout'] ) ? ' push' . $secondary: $push;
	} else {
		$class = ( 'left' == $bavotasan_theme_options['layout'] ) ? $class . ' fr' : $class;
	}

	echo 'class="' . esc_attr( $class ) . esc_attr( $push ) . '"';
}

/**
 * Create the required classes for the #secondary sidebar container
 *
 * @since 3.0.5
 */
function bavotasan_sidebar_class() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$primary = str_replace( 'c', '', $bavotasan_theme_options['primary'] );
	$pull = '';

	if ( is_active_sidebar( 'second-sidebar' ) ) {
		$class = $bavotasan_theme_options['secondary'];
		$pull = ( 'right' != $bavotasan_theme_options['layout'] ) ? ' pull' . $primary : '';
	} else {
		$end = ( 'right' == $bavotasan_theme_options['layout'] ) ? ' end' : '';
		$class = 'c' . ( 12 - $primary ) . $end;
	}

	echo 'class="' . esc_attr( $class ) . esc_attr( $pull ) . '"';
}

/**
 * Create the required classes for the #tertiary sidebar container
 *
 * @since 3.0.5
 */
function bavotasan_second_sidebar_class() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$primary = str_replace( 'c', '', $bavotasan_theme_options['primary'] );
	$secondary = str_replace( 'c', '', $bavotasan_theme_options['secondary'] );
	$pull = ( 'left' == $bavotasan_theme_options['layout'] ) ? ' pull' . $primary : '';

	$end = ( 'left' != $bavotasan_theme_options['layout'] ) ? ' end' : '';
	$class = 'c' . ( 12 - $primary - $secondary ) . $end;

	echo 'class="' . esc_attr( $class ) . esc_attr( $pull ) . '"';
}

/**
 * Set up the article class according to layout selection
 *
 * @since 3.0.0
 */
function bavotasan_article_class() {
	global $mb_content_area;
	$bavotasan_theme_options = bavotasan_theme_options();

	$class = ( 'sidebar' == $mb_content_area ) ? 'c12 widget-post' : '';
	if ( is_home() && empty( $class ) ) {
		global $wp_query;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var('paged') : 1;
		$grid = $bavotasan_theme_options['grid'];
		$count = $wp_query->current_post;
		$class = 'c12';
		if ( 'sidebar' != $mb_content_area ) {
			$class = ( ( 2 == $grid || 3 == $grid ) && ( 0 < $count || 1 < $paged ) ) ? 'two-col c6' : $class;
			$class = ( ( 3 == $grid && ( 2 < $count || 1 < $paged ) ) || ( 4 == $grid && ( 0 < $count || 1 < $paged ) ) ) ? 'three-col c4' : $class;
		}
	}

	return $class;
}

/**
 * Add class to sub-menu parent items
 *
 * @author Kirk Wight <http://kwight.ca/adding-a-sub-menu-indicator-to-parent-menu-items/>
 * @since 3.0.3
 */
class Bavotasan_Page_Navigation_Walker extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( !empty( $children_elements[ $element->$id_field ] ) )
            $element->classes[] = 'sub-menu-parent';

        Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }
}

add_filter( 'wp_nav_menu_args', 'bavotasan_nav_menu_args' );
/**
 * Set our new walker only if a menu is assigned and a child theme hasn't modified it to one level deep
 *
 * This function is attached to the 'wp_nav_menu_args' filter hook.
 *
 * @author Kirk Wight <http://kwight.ca/adding-a-sub-menu-indicator-to-parent-menu-items/>
 * @since 3.0.3
 */
function bavotasan_nav_menu_args( $args ) {
    if ( 1 !== $args[ 'depth' ] && has_nav_menu( 'primary' ) && ( 'primary' == $args[ 'theme_location' ] || 'secondary' == $args[ 'theme_location' ] ) )
        $args[ 'walker' ] = new Bavotasan_Page_Navigation_Walker;

    return $args;
}

/**
 * Retrieves the IDs for images in a gallery.
 *
 * @uses get_post_galleries() first, if available. Falls back to shortcode parsing,
 * then as last option uses a get_posts() call.
 *
 * @since 3.0.5
 *
 * @return array List of image IDs from the post gallery.
 */
function bavotasan_get_gallery_images() {
	$images = array();

	if ( function_exists( 'get_post_galleries' ) ) {
		$galleries = get_post_galleries( get_the_ID(), false );
		if ( isset( $galleries[0]['ids'] ) )
		 	$images = explode( ',', $galleries[0]['ids'] );
	} else {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", get_the_content(), $match );
		$atts = shortcode_parse_atts( $match[3] );
		if ( isset( $atts['ids'] ) )
			$images = explode( ',', $atts['ids'] );
	}

	if ( ! $images ) {
		$images = get_posts( array(
			'fields' => 'ids',
			'numberposts' => 999,
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'post_mime_type' => 'image',
			'post_parent' => get_the_ID(),
			'post_type' => 'attachment',
		) );
	}

	return $images;
}