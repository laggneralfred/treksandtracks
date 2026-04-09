<?php global $wp_theme_options; ?>
<?php do_action('above_footer') ?>
<div style="clear:both; height:0px"><!-- blank --></div>

</div><!--end #container-->

<div class="<?php do_action('container_style'); ?>" id="footer">
	<div class="alignleft">
	<strong><?php bloginfo('name'); ?></strong><br />
	<?php _e('Copyright &copy;','flexx'); echo ' '.date('Y').' '; _e('All Rights Reserved','flexx'); ?>
	</div>
	<div class="alignright">
		<?php	
		$footer_credit = '<a href="http://flexxtheme.com/" title="Flexx Theme">'.__('Flexx Theme','flexx').'</a> '.__('by','flexx').' <a href="http://ithemes.com" title="WordPress Themes">iThemes</a><br />';
		$footer_credit .= __('Powered by','flexx').' <a href="http://wordpress.org">'.__('WordPress','flexx').'</a>';
		echo apply_filters('ithemes_footer_credit',$footer_credit);
		?>
	</div>
       
	<?php wp_footer(); //We need this for plugins ?>
</div>

<?php do_action('flexx_footer_stats'); ?>
</body>
</html>
