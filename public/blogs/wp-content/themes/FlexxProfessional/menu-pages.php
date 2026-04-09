<?php global $wp_theme_options; ?>

<div id="pagemenu" class="wrap">
	<ul class="wrap">
		<?php /* A link back to the homepage ... unlesss the user chose to omit it */ ?>
		<?php if ( in_array( 'home', (array) $wp_theme_options['include_pages'] ) ) : ?>
			<li class="home <?php if ( is_home() ) { echo 'current_page_item'; } ?>"><a href="<?php echo get_settings( 'home' ); ?>"><?php _e( 'Home', 'flexx' ); ?></a></li>
		<?php endif; ?>
		<?php if ( in_array( 'site_name', (array) $wp_theme_options['include_pages'] ) ) : ?>
			<li class="site-name <?php if ( is_home() ) { echo 'current_page_item'; } ?>"><a href="<?php echo get_settings( 'home' ); ?>"><?php echo bloginfo( 'name' ); ?></a></li>
		<?php endif; ?>
		
		<?php /* Lists pages, excludes pages selected in theme options */ ?>
		<?php if ( ! empty( $wp_theme_options['include_pages'] ) ) : ?>
			<?php $include = implode( ',', (array) $wp_theme_options['include_pages'] ); ?>
			<?php $my_pages = "title_li=&depth=4&include=$include"; ?>
			<?php wp_list_pages( $my_pages ); ?>
		<?php endif; ?>
</ul>
</div>
