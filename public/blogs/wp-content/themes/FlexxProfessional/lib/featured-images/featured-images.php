<?php

/*
Written by Chris Jean for iThemes.com
Version 1.2.6

Version History
	See history.txt
*/


if ( ! class_exists( 'iThemesFeaturedImages' ) ) {
	class iThemesFeaturedImages {
		var $_var = 'ithemes_featured_images';
		var $_name = 'Featured Images';
		var $_page = 'ithemes-featured-images';
		
		var $_defaults = array(
			'id_name'		=> 'featured-image-fade',
			'width'			=> '100',
			'height'		=> '100',
			'sleep'			=> '2',
			'fade'			=> '1',
			'image_ids'		=> array(),
			'fade_sort'		=> 'ordered',
			'enable_fade'	=> '1',
			'link'			=> '',
			'enable_overlay'=> '1',
			'overlay_text_alignment'	=> 'center',
			'overlay_text_vertical_position'	=> 'middle',
			'overlay_text_padding'		=> '10',
			'overlay_header_text'		=> '',
			'overlay_header_size'		=> '36',
			'overlay_header_color'		=> '#FFFFFF',
			'overlay_subheader_text'	=> '',
			'overlay_subheader_size'	=> '18',
			'overlay_subheader_color'	=> '#FFFFFF'
		);
		
		var $_text_overlay = true;
		
		var $_width = '';
		var $_height = '';
		var $_sleep = '';
		var $_fade = '';
		
		var $_options = array();
		
		var $_class = '';
		var $_initialized = false;
		
		var $_userID = 0;
		var $_usedInputs = array();
		var $_selectedVars = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		var $_pageRef = '';
		
		
		function iThemesFeaturedImages( $options ) {
			$this->_defaults['link'] = get_option( 'home' );
			$this->_defaults['overlay_header_text'] = get_bloginfo( 'name' );
			$this->_defaults['overlay_subheader_text'] = get_bloginfo( 'description' );
			
			foreach ( (array) $options as $name => $val )
				$this->_defaults[$name] = $val;
			
			$this->_setVars();
			
			add_action( 'wp_head', array( &$this, 'initImages' ) );
			add_action( 'wp_print_scripts', array( &$this, 'addScripts' ) );
			add_action( 'ithemes_set_defaults', array( &$this, 'setDefaults' ) );
			add_action( 'ithemes_init', array( &$this, 'init' ) );
			add_action( 'admin_menu', array( &$this, 'addPages' ) );
		}
		
		function init() {
			$this->_load();
			
			$this->_text_overlay = apply_filters( 'it_featured_images_text_overlay', $this->_text_overlay );
		}
		
		function addPages() {
			global $wp_theme_name, $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				$this->_pageRef = add_submenu_page( $wp_theme_page_name, $this->_name, $this->_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
			else
				$this->_pageRef = add_theme_page( $wp_theme_name . ' ' . $this->_name, $wp_theme_name . ' ' . $this->_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
			
			add_action( 'admin_print_scripts-' . $this->_pageRef, array( $this, 'addAdminScripts' ) );
			add_action( 'admin_print_styles-' . $this->_pageRef, array( $this, 'addAdminStyles' ) );
		}
		
		function addScripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-cross-slide', $this->_pluginURL . '/js/jquery.cross-slide.js' );
		}
		
		function addAdminStyles() {
			wp_enqueue_style( 'thickbox' );
			
			wp_enqueue_style( $this->_var . '-featured-images', $this->_pluginURL . '/css/style.css' );
		}
		
		function addAdminScripts() {
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
			
			wp_enqueue_script( $this->_var . '-toolkit', $this->_pluginURL . '/js/javascript-toolbox-toolkit.js' );
			wp_enqueue_script( $this->_var . '-featured-images', $this->_pluginURL . '/js/featured-images.js.php' );
		}
		
		function initImages() {
			$this->_fadeImages();
		}
		
		function _setVars() {
			$this->_class = get_class( $this );
			
			$user = wp_get_current_user();
			$this->_userID = $user->ID;
			
			
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
			
			// Double parenthesis added around array_shift argument to fix PHP 4.4/5.0.5 bug
			// http://the-stickman.com/web-development/php/php-505-fatal-error-only-variables-can-be-passed-by-reference/
			$this->_selfLink = array_shift( ( explode( '?', $_SERVER['REQUEST_URI'] ) ) ) . '?page=' . $_REQUEST['page'];
		}
		
		
		// Options Storage ////////////////////////////
		
		function setDefaults() {
			global $ithemes_theme_options;
			
			if ( is_array( $ithemes_theme_options->_options[$this->_var] ) && ! isset( $ithemes_theme_options->_options[$this->_var]['enable_overlay'] ) )
				$this->_defaults['enable_overlay'] = '';
			
			$ithemes_theme_options->force_defaults[$this->_var] = $this->_defaults;
		}
		
		function _save() {
			do_action( 'ithemes_save', $this->_var, $this->_options );
			
			return true;
		}
		
		function _load() {
			global $ithemes_theme_options;
			
			
			$this->_options = $ithemes_theme_options->_options[$this->_var];
			
			$this->_options['sleep'] = floatval( $this->_options['sleep'] );
			$this->_options['fade'] = floatval( $this->_options['fade'] );
			
			if ( $this->_options['sleep'] <= 0 )
				$this->_options['sleep'] = $this->_defaults['sleep'];
			if ( $this->_options['fade'] <= 0 )
				$this->_options['fade'] = $this->_defaults['fade'];
			if ( empty( $this->_options['fade_sort'] ) )
				$this->_options['fade_sort'] = 'ordered';
			
			foreach ( array( 'width', 'height', 'sleep', 'fade' ) as $option )
				if ( ! is_numeric( $this->_defaults[$option] ) )
					$this->_options[$option] = $GLOBALS[$this->_defaults[$option]];
				elseif ( ( empty( $this->_options[$option] ) ) && ( '0' !== $this->_options[$option] ) )
					$this->_options[$option] = $this->_defaults[$option];
			
			$this->_options['id_name'] = $this->_defaults['id_name'];
			
			$this->_save();
			
			
			$variable_width = apply_filters( 'it_featured_images_filter_allow_variable_width', false );
			
			if ( ( false === $variable_width ) && ( is_numeric( $this->_defaults['width'] ) ) )
				$this->_options['width'] = $this->_defaults['width'];
			
			if ( empty( $this->_options['image_ids'] ) )
				$this->_initializeImages();
			
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			if ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_themes' ) )
				die( __( 'Cheatin uh?' ) );
			
			
			$action = $_REQUEST['action'];
			
			if ( 'save' === $action )
				$this->_saveForm();
			elseif ( 'upload' === $action )
				$this->_uploadImage();
			elseif ( 'delete' === $action )
				$this->_deleteImage();
			
			$this->_showForm();
		}
		
		function _saveForm() {
			check_admin_referer( $this->_var . '-nonce' );
			
			
			foreach ( (array) explode( ',', $_POST['used-inputs'] ) as $name ) {
				$is_array = ( preg_match( '/\[\]$/', $name ) ) ? true : false;
				
				$name = str_replace( '[]', '', $name );
				$var_name = preg_replace( '/^' . $this->_var . '-/', '', $name );
				
				if ( $is_array && empty( $_POST[$name] ) )
					$_POST[$name] = array();
				
				if ( ! is_array( $_POST[$name] ) )
					$this->_options[$var_name] = stripslashes( $_POST[$name] );
				else
					$this->_options[$var_name] = $_POST[$name];
			}
			
			
			$errorCount = 0;
			
			if ( ( $this->_options['sleep'] != floatval( $this->_options['sleep'] ) ) || ( floatval( $this->_options['sleep'] ) <= 0 ) )
				$errorCount++;
			if ( ( $this->_options['fade'] != floatval( $this->_options['fade'] ) ) || ( floatval( $this->_options['fade'] ) <= 0 ) )
				$errorCount++;
			if ( ( $this->_options['height'] != intval( $this->_options['height'] ) ) || ( intval( $this->_options['height'] ) < 0 ) )
				$errorCount++;
			
			if ( $errorCount < 1 ) {
				$this->_options['sleep'] = floatval( $this->_options['sleep'] );
				$this->_options['fade'] = floatval( $this->_options['fade'] );
				
				if ( $this->_options['sleep'] <= 0 )
					$this->_options['sleep'] = $this->_defaults['sleep'];
				if ( $this->_options['fade'] <= 0 )
					$this->_options['fade'] = $this->_defaults['fade'];
				if ( empty( $this->_options['fade_sort'] ) )
					$this->_options['fade_sort'] = 'ordered';
				
				foreach ( array( 'width', 'height', 'sleep', 'fade' ) as $option )
					if ( ! is_numeric( $this->_defaults[$option] ) )
						$this->_options[$option] = $GLOBALS[$this->_defaults[$option]];
					elseif ( ( empty( $this->_options[$option] ) ) && ( '0' !== $this->_options[$option] ) )
						$this->_options[$option] = $this->_defaults[$option];
				
				$this->_options['id_name'] = $this->_defaults['id_name'];
				
				if ( $this->_save() )
					$this->_showStatusMessage( __( 'Settings updated', $this->_var ) );
				else
					$this->_showErrorMessage( __( 'Error while updating settings', $this->_var ) );
			}
			else {
				$this->_showErrorMessage( __( 'The fade options timing values must be numeric values greater than 0.', $this->_var ) );
				
				$this->_showErrorMessage( __ngettext( 'Please fix the input marked in red below.', 'Please fix the inputs marked in red below.', $errorCount ) );
			}
		}
		
		function _uploadImage() {
			if ( is_array( $_FILES['image_file'] ) && ( 0 === $_FILES['image_file']['error'] ) ) {
				require_once( TEMPLATEPATH . '/lib/file-utility/file-utility.php' );
				
				check_admin_referer( $this->_var . '-nonce' );
				
				$file = iThemesFileUtility::uploadFile( 'image_file' );
				
				if ( is_wp_error( $file ) )
					$this->_showErrorMessage( 'Unable to save uploaded image. Ensure that the web server has permissions to write to the uploads folder' );
				else {
					$this->_options['image_ids'][] = $file['id'];
					$this->_save();
				}
			}
			else
				$this->_showErrorMessage( 'You must add a file by clicking the browse button first.' );
		}
		
		function _deleteImage() {
			wp_delete_attachment( $_GET['delete'] );
			
			$ids = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id )
				if ( $id != $_GET['delete'] )
					$ids[] = $id;
			
			
			if ( empty( $ids ) )
				$this->_initializeImages();
			else {
				$this->_options['image_ids'] = $ids;
				$this->_save();
			}
		}
		
		function _showForm() {
			$variable_height = apply_filters( 'it_featured_images_filter_allow_variable_height', true );
			
			if ( false === $variable_height )
				$display_height = $this->_defaults['height'];
			
?>
	<div class="wrap">
		<h2><?php _e( 'Images', $this->_var ); ?></h2>
		<br />
		<div>The uploaded image should be <?php echo $this->_options['width'] . 'x' . $display_height; ?> (<?php echo $this->_options['width']; ?>px wide by <?php echo $display_height; ?>px high).</div>
		<div>Images not matching the exact size will be resized and cropped to fit upon display.</div>
		
		<table class="form-table">
<?php
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = array();
					$file['ID'] = $id;
					$file['file'] = get_attached_file( $id );
					$file['url'] = wp_get_attachment_url( $id );
					$file['title'] = $post->post_title;
					$file['name'] = basename( get_attached_file( $id ) );
					
					$files[] = $file;
				}
			}
			
			usort( $files, array( &$this, _sortFiles ) );
			
			
			require_once( TEMPLATEPATH . '/lib/file-utility/file-utility.php' );
			
?>
			<?php foreach ( (array) $files as $file ) : ?>
				<?php $thumb = iThemesFileUtility::resize_image( $file['file'], 100, 100, false ); ?>
				<tr>
					<th scope="row">
						<a href="<?php echo $file['url']; ?>" target="imagePreview">
							<?php if ( ! is_wp_error( $thumb ) ) : ?>
								<img src="<?php echo $thumb['url']; ?>" alt="<?php echo $file['name']; ?>" />
							<?php else : ?>
								Thumbnail generation error: <?php echo $thumb->get_error_message(); ?>
							<?php endif; ?>
						</a>
					</th>
					<td style="vertical-align:top;">
						<div><?php echo $file['name']; ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->_selfLink; ?>&amp;action=delete&amp;delete=<?php echo urlencode( $file['ID'] ); ?>" onclick="if(!confirm('Are you sure you want to delete <?php echo $file['name']; ?>?')) return false;">Delete</a></div>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<tr>
				<th scope="row">Add a new image</th>
				<td>
					<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>">
						<?php echo wp_nonce_field( $this->_var . '-nonce' ); ?>
						<?php $this->_addHiddenNoSave( 'action', 'upload' ); ?>
						<div>Select a file: <?php $this->_addFileUpload( 'image_file' ); ?>&nbsp;&nbsp;&nbsp;<?php $this->_addSubmit( 'upload', 'Upload' ); ?></div>
					</form>
				</td>
			</tr>
		</table>
		<br /><br />
		
		<h2><?php _e( 'Settings', $this->_var ); ?></h2>
		<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>">
			<?php if ( false === $variable_height ) : ?>
				<?php $this->_addHidden( 'height', $this->_options['height'] ); ?>
			<?php endif; ?>
			
			<table class="form-table">
				<?php if ( false !== $variable_height ) : ?>
					<tr>
						<th scope="row">Featured&nbsp;Images&nbsp;Height</th>
						<td>
							<table>
								<tr>
									<td>Height in pixels:</td>
									<?php if ( ( ! empty( $_POST['save'] ) ) && ( intval( $_POST[$this->_var . '-height'] ) < 0 ) ) : ?>
										<td style="background-color:red;">
									<?php else: ?>
										<td>
									<?php endif; ?>
										<?php $this->_addTextBox( 'height', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<?php endif; ?>
				
				<tr>
					<th scope="row">Featured&nbsp;Image&nbsp;Link</th>
					<td>
						<?php $this->_addTextBox( 'link', array( 'size' => '70' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">Fade Animation</th>
					<td>
						<div>The fade animation will show each of the images with a smooth fade transition between each image.</div>
						<div>If the animation is disabled, a single random image will be shown.</div>
						<br />
						
						<?php $this->_addCheckBox( 'enable_fade', '1' ); ?> Enable Fade
					</td>
				</tr>
				<tr id="fade-options">
					<th scope="row">Fade Options</th>
					<td>
						<div>The following options control the fade animation.</div>
						<div>If the animation is disabled, these options will not make any effect.</div>
						<br />
						
						<div>Choose an image sort order: <?php $this->_addDropDown( 'fade_sort', array( 'ordered' => 'Alphabetical by file name (default)', 'random' => 'Random' ) ); ?></div>
						<br />
						
						<table>
							<tr>
								<td>Length of time to display each image in seconds</td>
								<?php if ( ( ! empty( $_POST['save'] ) ) && ( floatval( $_POST[$this->_var . '-sleep'] ) <= 0 ) ) : ?>
									<td style="background-color:red;">
								<?php else: ?>
									<td>
								<?php endif; ?>
									<?php $this->_addTextBox( 'sleep', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
								</td>
							</tr>
							<tr>
								<td>
									Length of time to fade each image in seconds
								</td>
								<?php if ( ( ! empty( $_POST['save'] ) ) && ( floatval( $_POST[$this->_var . '-fade'] ) <= 0 ) ) : ?>
									<td style="background-color:red;">
								<?php else: ?>
									<td>
								<?php endif; ?>
									<?php $this->_addTextBox( 'fade', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				
				<?php if( true === $this->_text_overlay ) : ?>
					<tr id="text-overlay">
						<th scope="row">Text Overlay</th>
						<td>
							<div>Use this feature to overlay custom text on top of featured image(s).</div>
							<br />
							
							<div><?php $this->_addCheckBox( 'enable_overlay', '1' ); ?> Enable Text Overlay</div>
						</td>
					</tr>
					<tr id="text-overlay-options">
						<th scope="row">Text Overlay Options</th>
						<td>
							<table>
								<tr><td>Text Horizontal Alignment:</td>
									<td><?php $this->_addDropDown( 'overlay_text_alignment', array( 'center' => 'Center (default)', 'left' => 'Left', 'right' => 'Right' ) ); ?></td>
								</tr>
								<tr><td>Text Vertical Position:</td>
									<td><?php $this->_addDropDown( 'overlay_text_vertical_position', array( 'bottom' => 'Bottom', 'middle' => 'Middle (default)', 'top' => 'Top' ) ); ?></td>
								</tr>
								<tr><td>Text Padding in Pixels:</td>
									<td><?php $this->_addTextBox( 'overlay_text_padding', array( 'size' => '4' ) ); ?></td>
								</tr>
							</table>
							
							<h3>Header Text</h3>
							<table>
								<tr><td>Text:</td>
									<td><?php $this->_addTextBox( 'overlay_header_text', array( 'size' => '40' ) ); ?></td>
								</tr>
								<tr><td>Size in pixels:</td>
									<td><?php $this->_addTextBox( 'overlay_header_size', array( 'size' => '4' ) ); ?></td>
								</tr>
								<tr><td>Color:</td>
									<td><?php $this->_addTextBox( 'overlay_header_color', array( 'size' => '7' ) ); ?>&nbsp;<?php $this->_addButton( 'show_overlay_header_color_picker', 'Show Picker' ); ?></td>
								</tr>
							</table>
							
							<h3>Subheader Text</h3>
							<table>
								<tr><td>Text:</td>
									<td><?php $this->_addTextBox( 'overlay_subheader_text', array( 'size' => '40' ) ); ?></td>
								</tr>
								<tr><td>Size in pixels:</td>
									<td><?php $this->_addTextBox( 'overlay_subheader_size', array( 'size' => '4' ) ); ?></td>
								</tr>
								<tr><td>Color:</td>
									<td><?php $this->_addTextBox( 'overlay_subheader_color', array( 'size' => '7' ) ); ?>&nbsp;<?php $this->_addButton( 'show_overlay_subheader_color_picker', 'Show Picker' ); ?></td>
								</tr>
							</table>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<br />
			
			<p class="submit"><?php $this->_addSubmit( 'save', 'Save' ); ?></p>
			<?php $this->_addHiddenNoSave( 'action', 'save' ); ?>
			<?php $this->_addUsedInputs(); ?>
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
			
			<div id="overlay_header_color_ColorPickerWrapper" style="padding:10px; border:1px solid black; position:absolute; z-index:10; background-color:white; display:none;">
				<table><tr>
					<td style="vertical-align:top;"><div id="overlay_header_color_ColorMap"></div><br /><a href="javascript:void(0);" style="float:right;" id="overlay_header_color_hide_div">save selection</a></td>
					<td style="vertical-align:top;"><div id="overlay_header_color_ColorBar"></div></td>
					<td style="vertical-align:top;">
						<table>
							<tr><td colspan="3"><div id="overlay_header_color_Preview" style="background-color:#fff; width:95px; height:60px; padding:0; margin:0; border:solid 1px #000;"><br /></div></td></tr>
							<tr><td><input type="radio" id="overlay_header_color_HueRadio" name="overlay_header_color_Mode" value="0" /></td><td><label for="overlay_header_color_HueRadio">H:</label></td><td><input type="text" id="overlay_header_color_Hue" value="0" style="width: 40px;" /> &deg;</td></tr>
							<tr><td><input type="radio" id="overlay_header_color_SaturationRadio" name="overlay_header_color_Mode" value="1" /></td><td><label for="overlay_header_color_SaturationRadio">S:</label></td><td><input type="text" id="overlay_header_color_Saturation" value="100" style="width: 40px;" /> %</td></tr>
							<tr><td><input type="radio" id="overlay_header_color_BrightnessRadio" name="overlay_header_color_Mode" value="2" /></td><td><label for="overlay_header_color_BrightnessRadio">B:</label></td><td><input type="text" id="overlay_header_color_Brightness" value="100" style="width: 40px;" /> %</td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><input type="radio" id="overlay_header_color_RedRadio" name="overlay_header_color_Mode" value="r" /></td><td><label for="overlay_header_color_RedRadio">R:</label></td><td><input type="text" id="overlay_header_color_Red" value="255" style="width: 40px;" /></td></tr>
							<tr><td><input type="radio" id="overlay_header_color_GreenRadio" name="overlay_header_color_Mode" value="g" /></td><td><label for="overlay_header_color_GreenRadio">G:</label></td><td><input type="text" id="overlay_header_color_Green" value="0" style="width: 40px;" /></td></tr>
							<tr><td><input type="radio" id="overlay_header_color_BlueRadio" name="overlay_header_color_Mode" value="b" /></td><td><label for="overlay_header_color_BlueRadio">B:</label></td><td><input type="text" id="overlay_header_color_Blue" value="0" style="width: 40px;" /></td></tr>
							<tr><td>#:</td><td colspan="2"><input type="text" id="overlay_header_color_Hex" value="FF0000" style="width: 60px;" /></td></tr>
						</table>
					</td>
				</tr></table>
			</div>

			<div id="overlay_subheader_color_ColorPickerWrapper" style="padding:10px; border:1px solid black; position:absolute; z-index:10; background-color:white; display:none;">
				<table><tr>
					<td style="vertical-align:top;"><div id="overlay_subheader_color_ColorMap"></div><br /><a href="javascript:void(0);" style="float:right;" id="overlay_subheader_color_hide_div">save selection</a></td>
					<td style="vertical-align:top;"><div id="overlay_subheader_color_ColorBar"></div></td>
					<td style="vertical-align:top;">
						<table>
							<tr><td colspan="3"><div id="overlay_subheader_color_Preview" style="background-color:#fff; width:95px; height:60px; padding:0; margin:0; border:solid 1px #000;"><br /></div></td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_HueRadio" name="overlay_subheader_color_Mode" value="0" /></td><td><label for="overlay_subheader_color_HueRadio">H:</label></td><td><input type="text" id="overlay_subheader_color_Hue" value="0" style="width: 40px;" /> &deg;</td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_SaturationRadio" name="overlay_subheader_color_Mode" value="1" /></td><td><label for="overlay_subheader_color_SaturationRadio">S:</label></td><td><input type="text" id="overlay_subheader_color_Saturation" value="100" style="width: 40px;" /> %</td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_BrightnessRadio" name="overlay_subheader_color_Mode" value="2" /></td><td><label for="overlay_subheader_color_BrightnessRadio">B:</label></td><td><input type="text" id="overlay_subheader_color_Brightness" value="100" style="width: 40px;" /> %</td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_RedRadio" name="overlay_subheader_color_Mode" value="r" /></td><td><label for="overlay_subheader_color_RedRadio">R:</label></td><td><input type="text" id="overlay_subheader_color_Red" value="255" style="width: 40px;" /></td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_GreenRadio" name="overlay_subheader_color_Mode" value="g" /></td><td><label for="overlay_subheader_color_GreenRadio">G:</label></td><td><input type="text" id="overlay_subheader_color_Green" value="0" style="width: 40px;" /></td></tr>
							<tr><td><input type="radio" id="overlay_subheader_color_BlueRadio" name="overlay_subheader_color_Mode" value="b" /></td><td><label for="overlay_subheader_color_BlueRadio">B:</label></td><td><input type="text" id="overlay_subheader_color_Blue" value="0" style="width: 40px;" /></td></tr>
							<tr><td>#:</td><td colspan="2"><input type="text" id="overlay_subheader_color_Hex" value="FF0000" style="width: 60px;" /></td></tr>
						</table>
					</td>
				</tr></table>
			</div>
			
			<div style="display:none;">
				<?php
					$images = array( 'rangearrows.gif', 'mappoint.gif', 'bar-saturation.png', 'bar-brightness.png', 'bar-blue-tl.png', 'bar-blue-tr.png', 'bar-blue-bl.png', 'bar-blue-br.png', 'bar-red-tl.png',
						'bar-red-tr.png', 'bar-red-bl.png', 'bar-red-br.png', 'bar-green-tl.png', 'bar-green-tr.png', 'bar-green-bl.png', 'bar-green-br.png', 'map-red-max.png', 'map-red-min.png',
						'map-green-max.png', 'map-green-min.png', 'map-blue-max.png', 'map-blue-min.png', 'map-saturation.png', 'map-saturation-overlay.png', 'map-brightness.png', 'map-hue.png' );
					
					foreach( (array) $images as $image )
						echo '<img src="' . $this->_pluginURL . '/js/colorpicker/images/' . $image . "\" />\n";
				?>
				
			</div>
		</form>
	</div>
	
<?php
		}
		
		
		// Form Functions ///////////////////////////
		
		function _newForm() {
			$this->_usedInputs = array();
		}
		
		function _addSubmit( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'submit';
			$options['name'] = $var;
			$options['class'] = ( empty( $options['class'] ) ) ? 'button-primary' : $options['class'];
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addButton( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'button';
			$options['name'] = $var;
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'text';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextArea( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'textarea';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addFileUpload( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'file';
			$options['name'] = $var;
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addMultiCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$var = $var . '[]';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addRadio( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'radio';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addDropDown( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array();
			elseif ( ! is_array( $options['value'] ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'dropdown';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHidden( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'hidden';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHiddenNoSave( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['name'] = $var;
			
			$this->_addHidden( $var, $options, $override_value );
		}
		
		function _addDefaultHidden( $var ) {
			$options = array();
			$options['value'] = $this->defaults[$var];
			
			$var = "default_option_$var";
			
			$this->_addHiddenNoSave( $var, $options );
		}
		
		function _addUsedInputs() {
			$options['type'] = 'hidden';
			$options['value'] = implode( ',', $this->_usedInputs );
			$options['name'] = 'used-inputs';
			
			$this->_addSimpleInput( 'used-inputs', $options, true );
		}
		
		function _addSimpleInput( $var, $options = false, $override_value = false ) {
			if ( empty( $options['type'] ) ) {
				echo "<!-- _addSimpleInput called without a type option set. -->\n";
				return false;
			}
			
			
			$scrublist['textarea']['value'] = true;
			$scrublist['file']['value'] = true;
			$scrublist['dropdown']['value'] = true;
			
			$defaults = array();
			$defaults['name'] = $this->_var . '-' . $var;
			
			$var = str_replace( '[]', '', $var );
			
			if ( 'checkbox' === $options['type'] )
				$defaults['class'] = $var;
			else
				$defaults['id'] = $var;
			
			$options = $this->_merge_defaults( $options, $defaults );
			
			if ( ( false === $override_value ) && isset( $this->_options[$var] ) ) {
				if ( 'checkbox' === $options['type'] ) {
					if ( $this->_options[$var] == $options['value'] )
						$options['checked'] = 'checked';
				}
				elseif ( 'dropdown' !== $options['type'] )
					$options['value'] = $this->_options[$var];
			}
			
			if ( ( preg_match( '/^' . $this->_var . '/', $options['name'] ) ) && ( ! in_array( $options['name'], $this->_usedInputs ) ) )
				$this->_usedInputs[] = $options['name'];
			
			
			$attributes = '';
			
			if ( false !== $options )
				foreach ( (array) $options as $name => $val )
					if ( ! is_array( $val ) && ( true !== $scrublist[$options['type']][$name] ) )
						if ( ( 'submit' === $options['type'] ) || ( 'button' === $options['type'] ) )
							$attributes .= "$name=\"$val\" ";
						else
							$attributes .= "$name=\"" . htmlspecialchars( $val ) . '" ';
			
			if ( 'textarea' === $options['type'] )
				echo '<textarea ' . $attributes . '>' . $options['value'] . '</textarea>';
			elseif ( 'dropdown' === $options['type'] ) {
				echo "<select $attributes>\n";
				
				foreach ( (array) $options['value'] as $val => $name ) {
					$selected = ( $this->_options[$var] == $val ) ? ' selected="selected"' : '';
					echo "<option value=\"$val\"$selected>$name</option>\n";
				}
				
				echo "</select>\n";
			}
			else
				echo '<input ' . $attributes . '/>';
		}
		
		
		// Plugin Functions ///////////////////////////
		
		function _fadeImages() {
			require_once( TEMPLATEPATH . '/lib/file-utility/file-utility.php' );
			
			
			if ( true !== $this->_text_overlay ) {
				global $wp_theme_options;
				
				$wp_theme_options['ithemes_featured_images']['enable_overlay'] = '';
				$this->_options['enable_overlay'] = '';
			}
			
			
			$variable_height = apply_filters( 'it_featured_images_filter_allow_variable_height', true );
			
			if ( false === $variable_height )
				$this->_options['height'] = $this->_defaults['height'];
			
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = get_attached_file( $id );
					$data = iThemesFileUtility::resize_image( $file, $this->_options['width'], $this->_options['height'], true );
					
					if ( ! is_array( $data ) && is_wp_error( $data ) )
						echo "<!-- Resize Error: " . $data->get_error_message() . " -->";
					else
						$files[] = $data['url'];
				}
			}
			
			if ( 0 === count( $files ) ) {
				if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
					while ( ( $file = readdir( $dir ) ) !== false )
						if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) )
							$files[] = get_bloginfo( 'template_directory' ) . '/images/random/' . $file;
					
					closedir( $dir );
				}
			}
			
			if ( 'bottom' === $this->_options['overlay_text_vertical_position'] ) {
				$title_overlay_top = $this->_options['height'] - $this->_options['overlay_header_size'] - ( $this->_options['overlay_text_padding'] * 2 );
				if ( ! empty( $this->_options['overlay_subheader_text'] ) )
					$title_overlay_top -= $this->_options['overlay_subheader_size'];
			}
			else if ( 'top' === $this->_options['overlay_text_vertical_position'] ) {
				$title_overlay_top = 0;
			}
			else {
				$title_overlay_top = intval( ( $this->_options['height'] - $this->_options['overlay_header_size'] ) / 2 ) - $this->_options['overlay_text_padding'];
				if ( ! empty( $this->_options['overlay_subheader_text'] ) )
					$title_overlay_top -= intval( $this->_options['overlay_subheader_size'] / 2 );
			}
			
			
			if ( ( '1' == $this->_options['enable_fade'] ) && ( count( $files ) > 1 ) ) {
				if ( 'ordered' == $this->_options['fade_sort'] )
					usort( $files, array( &$this, _sortFiles ) );
				else
					shuffle( $files );
				
				$list = '';
				
				foreach ( (array) $files as $file ) {
					if ( ! empty( $list ) )
						$list .= ",\n";
					
					if ( ! empty( $this->_options['link'] ) )
						$list .= "{src: '$file', href: '" . $this->_options['link'] . "'}";
					else
						$list .= "{src: '$file'}";
				}
				
?>
	<style type="text/css">
		#<?php echo $this->_options['id_name']; ?> {
			width: <?php echo $this->_options['width']; ?>px;
			height: <?php echo $this->_options['height']; ?>px;
			text-align: left;
		}
		#title_overlay {
			width: <?php echo ( $this->_options['width'] - ( $this->_options['overlay_text_padding'] * 2 ) ); ?>px;
			position: absolute;
			font-size: <?php echo $this->_options['overlay_header_size']; ?>px;
			line-height: <?php echo $this->_options['overlay_header_size']; ?>px;
			text-align: <?php echo $this->_options['overlay_text_alignment']; ?>;
			top: <?php echo $title_overlay_top; ?>px;
			padding: <?php echo $this->_options['overlay_text_padding']; ?>px;
			display: block;
			color: <?php echo $this->_options['overlay_header_color']; ?>;
		}
		
		#title_overlay span {
			display: block;
			color: <?php echo $this->_options['overlay_subheader_color']; ?>;
			font-size: <?php echo $this->_options['overlay_subheader_size']; ?>px;
			line-height: <?php echo $this->_options['overlay_subheader_size']; ?>px;
		} 
	</style>
	
	<script type='text/javascript'>
		/* <![CDATA[ */
			var run_featured_images = function() {
				if(jQuery('#<?php echo $this->_options['id_name']; ?>').length > 0) {
					jQuery('#<?php echo $this->_options['id_name']; ?>').crossSlide(
						{sleep: <?php echo $this->_options['sleep']; ?>, fade: <?php echo $this->_options['fade']; ?>, debug: true},
						[
						<?php echo "$list\n"; ?>
						]
					);
				}
			}
			
			if(jQuery("#TB_iframeContent", top.document).length > 0) {
				jQuery("#TB_iframeContent", top.document).load(run_featured_images);
			}
			else {
				jQuery(document).ready(run_featured_images);
			}
		/* ]]> */
	</script>
<?php
				
			}
			else {
				shuffle( $files );
				
?>  
	<style type="text/css">
		#<?php echo $this->_options['id_name']; ?> {
			width: <?php echo $this->_options['width']; ?>px;
			height: <?php echo $this->_options['height']; ?>px;
			text-align: left;
		}
		#title_overlay {
			width: <?php echo ( $this->_options['width'] - ( $this->_options['overlay_text_padding'] * 2 ) ); ?>px;
			color: <?php echo $this->_options['overlay_header_color']; ?>;
			font-size: <?php echo $this->_options['overlay_header_size']; ?>px;
			line-height: <?php echo $this->_options['overlay_header_size']; ?>px;
			position: absolute;
			text-align: <?php echo $this->_options['overlay_text_alignment']; ?>;
			top: <?php echo $title_overlay_top; ?>px;
			padding: <?php echo $this->_options['overlay_text_padding']; ?>px;
			display: block;
		}
		#title_overlay span {
			display: block;
			color: <?php echo $this->_options['overlay_subheader_color']; ?>;
			font-size: <?php echo $this->_options['overlay_subheader_size']; ?>px;
			line-height: <?php echo $this->_options['overlay_subheader_size']; ?>px;
		}
	</style>
	
	    
	<script type='text/javascript'>
		/* <![CDATA[ */
			jQuery(function($) {$(document).ready(function() {
				$('#<?php echo $this->_options['id_name']; ?>').html('<a href="<?php echo $this->_options['link']; ?>"><img src="<?php echo $files[0]; ?>" alt="Header Image" /></a>');
			});});
		/* ]]> */
	</script>
	
<?php
				
			}
		}
		
		function get_random_image() {
			require_once( TEMPLATEPATH . '/lib/file-utility/file-utility.php' );
			
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = get_attached_file( $id );
					$data = iThemesFileUtility::resize_image( $file, $this->_options['width'], $this->_options['height'], true );
					
					if ( ! is_array( $data ) && is_wp_error( $data ) )
						echo "<!-- Resize Error: " . $data->get_error_message() . " -->";
					else
						$files[] = $data['url'];
				}
			}
			
			if ( 0 === count( $files ) ) {
				if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
					while ( ( $file = readdir( $dir ) ) !== false )
						if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) )
							$files[] = get_bloginfo( 'template_directory' ) . '/images/random/' . $file;
					
					closedir( $dir );
				}
			}
			
			shuffle( $files );
			
			return $files[0];
		}
		
		function _sortFiles( $a, $b ) {
			if ( is_array( $a ) ) {
				$a = basename( $a['name'] );
				$b = basename( $b['name'] );
			}
			else {
				$a = preg_replace( '/-\d+x\d+\./', '.', $a );
				$b = preg_replace( '/-\d+x\d+\./', '.', $b );
				
				$a = basename( $a );
				$b = basename( $b );
			}
			
			return strnatcasecmp( $a, $b );
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
		
		function _merge_defaults( $values, $defaults, $force = false ) {
			if ( ! $this->_is_associative_array( $defaults ) ) {
				if ( ! isset( $values ) )
					return $defaults;
				
				if ( false === $force )
					return $values;
				
				if ( isset( $values ) || is_array( $values ) )
					return $values;
				return $defaults;
			}
			
			foreach ( (array) $defaults as $key => $val )
				$values[$key] = $this->_merge_defaults($values[$key], $val, $force );
			
			return $values;
		}
		
		function _is_associative_array( &$array ) {
			if ( ! is_array( $array ) || empty( $array ) )
				return false;
			
			$next = 0;
			
			foreach ( $array as $k => $v )
				if ( $k !== $next++ )
					return true;
			
			return false;
		}
		
		
		// Utility Functions //////////////////////////
		
		function _initializeImages() {
			if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
				require_once( TEMPLATEPATH . '/lib/file-utility/file-utility.php' );
				
				if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
					return new WP_Error( 'upload_dir_failure', 'Unable to load images into the uploads directory: ' . $uploads['error'] );
				
				
				$ids;
				
				while ( ( $file = readdir( $dir ) ) !== false ) {
					if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) ) {
						$filename = wp_unique_filename( $uploads['path'], basename( $file ) );
						
						// Move the file to the uploads dir
						$new_file = $uploads['path'] . "/$filename";
						if ( false === copy( TEMPLATEPATH . '/images/random/' . $file, $new_file ) ) {
							closedir( $dir );
							return new WP_Error( 'copy_file_failure', 'The theme images were unable to be loaded into the uploads directory' );
						}
						
						// Set correct file permissions
						$stat = stat( dirname( $new_file ));
						$perms = $stat['mode'] & 0000666;
						@chmod( $new_file, $perms );
						
						// Compute the URL
						$url = $uploads['url'] . "/$filename";
						
						
						$wp_filetype = wp_check_filetype( $file );
						$type = $wp_filetype['type'];
						
						
						$file_obj['url'] = $url;
						$file_obj['type'] = $type;
						$file_obj['file'] = $new_file;
						
						
						$title = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
						$content = '';
						
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						
						// use image exif/iptc data for title and caption defaults if possible
						if ( $image_meta = @wp_read_image_metadata( $new_file ) ) {
							if ( trim( $image_meta['title'] ) )
								$title = $image_meta['title'];
							if ( trim( $image_meta['caption'] ) )
								$content = $image_meta['caption'];
						}
						
						// Construct the attachment array
						$attachment = array(
							'post_mime_type' => $type,
							'guid' => $url,
							'post_title' => $title,
							'post_content' => $content
						);
						
						// Save the data
						$id = wp_insert_attachment( $attachment, $new_file );
						if ( ! is_wp_error( $id ) ) {
							wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $new_file ) );
						}
						
						
						$ids[] = $id;
					}
				}
				
				closedir( $dir );
				
				
				$this->_options['image_ids'] = $ids;
				$this->_save();
			}
		}
	}
}

?>
