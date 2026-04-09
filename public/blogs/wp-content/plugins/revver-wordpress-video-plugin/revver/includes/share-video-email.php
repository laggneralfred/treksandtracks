<?php
/**
$Id: share-video-email.php 184 2007-09-19 22:40:07Z gregbrown $
$LastChangedDate: 2007-09-19 15:40:07 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 184 $
$LastChangedBy: gregbrown $

This file is called via javascript when the user sends
a video to their friend.
*/

$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVERWP_ABSPATH . 'wp-config.php');

$msg       = (!isset($_POST['msg']) ? '' : $_POST['msg']);
$toemail   = (!isset($_POST['toemail']) ? '' : $_POST['toemail']);
$fromemail = (!isset($_POST['fromemail']) ? '' : $_POST['fromemail']);
$permalink = (!isset($_POST['permalink']) ? '' : $_POST['permalink']);

$send = true;
$sent = false;

if ( $msg == "" ) $send = false;
if ( !is_email($toemail) ) $send = false;
if ( !is_email($fromemail) ) $send = false;
if ( $permalink == "" ) $send = false;

if ($send) {

	$message = sprintf(__("Dear %1\$s

%2\$s has sent you a video they thought you might enjoy. Their message is below:

%3\$s

Click below to watch the video:
%4\$s

---
%5\$s

", $revverWP->pluginName), $toemail, $fromemail, $msg, $permalink, get_option('siteurl'));

	$subject = sprintf(__("%1\$s has sent you a video", $revverWP->pluginName), $fromemail);

	$headers = "MIME-Version: 1.0\n" .
		"From: " . $fromemail . "\n" . 
		"Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

	$sent = wp_mail($toemail, $subject, $message, $headers);
}

// this page is called from an ajax request so we
// just return a json struct with a message
if ($sent) {
	echo '{"msg" : "' . __('Your email has been sent.', $revverWP->pluginName) . '"}';
} else {
	echo '{"msg" : "' . __('The email could not be sent.  Make sure you typed in the to and from email addresses correctly.', $revverWP->pluginName) . '"}';
}
?>