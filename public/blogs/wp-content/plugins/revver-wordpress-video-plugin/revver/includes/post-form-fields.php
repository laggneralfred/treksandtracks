<?php
/**
$Id: post-form-fields.php 215 2007-10-16 21:43:32Z gregbrown $
$LastChangedDate: 2007-10-16 14:43:32 -0700 (Tue, 16 Oct 2007) $
$LastChangedRevision: 215 $
$LastChangedBy: gregbrown $

This page include all of the additional fields needed to be able to 
post and upload videos as part of a post in wordpress.

$revverPost properties:
->post_id 				= the wordpress post id
->video_id 				= the id of the video that was uploaded to revver
->video_owner 			= the username of the owner of the video that was uploaded
->auto_publish 			= whether or not to automatically publish the wordpress post once the revver video is approved
->is_auto_published		= a flag indicating whether or not the post was already auto published
->allow_video_comments  = a flag indicating whether or not video comments can be posted
->collection_id			= the id of the revver collection that all the video comments will be put into
*/
?>

<div class="dbx-b-ox-wrapper">
	<fieldset class="dbx-box">
		<div class="dbx-h-andle-wrapper">
			<h3 class="dbx-handle"><?php _e('Revver Video Options', $this->pluginName); ?></h3>
		</div>

		<div style="padding: 15px;">
			<label for="revverVideoId"><?php _e('If you already know the video id enter it here:', $this->pluginName); ?></label><br />
			<input type="text" name="revver_video_id" size="30" value="<?php echo ($revverPost->video_id == 0) ? '' : htmlspecialchars($revverPost->video_id); ?>" id="revverVideoId" />
			<a href="#" onclick="Element.toggle('revverVideoSelector'); return false;"><?php _e('Add Video', $this->pluginName); ?></a>

			<br /><br />
			<input style="width: 15px;" type="checkbox" name="revver_allow_video_comments" id="revverAllowVideoComments" value="1" <?php echo ($revverPost->allow_video_comments != 0) ? 'checked="checked" ' : ''; ?>/>
			<label for="revverAllowVideoComments"><?php _e('* Enable Video Responses', $this->pluginName); ?></label>

			<?php if ( $revverPost->is_auto_published == 0 && $post->post_status != 'publish') { ?>
			<br /><br />
			<input style="width: 15px;" type="checkbox" name="revver_auto_publish" id="revverAutoPublish" value="1" <?php echo ($revverPost->auto_publish != 0) ? 'checked="checked" ' : ''; ?>/>
			<label for="revverAutoPublish"><?php _e('** Publish upon Video Approval?', $this->pluginName); ?></label>
			<?php } ?>

			<?php if ( $revver_playlist_id > 0 ) { ?>
			<br /><br />
			<input style="width: 15px;" type="checkbox" name="revver_add_to_playlist" id="revverAddToPlaylist" value="1" <?php if ($post->post_status != 'publish') echo 'checked="checked" '; ?>/>
			<label for="revverAddToPlaylist"><?php _e('*** Add video to my playlist', $this->pluginName); ?></label>
			<?php } ?>

			<div id="revverVideoSelector" style="display: none;">
				<br /><br />
				<?php
					$iframeUrl = get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName . '/includes/';
					if ($revverPost->video_id == 0) {
						$iframeUrl .= 'search.php';
					} else {
						$iframeUrl .= 'video-details.php?fromSearch=0&id=' . $revverPost->video_id;
					}
				?>
				<iframe id="revverVideoSelectorFrame" src="<?php echo $iframeUrl; ?>" style="width: 99%; height: 550px;"></iframe>
			</div>
		</div>

		<input type="hidden" name="revver_video_owner" value="<?php echo htmlspecialchars($revverPost->video_owner); ?>" />
		<input type="hidden" name="revver_collection_id" value="<?php echo htmlspecialchars($revverPost->collection_id); ?>" />
		<input type="hidden" name="revver_editting_post" value="true" />
	</fieldset>

	<p>
		<small>
			<?php _e('* Video Responses allow subscribers to upload their own videos attach an existing Revver video to their comment.', $this->pluginName); ?><br />
			<?php _e('** When you first upload a video, your post is saved but not published as your video has yet to go online. If you do not select the "Publish upon Video Approval" checkbox, when you\'re video goes online, you will still have to manually publish your post.', $this->pluginName); ?><br />
			<?php _e('** Checking this option will automatically add the video id into your playlist collection.  The playlist is a collection configured in the \'Auto Playlist Creation\' section of the Revver Configuration.', $this->pluginName); ?>
		</small>
	</p>
</div>

<br /><br />