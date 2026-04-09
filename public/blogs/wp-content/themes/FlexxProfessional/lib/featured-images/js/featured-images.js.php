<?php
	if ( defined( 'ABSPATH' ) )
	    require_once( ABSPATH . 'wp-load.php' );
	else {
		if ( file_exists( '../../../../wp-load.php' ) )
			require_once('../../../../wp-load.php');
		elseif ( file_exists( '../../../../../../wp-load.php' ) )
			require_once('../../../../../../wp-load.php');
		else
			die( 'Fatal Error: Could not locate wp-load.php' );
	}
	
	
	$abspath = ABSPATH;
	
	if ( preg_match( '/^[a-zA-Z]:/', $abspath ) )
		$abspath = preg_replace( '|\/$|', '\\', $abspath );
	
	$url = get_option( 'siteurl' ) . '/' . str_replace( '\\', '/', str_replace( $abspath, '', dirname( __FILE__ ) ) );
	
	
	header('Content-type: text/javascript');
?>


(function($) {
	function setupColorPicker(colorPicker, id) {
		colorPicker.hide();
		
		var pageHeight = JTTScreen.getDocumentHeight();
		var position = $("#" + id).position();
		var height = $("#" + id).outerHeight();
		var wrapperHeight = $("#" + id + "_ColorPickerWrapper").outerHeight();
		
		var top = 0;
		
		if(((position.top - wrapperHeight) <= 0) || ((pageHeight) > (position.top + parseInt(height) + 5 + wrapperHeight))) {
			top = position.top + parseInt(height) + 5;
		}
		else {
			top = position.top - wrapperHeight;
		}
		
		$("#" + id + "_ColorPickerWrapper").css("left", position.left).css("top", top);
		
		$("#show_" + id + "_picker,#" + id + "_hide_div").click(
			function(e) {
				colorPickerToggle(colorPicker, id);
			}
		);
	}
	
	function colorPickerToggle(colorPicker, id, show) {
		if(show == "show") {
			$("#" + id + "_ColorPickerWrapper").fadeIn("fast");
			colorPicker.show();
			
			$("#show_" + id + "_picker").attr("value", "Hide Picker");
		}
		else if(show == "hide") {
			colorPicker.hide();
			$("#" + id + "_ColorPickerWrapper").fadeOut("fast");
			
			$("#show_" + id + "_picker").attr("value", "Show Picker");
		}
		else if(($("#" + id + "_ColorPickerWrapper").css("display") == 'none')) {
			$("#" + id + "_ColorPickerWrapper").fadeIn("fast");
			colorPicker.show();
			
			$("#show_" + id + "_picker").attr("value", "Hide Picker");
		}
		else {
			colorPicker.hide();
			$("#" + id + "_ColorPickerWrapper").fadeOut("fast");
			
			$("#show_" + id + "_picker").attr("value", "Show Picker");
		}
	}
	
	$(document).ready(
		function(){
			overlayHeaderColor = new Refresh.Web.ColorPicker('overlay_header_color', '#overlay_header_color', {startHex: $("#overlay_header_color").attr("value").substr(1), startMode: 's', clientFilesPath: '<?php echo $url; ?>/colorpicker/images/'});
			setupColorPicker(overlayHeaderColor, 'overlay_header_color');
			
			overlaySubheaderColor = new Refresh.Web.ColorPicker('overlay_subheader_color', '#overlay_subheader_color', {startHex: $("#overlay_subheader_color").attr("value").substr(1), startMode: 's', clientFilesPath: '<?php echo $url; ?>/colorpicker/images/'});
			setupColorPicker(overlaySubheaderColor, 'overlay_subheader_color');
			
			
			if(!$(".enable_fade").attr('checked')) {
				$("#fade-options").hide();
			}
			
			$(".enable_fade").change(
				function(e) {
					if($(".enable_fade").attr('checked')) {
						$("#fade-options").fadeIn();
					}
					else {
						$("#fade-options").fadeOut();
					}
				}
			);
			
			
			if(!$(".enable_overlay").attr('checked')) {
				$("#text-overlay-options").hide();
			}
			
			$(".enable_overlay").change(
				function(e) {
					if($(".enable_overlay").attr('checked')) {
						$("#text-overlay-options").fadeIn();
					}
					else {
						$("#text-overlay-options").fadeOut();
					}
				}
			);
		}
	);
})(jQuery);
