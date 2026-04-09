<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Nathan Rice & Chris Jean for the Flexx theme

Version History
	1.0.1 - 2008-11-07
		Initial Release
	1.0.2 - 2008-11-17
		Added PHP4 object compatibility
	1.0.3 - 2008-11-24
		Fixed background uploader link in order to prevent XSS problems caused
		when the WordPress URL and the Blog URL addresses use different domains.
	1.1.0 - 2009-02-12
		Fixed the Category Menu Builder sorting
		Added Site Name to Page Menu Builder
	1.1.1 - 2009-02-13
		Added favicon uploader
*/


$GLOBALS['wp_theme_name']		= "Flexx Theme";
$GLOBALS['wp_theme_shortname']	= "ft";
$GLOBALS['wp_theme_page_name']	= 'ithemes-flexx-theme';

require_once( 'theme-options-framework.php' );

if ( ! class_exists( 'iThemesThemeOptions' ) && class_exists( 'iThemesThemeOptionsFramework' ) ) {
	class iThemesThemeOptions extends iThemesThemeOptionsFramework {
		function afterLoad() {
			if ( 'default' == $this->_options['background_option'] )
				foreach ( array( 'background_color', 'background_repeat', 'background_image', 'background_attachment', 'background_position', 'default_favicon_image' ) as $option )
					$this->_options[$option] = $this->force_defaults[$option];
		}
		
		function setDefaults() {
			$this->force_defaults['flexx']['above_header'] = 'none';
			$this->force_defaults['flexx']['below_header'] = 'none';
			$this->force_defaults['flexx']['feature_top'] = 'none';
			$this->force_defaults['flexx']['sidebars'] = 'none';
			$this->force_defaults['flexx']['feature_bottom'] = 'none';
			$this->force_defaults['background_option'] = 'default';
			$this->force_defaults['background_color'] = '#e5e5e5';
			$this->force_defaults['background_repeat'] = 'repeat-x';
			$this->force_defaults['background_image'] = $this->_themeURL . '/images/body-bg.gif';
			$this->force_defaults['background_attachment'] = 'scroll';
			$this->force_defaults['background_position'] = 'top left';
			$this->force_defaults['include_pages'] = array( 'home' );
			$this->force_defaults['include_cats'] = array();
			$this->force_defaults['tracking_pos'] = 'footer';
			$this->force_defaults['identify_widget_areas'] = 'yes';
			$this->force_defaults['background_preview_width'] = '400px';
			$this->force_defaults['background_preview_height'] = '400px';
 			$this->force_defaults['favicon_option'] = 'default';
 			$this->force_defaults['default_favicon_image'] = $this->_themeURL . '/images/favicon.ico';
 			$this->force_defualts['favicon_preview_width'] = '16px';
 			$this->force_defaults['favicon_preview_height'] = '16px';
		}
		
		function addScripts() {
			global $wp_scripts;
			
			
			$queue = array();
			
			foreach ( (array) $wp_scripts->queue as $item )
				if ( ! in_array( $item, array( 'page', 'editor', 'editor_functions', 'tiny_mce', 'media-upload', 'post' ) ) )
					$queue[] = $item;
			
			$wp_scripts->queue = $queue;
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'thickbox' );
			
			wp_enqueue_script( $this->_var . '-prototype', $this->_pluginURL . '/js/prototype.js' );
			wp_enqueue_script( $this->_var . '-color-methods', $this->_pluginURL . '/js/colorpicker/ColorMethods.js' );
			wp_enqueue_script( $this->_var . '-color-value-picker', $this->_pluginURL . '/js/colorpicker/ColorValuePicker.js' );
			wp_enqueue_script( $this->_var . '-slider', $this->_pluginURL . '/js/colorpicker/Slider.js' );
			wp_enqueue_script( $this->_var . '-color-picker', $this->_pluginURL . '/js/colorpicker/ColorPicker.js' );
			
			wp_enqueue_script( $this->_var . '-theme-options', $this->_pluginURL . '/js/theme-options.js.php' );
		}
		
		function addStyles() {
			wp_enqueue_style( 'thickbox' );
			
			wp_enqueue_style( $this->_var . '-theme-options', $this->_pluginURL . '/css/theme-options.css' );
		}
		
		function renderForm() {
			
?>
	<tr><th scope="row">Menu Builder</th>
		<td><div>Please select the pages and categories you would like to <strong>INCLUDE</strong> in the Header Menus.</div>
			<table>
				<tr><th style="border:none; padding:0px;"><strong>Pages:</strong></th>
					<th style="border:none; padding:0px 0px 0px 20px;"><strong>Categories:</strong></th>
				</tr>
				<tr><td style="border-bottom:none; vertical-align:top; padding:0px;"><?php $this->createMenuBuilderCheckboxes( 'include_pages', 'pages' ); ?></td>
					<td style="border-bottom:none; vertical-align:top; padding:0px 0px 0px 20px;"><?php $this->createMenuBuilderCheckboxes( 'include_cats', 'categories' ); ?></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr><th scope="row">Identify Widget Areas</th>
		<td><div>This option fills in widget areas with default text to make identifying the different widget areas easier.</div>
			<div>Once you have your layout and widgets set up as desired, we recommend that you change this setting to No.</div>
			<?php $this->_addDropDown( 'identify_widget_areas', array( 'yes' => 'Yes (default)', 'no' => 'No' ) ); ?>
		</td>
	</tr>
	
 	<?php require_once( $GLOBALS['ithemes_theme_path'] . '/lib/file-utility/file-utility.php' ); ?>
 	<?php global $wp_theme_options; ?>
 	<tr><th scope="row">Favicon Image</th> 
 		<td>
 			<div><strong>Select a favicon image</strong></div>
 			<p><?php $this->_addDropDown( 'favicon_option', array( 'default' => 'Default', 'custom_image' => 'Custom Image') ); ?></p>
 			
 			<div id="favicon_image_options">
 				<div>Upload a new image: <a href="<?php echo get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath; ?>/file-upload-handler.php?update=favicon_image:url:attribute:value,favicon_preview:url:css:favicon-image&auth_cookie=<?php echo $_COOKIE[AUTH_COOKIE]; ?>&TB_iframe=true&height=130&width=270" id="upload_button" class="thickbox upload_file">Upload File</a></div>
 				<?php $this->_addHidden( 'favicon_image' ); ?>
 				<?php $this->_addDefaultHidden( 'default_favicon_image' ); ?>
 				
 				<?php $favicon_url = ( ! empty( $wp_theme_options['favicon_image'] ) ) ? $wp_theme_options['favicon_image'] : $wp_theme_options['default_favicon_image']; ?>
 				<?php if ( ! empty( $favicon_url ) ) : ?>
 					<?php $favicon_path = iThemesFileUtility::get_file_from_url( $favicon_url ); ?>
 					<?php if ( ! is_wp_error( $favicon_path ) ) : ?>
 						<?php $thumb = iThemesFileUtility::resize_image( $favicon_path, $this->_options['favicon_preview_width'], $this->_options['favicon_preview_height'], true ); ?>
 						<?php if ( ! is_wp_error( $thumb ) ) : ?>
 							<a href="<?php echo $favicon_url; ?>" target="_blank"><img id="image_thumbnail" src="<?php echo $thumb['url']; ?>" /></a>
 						<?php else : ?>
 							<!-- Favicon image generation error: <?php echo $thumb->get_error_message(); ?> -->
 						<?php endif; ?>
 					<?php else : ?>
 						<!-- Favicon image error: <?php echo $favicon_path->get_error_message(); ?> -->
 					<?php endif; ?>
 				<?php endif; ?>
 			</div>
 		</td>
 	</tr>
 	
	<tr>
		<th scope="row">Site Background</th>
		<td>
			<table>
				<tr>
					<td style="border-bottom:none; vertical-align:top; padding:0px 10px 0px 0px; width:380px;">
						<div><strong>Select a background style</strong></div>
						<p><?php $this->_addDropDown( 'background_option', array( 'default' => 'Default', 'custom_image' => 'Custom Image and Color', 'custom_color' => 'Custom Color Only' ) ); ?></p>
						
						<div id="background_color_options">
							<strong>Background Color:</strong>
							<p><?php $this->_addTextBox( 'background_color' ); ?>&nbsp;<?php $this->_addButton( 'show_background_color_picker', 'Show Picker' ); ?></p>
							<?php $this->_addDefaultHidden( 'background_color' ); ?>
						</div>
						
						<div id="background_image_options">
							<div><strong>Background Image</strong></div>
							
							<p>Upload a new image: <a href="<?php echo get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath; ?>/file-upload-handler.php?update=background_image:url:attribute:value,background_preview:url:css:background-image&auth_cookie=<?php echo $_COOKIE[AUTH_COOKIE]; ?>&TB_iframe=true&height=130&width=270" class="thickbox upload_file">Upload File</a></p>
							<?php $this->_addHidden( 'background_image' ); ?>
							<?php $this->_addDefaultHidden( 'background_image' ); ?>
							<br />
							
							<p>Select an image repeat style:</p>
							<?php $this->_addDropDown( 'background_repeat', array( 'repeat-x' => 'Repeat Horizontally (repeat-x) (default)', 'repeat-y' => 'Repeat Vertically (repeat-y)', 'repeat' => 'Repeat Both (repeat)', 'no-repeat' => 'No Repeat (no-repeat)' ) ); ?>
							<?php $this->_addDefaultHidden( 'background_repeat' ); ?>
							
							<p>Select whether the background image<br />scrolls with the page or is fixed in place:</p>
							<?php $this->_addDropDown( 'background_attachment', array( 'scroll' => 'Scroll with page (default)', 'fixed' => 'Fixed in place' ) ); ?>
							<?php $this->_addDefaultHidden( 'background_attachment' ); ?>
							
							<p>Select the starting position of the background image:</p>
							<?php $this->_addDropDown( 'background_position', array( 'top left' => 'Top Left (default)', 'top center' => 'Top Center', 'top right' => 'Top Right', 'center left' => 'Center Left', 'center center' => 'Center Center', 'center right' => 'Center Right', 'bottom left' => 'Bottom Left', 'bottom center' => 'Bottom Center', 'bottom right' => 'Bottom Right' ) ); ?>
							<?php $this->_addDefaultHidden( 'background_position' ); ?>
						</div>
					</td>
					<td style="border-bottom:none; vertical-align:top; padding:0px;">
						<div>
							<a href="javascript:void(0);" id="show_hide_background_preview">Show Background Preview</a>&nbsp;&nbsp;&nbsp;
							<a href="javascript:void(0);" id="larger_background_preview" style="display:none;">Larger</a>&nbsp;&nbsp;&nbsp;
							<a href="javascript:void(0);" id="smaller_background_preview" style="display:none;">Smaller</a>
						</div>
						
						<div id="background_preview" style="display:none; width:<?php echo $this->_options['background_preview_width']; ?>; height:<?php echo $this->_options['background_preview_height']; ?>;">&nbsp;</div>
						
						<?php $this->_addHidden( 'background_preview_width' ); ?>
						<?php $this->_addHidden( 'background_preview_height' ); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr><th scope="row">Tracking Code</th>
		<td>If you use a tracking service like <a href="http://google.com/analytics">Google Analytics</a>, paste the tracking code in the box below:<br />
			(leave blank for none)<br />
			<?php $this->_addTextArea( 'tracking', array( 'rows' => '3', 'cols' => '50' ) ); ?><br />
			Does your tracking service go in the header or footer of the code?<br />
			<?php $this->_addDropDown( 'tracking_pos', array( 'footer' => 'Footer (default)', 'header' => 'Header' ) ); ?>
		</td>
	</tr>

<?php
		
	}
	
	function afterRenderForm() {
		
?>
	<div id="cp1_ColorPickerWrapper" style="padding:10px; border:1px solid black; position:absolute; z-index:10; background-color:white; display:none;">
		<table><tr>
			<td style="vertical-align:top;"><div id="cp1_ColorMap"></div></td>
			<td style="vertical-align:top;"><div id="cp1_ColorBar"></div></td>
			<td style="vertical-align:top;">
				<table>
					<tr><td colspan="3"><div id="cp1_Preview" style="background-color: #fff; width: 60px; height: 60px; padding: 0; margin: 0; border: solid 1px #000;"><br /></div></td></tr>
					<tr><td><input type="radio" id="cp1_HueRadio" name="cp1_Mode" value="0" /></td><td><label for="cp1_HueRadio">H:</label></td><td><input type="text" id="cp1_Hue" value="0" style="width: 40px;" /> &deg;</td></tr>
					<tr><td><input type="radio" id="cp1_SaturationRadio" name="cp1_Mode" value="1" /></td><td><label for="cp1_SaturationRadio">S:</label></td><td><input type="text" id="cp1_Saturation" value="100" style="width: 40px;" /> %</td></tr>
					<tr><td><input type="radio" id="cp1_BrightnessRadio" name="cp1_Mode" value="2" /></td><td><label for="cp1_BrightnessRadio">B:</label></td><td><input type="text" id="cp1_Brightness" value="100" style="width: 40px;" /> %</td></tr>
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td><input type="radio" id="cp1_RedRadio" name="cp1_Mode" value="r" /></td><td><label for="cp1_RedRadio">R:</label></td><td><input type="text" id="cp1_Red" value="255" style="width: 40px;" /></td></tr>
					<tr><td><input type="radio" id="cp1_GreenRadio" name="cp1_Mode" value="g" /></td><td><label for="cp1_GreenRadio">G:</label></td><td><input type="text" id="cp1_Green" value="0" style="width: 40px;" /></td></tr>
					<tr><td><input type="radio" id="cp1_BlueRadio" name="cp1_Mode" value="b" /></td><td><label for="cp1_BlueRadio">B:</label></td><td><input type="text" id="cp1_Blue" value="0" style="width: 40px;" /></td></tr>
					<tr><td>#:</td><td colspan="2"><input type="text" id="cp1_Hex" value="FF0000" style="width: 60px;" /></td></tr>
				</table>
			</td>
		</tr></table>
		
		<a href="javascript:void(0);" style="float:right;" id="cp1_hide_div">save selection</a>
	</div>
	
	<div style="display:none;">
		<?php
			$images = array( 'rangearrows.gif', 'mappoint.gif', 'bar-saturation.png', 'bar-brightness.png', 'bar-blue-tl.png', 'bar-blue-tr.png', 'bar-blue-bl.png', 'bar-blue-br.png', 'bar-red-tl.png',
				'bar-red-tr.png', 'bar-red-bl.png', 'bar-red-br.png', 'bar-green-tl.png', 'bar-green-tr.png', 'bar-green-bl.png', 'bar-green-br.png', 'map-red-max.png', 'map-red-min.png',
				'map-green-max.png', 'map-green-min.png', 'map-blue-max.png', 'map-blue-min.png', 'map-saturation.png', 'map-saturation-overlay.png', 'map-brightness.png', 'map-hue.png' );
			
			foreach( (array) $images as $image )
				echo '<img src="' . $ithemes_theme_url . '/js/refresh_web/colorpicker/images/' . $image . "\" />\n";
		?>
	</div>
<?php
			
		}
		
		function createMenuBuilderCheckboxes( $var, $type ) {
			if ( empty( $this->_options[$var] ) )
				$this->_options[$var] = array();
			
			$options = array();
			
			if ( 'pages' == $type ) {
				$options['home'] = array( 'title' => 'Home', 'depth' => 0 );
				$options['site_name'] = array( 'title' => 'Site Name', 'depth' => 0 );
				$source_options = get_pages();
			}
			elseif ( 'categories' == $type ) {
				$source_options = array();
				$this->_getSortedHierarchicalCategories( $source_options );
			}
			
			
			foreach ( (array) $source_options as $option ) {
				if ( 'pages' == $type ) {
					$parent = $option->post_parent;
					$title = $option->post_title;
					$id = $option->ID;
				}
				elseif ( 'categories' == $type ) {
					$parent = $option['parent'];
					$title = $option['name'];
					$id = $option['id'];
				}
				
				if ( 0 == $parent )
					$options[$id] = array( 'title' => $title, 'depth' => 0 );
				else
					$options[$id] = array( 'title' => $title, 'depth' => ( $options[$parent]['depth'] + 1 ) );
			}
			
			foreach ( (array) $options as $id => $data ) {
				$attributes = array();
				$attributes['value'] = $id;
				
				if ( in_array( $id, $this->_options[$var] ) )
					$attributes['checked'] = 'checked';
				?>
					<div style="position:relative; left:<?php echo ( $data['depth'] * 15 ); ?>px;"><?php $this->_addMultiCheckBox( $var, $attributes ); ?> <?php echo $data['title']; ?></div>
				<?php
			}
		}
	}
}


if ( empty( $ithemes_theme_options ) )
	$GLOBALS['ithemes_theme_options'] =& new iThemesThemeOptions();

?>
