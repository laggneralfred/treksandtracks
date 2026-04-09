<?php
/**
$Id: comment-upload.php 231 2008-04-30 21:06:54Z gregbrown $
$LastChangedDate: 2008-04-30 14:06:54 -0700 (Wed, 30 Apr 2008) $
$LastChangedRevision: 231 $
$LastChangedBy: gregbrown $

This is the upload form that is called from the "upload video" link
that is on the comments-form-fields.php file.  A user must be
logged in to execute this page.

Once a user uploads the video they are shown the video id and then
a link which will post the id back to the comment form.
*/
$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

$tokens = $revverWP->getUploadTokens();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title><?php _e('Revver Video Upload', $revverWP->pluginName); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<?php
wp_print_scripts( array('prototype') );
$revverWP->includeAdminCss();
$revverWP->includeAdminJs();
?>

<script type="text/javascript">
//<![CDATA[
var __meta_data_complete = false;
var __video_upload_complete = false;
var __video_upload_inprogress = false;
var __upload_only = false;
var __auto_publish = false;
var __video_id = "";
//]]>
</script>
</head>

<body style="margin: 0px;">

<div id="revver-uploader-header">
	<h1><?php bloginfo('name'); ?></h1>
	<h2><?php _e('Video Uploader', $revverWP->pluginName); ?></h2>
	<div id="revver-uploader-logo">
		<a href="http://www.revver.com/" title="Revver" target="_blank"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/' . $revverWP->pluginName; ?>/img/powered-by-revver.gif" style="border: 0px;" /></a>
	</div>
</div>

<div id="revver-uploader">
	<embed
		src="http://widget.revver.com/flash/uploader/1.0/uploader.swf?sessionToken=<?php echo $tokens[1][0]; ?>&onStart=javascript:revver_uploadStart()&onSuccess=javascript:revver_uploadSuccess()&onFail=javascript:revver_uploadFail()&onCancel=javascript:revver_uploadCancel()<?php if ($revverWP->isStagingMode()) { echo "&staging=revver"; } ?>"
		quality="high"
		width="500"
		height="172"
		type="application/x-shockwave-flash"
		pluginspage="http://www.macromedia.com/go/getflashplayer"
		allowscriptaccess="always"
		id="revver-video-uploader">
	</embed>
</div>

<div id="revver-video-metaform-container" style="clear: both;">
	<form id="revver-video-metaform" action="">
	<fieldset class="revver-fieldset">
		<table border="0" cellpadding="4">
		<tr>
			<td>
				<label for="title" class="revver-label">
					<?php _e('Title', $revverWP->pluginName); ?>&nbsp;
					<span class="required"><?php _e('(required)', $revverWP->pluginName); ?></span>
				</label>
			</td>
		</tr>
		<tr>
			<td><input type="text" name="title" id="title" value="" size="60" maxlength="250" /></td>
		</tr>
		<tr>
			<td>
				<label for="description" class="revver-label">
					<?php _e('Description', $revverWP->pluginName); ?>&nbsp;
					<span class="required"><?php _e('(required)', $revverWP->pluginName); ?></span>
				</label>
			</td>
		</tr>
		<tr>
			<td><textarea name="description" id="description" cols="45" rows="2"></textarea></td>
		</tr>
		<tr>
			<td>
				<label for="keywords" class="revver-label">
					<?php _e('Keywords', $revverWP->pluginName); ?>&nbsp;
					<span class="required"><?php _e('(required)', $revverWP->pluginName); ?></span>
					<small style="margin-left: 5px; color: #666; font-weight: normal;"><?php _e('( separate with commas | 100 character limit )', $revverWP->pluginName); ?></small>
				</label>
			</td>
		</tr>
		<tr>
			<td><input type="text" name="keywords" id="keywords" value="" size="60" maxlength="100" /></td>
		</tr>
		<tr>
			<td><label for="credits" class="revver-label"><?php _e('Credits', $revverWP->pluginName); ?></label></td>
		</tr>
		<tr>
			<td><input type="text" name="credits" id="credits" value="" size="60" maxlength="250" /></td>
		</tr>
		<tr>
			<td><label for="website" class="revver-label"><?php _e('Website', $revverWP->pluginName); ?></label></td>
		</tr>
		<tr>
			<td><input type="text" name="website" id="website" value="" size="60" maxlength="250" /></td>
		</tr>
		<tr>
        	<td><label class="revver-label"><?php _e('Age Rating', $revverWP->pluginName); ?></label></td>
		</tr>
		<tr>
        	<td>
            	<select name="ageRestriction" style="width: 385px;">
                	<option value="1"><?php _e('General', $revverWP->pluginName); ?></option>
                	<option value="3"><?php _e('13+', $revverWP->pluginName); ?></option>
                	<option value="4"><?php _e('17+', $revverWP->pluginName); ?></option>
                	<option value="5"><?php _e('17+ Explicit', $revverWP->pluginName); ?></option>
            	</select>
        	</td>
        </tr>
		<tr>
        	<td colspan="2">
        		<br />
				<input type="checkbox" name="agree_to_terms" id="agreeToTerms" value="1" />
				<label class="revver-label" for="agreeToTerms"><?php _e('I agree to the', $revverWP->pluginName); ?> <a href="http://revver.com/go/tou" target="_blank"><?php _e('Terms of Service', $revverWP->pluginName); ?></a></label>
        	</td>
		</tr>
        <tr>
        	<td>
        		<br />
        		<input type="button" name="btnCommit" value="<?php _e('Commit Settings', $revverWP->pluginName); ?>" onclick="validateMetaForm(true);" />
        	</td>
        </tr>
		</table>

		<input type="hidden" name="token" id="token" value="<?php echo $tokens[1][0]; ?>" />
	</fieldset>

	<br />
	</form>
</div>

<div id="revver-results-content">
	<div id="revver-video-uploadsuccess" style="display: none; width: 500px; padding-top: 0px; margin-top: 0px;">
		<h2 class="revver-msg-headline" style="margin-top: 0px;"><?php _e('Your upload was successful!', $revverWP->pluginName); ?></h2>
		<p class="bigger"><?php _e('You have successfully submitted your video\'s details. Do not navigate away from this page until the video has been fully uploaded. As soon as the upload is complete, your video will be submitted for review and added to the Revver library.<br /><br />You\'ll receive a notice in your Dashboard when your video is ready to view.', $revverWP->pluginName); ?></p>
		<h3 class="revver-msg-subheadline"><?php _e('Your video id is:', $revverWP->pluginName); ?> <strong><span id="revverVideoId" class="revverVideoId"></span></strong></h3>
		<p><a href="javascript:setCommentVideoIdPostUpload(__video_id, true);"><strong><?php _e('Close Uploader', $revverWP->pluginName); ?></strong></a></p>
	</div>
</div>

</body>
</html>