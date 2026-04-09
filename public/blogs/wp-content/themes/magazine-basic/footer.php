<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @since 3.0.0
 */
$bavotasan_theme_options = bavotasan_theme_options();
?>
	</div> <!-- #main.row -->
</div> <!-- #page.grid -->

<footer id="footer" role="contentinfo">

	<div id="footer-content" class="grid <?php echo esc_attr( $bavotasan_theme_options['width'] ); ?>">
		<div class="row">

			<p class="copyright c12">
				<span class="fl">Copyright &copy; <?php echo date( 'Y' ); ?> <a href="<?php echo home_url(); ?>"><?php echo bloginfo( 'name' ); ?></a>. All Rights Reserved.</span>
				<span class="fr"><?php printf( __( 'The %s Theme by %s.', 'magazine-basic' ), BAVOTASAN_THEME_NAME, '<a href="https://themes.bavotasan.com/">bavotasan.com</a>' ); ?></span>
			</p><!-- .c12 -->

		</div><!-- .row -->
	</div><!-- #footer-content.grid -->

</footer><!-- #footer -->

<?php wp_footer(); ?>
</body>
</html>