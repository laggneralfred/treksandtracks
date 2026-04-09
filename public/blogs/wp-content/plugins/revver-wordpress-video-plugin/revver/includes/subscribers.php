<?php
/**
$Id: subscribers.php 119 2007-06-04 18:05:59Z gregbrown $
$LastChangedDate: 2007-06-04 11:05:59 -0700 (Mon, 04 Jun 2007) $
$LastChangedRevision: 119 $
$LastChangedBy: gregbrown $

This page renders a list of subscribers (includes a search form as well)
*/

?>

<div class="wrap">
	<h2><?php _e('Revver Subscribers (Child Accounts)', $this->pluginName); ?></h2>

	<form action="<?php echo $basePage; ?>" method="post" id="revverSubscribersSearchForm">
	<fieldset>
		<table>
		<tr>
			<td><label for="revverKeywords"><?php _e('Search', $this->pluginName); ?></label></td>
			<td><input type="text" name="revver_keywords" id="revverKeywords" value="<?php echo htmlspecialchars($revver_keywords); ?>" size="15" maxlength="20" /></td>
			<td>
				<select name="revver_orderby">
					<option value="createdDate"<?php if ($revver_orderby == "createdDate") echo 'selected="selected"'; ?>><?php _e('Order By Date Created', $this->pluginName); ?></option>
					<option value="balanceAvailable"<?php if ($revver_orderby == "balanceAvailable") echo 'selected="selected"'; ?>><?php _e('Order By Balance Available', $this->pluginName); ?></option>
				</select>
			</td>
			<td>
				<select name="revver_orderby_dir">
					<option value="1"<?php if ($revver_orderby_dir) echo 'selected="selected"'; ?>><?php _e('Asc', $this->pluginName); ?></option>
					<option value="0"<?php if (!$revver_orderby_dir) echo 'selected="selected"'; ?>><?php _e('Desc', $this->pluginName); ?></option>
				</select>
			</td>
			<td><input type="submit" name="submit" value="<?php _e('Search', $this->pluginName); ?> &raquo;" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="postback" value="1" />
	</fieldset>
	</form>

	<?php if ( $postback == 1 ) { ?>
	<br />
	<table class="widefat">
	<tr>
		<th colspan="3">&nbsp;</th>
		<th colspan="3" style="text-align: center;" class="alternate"><?php _e('Revenue', $this->pluginName); ?></th>
		<th colspan="3">&nbsp;</th>
	</tr>
	<tr class="thead">
		<th><?php _e('Revver Username', $this->pluginName); ?></th>
		<th><?php _e('E-mail', $this->pluginName); ?></th>
		<th><?php _e('PayPal E-mail', $this->pluginName); ?></th>
		<th><?php _e('Pending Payment', $this->pluginName); ?></th>
		<th><?php _e('Current Earnings', $this->pluginName); ?></th>
		<th><?php _e('Total Paid', $this->pluginName); ?></th>
		<th colspan="3" style="text-align: center;"><?php _e('Actions', $this->pluginName); ?></th>
	</tr>
	<?php
		foreach($subscribers as $subscriber) {
			$class = ('alternate' == $class) ? '' : 'alternate';
	?>
	<tr class="<?php echo $class; ?>">
		<td><?php echo $subscriber['login']; ?></td>
		<td><a href="mailto:<?php echo $subscriber['email']; ?>"><?php echo $subscriber['email']; ?></a></td>
		<td><a href="mailto:<?php echo $subscriber['paypal']; ?>"><?php echo $subscriber['paypal']; ?></a></td>
		<td><?php echo revver_currency_format($subscriber['balance']['available'], 2, true, true, true); ?></td>
		<td><?php echo revver_currency_format($subscriber['balance']['pending'], 2, true, true, true); ?></td>
		<td><?php echo revver_currency_format($subscriber['balance']['paid'], 2, true, true, true); ?></td>
		<td style="text-align: center;">
			<a href="<?php echo $basePage . "&revver_subscriber=" . $subscriber['login']; ?>"><?php _e('View Videos', $this->pluginName); ?></a> &nbsp;|&nbsp;
			<a href="javascript:loadEditUserBySubscriber('<?php echo $subscriber['login']; ?>');"><?php _e('Edit User', $this->pluginName); ?></a>
		</td>
	</tr>
	<?php
		}
	?>
	</table>
	<?php
		if (!$count) {
			echo "<p>" . __('No subscribers matched your search criteria.', $this->pluginName) . "</p>";
		} else {
			if ($count > $resultsPerPage) {
				echo "<br /><p class='revver-results-pagingmenu'>&nbsp; Page: ";
				$this->genPager($results, $pageNum, $baseURL, $resultsPerPage);
				echo "</p>";
			}
		}
	?>
	<?php } ?>
	
	<br /><br />

</div>