<?php
/**
 * The template used for displaying page content in page.php
 *
 * @since 3.0.0
 */
$class = bavotasan_article_class();
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
		<?php
		if ( is_search() ) :
		the_title( sprintf( '<h2 class="entry-title taggedlink"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		elseif ( is_front_page() ) :
			the_title( '<h2 class="entry-title">', '</h2>' );
		else :
			the_title( '<h1 class="entry-title">', '</h1>' );
		endif;
		?>

	    <div class="entry-content">
		    <?php the_content( 'Read more &rarr;' ); ?>
	    </div><!-- .entry-content -->

	    <?php get_template_part( 'content', 'footer' ); ?>

	</article><!-- #post-<?php the_ID(); ?> -->