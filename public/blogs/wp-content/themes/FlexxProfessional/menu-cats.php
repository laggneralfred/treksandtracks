<?php global $wp_theme_options; ?>

<div id="catmenu" class="clearfix">
	<ul class="clearfix">
		<?php /* A link back to the homepage ... unlesss the user chose to omit it */ ?>
		<?php if ( in_array( 'home', ( array) $wp_theme_options['include_cats'] ) ) : ?>
			<li class="home <?php if ( is_home() ) { echo 'current_page_item'; } ?>"><a href="<?php echo get_settings('home'); ?>"><?php _e("Home","flexx"); ?></a></li>
		<?php endif; ?>
		
		<?php /* Lists categories the user chose in the menu builder */ ?>
		<?php if ( ! empty( $wp_theme_options['include_cats'] ) ) : ?>
			<?php $include = implode( ',', (array) $wp_theme_options['include_cats'] ); ?>
			<?php $my_cats = "title_li=&depth=4&sort_column=menu_order&include=$include"; ?>
			<?php wp_list_categories( $my_cats ); ?>
		<?php endif; ?>
</ul>
</div>