<?php
/**
$Id: delete-videos.php 184 2007-09-19 22:40:07Z gregbrown $
$LastChangedDate: 2007-09-19 15:40:07 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 184 $
$LastChangedBy: gregbrown $

This page deletes videos from the revver system and
also removes the videos from any posts or comments.

It is normally called from the user profile page as the
user's videos are listed on that page.
*/
$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

$video_ids = (!isset($_POST['revver_video_ids']) ? '' : $_POST['revver_video_ids']);

if (!empty($video_ids)) {
	$revverWP->deleteVideosOfCurrentUser(explode(",", $video_ids));
}

// this page is called from an ajax request so we
// just return a json struct with a simple message.
echo '{"msg" : "' . __('The video(s) have been deleted.', $revverWP->pluginName) . '"}';
?>