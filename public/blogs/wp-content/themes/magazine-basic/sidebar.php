<?php
/**
 * The first/left sidebar widgetized area.
 *
 * If no active widgets in sidebar, default login widget will appear.
 *
 * @since 1.0.0
 */
?>
	<div id="secondary" <?php bavotasan_sidebar_class(); ?> role="complementary">
		<?php if ( ! dynamic_sidebar( 'sidebar' ) ) : ?>

		<?php if ( current_user_can( 'edit_theme_options' ) ) { ?>
			<span class="instructions"><?php printf( __( 'Add your own widgets by going to the %sWidgets admin page%s.', 'magazine-basic' ), '<a href="' . admin_url( 'widgets.php' ) . '">', '</a>' ); ?></span>
		<?php } ?>

		<aside id="meta" class="widget">
			<h3 class="widget-title"><?php _e( 'Meta', 'magazine-basic' ); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<?php wp_meta(); ?>
			</ul>
		</aside>
		<?php endif; ?>
	</div><!-- #secondary.widget-area -->

	<?php
	/**
	 * The secondary/right sidebar widgetized area.
	 *
	 * Only appears if a widget has been added.
	 *
	 * @since 1.0.0
	 */
	if ( is_active_sidebar( 'second-sidebar' ) ) {
		?>
		<div id="tertiary" <?php bavotasan_second_sidebar_class(); ?> role="complementary">
			<?php dynamic_sidebar( 'second-sidebar' ); ?>
		</div><!-- #tertiary.widget-area -->
		<?php
	}