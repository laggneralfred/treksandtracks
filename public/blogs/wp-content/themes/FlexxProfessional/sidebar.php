<?php global $wp_theme_options; ?>
<div class="w360- left sidebar" id="sidebar">

<!--sidebar.php-->

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Wide Sidebar Top') ) : ?>
<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Wide Sidebar Top"</p>
	</div>
<?php endif; endif; ?>

<?php get_sidebar('left'); ?>
<?php get_sidebar('right'); ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Wide Sidebar Bottom') ) : ?>
<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Wide Sidebar Bottom"</p>
	</div>
<?php endif; endif; ?>

</div>