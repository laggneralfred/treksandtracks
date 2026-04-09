<?php
/**
$Id: comments-widget.php 218 2007-10-19 02:33:12Z gregbrown $
$LastChangedDate: 2007-10-18 19:33:12 -0700 (Thu, 18 Oct 2007) $
$LastChangedRevision: 218 $
$LastChangedBy: gregbrown $

This is the template for rendering the video comments widget 
that goes underneath the post.  For information on how to
customize this widget please refer to:
http://developer.revver.com/widget

Note, this will allow output if we have a collection id
on the post and this comes about by the post author allowing
video comments on the post.

If the blog owner at some point decides to remove this 
permission but the post did have it at one time then it will
still show this widget.
*/
?>

<div id="<?php echo $divId; ?>" class="revver-widget-container">
<?php if ( $video_comments_online == 1 ) { ?>
<h3><?php echo $video_comments_online . __(' Video Response to ', $this->pluginName) . " \"" . $post->post_title . "\" "; ?></h3>
<?php } else { ?>
<h3><?php echo $video_comments_online . __(' Video Responses to ', $this->pluginName) . " \"" . $post->post_title . "\" "; ?></h3>
<?php } ?>

<script type="text/javascript">
//<![CDATA[
new REVVER.widget.VideoCollection({
	"display": {
		<?php if ( $video_comments_online == 1 ) { ?>
		"title": "<?php echo addslashes( $video_comments_online . __(' Video Response to ', $this->pluginName) . " \"" . $post->post_title . "\" " ); ?>",
		<?php } else { ?>
		"title": "<?php echo addslashes( $video_comments_online . __(' Video Responses to ', $this->pluginName) . " \"" . $post->post_title . "\" " ); ?>",
		<?php } ?>
		"noResultsMsg": "<?php echo addslashes(__('There are no video responses to display yet.  If you just posted your video please be patient as it may take up to an hour to show up here.', $this->pluginName)); ?>",
		"rows": 1,
		"cols": 4,
        "thumbWidth"  : "88",
        "thumbHeight" : "66",
		"styleRules": {
			// Style for the containing div
            "video-collection": [{
				"border": "none",
				"padding": "0px"
			}],
            // Style for title at top of thumb strip
            "video-collection-title": [{
				"display": "none"
			}],
			// class name assigned to the "customize" link
            "customize-link": [{
				"display": "none"
			}],
			// styles the revver logo
			"logo-image": [{
				"display": "none"
			}],
			// styles the rss image
			"rss-image": [{
				"display": "none"
			}],
			// Style for the div that contains all components in a
			// thumbnail (title, image, other text)
			"thumb-div": [{
				"margin": "5px"
			}],
			// Style for the thumbnail title
			"thumb-title": [{
				"display": "none"
			}],
			// Style for the thumbnail image itself
			"thumb-img": [{
				"border": "1px solid #333",
				"margin": "0px",
				"padding": "0px"
			}],
			// Style for the count line, eg. '1-3 of 2350'
			"count-text": [{
				"display": "none"
			}]
		},
		"backarrow": REVVER.jsApiURL + "skins/simple/left.gif",
		"fwdarrow": REVVER.jsApiURL + "skins/simple/right.gif",
		"uparrow": REVVER.jsApiURL + "skins/simple/up.gif",
		"downarrow": REVVER.jsApiURL + "skins/simple/down.gif"
	},
	"flashPlayerParams": {
	<?php
		echo '"width": ' . $flash_width . ', "height": ' . $flash_height;
		if ($flash_logo == "custom") {
			echo ', "pngLogo": "' . $flash_logo_uri . '"';
		} else {
			echo ', "pngLogo": "' . $flash_logo . '"';
		}
		if ($flash_allowfullscreen == "yes") {
			echo ', "allowFullScreen": true';
		} else {
			echo ', "allowFullScreen": false';
		}
	?>
	},
	"collection" : <?php echo $post->collection_id; ?>,
	"affiliate" : "<?php echo $this->username; ?>"
});
//]]>
</script>

<?php
if ( $video_comments_pending == 1 ) {
	echo '<p><em>' . __("1 video response is waiting to be posted, pending approval.", $this->pluginName) . '</em></p>';
} elseif ($video_comments_pending > 1) {
	echo '<p><em>' . $video_comments_pending . __(" video responses are waiting to be posted, pending approval.", $this->pluginName) . '</em></p>';
}
?>
</div>