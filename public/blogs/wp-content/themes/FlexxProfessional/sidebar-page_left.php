<?php global $wp_theme_options; ?>
<div class="w180- left sidebar">

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Page Skinny Left Sidebar') ) : ?>
<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Page Skinny Left Sidebar"</p>
	</div>
<?php endif; endif; ?>

</div>