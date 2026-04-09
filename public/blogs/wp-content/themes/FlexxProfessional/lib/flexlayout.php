<?php
add_action( 'ithemes_init', 'ithemes_flexx_layout_loader' );

function ithemes_flexx_layout_loader() {
	global $wp_theme_options, $container_width, $content_width, $feature_top_width, $feature_bottom_width;
	
	foreach ( (array) $wp_theme_options['flexx'] as $name => $val )
		$GLOBALS[$name] = $val;
	
	extract( (array) $wp_theme_options['flexx'] );
	
	//How wide should the container be?
	if(preg_match('/^(2_right|2_left|split)$/', $sidebars)) $container_width = 960;
	else $container_width = 780;
	$container_width = intval( $container_width ); //just in case something screws up
	
	//How wide should the content be?
	$content_width = ($sidebars != 'none') ? 600 : 780;
	
	//How wide should each of the boxes in
	//the feature_top/feature_bottom sections be?
	if ( ! empty( $feature_top ) && ( $feature_top != 'none' ) )
		$feature_top_width = ( $container_width / $feature_top );
	if ( ! empty( $feature_bottom ) && ( $feature_bottom != 'none' ) )
		$feature_bottom_width = ( $container_width / $feature_bottom );
	
	//The container width
	add_action( 'container_style', 'flexx_container_style' );
	function flexx_container_style() {
		global $container_width;
		echo 'c' . $container_width . ' center wrap';
	}
	
	//The content width
	add_action('content_style', 'flexx_content_style');
	function flexx_content_style() {
		global $content_width;
		echo 'w'.$content_width.'-';
	}
	
	//What menu goes above the header (if any)
	add_action('above_header', 'flexx_menu_above_header');
	function flexx_menu_above_header() {
		global $above_header;
		if($above_header != 'none') echo '<div id="above-header">';
		if($above_header == 'pages') { get_template_file('menu-pages.php'); }
		if($above_header == 'categories') { get_template_file('menu-cats.php'); }
		if($above_header == 'pages_categories') { get_template_file('menu-pages.php'); get_template_file('menu-cats.php'); }
		if($above_header == 'categories_pages') { get_template_file('menu-cats.php'); get_template_file('menu-pages.php'); }
		if($above_header != 'none') echo '</div>';
	}
	
	//What menu goes below the header (if any)
	add_action('below_header', 'flexx_menu_below_header');
	function flexx_menu_below_header() {
		global $below_header;
		if($below_header != 'none') echo '<div id="below-header">';
		if($below_header == 'pages') { get_template_file('menu-pages.php'); }
		if($below_header == 'categories') { get_template_file('menu-cats.php'); }
		if($below_header == 'pages_categories') { get_template_file('menu-pages.php'); get_template_file('menu-cats.php'); }
		if($below_header == 'categories_pages') { get_template_file('menu-cats.php'); get_template_file('menu-pages.php'); }
		if($below_header != 'none') echo '</div>';
	}
	
	//Feature Top
	add_action('below_header', 'flexx_feature_top', 11);
	function flexx_feature_top() {
		global $feature_top;
		if ( ! empty( $feature_top ) && ( $feature_top != 'none' ) ) { 
			get_template_file('feature-top.php'); } else { return; }
	}
	//Feature Top Style
	add_action('feature_top_style','flexx_feature_top_style');
	function flexx_feature_top_style() {
		global $feature_top_width;
		if($feature_top_width) echo 'w'.$feature_top_width.'-';
	}
	
	//Feature Bottom
	add_action('above_footer', 'flexx_feature_bottom');
	function flexx_feature_bottom() {
		global $feature_bottom;
		if ( ! empty( $feature_bottom ) && ( $feature_bottom != 'none' ) ) { 
			get_template_file('feature-bottom.php'); } else { return; }
	}
	//Feature Bottom Style
	add_action('feature_bottom_style','flexx_feature_bottom_style');
	function flexx_feature_bottom_style() {
		global $feature_bottom_width;
		if($feature_bottom_width) echo 'w'.$feature_bottom_width.'-';
	}
	
	//Sidebars
	add_action('before_content','flexx_before_content');
	function flexx_before_content() {
		global $sidebars;
		if($sidebars == '2_left') get_sidebar();
		if(preg_match('/^(1_left|split)$/', $sidebars)) get_sidebar('left');
	}
	add_action('after_content','flexx_after_content');
	function flexx_after_content() {
		global $sidebars;
		if($sidebars == '2_right') get_sidebar();
		if(preg_match('/^(1_right|split)$/', $sidebars)) get_sidebar('right');
	}
	
	//Register Widget Areas (conditionally)
	if ( function_exists('register_sidebar') ) {
		if(preg_match('/^(1_left|2_right|2_left|split)$/', $sidebars)) {
		register_sidebar(array('name'=>'Skinny Left Sidebar','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		
		if(preg_match('/^(1_right|2_right|2_left|split)$/', $sidebars)) {
		register_sidebar(array('name'=>'Skinny Right Sidebar','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		
		if(preg_match('/^(2_right|2_left)$/', $sidebars)) {
		register_sidebar(array('name'=>'Wide Sidebar Bottom','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
		register_sidebar(array('name'=>'Wide Sidebar Top','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		
		if(preg_match('/^(2|3)$/', $feature_top)) {
		register_sidebar(array('name'=>'Feature Top Left','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
		if($feature_top == 3) {
		register_sidebar(array('name'=>'Feature Top Middle','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		register_sidebar(array('name'=>'Feature Top Right','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		if($feature_top == 1) {
		register_sidebar(array('name'=>'Feature Top','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		
		if(preg_match('/^(2|3)$/', $feature_bottom)) {
		register_sidebar(array('name'=>'Feature Bottom Left','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',));
		if($feature_bottom == 3) {
		register_sidebar(array('name'=>'Feature Bottom Middle','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		register_sidebar(array('name'=>'Feature Bottom Right','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
		if($feature_bottom == 1) {
		register_sidebar(array('name'=>'Feature Bottom','before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h4>','after_title' => '</h4>',)); }
	}
}
?>