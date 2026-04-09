<?php
/**
$Id: comment-form-fields.php 193 2007-09-23 02:27:40Z gregbrown $
$LastChangedDate: 2007-09-22 19:27:40 -0700 (Sat, 22 Sep 2007) $
$LastChangedRevision: 193 $
$LastChangedBy: gregbrown $

This page is included on the public website when the "comment_form"
action is announced.
*/
?>

<script type="text/javascript">
//<![CDATA[

function showCommentUploadForm() {
	var url = "<?php echo get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName . '/includes/comment-upload.php'; ?>";
	window.open(url, "commentUpload", "width=650,height=510,scrollbars=yes,resizable=yes,status=yes");
}

//]]>
</script>

<p>
	<input type="text" name="revver_video_id" id="revver_video_id" value="" <?php if (!$allow_manual_video_id) echo 'style="display: none;"'; ?> />
	<label for="author" style="font-weight: normal; <?php if (!$allow_manual_video_id) echo 'display: none;'; ?>">
		<strong><?php _e('Revver Video Id', $this->pluginName); ?></strong>
	</label>
	<?php if ($allow_manual_video_id) echo '&nbsp;|&nbsp;'; ?> 
	<?php if ( !$user_id ) { ?>
		<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><strong><?php _e('Login', $this->pluginName); ?></strong></a> <?php _e('to upload a video', $this->pluginName); ?>.
	<?php } else { ?>
		<a href="javascript:showCommentUploadForm();"><?php _e('Upload a Video', $this->pluginName); ?></a>
	<?php } ?>
</p>
<p style="display: none;" id="revver-comment-upload-msg"><?php _e('Your upload was successful!<br />Make sure to submit your comment to post the response to this blog post.', $this->pluginName); ?></p>

<input type="hidden" name="revver_video_owner" id="revver_video_owner" value="" />
<input type="hidden" name="revver_posting_comment" value="true" />