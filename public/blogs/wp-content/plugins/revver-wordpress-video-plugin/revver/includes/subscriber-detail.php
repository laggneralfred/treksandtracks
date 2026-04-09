<?php
/**
$Id: subscriber-detail.php 197 2007-09-24 01:55:11Z asirevver $
$LastChangedDate: 2007-09-23 18:55:11 -0700 (Sun, 23 Sep 2007) $
$LastChangedRevision: 197 $
$LastChangedBy: asirevver $

This is a form for updating the subscriber's profile on the 
revver system.
*/

?>

<?php if ( !empty($revver_message) ) { ?>
<div class="error">
	<ul>
		<li><?php echo $revver_message; ?></li>
	</ul>
</div>
<?php } ?>

<fieldset>
	<legend><?php _e('Revver Account Settings', $this->pluginName); ?></legend>

	<p>
		<label for="revver-broadcast">
			<input style="width: 15px;" type="checkbox" name="revver_broadcast" id="revver-broadcast" value="1" <?php echo ($subscriber['allowBroadcast'] != 0) ? 'checked="checked" ' : ''; ?>/>
			<?php _e('Broadcast Distribution', $this->pluginName); ?>
		</label><br />
		
		<label for="revver-mobile">
			<input style="width: 15px;" type="checkbox" name="revver_mobile" id="revver-mobile" value="1" <?php echo ($subscriber['allowMobile'] != 0) ? 'checked="checked" ' : ''; ?>/>
			<?php _e('Mobile Distribution', $this->pluginName); ?> &nbsp;|&nbsp; <a href="http://revver.com/go/faq#profile3" target="_blank"><?php _e('What\'s This?', $this->pluginName); ?></a>
		</label>
	</p>
	
	<p><?php _e('You must have a valid Paypal and mailing address to receive payment of any revenue generated from your videos.', $this->pluginName); ?></p>
	
	<p>
		<label for="revver-paypal">
			<?php _e('PayPal E-mail: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_paypal" id="revver-paypal" value="<?php echo htmlspecialchars($subscriber['paypal']); ?>" maxlength="150" />
		</label>
	</p>
	
	<p>
		<label for="revver-address1">
			<?php _e('Address Line 1: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_address1" id="revver-address1" value="<?php echo htmlspecialchars($subscriber['address']['address1']); ?>" maxlength="150" />
		</label>
	</p>
	
	<p>
		<label for="revver-address2">
			<?php _e('Address Line 2', $this->pluginName); ?><br />
			<input type="text" name="revver_address2" id="revver-address2" value="<?php echo htmlspecialchars($subscriber['address']['address2']); ?>" maxlength="150" />
		</label>
	</p>
	
	<p>
		<label for="revver-city">
			<?php _e('City: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_city" id="revver-city" value="<?php echo htmlspecialchars($subscriber['address']['city']); ?>" maxlength="150" />
		</label>
	</p>
	
	<p>
		<label for="revver-state">
			<?php _e('State/Province: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_state" id="revver-state" value="<?php echo htmlspecialchars($subscriber['address']['state']); ?>" maxlength="150" />
		</label>
	</p>
	
	<p>
		<label for="revver-postalcode">
			<?php _e('Postal Code: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_postalcode" id="revver-postalcode" value="<?php echo htmlspecialchars($subscriber['address']['postcode']); ?>" maxlength="20" />
		</label>
	</p>
	
	<p>
		<label for="revver-country">
			<?php _e('Country: (required)', $this->pluginName); ?><br />
			<input type="text" name="revver_country" id="revver-country" value="<?php echo htmlspecialchars($subscriber['address']['country']); ?>" maxlength="150" />
		</label>
	</p>

</fieldset>


<fieldset>
	<legend><?php _e('Revver Video Revenue', $this->pluginName); ?></legend>
	
	<h4><?php _e('Total Paid', $this->pluginName); ?>:  <span style="color: green;"><?php echo revver_currency_format($subscriber['balance']['paid'], 2, true, true, true); ?></span></h4>
	
	<h4 style="margin-bottom: 0px;"><?php _e('Pending Payment', $this->pluginName); ?>:  <span style="color: green;"><?php echo revver_currency_format($subscriber['balance']['available'], 2, true, true, true); ?></span></h4>
	<p style="margin-top: 5px;"><a href="http://revver.com/go/faq#makingmoney3" target="_blank"><?php _e('How do payments work?', $this->pluginName); ?></a></p>
	
	<h4 style="margin-bottom: 0px;"><?php _e('Current Earnings', $this->pluginName); ?>:  <span style="color: green;"><?php echo revver_currency_format($subscriber['balance']['pending'], 2, true, true, true); ?></span></h4>
	<p style="margin-top: 5px;"><?php _e('Earnings that are still processing.', $this->pluginName); ?></p>
	

</fieldset>