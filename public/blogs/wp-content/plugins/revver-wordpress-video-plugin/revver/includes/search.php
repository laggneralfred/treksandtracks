<?php
/**
$Id: search.php 226 2007-12-03 20:23:43Z gregbrown $
$LastChangedDate: 2007-12-03 12:23:43 -0800 (Mon, 03 Dec 2007) $
$LastChangedRevision: 226 $
$LastChangedBy: gregbrown $

This is a page for searching revver videos.
*/

$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 35);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 9);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVER_ABSPATH . 'authHack.php');

// setup the form field vals
$postback = (int) (!isset($_REQUEST['postback']) ? 0 : $_REQUEST['postback']);
$keywords = (!isset($_REQUEST['keywords']) ? __('Enter Keywords', $revverWP->pluginName) : $_REQUEST['keywords']);
$search_all = (boolean) (!isset($_REQUEST['search_all']) ? false : $_REQUEST['search_all']);

$resultsPerPage = 8;
$pageNum = (int) (!isset($_REQUEST['pageNum']) ? 1 : $_REQUEST['pageNum']);
$orderby = (!isset($_REQUEST['orderby']) ? 'publicationDate' : $_REQUEST['orderby']);
$orderbyAsc = (boolean) (!isset($_REQUEST['orderbyAsc']) ? false : $_REQUEST['orderbyAsc']);

$listStyle = (!isset($_REQUEST['listStyle']) ? 'thumb' : $_REQUEST['listStyle']);

