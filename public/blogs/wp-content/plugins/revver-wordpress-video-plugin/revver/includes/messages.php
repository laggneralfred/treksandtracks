<?php
/**
$Id: messages.php 110 2007-05-30 00:29:12Z gregbrown $
$LastChangedDate: 2007-05-29 17:29:12 -0700 (Tue, 29 May 2007) $
$LastChangedRevision: 110 $
$LastChangedBy: gregbrown $

this page outputs the latest 50 messages from revver.
*/

?>

<div class="wrap">
	<h2><?php _e('Revver Messages', $this->pluginName); ?></h2>

	<p><?php _e('This list is read-only and only contains the 50 most recent messages.  Login to <a href="http://www.revver.com/" target="_blank">Revver</a> to delete and/or see all of your messages.', $this->pluginName); ?></p>

	<table class="widefat">
	<tr class="thead">
		<th><?php _e('From', $this->pluginName); ?></th>
		<th><?php _e('Message', $this->pluginName); ?></th>
		<th><?php _e('Type', $this->pluginName); ?></th>
		<th><?php _e('Video', $this->pluginName); ?></th>
		<th><?php _e('Date', $this->pluginName); ?></th>
	</tr>
	<?php
		foreach($messages as $msg) {
			$class = ('alternate' == $class) ? '' : 'alternate';
			
			switch ($msg['code']) {
				case 901:
						$code = __('Urgent WP Message', $this->pluginName);
						$bg_style = "background-color: #FFCCCC;";
					break;
					
				case 902:
						$code = __('WP Update Available', $this->pluginName);
						$bg_style = "background-color: #FFFFCC;";
					break;
					
				case 903:
						$code = __('General WP Message', $this->pluginName);
						$bg_style = "background-color: #CCFFCC;";
					break;

				case 12201:
						$code = __('Urgent', $this->pluginName);
						$bg_style = "";
					break;
					
				case 12202:
						$code = __('Warning', $this->pluginName);
						$bg_style = "";
					break;
				
				default:
						$code = __('General', $this->pluginName);
						$bg_style = "";
					break;
			}
	?>
	<tr class="<?php echo $class; ?>" style="<?php echo $bg_style; ?>">
		<td valign="top"><?php echo $msg['sender']; ?></td>
		<td valign="top">
			<strong><?php echo $msg['subject']; ?></strong><br />
			<?php echo $msg['body']; ?>
		</td>
		<td valign="top"><?php echo $code; ?></td>
		<td valign="top">
			<?php
				if ( !empty($msg['video_id']) ) {
					echo '<a href="http://one.revver.com/watch/' . $video_id . '/affiliate/' . $this->userId . '" target="_blank">' . $video_id . '</a>';
				} else {
					echo '&nbsp;';
				}
			?>
		</td>
		<td valign="top" nowrap="nowrap">
			<?php echo date('M, j Y', strtotime($msg['created_on'])); ?><br />
			<?php echo date('g:i a', strtotime($msg['created_on'])); ?>
		</td>
	</tr>
	<?php
		}
	?>
	</table>

	<?php
		if ( !count($messages) ) {
			echo "<p>" . __('There are no messages at this time.', $this->pluginName) . "</p>";
		}
	?>
	
	<br /><br />

</div>