<?php global $wp_theme_options, $feature_top, $feature_top_width; ?>

<?php if(preg_match('/^(1|2|3)$/', $feature_top)) : ?>
<div class="wrap" id="feature-top">

	<div class="<?php do_action('feature_top_style'); ?>" <?php if($feature_top != 1) echo 'id="feature-top-left"'; ?>>
	<?php if($feature_top == 1) { $feature_top_section = 'Feature Top'; } else { $feature_top_section = 'Feature Top Left'; } ?>
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($feature_top_section) ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "<?php if($feature_top == 1) {echo 'Feature Top';} else {echo 'Feature Top Left';} ?>"</p>
	</div>
	<?php endif; endif; ?>
	</div>
	
<?php if ($feature_top == 3) : ?>
	<div class="<?php do_action('feature_top_style'); ?>" id="feature-top-middle">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Feature Top Middle') ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Feature Top Middle"</p>
	</div>
	<?php endif; endif; ?> 
	</div>
<?php endif; ?>

<?php if(preg_match('/^(2|3)$/', $feature_top)) : ?>
	<div class="<?php do_action('feature_top_style'); ?>" id="feature-top-right">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Feature Top Right') ) : ?>
	<?php if ($wp_theme_options['identify_widget_areas'] == 'yes') : ?>
	<div class="widget">
	<h4>This is a Widget Section</h4>
	<p>This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "Feature Top Right"</p>
	</div>
	<?php endif; endif; ?>
	</div>
<?php endif; ?>

</div>
<?php endif; ?>