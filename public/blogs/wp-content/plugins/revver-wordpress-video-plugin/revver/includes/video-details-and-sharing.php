<?php
/**
$Id: video-details-and-sharing.php 217 2007-10-17 01:19:38Z gregbrown $
$LastChangedDate: 2007-10-16 18:19:38 -0700 (Tue, 16 Oct 2007) $
$LastChangedRevision: 217 $
$LastChangedBy: gregbrown $

The video details and sharing options that are displayed
below the video itself on posts.
*/
?>

<div class="revver-video-details-sharing">
	<p>
		<?php if ($share_displaydetails == "yes") { ?>
			<a href="#" onclick="showVideoDetails(<?php echo $post_id . "," . $video_id; ?>); return false;"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName; ?>/img/details_closed.gif" id="revver-video-details-btn-<?php echo "p" . $post_id . "v" . $video_id; ?>" /></a>
		<?php } ?>
		<?php if ($share_displayshare == "yes") { ?>
			<a href="#" onclick="showVideoShare(<?php echo $post_id . "," . $video_id; ?>); return false;"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName; ?>/img/share_closed.gif" id="revver-video-share-btn-<?php echo "p" . $post_id . "v" . $video_id; ?>" /></a>
		<?php } ?>
	</p>
	<div class="revver-video-details-panel" id="revver-video-details-panel-<?php echo "p" . $post_id . "v" . $video_id; ?>" style="display: none;">
		<p>
			<strong><?php _e('Description:', $this->pluginName); ?></strong><br />
			<span id="revver-video-details-desc-<?php echo "p" . $post_id . "v" . $video_id; ?>"><?php _e('loading...', $this->pluginName); ?></span>
		</p>
		<p>
			<strong><?php _e('Tags:', $this->pluginName); ?></strong><br />
			<span id="revver-video-details-tags-<?php echo "p" . $post_id . "v" . $video_id; ?>"><?php _e('loading...', $this->pluginName); ?></span>
		</p>
		<p>
			<strong><?php _e('Credits:', $this->pluginName); ?></strong><br />
			<span id="revver-video-details-credits-<?php echo "p" . $post_id . "v" . $video_id; ?>"><?php _e('loading...', $this->pluginName); ?></span>
		</p>
		<p>
			<strong><?php _e('Website:', $this->pluginName); ?></strong><br />
			<a href="" target="_blank" id="revver-video-details-website-<?php echo "p" . $post_id . "v" . $video_id; ?>"><?php _e('loading...', $this->pluginName); ?></a>
		</p>
	</div>
	<div class="revver-video-share-panel" id="revver-video-share-panel-<?php echo "p" . $post_id . "v" . $video_id; ?>" style="display: none;">
		<p>
			<strong><a href="#" onclick="showVideoShareSend(<?php echo $post_id . "," . $video_id; ?>); return false;"><?php _e('Email to a Friend', $this->pluginName); ?></a></strong> |
			<strong><a href="#" onclick="showVideoShareGrab(<?php echo $post_id . "," . $video_id; ?>); return false;"><?php _e('Grab Code', $this->pluginName); ?></a></strong>
		</p>
		<form id="revver-video-share-send-<?php echo "p" . $post_id . "v" . $video_id; ?>" style="display: block;">
			<p style="display: none;" id="revver-video-share-send-result-<?php echo "p" . $post_id . "v" . $video_id; ?>"></p>
			<table>
			<tr>
				<td><strong><?php _e('Message (optional):', $this->pluginName); ?></strong></td>
				<td>&nbsp;&nbsp;</td>
				<td><strong><?php _e('Your friend\'s email address:', $this->pluginName); ?></strong></td>
			</tr>
			<tr>
				<td rowspan="4"><textarea name="msg" id="revver-video-share-message-<?php echo "p" . $post_id . "v" . $video_id; ?>" cols="20" rows="6"><?php echo __('Found this at ', $this->pluginName) . htmlspecialchars(get_option('siteurl')) . __(' and thought you would dig it!', $this->pluginName); ?></textarea></td>
				<td>&nbsp;&nbsp;</td>
				<td><input type="text" name="toemail" id="revver-video-share-toemail-<?php echo "p" . $post_id . "v" . $video_id; ?>" size="30" maxlength="150" style="width: 200px;" /></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td><strong><?php _e('Your email address:', $this->pluginName); ?></strong></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td><input type="text" name="fromemail" id="revver-video-share-fromemail-<?php echo "p" . $post_id . "v" . $video_id; ?>" size="30" maxlength="150" style="width: 200px;" /></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td align="right"><input type="button" name="sendBtn" value="Send" onclick="sendRevverShareEmail(<?php echo $post_id . "," . $video_id; ?>);" /></td>
			</tr>
			</table>
			<input type="hidden" name="permalink" id="permalink-<?php echo "p" . $post_id . "v" . $video_id; ?>" value="<?php the_permalink(); ?>" />
		</form>

		<form id="revver-video-share-grab-<?php echo "p" . $post_id . "v" . $video_id; ?>" style="display: none;">
			<p><?php _e('Paste this code anywhere you can add HTML.', $this->pluginName); ?></p>
			<table>
			<tr>
				<td><strong><?php _e('Select a format:', $this->pluginName); ?></strong></td>
			</tr>
			<tr>
				<td>
					<select name="grabFormat" onchange="updateRevverGrabFormat(this, <?php echo $post_id . "," . $video_id; ?>);" style="width: 250px;">
						<option value="flashjs" selected="selected"><?php _e('Flash using JS', $this->pluginName); ?></option>
						<option value="quicktime"><?php _e('Quicktime', $this->pluginName); ?></option>
						<option value="flash"><?php _e('Flash Embed', $this->pluginName); ?></option>
						<option value="quicktimejs"><?php _e('Quicktime using JS', $this->pluginName); ?></option>
						<option value="thumbnail"><?php _e('Thumbnail Link', $this->pluginName); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><strong><?php _e('Grab Code:', $this->pluginName); ?></strong></td>
			</tr>
			<tr>
				<td><textarea name="grab" id="revver-video-share-grabcode-<?php echo "p" . $post_id . "v" . $video_id; ?>" cols="40" rows="6" onclick="this.focus(); this.select();"></textarea></td>
			</tr>
			</table>
		</form>
	</div>
</div>