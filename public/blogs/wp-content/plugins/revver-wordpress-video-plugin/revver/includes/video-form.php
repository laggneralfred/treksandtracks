<?php
/**
$Id: video-form.php 97 2007-05-24 09:11:28Z gregbrown $
$LastChangedDate: 2007-05-24 02:11:28 -0700 (Thu, 24 May 2007) $
$LastChangedRevision: 97 $
$LastChangedBy: gregbrown $

Shows the details of a revver video and allows the user to
update it if they are authorized.
*/

?>

<script type="text/javascript">
//<![CDATA[

function deleteVideo(video_id) {
    if (!confirm("<?php _e('Are you sure you want to delete this video?', $this->pluginName); ?>")) {
        return false;
    }

	var request = new Ajax.Request(
            '<?php echo get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName . '/includes/delete-videos.php'; ?>',
            {
                method: 'post',
                parameters: 'revver_video_ids=' + video_id,
                onSuccess: function (request) {
                    var json = eval( '(' + request.responseText + ')' );
                    alert(json.msg);
                    window.location.href = "<?php echo addslashes($basePage); ?>";
                }
            }
        );
}

//]]>
</script>

<?php if ( !empty($revver_message) ) { ?>
<div id="message" class="updated fade">
	<p><strong><?php echo $revver_message; ?></strong></p>	
</div>
<?php } ?>

<div class="wrap">
	<h2><?php echo __('Edit Revver Video:', $this->pluginName) . ' "' . $video['title'] . '"'; ?></h2>

	<table border="0" cellpadding="5">
	<tr>
		<td valign="top">
			<script type="text/javascript" src="<?php echo $video['flashJsUrl']; ?>"></script>
			<p><a href="<?php echo $basePage; ?>">&laquo; <?php _e('back to video search', $this->pluginName); ?></a></p>
		</td>
		<td valign="top">
			<form action="<?php echo $basePage; ?>" method="post" id="revverVideoForm">
			<fieldset>
				<table>
				<tr>
					<td><label for="revver-video-title"><?php _e('Title:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
					<td><input type="text" name="revver_video_title" id="revver-video-title" value="<?php echo htmlspecialchars($video['title']); ?>" size="40" maxlength="250" /></td>
				</tr>
				<tr>
					<td><br /><label for="revver-video-keywords"><?php _e('Keywords: (required)', $this->pluginName); ?></label></td>
				</tr>
				<tr>
					<td>
						<input type="text" name="revver_video_keywords" id="revver-video-keywords" value="<?php echo htmlspecialchars(implode(', ', $video['keywords'])); ?>" size="40" maxlength="100" /><br />
						<small style="margin-left: 5px; color: #666; font-weight: normal;"><?php _e('( separate with commas | 100 character limit )', $this->pluginName); ?></small>
					</td>
				</tr>
				<tr>
					<td><br /><label for="revver-video-description"><?php _e('Description:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
					<td><textarea name="revver_video_description" id="revver-video-description" cols="45" rows="8"><?php echo htmlspecialchars($video['description']); ?></textarea></td>
				</tr>
				<tr>
					<td><br /><label for="revver-video-credits"><?php _e('Credits:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
					<td><input type="text" name="revver_video_credits" id="revver-video-credits" value="<?php echo htmlspecialchars($video['credits']); ?>" size="40" maxlength="250" /></td>
				</tr>
				<tr>
					<td><br /><label for="revver-video-url"><?php _e('Website:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
					<td><input type="text" name="revver_video_url" id="revver-video-url" value="<?php echo htmlspecialchars($video['url']); ?>" size="40" maxlength="250" /></td>
				</tr>
				<?php 
				/*
				<tr>
					<td><br /><label for="revver-video-ageRestriction"><?php _e('Age-Rating:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
		        	<td>
		            	<select name="revver_video_ageRestriction" style="width: 300px;">
		                	<option value="1" <?php if ($video['ageRestriction'] == 1) echo 'selected="selected"' ;?>><?php _e('General', $this->pluginName); ?></option>
		                	<option value="2" <?php if ($video['ageRestriction'] == 2) echo 'selected="selected"' ;?>><?php _e('General', $this->pluginName); ?></option>
		                	<option value="3" <?php if ($video['ageRestriction'] == 3) echo 'selected="selected"' ;?>><?php _e('13+', $this->pluginName); ?></option>
		                	<option value="4" <?php if ($video['ageRestriction'] == 4) echo 'selected="selected"' ;?>><?php _e('17+', $this->pluginName); ?></option>
		                	<option value="5" <?php if ($video['ageRestriction'] == 5) echo 'selected="selected"' ;?>><?php _e('17+ Explicit', $this->pluginName); ?></option>
		            	</select>
		        	</td>
		        </tr>
		        */
		        ?>
				<tr>
					<td><br /><label for="revver-video-status"><?php _e('Video Status:', $this->pluginName); ?></label></td>
				</tr>
				<tr>
		        	<td>
		            	<select name="revver_video_status" style="width: 300px;">
		                	<option value="online" <?php if ($video['status'] == 'online') echo 'selected="selected"' ;?>><?php _e('Online', $this->pluginName); ?></option>
		                	<option value="offline" <?php if ($video['status'] == 'offline') echo 'selected="selected"' ;?>><?php _e('Offline', $this->pluginName); ?></option>
		            	</select>
		        	</td>
		        </tr>
				<tr>
					<td>
						<br />
						<h4><?php _e('Total Video Earnings', $this->pluginName); ?>:  <span style="color: green;"><?php echo revver_currency_format($video['revenue'], 2, true, true, true); ?></span></h4>

						<p class="submit" style="text-align: left;">
							<input type="submit" name="submit" value="<?php _e('Update Video', $this->pluginName); ?> &raquo;" />
							&nbsp;&nbsp;
							<a href="#" onclick="deleteVideo(<?php echo $video_id; ?>); return false;"><?php _e('Delete Video', $this->pluginName); ?></a>
						</p>
					</td>
				</tr>
				</table>
				
				<input type="hidden" name="revver_video_id" value="<?php echo $video_id; ?>" />
				<input type="hidden" name="postback" value="1" />
			</fieldset>
			</form>
		</td>
	</tr>
	</table>

</div>