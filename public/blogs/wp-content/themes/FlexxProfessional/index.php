<?php get_header(); global $wp_theme_options; ?>
<?php do_action('before_content'); ?>

<!--index.php-->
<div class="<?php do_action('content_style'); ?>" id="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>
		
	<!--Post Wrapper Class-->
	<div <?php if (function_exists('post_class')) { post_class(); } else { echo 'class="post"'; } ?>>
	
	<!--Title/Date/Meta-->
	<div class="title wrap">
		<div class="date">
			<div class="month"><?php the_time('M'); ?></div>
			<div class="day"><?php the_time('d'); ?></div>
		</div>
		<div class="post-title">
			<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
			By
			<span class="meta-author"><?php the_author_posts_link(); ?></span> 
			 &middot;  Comments
			<span class="meta-comments"><?php comments_popup_link('(0)', '(1)', '(%)'); ?></span>
		</div>
	</div>

	<!--post text with the read more link-->
	<?php the_content('Read More&rarr;'); ?>
	<?php ithemes_insert_edit_link(); ?>

	<!--post meta info-->
	<div class="meta-bottom wrap">
		<div class="alignleft"><span class="categories">Categories : <?php the_category(', ') ?></span></div>
		<div class="alignright"><span class="comments">Comments <?php comments_popup_link('(0)', '(1)', '(%)'); ?></span></div>	
	</div>
    
	</div><!--end .post-->
	
	<?php endwhile; // end of one post ?>

    <!-- Previous/Next page navigation -->
    <div class="paging clearfix">
	    <div class="alignleft"><?php previous_posts_link('&laquo; Previous Page') ?></div>
	    <div class="alignright"><?php next_posts_link('Next Page &raquo;') ?></div>
    </div>    

	<?php else : // do not delete ?>

	<div class="post">
	<h3><?php _e("Page not Found"); ?></h3>
    <p><?php _e("We're sorry, but the page you're looking for isn't here."); ?></p>
    <p><?php _e("Try searching for the page you are looking for or using the navigation in the header or sidebar"); ?></p>
    </div>

	<?php endif; // do not delete ?>
	
</div><!--end #content-->

<?php do_action('after_content'); ?>
<?php get_footer(); //Include the Footer ?>
