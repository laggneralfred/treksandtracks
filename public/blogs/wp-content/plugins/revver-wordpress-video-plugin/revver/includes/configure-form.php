<?php
/**
$Id: configure-form.php 213 2007-10-16 05:09:37Z gregbrown $
$LastChangedDate: 2007-10-15 22:09:37 -0700 (Mon, 15 Oct 2007) $
$LastChangedRevision: 213 $
$LastChangedBy: gregbrown $

This form is for configuring the various options for this 
revver plugin itself.
*/
?>

<script type="text/javascript">
//<![CDATA[
function revver_ChangeUsername() {
	if ( confirm("<?php _e("Warning: Modifying the blog's primary Revver username \\nmay prevent existing subscribers and Revver child accounts \\nfrom being able to view their Revver account details. \\n\\nAre you sure you want to change this value?", $this->pluginName); ?>") ) {
		$('revverUsernameReadOnly').hide();
		$('revverUsername').show();
	}
	return false;
}
//]]>
</script>

<?php if (isset($_POST['revver_updating_config'])) { ?>
<div id="message" class="updated fade">
	<?php
		if ($update_success) {
			echo '<p><strong>' . __('Your configuration options have been updated.', $this->pluginName) . '</strong></p>';
		} else {
			echo '<p><strong>' . __('An error occurred when updating the configuration.  Make sure your Revver username and password are correct.', $this->pluginName) . '</strong></p>';
		}
	?>	
</div>
<?php } ?>

<div class="wrap">
	<h2><?php _e('Revver Configuration', $this->pluginName); ?></h2>

	<form action="<?php echo $basePage; ?>" method="post" id="revverConfigForm">
	<fieldset>
		<p class="error" style="padding: 4px 10px 4px 10px;">
			<?php _e('<strong>Important Note:</strong> The Revver username entered here will be the parent account of any users who subscribe to your blog and post video responses. Once entered, this value should not be changed.', $this->pluginName); ?>
		</p>

		<br />
		<table>
		<tr>
			<td colspan="2">
				<h3><?php _e('Basic Plugin Settings', $this->pluginName); ?></h3>
				<p>
					<?php _e('Please enter your Revver username and password below.', $this->pluginName); ?><br />
					<?php _e('If you do not have a Revver account yet please', $this->pluginName); ?>
					<a href="http://www.revver.com/" target="_blank"><?php _e('click here', $this->pluginName); ?></a>.
					<br />&nbsp;
				</p>
			</td>
		</tr>
		<tr>
			<td style="width: 125px;" nowrap="nowrap"><label for="revverUsername"><?php _e('Revver Username:', $this->pluginName); ?></label></td>
		<?php if ( empty($revver_username) ) { ?>
			<td><input type="text" name="revver_username" id="revverUsername" value="<?php echo htmlspecialchars($revver_username); ?>" size="30" maxlength="14" /></td>
		<?php } else { ?>
			<td>
				<span id="revverUsernameReadOnly"><?php echo $revver_username; ?> [ <a href="#" onclick="return revver_ChangeUsername();">change</a> ]</span>
				<input type="text" name="revver_username" id="revverUsername" value="<?php echo htmlspecialchars($revver_username); ?>" size="30" maxlength="14" style="display: none;" />
			</td>
		<?php } ?>
		<tr>
			<td><br /><label for="revverPassword"><?php _e('Revver Password:', $this->pluginName); ?></label></td>
			<td><br /><input type="password" name="revver_password" id="revverPassword" value="<?php echo htmlspecialchars($revver_password); ?>" size="30" maxlength="50" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<br />
				<input type="checkbox" name="revver_anon_response" id="revverAnonResponse" value="yes" <?php if ($revver_anon_response == "yes") echo 'checked="checked"'; ?> />
				<label for="revverAnonResponse"><?php _e('Allow Unregistered User Video Responses', $this->pluginName); ?></label><br />
				<small><?php _e('Enabling this will allow users to post a Revver video not uploaded via your blog as a video response without having to register.<br />If left unchecked, be sure to enable "Anyone can register" in the Options - General tab to allow visitors to register accounts.', $this->pluginName); ?></small>
			</td>
		</tr>
		<!--
		<tr>
			<td>&nbsp;</td>
			<td>
				<br />
				<input type="checkbox" name="revver_use_staging" id="revverUseStaging" value="yes" <?php if ($revver_use_staging == "yes") echo 'checked="checked"'; ?> />
				<label for="revverUseStaging"><?php _e('Use Staging Server', $this->pluginName); ?></label><br />
				<small><?php _e('Useful for debugging. You must have an account on staging to use this option.', $this->pluginName); ?></small>
			</td>
		</tr>
		//-->
		<tr>
			<td><br /><label for="revverCurlProxy"><?php _e('cURL Proxy:', $this->pluginName); ?></label></td>
			<td><br /><input type="text" name="revver_curl_proxy" id="revverCurlProxy" value="<?php echo htmlspecialchars($revver_curl_proxy); ?>" size="50" maxlength="150" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php _e('Some hosts, such as Godaddy, require a proxy server for cURL calls to HTTPS services. Most hosts do <strong>not</strong> require this. If your Revver username and password do not work, check with your hosting provider to see if you need to specify a proxy for cURL.', $this->pluginName); ?></td>
		</tr>


		<tr>
			<td colspan="2">
				<br /><br />
				<h3><?php _e('Auto Playlist Creation', $this->pluginName); ?></h3>
				<p>
					<?php _e('Insert a collection/playlist id below to enable the ability to save posts with Revver videos to the playlist specified. This is especially useful if you would like to include a Revver widget with your shows to place on your blog or for your users to embed on their blogs.', $this->pluginName); ?>
					<br />&nbsp;
				</p>
			</td>
		</tr>
		<tr>
			<td><label for="revverPlaylistId"><?php _e('Playlist id:', $this->pluginName); ?></label></td>
			<td><input type="text" name="revver_playlist_id" id="revverPlaylistId" value="<?php echo htmlspecialchars($revver_playlist_id); ?>" size="12" maxlength="10" /></td>
		</tr>


		<tr>
			<td colspan="2">
				<br /><br />
				<h3><?php _e('Flash Player Configuration', $this->pluginName); ?></h3>
			</td>
		</tr>
		<tr>
			<td><label for="revverFlashWidth"><?php _e('Video Size:', $this->pluginName); ?></label></td>
			<td>
				<input type="text" name="revver_flash_width" id="revverFlashWidth" value="<?php echo htmlspecialchars($revver_flash_width); ?>" size="5" maxlength="4" />
				<?php _e('w &nbsp; <strong>X</strong> &nbsp;', $this->pluginName); ?>
				<input type="text" name="revver_flash_height" id="revverFlashHeight" value="<?php echo htmlspecialchars($revver_flash_height); ?>" size="5" maxlength="4" />
				<?php _e('h &nbsp; <small>Does not include player controls - 32 pixels will be automatically added to the height</small>', $this->pluginName); ?>
			</td>
		</tr>
		<tr>
			<td valign="top"><br /><label><?php _e('Logo Options:', $this->pluginName); ?></label></td>
			<td>
				<br />
				<input type="radio" name="revver_flash_logo" id="revverFlashLogoRevver" value="" <?php if ($revver_flash_logo == "") echo 'checked="checked"'; ?> />
				<label for="revverFlashLogoRevver"><?php _e('Revver Branded', $this->pluginName); ?></label><br />

				<br />
				<input type="radio" name="revver_flash_logo" id="revverFlashLogoCustom" value="custom" <?php if ($revver_flash_logo == "custom") echo 'checked="checked"'; ?> />
				<label for="revverFlashLogoCustom"><?php _e('Custom', $this->pluginName); ?></label>
				<input type="text" name="revver_flash_logo_uri" id="revverFlashLogoCustomUri" value="<?php echo htmlspecialchars($revver_flash_logo_uri); ?>" size="40" maxlength="150" /><br />
				<small><?php _e('Full URI to a 24-bit, 70x20 custom transparent PNG. Like: http://yoursite.com/logo.png', $this->pluginName); ?></small><br />

				<br />
				<input type="radio" name="revver_flash_logo" id="revverFlashLogoUnbranded" value="unbranded" <?php if ($revver_flash_logo == "unbranded") echo 'checked="checked"'; ?> />
				<label for="revverFlashLogoUnbranded"><?php _e('Unbranded', $this->pluginName); ?></label><br />

				<br />
				<input type="checkbox" name="revver_flash_logo_updategrab" id="revverFlashLogoUpdateGrab" value="yes" <?php if ($revver_flash_logo_updategrab == "yes") echo 'checked="checked"'; ?> />
				<label for="revverFlashLogoUpdateGrab"><?php _e('Update grab code logos', $this->pluginName); ?></label>
			</td>
		</tr>
		<tr>
			<td><br /><label><?php _e('Auto Play:', $this->pluginName); ?></label></td>
			<td>
				<br />
				<input type="checkbox" name="revver_flash_autoplay" id="revverFlashAutoplay" value="yes" <?php if ($revver_flash_autoplay == "yes") echo 'checked="checked"'; ?> />
				<label for="revverFlashAutoplay"><?php _e('Start videos automatically on individual post view', $this->pluginName); ?></label>
			</td>
		</tr>
		<tr>
			<td><br /><label><?php _e('Full Screen:', $this->pluginName); ?></label></td>
			<td>
				<br />
				<input type="checkbox" name="revver_flash_allowfullscreen" id="revverFlashAllowFullscreen" value="yes" <?php if ($revver_flash_allowfullscreen == "yes") echo 'checked="checked"'; ?> />
				<label for="revverFlashAllowFullscreen"><?php _e('Allow viewers to watch videos in full screen mode', $this->pluginName); ?></label>
			</td>
		</tr>


		<tr>
			<td colspan="2">
				<br /><br />
				<h3><?php _e('Share Options', $this->pluginName); ?></h3>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="checkbox" name="revver_share_displayshare" id="revverShareDisplayshare" value="yes" <?php if ($revver_share_displayshare == "yes") echo 'checked="checked"'; ?> />
				<label for="revverShareDisplayshare"><?php _e('Display share button for Revver videos', $this->pluginName); ?></label>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<br />
				<input type="checkbox" name="revver_share_displaydetails" id="revverShareDisplaydetails" value="yes" <?php if ($revver_share_displaydetails == "yes") echo 'checked="checked"'; ?> />
				<label for="revverShareDisplaydetails"><?php _e('Display video details button for Revver videos', $this->pluginName); ?></label>
			</td>
		</tr>


		<tr>
			<td colspan="2">
				<br /><br />
				<p class="submit" style="text-align: left;"><input type="submit" name="submit" value="<?php _e('Update Config', $this->pluginName); ?> &raquo;" /></p>
			</td>
		</tr>
		</table>
		
		<input type="hidden" name="revver_use_staging" value="no" />
		<input type="hidden" name="revver_updating_config" value="1" />
	</fieldset>
	</form>
	
	<p>
		<?php _e('Developers can find more information about the Revver API at', $this->pluginName); ?>
		<a href="http://developer.revver.com/" target="_blank"><?php _e('http://developer.revver.com/', $this->pluginName); ?></a>.
	</p>
</div>