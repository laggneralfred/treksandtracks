<?php
/**
 * The Template for displaying all single posts.
 *
 * @since 3.0.0
 */
get_header(); ?>

	<div id="primary" <?php bavotasan_primary_attr(); ?> role="main">
		<?php while ( have_posts() ) : the_post();
	    	global $mb_content_area;
	    	$mb_content_area = 'main';
	    	get_template_part( 'content', get_post_format() );
	    	?>

			<div id="posts-pagination">
				<h3 class="screen-reader-text"><?php _e( 'Post navigation', 'magazine-basic' ); ?></h3>
				<div class="previous fl"><?php previous_post_link( '%link', __( '&larr; %title', 'magazine-basic' ) ); ?></div>
				<div class="next fr"><?php next_post_link( '%link', __( '%title &rarr;', 'magazine-basic' ) ); ?></div>
			</div><!-- #posts-pagination -->

			<?php comments_template( '', true ); ?>

		<?php endwhile; // end of the loop. ?>
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>