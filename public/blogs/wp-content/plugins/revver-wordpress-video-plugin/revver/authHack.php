<?php
/**
$Id: authHack.php 182 2007-09-19 20:28:24Z gregbrown $
$LastChangedDate: 2007-09-19 13:28:24 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 182 $
$LastChangedBy: gregbrown $
*/

// we have to get the wp code initialized before running any code
// on the videoSelector pages since the user must be logged into 
// their wp admin and we've got to have the ability to call 
// wp functions.
require_once(REVVERWP_ABSPATH . 'wp-config.php');
require_once(REVVERWP_ABSPATH . 'wp-admin/admin.php');
?>