if ( $postback == 1 ) {
	if (!$search_all) {
		$query = array();
		if (!empty($keywords)) $query['search'] = explode(" ", $keywords);
		$query['owners'] = array($revverWP->username);
		// $query['owners'] = array($revverWP->getCurrentRevverUsername()); // possible v2 enhancement...
	} else {
		$query = null;
		if (!empty($keywords)) $query = array('search' => explode(" ", $keywords));
	}
	if ( $pageNum == 1 ) {
		$offset = 0;
	} else {
		$offset = $resultsPerPage * ($pageNum - 1);
	}
	$results = $revverWP->searchVideos($query, $offset, $orderby, $orderbyAsc, $resultsPerPage);
	$count = $results[0];
	$videos = $results[1];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title><?php _e('Revver Video Selector Search', $revverWP->pluginName); ?></title>
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
		<a href="http://revver.com/" title="<?php _e('Revver', $revverWP->pluginName); ?>" target="_blank"><img src="<?php echo get_option('siteurl') . '/wp-content/plugins/' . $revverWP->pluginName ;?>/img/revver.gif" style="border: 0px;" /></a>
	</div>
	<div id="revver-gnav">
    	<ul id="revver-gnav-tabs">
        	<li><a href="<?php echo $PHP_SELF; ?>" class="current"><?php _e('VIDEOS', $revverWP->pluginName); ?></a></li>
        	<li><a href="post-upload.php"><?php _e('UPLOAD', $revverWP->pluginName); ?></a></li>
        </ul>
	</div>
</div>

<div id="revver-search-form">
	<form id="searchForm" method="get" action="<?php echo $PHP_SELF; ?>" onsubmit="clearDefault($('keywords'), '<?php _e('Enter Keywords', $revverWP->pluginName); ?>');">
		<table border="0" cellpadding="4">
		<tr>
			<td><input type="text" name="keywords" id="keywords" value="<?php echo htmlspecialchars($keywords); ?>" onfocus="clearDefault(this, '<?php _e('Enter Keywords', $revverWP->pluginName); ?>');" size="15" /></td>
			<td>
				<select name="search_all" id="search_all">
					<option value="0"<?php if ($search_all) echo 'selected="selected"' ;?>><?php _e('My Videos', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="1"<?php if ($search_all) echo 'selected="selected"' ;?>><?php _e('Full Revver Library', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td>
				<select name="orderby" id="orderby">
					<option value="publicationDate"><?php _e('Sort by', $revverWP->pluginName); ?>...</option>
					<option value="publicationDate"><?php _e('Publication', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="title"<?php if ($orderby == "title") echo 'selected="selected"' ;?>><?php _e('Title', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="views"<?php if ($orderby == "views") echo 'selected="selected"' ;?>><?php _e('Views', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="ratingAverage"<?php if ($orderby == "ratingAverage") echo 'selected="selected"' ;?>><?php _e('Avg. Rating', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="modifiedDate"<?php if ($orderby == "modifiedDate") echo 'selected="selected"' ;?>><?php _e('Last Modified', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="createdDate"<?php if ($orderby == "createdDate") echo 'selected="selected"' ;?>><?php _e('Creation', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td>
				<select name="orderbyAsc" id="orderbyAsc">
					<option value="1"<?php if ($orderbyAsc) echo 'selected="selected"' ;?>><?php _e('Asc', $revverWP->pluginName); ?> &nbsp;</option>
					<option value="0"<?php if (!$orderbyAsc) echo 'selected="selected"' ;?>><?php _e('Desc', $revverWP->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td><input type="submit" id="btnSubmit" value="<?php _e('Search', $revverWP->pluginName); ?>" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="postback" value="1" />
	</form>
</div>

<?php if ( $postback == 1 ) { ?>
<form id="searchResultsForm" method="get" action="<?php echo $PHP_SELF; ?>" style="margin: 0px;">
	<div id="revver-results-header">
		<div style="float: left;">
			<p><?php if (!$search_all) { _e('MY VIDEOS', $revverWP->pluginName); } else { _e('FULL REVVER LIBRARY', $revverWP->pluginName); } ?></p>
		</div>
		<div style="float: right; display: none;">
			<p>
				<a href="#" onclick="$('listStyle').value = 'thumb'; $('searchResultsForm').submit();" <?php if ($listStyle == "thumb") { echo 'style="color: #999;"'; } ?>><?php _e('THUMB', $revverWP->pluginName); ?></a>
				 &nbsp;|&nbsp;
				<a href="#" onclick="$('listStyle').value = 'list'; $('searchResultsForm').submit();" <?php if ($listStyle == "list") { echo 'style="color: #999;"'; } ?>><?php _e('LIST', $revverWP->pluginName); ?></a> 
			</p>
		</div>
		<br clear="both" />
	</div>

	<div id="revver-results-content">
	<?php
		if (!$count) {
			echo "<p>" . __('No videos matched your search criteria.', $revverWP->pluginName) . "</p>";
		} else {
			if ($count > $resultsPerPage) {
				$baseURL = $PHP_SELF . "?keywords=" . htmlspecialchars($keywords) . "&search_all=" . $search_all . "&orderby=" . htmlspecialchars($orderby) . "&orderbyAsc=" . $orderbyAsc . "&listStyle=" . $listStyle . "&postback=1";
				echo "<p class='revver-results-pagingmenu'>";
				$revverWP->genPager($results, $pageNum, $baseURL);
				echo "</p>";
			}
		}
		
		if ( $listStyle == "list" ) {
			foreach($videos as $video) {
	?>
		<div class="revver-results-video" style="float: none; width: 80%;">
			<p>
				<strong><?php echo $video["title"]; ?></strong><br />
				<?php echo $video["description"]; if (!empty($video["description"])) echo "<br />"; ?>
				[ <a href="javascript:setPostVideoId(<?php echo $video["id"]; ?>);"><?php _e('Post Video', $revverWP->pluginName); ?></a>  &nbsp;|&nbsp;
				<a href="http://revver.com/watch/<?php echo $video["id"]; ?>/affiliate/<?php echo $revverWP->userId; ?>" target="_blank" title="<?php _e('Watch', $revverWP->pluginName); ?> <?php echo htmlspecialchars($video["title"]); ?>"><?php _e('Watch Video', $revverWP->pluginName); ?></a> ]<br />
				&nbsp;
			</p>
		</div>
	<?php
			}
		} else {
			$cols = 1;
			foreach($videos as $video) {
	?>
		<div class="revver-results-video">
			<a href="video-details.php?id=<?php echo $video["id"]; ?>" title="<?php echo htmlspecialchars($video["title"]); ?>"><img src="<?php echo $video["thumbnailUrl"]; ?>" width="120" height="90" /></a>
			<div style="padding-top: 2px; width: 100px; float: left;">
				<p>
					<?php echo $video["title"]; ?><br />
					<a href="javascript:setPostVideoId(<?php echo $video["id"]; ?>);"><?php _e('Post Video', $revverWP->pluginName); ?></a>
				</p>
			</div>
			<div style="padding-top: 5px; width: 15px; float: right;">
				<a href="http://revver.com/watch/<?php echo $video["id"]; ?>/affiliate/<?php echo $revverWP->userId; ?>" target="_blank" title="<?php _e('Watch', $revverWP->pluginName); ?> <?php echo htmlspecialchars($video["title"]); ?>"><img src="../img/newwindow.png" style="border: 0px;" /></a>
			</div>
		</div>
	<?php
				$cols += 1;
				if ( $cols > 4  ) {
					echo '<br clear="both" />';
					$cols = 1;
				}
			}
		}
	?>
	</div>
	
	<input type="hidden" name="keywords" value="<?php echo htmlspecialchars($keywords); ?>" />
	<input type="hidden" name="search_all" value="<?php echo $search_all; ?>" />
	<input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
	<input type="hidden" name="orderby" value="<?php echo htmlspecialchars($orderby); ?>" />
	<input type="hidden" name="orderbyAsc" value="<?php echo $orderbyAsc; ?>" />
	<input type="hidden" name="listStyle" id="listStyle" value="<?php echo $listStyle; ?>" />
	<input type="hidden" name="postback" value="1" />
</form>
<?php } ?>

</body>
</html>