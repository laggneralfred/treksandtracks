<?php
/**
$Id: subscriber-videos.php 113 2007-06-01 21:18:48Z gregbrown $
$LastChangedDate: 2007-06-01 14:18:48 -0700 (Fri, 01 Jun 2007) $
$LastChangedRevision: 113 $
$LastChangedBy: gregbrown $

This page renders a list of videos that this user/subscriber has
uploaded to revver.
*/

?>

<div class="wrap">

	<h2><?php echo $login . __('\'s Revver Videos', $this->pluginName); ?></h2>
	
	<fieldset style="border: 1px solid #ccc; padding-left: 10px;">
		<legend><strong><?php echo $login . __('\'s Video Library', $this->pluginName); ?></strong></legend>
		<table style="margin-left: 10px;">
		<tr>
			<td><strong><?php _e('Total:', $this->pluginName); ?></strong></td>
			<td><?php echo $video_stats['total']; ?></td>
		</tr>
		<tr>
			<td><?php _e('Online:', $this->pluginName); ?></td>
			<td><?php echo $video_stats['online']; ?></td>
		</tr>
		<tr>
			<td><?php _e('Offline:', $this->pluginName); ?></td>
			<td><?php echo $video_stats['offline']; ?></td>
		</tr>
		</table>
	</fieldset>
	
	<br />
	<form action="<?php echo $basePage; ?>" method="post" id="revverVideosSearchForm">
	<fieldset>
		<table>
		<tr>
			<td><label for="revverKeywords"><?php _e('Search', $this->pluginName); ?></label></td>
			<td><input type="text" name="revver_keywords" id="revverKeywords" value="<?php echo htmlspecialchars($revver_keywords); ?>" size="15" maxlength="20" /></td>
			<td>
				<select name="revver_orderby">
					<option value="publicationDate"><?php _e('Sort by', $this->pluginName); ?>...</option>
					<option value="publicationDate"><?php _e('Publication', $this->pluginName); ?> &nbsp;</option>
					<option value="title"<?php if ($revver_orderby == "title") echo 'selected="selected"' ;?>><?php _e('Title', $this->pluginName); ?> &nbsp;</option>
					<option value="views"<?php if ($revver_orderby == "views") echo 'selected="selected"' ;?>><?php _e('Views', $this->pluginName); ?> &nbsp;</option>
					<option value="ratingAverage"<?php if ($revver_orderby == "ratingAverage") echo 'selected="selected"' ;?>><?php _e('Avg. Rating', $this->pluginName); ?> &nbsp;</option>
					<option value="modifiedDate"<?php if ($revver_orderby == "modifiedDate") echo 'selected="selected"' ;?>><?php _e('Last Modified', $this->pluginName); ?> &nbsp;</option>
					<option value="createdDate"<?php if ($revver_orderby == "createdDate") echo 'selected="selected"' ;?>><?php _e('Creation', $this->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td>
				<select name="revver_orderby_dir">
					<option value="1"<?php if ($revver_orderby_dir) echo 'selected="selected"' ;?>><?php _e('Asc', $this->pluginName); ?> &nbsp;</option>
					<option value="0"<?php if (!$revver_orderby_dir) echo 'selected="selected"' ;?>><?php _e('Desc', $this->pluginName); ?> &nbsp;</option>
				</select>
			</td>
			<td><input type="submit" name="submit" value="<?php _e('Search', $this->pluginName); ?> &raquo;" /></td>
		</tr>
		</table>

		<input type="hidden" name="postback" value="1" />
	</fieldset>
	</form>
	
	
	<?php
		$ctr = 1;
		$min_height = round(($count/4)*120) + 100;
		
		if ( $postback == 1 ) {
	?>
	<br />
	<div id="revver-results-content" style="padding-top: 0px; margin-top: 0px; min-height: <?php echo $min_height; ?>px;">
	<?php
		foreach($videos as $video) {
	?>
		<div class="revver-results-video">
			<a href="<?php echo $basePage . "&revver_video_id=" . $video["id"]; ?>" title="<?php _e('Edit', $this->pluginName); ?> <?php echo htmlspecialchars($video["title"]); ?>" style="text-decoration: none; border: 0px;"><img src="<?php echo $video["thumbnailUrl"]; ?>" width="120" height="90" /></a>
			<p style="overflow: hidden;">
				<strong><?php echo $video["title"]; ?></strong><br />
				Views: <?php echo $video["views"]; ?><br />
				Status: <?php echo $video["status"]; ?>
			</p>
		</div>
	<?php
			if ( $ctr > 4 ) {
				echo '<br clear="both" />';
				$ctr = 0;
			}
			$ctr += 1;
		}
	?>
	
	<?php
		if (!$count) {
			echo "<p>" . __('No videos matched your search criteria.', $this->pluginName) . "</p>";
		} else {
			if ($count > $resultsPerPage) {
				echo "<br clear='both' /><p class='revver-results-pagingmenu'>&nbsp; Page: ";
				$this->genPager($results, $pageNum, $baseURL, $resultsPerPage);
				echo "</p>";
			}
		}
	?>
	</div>
	<?php } ?>
	
	<br /><br />

</div>