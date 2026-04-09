<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @since 3.0.5
 */
get_header(); ?>

	<div id="primary" <?php bavotasan_primary_attr(); ?> role="main">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				global $mb_content_area;
		    	$mb_content_area = 'main';

		    	/* Include the post format-specific template for the content. If you want to
				 * this in a child theme then include a file called called content-___.php
				 * (where ___ is the post format) and that will be used instead.
				 */
		    	get_template_part( 'content', get_post_format() );
			endwhile;

			bavotasan_pagination();
		else :
			get_template_part( 'content', 'none' );
		endif;
		?>
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>