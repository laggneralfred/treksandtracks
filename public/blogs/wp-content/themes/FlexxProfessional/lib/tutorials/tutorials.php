<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.4

Version History
	See history.txt
*/


if ( ! class_exists( 'iThemesTutorials' ) ) {
	class iThemesTutorials {
		var $_var = 'ithemes-tutorials';
		var $_name = 'iThemes Tutorials';
		var $_version = '1.1.4';
		var $_page = 'ithemes-tutorials';
		
		var $_pluginPath = '';
		var $_pluginRelativePage = '';
		var $_pluginURL = '';
		var $_pageRef = '';
		
		
		function iThemesTutorials() {
			global $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				$this->_page = $wp_theme_page_name;
			
			
			$this->_setVars();
			
			add_action( 'admin_menu', array( &$this, 'addPages' ), -10 );
		}
		
		function addPages() {
			$GLOBALS['wp_theme_name'] = apply_filters( 'it_tutorials_top_menu_name', 'My Theme' );
			
			global $wp_theme_name, $wp_theme_page_name, $wp_version;
			
			$tutorial_menu_name = 'Start Here';
			$tutorial_menu_name = apply_filters( 'it_tutorials_menu_name', $tutorial_menu_name );
			
			if ( ! empty( $wp_theme_page_name ) ) {
				global $menu;
				
				
				$this->_pageRef = add_menu_page( $tutorial_menu_name, $wp_theme_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
				add_submenu_page( $this->_page, $tutorial_menu_name, $tutorial_menu_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
				
				
				if ( version_compare( $wp_version, '2.6.9', '>' ) ) {
					$separator = array( '', 'read', '', '', 'wp-menu-separator' );
					$appearance_index = false;
					
					foreach ( (array) $menu as $menu_item ) {
						if ( empty( $menu_item[0] ) ) {
							$separator = $menu_item;
							break;
						}
					}
					
					$new_menu_item = array_pop( $menu );
					
					
					$last_index = 0;
					$gaps = array();
					
					ksort( $menu );
					
					foreach ( (array) $menu as $index => $menu_item ) {
						if ( ( 'themes.php' === $menu_item[2] ) && ( false === $appearance_index ) )
							$appearance_index = $index;
						
						if ( ! empty( $menu[$last_index][0] ) && empty( $menu[$index][0] ) ) {
							if ( ( $index - $last_index ) > 2 )
								$gaps[$index] = array( $last_index, $menu[$last_index][0], $index, $menu[$index][0] );
						}
						
						$last_index = $index;
					}
					
					$position = false;
					
					if ( ! empty( $gaps[$appearance_index - 1] ) )
						$position = $appearance_index - 3;
					else if ( ! empty( $gaps ) ) {
						reset( $gaps );
						
						$gap = each( $gaps );
						$position = $gap['key'] - 2;
					}
					else {
						array_reverse( $menu );
						
						$last_separator_index = false;
						$last_item_index = false;
						
						foreach ( (array) $menu as $index => $menu_item ) {
							if ( false === $last_separator_index ) {
								if ( 'wp-menu-separator-last' === $menu_item[4] )
									$last_separator_index = $index;
							}
							else {
								$last_item_index = $index;
								break;
							}
						}
						
						if ( ( false !== $last_separator_index ) && ( ( $last_separator_index - $last_item_index ) > 2 ) )
							$position = $last_separator_index - 2;
					}
					
					if ( false === $position )
						$menu[] = $new_menu_item;
					else {
						$menu[$position] = $separator;
						$menu[$position + 1] = $new_menu_item;
					}
					
					ksort( $menu );
					reset( $menu );
				}
			}
			else
				$this->_pageRef = add_theme_page( "$wp_theme_name Start Here", "$wp_theme_name Start Here", 'edit_themes', $this->_page, array( &$this, 'index' ) );
			
			
			add_action( 'admin_print_scripts-' . $this->_pageRef, array( $this, 'addScripts' ) );
			add_action( 'admin_print_styles-' . $this->_pageRef, array( $this, 'addStyles' ) );
		}
		
		function addScripts() {
			wp_enqueue_script( $this->_var . '-dw_viewport', $this->_pluginURL . '/js/dw_viewport.js' );
			wp_enqueue_script( $this->_var . '-tutorials', $this->_pluginURL . '/js/tutorials.js' );
		}
		
		function addStyles() {
			wp_enqueue_style( $this->_var . '-tutorials', $this->_pluginURL . '/css/tutorials.css' );
		}
		
		function _setVars() {
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			$filter_url = 'http://ithemes.com/tv/index.html';
			$filter_url = apply_filters( 'it_tutorials_filter_url', $filter_url );
			
?>
	<div style="text-align:center;" id="tutorial_frame_container">
		<iframe name="tutorials" id="tutorial_frame" src="<?php echo $filter_url ?>" frameborder="0"></iframe>
	</div>
<?php
			
		}
	}
}

global $ithemes_tutorials;
$ithemes_tutorials =& new iThemesTutorials();

?>
