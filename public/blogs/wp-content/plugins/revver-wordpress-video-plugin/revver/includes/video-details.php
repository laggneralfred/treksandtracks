<?php
/**
$Id: video-details.php 184 2007-09-19 22:40:07Z gregbrown $
$LastChangedDate: 2007-09-19 15:40:07 -0700 (Wed, 19 Sep 2007) $
$LastChangedRevision: 184 $
$LastChangedBy: gregbrown $

Shows the details of a revver video.  The video data is pulled
from the revver api.
*/

$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

$id = (int) (!isset($_REQUEST['id']) ? 0 : $_REQUEST['id']);
$fromSearch = (int) (!isset($_REQUEST['fromSearch']) ? 1 : $_REQUEST['fromSearch']);
$video = $revverWP->getVideoById($id);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title><?php _e('Revver Video Details', $revverWP->pluginName); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
wp_print_scripts( array('prototype') );
$revverWP->includeAdminCss();
$revverWP->includeAdminJs();
?>
</head>

<body>

<div id="revver-header">
	<div id="revver-logo">
		<a href="http://www.revver.com/" title="<?php _e('Revver', $revverWP->pluginName); ?>" target="_blank"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/' . $revverWP->pluginName ;?>/img/revver.gif" style="border: 0px;" /></a>
	</div>
	<div id="revver-gnav">
    	<ul id="revver-gnav-tabs">
        	<li><a href="search.php" class="current"><?php _e('VIDEOS', $revverWP->pluginName); ?></a></li>
        	<li><a href="post-upload.php"><?php _e('UPLOAD', $revverWP->pluginName); ?></a></li>
        </ul>
	</div>
</div>

<div id="revver-search-form">
	<form id="searchForm" method="get" action="search.php" onsubmit="clearDefault($('keywords'), '<?php _e('Enter Keywords', $revverWP->pluginName); ?>');">
		<table border="0" cellpadding="4">
		<tr>
			<td><input type="text" name="keywords" id="keywords" value="<?php _e('Enter Keywords', $revverWP->pluginName); ?>" onfocus="clearDefault(this, '<?php _e('Enter Keywords', $revverWP->pluginName); ?>');" size="15" /></td>
			<td>
				<select name="search_all" id="search_all">
					<option value="0"><?php _e('My Videos', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="1"><?php _e('Full Revver Library', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td>
				<select name="orderby" id="orderby">
					<option value="publicationDate"><?php _e('Sort by', $revverWP->pluginName); ?>...</option>
					<option value="publicationDate"><?php _e('Publication', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="title"><?php _e('Title', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="views"><?php _e('Views', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="ratingAverage"><?php _e('Avg. Rating', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="modifiedDate"><?php _e('Last Modified', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="createdDate"><?php _e('Creation', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td>
				<select name="orderbyAsc" id="orderbyAsc">
					<option value="1"><?php _e('Asc', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="0" selected="selected"><?php _e('Desc', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td><input type="submit" id="btnSubmit" value="<?php _e('Search', $revverWP->pluginName); ?>" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="postback" value="1" />
	</form>
</div>

<div id="revver-results-header">
	<?php
		if ( empty($video['owner']) ) {
			echo "<p class='error'>" . __('The video with id:', $revverWP->pluginName) . $id . __(' doesn\'t exist.</p>', $revverWP->pluginName);
		}
	?>
	<div style="float: left;">
		<p><?php echo $video['title']; ?></p>
	</div>
	<div style="float: right">
		<p>
			<?php if ($fromSearch) { ?>
			<a href="javascript:history.go(-1);"><?php _e('back to results', $revverWP->pluginName); ?></a>
			<?php } ?>
		</p>
	</div>
	<br clear="both" />
</div>

<table border="0" cellpadding="5">
<tr>
	<td valign="top">
		<script type="text/javascript" src="<?php echo $video['flashJsUrl']; ?>"></script>
	</td>
	<td valign="top" class="revver-results-video">
		<p>
			<strong><?php _e('Owner', $revverWP->pluginName); ?>:</strong> <?php echo $video['owner']; ?><br />
			<strong><?php _e('Duration', $revverWP->pluginName); ?>:</strong> <?php echo $video['duration']; ?><br />
			<strong><?php _e('Age Rating', $revverWP->pluginName); ?>:</strong> <?php echo $video['ageRestriction']; ?><br />
			<strong><?php _e('Views', $revverWP->pluginName); ?>:</strong> <?php echo $video['views']; ?>
		</p>
		<p>
			<strong><a href="javascript:setPostVideoId(<?php echo $video["id"]; ?>);"><?php _e('Post Video', $revverWP->pluginName); ?></a></strong>
		</p>
		<p>
			<strong><?php _e('Wordpress Embed', $revverWP->pluginName); ?></strong>:<br />
			<form id="wpEmbed">
				<textarea id="wordpressEmbed" cols="22" rows="2"><?php echo '<script type="text/javascript" src="' . $video['flashJsUrl'] . '"></script>'; ?></textarea>
			</form>
		</p>
	</td>
</tr>
</table>

</body>
</html>