<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 1.0.4

Version History
	See history.txt
*/


if ( !class_exists( 'FlexxLayoutEditor' ) ) {
	class FlexxLayoutEditor {
		var $_var = 'flexx';
		var $_name = 'Flexx Layout Editor';
		var $_page = 'flexx-layout-editor';
		var $_version = '1.0.4';
		
		var $_initialized = false;
		var $_class = '';
		var $_options = array();
		var $_userID = 0;
		var $_usedInputs = array();
		var $_selectedVars = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		
		
		
		function FlexxLayoutEditor() {
			add_action( 'admin_head', array( &$this, 'addHeader') );
			add_action( 'admin_menu', array( &$this, 'addPages' ) );
		}
		
		function addPages() {
			global $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				add_submenu_page( $wp_theme_page_name, $this->_name, 'Flexx Layout', 'edit_themes', $this->_page, array( &$this, 'mainPage' ) );
			else
				add_theme_page( $this->_name, 'Flexx Layout', 'edit_themes', $this->_page, array( &$this, 'mainPage' ) );
		}
		
		function addHeader() {
			if ( $this->_page == $_GET['page'] ) {
				$this->_setVars();
				
				echo '<link type="text/css" rel="stylesheet" href="' . $this->_pluginURL . '/css/style.css" />' . "\n";
				echo '<script type="text/javascript" src="' . $this->_pluginURL . '/js/flexx.js"></script>' . "\n";
			}
		}
		
		function _setVars() {
			$this->_class = get_class( $this );
			
			$user = wp_get_current_user();
			$this->_userID = $user->ID;
			
			
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
		}
		
		
		// Pages //////////////////////////////////////
		
		function mainPage() {
			global $wp_theme_options, $wp_theme_shortname;
			
			
			if ( empty( $wp_theme_shortname ) ) {
				$this->_showErrorMessage( "Flexx Layout can only be used with themes provided by iThemes." );
				$this->_showErrorMessage( "If you are using a iThemes' theme, please make sure that the theme folder contains the original functions.php file." );
				
				exit;
			}
			
			if ( ! empty( $_POST['save'] ) ) {
				$wp_theme_options[$this->_var]['above_header'] = $_POST['above_header'];
				$wp_theme_options[$this->_var]['below_header'] = $_POST['below_header'];
				$wp_theme_options[$this->_var]['feature_top'] = $_POST['feature_top'];
				$wp_theme_options[$this->_var]['feature_bottom'] = $_POST['feature_bottom'];
				$wp_theme_options[$this->_var]['sidebars'] = $_POST['sidebars'];
				
				update_option( $wp_theme_shortname . '-options', $wp_theme_options );
				
				$this->_showStatusMessage( "Layout options updated." );
			}
			
			
			$above_header_options = array();
			$above_header_options['none'] = 'none';
			$above_header_options['pages'] = 'pages';
			$above_header_options['categories'] = 'categories';
			$above_header_options['pages_categories'] = 'pages & categories';
			$above_header_options['categories_pages'] = 'categories & pages';
			
			$below_header_options = array();
			$below_header_options['none'] = 'none';
			$below_header_options['pages'] = 'pages';
			$below_header_options['categories'] = 'categories';
			$below_header_options['pages_categories'] = 'pages & categories';
			$below_header_options['categories_pages'] = 'categories & pages';
			
			$feature_top_options = array();
			$feature_top_options['none'] = 'none';
			$feature_top_options['1'] = '1';
			$feature_top_options['2'] = '2';
			$feature_top_options['3'] = '3';
			
			$feature_bottom_options = array();
			$feature_bottom_options['none'] = 'none';
			$feature_bottom_options['1'] = '1';
			$feature_bottom_options['2'] = '2';
			$feature_bottom_options['3'] = '3';
			
			$sidebar_options = array();
			$sidebar_options['none'] = 'none';
			$sidebar_options['1_left'] = '1_left';
			$sidebar_options['2_left'] = '2_left';
			$sidebar_options['split'] = 'split';
			$sidebar_options['1_right'] = '1_right';
			$sidebar_options['2_right'] = '2_right';
			
?>
	<div class="wrap">
		<h2><?php _e( 'Flexx Layout', $this->_var ); ?></h2>
		
		<form enctype="multipart/form-data" method="post" action="<?php echo $this->_getBackLink() ?>">
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
			<table class="form-table">
				<?php $this->_addRadioOptionGroup( 'Above Header', 'above_header', $above_header_options ); ?>
				<?php $this->_addRadioOptionGroup( 'Below Header', 'below_header', $below_header_options ); ?>
				<?php $this->_addRadioOptionGroup( 'Feature Top', 'feature_top', $feature_top_options ); ?>
				<?php $this->_addRadioOptionGroup( 'Sidebars', 'sidebars', $sidebar_options ); ?>
				<?php $this->_addRadioOptionGroup( 'Feature Bottom', 'feature_bottom', $feature_bottom_options ); ?>
				<tr>
					<td>
						<h3>Layout Preview</h3>
						<?php $this->_addPreviewGroup( 'above_header' ); ?>
						<?php $this->_addPreviewGroup( 'below_header' ); ?>
						<?php $this->_addPreviewGroup( 'feature_top' ); ?>
						<?php $this->_addPreviewGroup( 'sidebars' ); ?>
						<?php $this->_addPreviewGroup( 'feature_bottom' ); ?>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input class="button-primary" type="submit" name="save" value="Save Layout" />
			</p>
		</form>
	</div>
<?php
		}
		
		
		// Plugin Functions ///////////////////////////
		
		function _addPreviewGroup( $var ) {
			global $wp_theme_options;
			
			
			echo '<div id="' . $var . '_flexx_preview" class="flexx_preview">';
			
			if ( file_exists( $this->_pluginPath . '/images/' . $var . '_' . $wp_theme_options[$this->_var][$var] . '.gif' ) )
				echo '<img src="' . $this->_pluginURL .  '/images/' . $var . '_' . $wp_theme_options[$this->_var][$var] . '.gif" />';
			
			echo "</div>\n";
		}
		
		function _addRadioOptionGroup( $name, $var, $options, $images = false ) {
			global $wp_theme_options;
			
			
			if ( ! empty( $wp_theme_options[$this->_var][$var] ) )
				$default = $wp_theme_options[$this->_var][$var];
			
			echo "<tr><td>\n";
			echo "<h3>$name</h3>\n";
			
			foreach ( (array) $options as $value => $name ) {
				if ( empty( $default ) )
					$default = $value;
				
				if ( $default == $value )
					$class = 'flexx-option-selected';
				else
					$class = 'flexx-option-not-selected';
				
				echo '<div id="' . $value . '" class="flexx-option ' . $class . '">';
				
				if ( file_exists( $this->_pluginPath . "/images/${var}_$value.gif" ) )
					echo '<img src="' . $this->_pluginURL . "/images/${var}_$value.gif" . '" />';
				else
					echo $name;
				
				echo "</div>\n";
			}
			
			echo '<br style="clear:both;" />' . "\n";
			echo '<input type="hidden" class="flexx-input" name="' . $var . '" value="' . $default . '" />' . "\n";
			echo "</td></tr>\n";
		}
		
		function _getBackLink() {
			return $_SERVER['REQUEST_URI'];
		}
		
		function _showStatusMessage( $message ) {
			
?>
	<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
<?php
			
		}
		
		function _showErrorMessage( $message ) {
			
?>
	<div id="message" class="error"><p><strong><?php echo $message; ?></strong></p></div>
<?php
			
		}
	}
}


if ( class_exists( 'FlexxLayoutEditor' ) ) {
	$FlexxLayoutEditor =& new FlexxLayoutEditor();
}

?>
