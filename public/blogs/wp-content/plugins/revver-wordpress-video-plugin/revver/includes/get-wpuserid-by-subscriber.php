<?php
/**
$Id: get-wpuserid-by-subscriber.php 184 2007-09-19 22:40:07Z gregbrown $
$LastChangedDate: 2007-09-19 15:40:07 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 184 $
$LastChangedBy: gregbrown $

this page returns a json id that is the wordpress user id
that is connected to a revver subscriber.

*/
$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

$user_id = 0;
$subscriber = (!isset($_POST['subscriber']) ? '' : $_POST['subscriber']);

if (!empty($subscriber)) {
	$user_id = $revverWP->getWPUserIdBySubAccount($subscriber);
}

// this page is called from an ajax request so we
// just return a json struct with the user id
echo '{"id" : "' . $user_id . '"}';
?>