<?php
/**
$Id: create-video.php 184 2007-09-19 22:40:07Z gregbrown $
$LastChangedDate: 2007-09-19 15:40:07 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 184 $
$LastChangedBy: gregbrown $

This page creates the video on the revver system and then
returns the id itself in a json string.  This page is called
via javascript.

*/
$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

$video_id = 0;

$token 		 	= (!isset($_POST['token']) ? '' : $_POST['token']);
$title 		 	= (!isset($_POST['title']) ? '' : $_POST['title']);
$description 	= (!isset($_POST['description']) ? '' : $_POST['description']);
$keywords 	    = (!isset($_POST['keywords']) ? '' : $_POST['keywords']);
$keywordsArray  = explode(",", $keywords);
$credits 	    = (!isset($_POST['credits']) ? '' : $_POST['credits']);
$website 	    = (!isset($_POST['website']) ? '' : $_POST['website']);
$ageRestriction = (int) (!isset($_POST['ageRestriction']) ? 1 : $_POST['ageRestriction']);

$video_id = $revverWP->createVideo($token, $title, $description, $keywordsArray, $credits, $website, $ageRestriction);

// this page is called from an ajax request so we
// just return a json struct with the video id
echo '{"id" : "' . $video_id . '"}';
?>