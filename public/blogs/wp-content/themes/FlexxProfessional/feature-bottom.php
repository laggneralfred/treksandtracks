<?php global $wp_theme_options, $feature_bottom, $feature_bottom_width; ?>

<?php if(preg_match('/^(1|2|3)$/', $feature_bottom)) : ?>
<div class="wrap" id="feature-bottom">

	<div class="<?php do_action('feature_bottom_style'); ?>" <?php if($feature_bottom != 1) echo 'id="feature-bottom-left"'; ?>>
	<?php if($feature_bottom == 1) { $feature_bottom_section = 'Feature Bottom'; } else { $feature_bottom_section = 'Feature Bottom Left'; } ?>
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($feature_bottom_section) ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "<?php if($feature_bottom == 1) {echo 'Feature bottom';} else {echo 'Feature Bottom Left';} ?>"</p>
	</div>
	<?php endif; endif; ?>
	</div>
	
<?php if ($feature_bottom == 3) : ?>
	<div class="<?php do_action('feature_bottom_style'); ?>" id="feature-bottom-middle">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Feature Bottom Middle') ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Feature Bottom Middle"</p>
	</div>
	<?php endif; endif; ?> 
	</div>
<?php endif; ?>

<?php if(preg_match('/^(2|3)$/', $feature_bottom)) : ?>
	<div class="<?php do_action('feature_bottom_style'); ?>" id="feature-bottom-right">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Feature Bottom Right') ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Feature Bottom Right"</p>
	</div>
	<?php endif; endif; ?>
	</div>
<?php endif; ?>

</div>
<?php endif; ?>