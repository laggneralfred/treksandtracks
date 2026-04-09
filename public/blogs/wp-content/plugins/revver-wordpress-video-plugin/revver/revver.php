<?php
/**
Plugin Name: Revver Video WordPress Plugin
Plugin URI: http://revver.com/go/wp/
Description: Transforms your blog into a full video portal, letting you manage and upload videos directly from your blog admin pages, register child users on the Revver system, manage Revver child user accounts, and letting visitors post video responses they can earn money for.
Author: Revver
Version: 1.0.2 (20080430)
Author URI: http://revver.com/go/wp/
*/

if(!defined('REVVER_ABSPATH')) {
	define('REVVER_ABSPATH', dirname(__FILE__) . "/");
}
include(REVVER_ABSPATH . "RevverAPI.php");

class RevverWP {

	/**
	 * properties
	 */
	var $pluginName    = "revver";
	var $pluginVersion = "1.0.2 (20080430)";

	var $userId 	   = "";
	var $username      = "";
	var $password      = "";

	var $apiBaseURL 	   = "https://api.revver.com/xml/1.0?";
	var $apiBaseURLStaging = "https://api.staging.revver.com/xml/1.0?";
	var $apiURL 	       = "";
	var $useStaging        = false;
	var $api; // this will be the RevverAPI object.

	/**
	 * custom tables used by this plugin
	 */
	var $db_posts 	   = "posts_revver";
	var $db_comments   = "comments_revver";

	/**
	 * custom fields that this plugin will cause WordPress
	 * to select as apart of the loop and other areas.
	 */
	var $posts_fields  	 = "";
	var $comments_fields = "";

	/**
	 * constructor
	 *
	 */
	function RevverWP() {
		global $wpdb;

		$this->db_posts        = $wpdb->prefix . $this->db_posts;
		$this->db_comments	   = $wpdb->prefix . $this->db_comments;

		$this->posts_fields    = $this->db_posts . ".*";
		$this->comments_fields = $this->db_comments . ".*";

		$this->userId		   = get_option($this->pluginName . "_user_id");
		$this->username        = get_option($this->pluginName . "_username");
		$this->password        = get_option($this->pluginName . "_password");

		$this->setStagingMode( get_option($this->pluginName . "_use_staging") );
		$this->setApiURL(); // sets the correct url (staging or live)
		$this->api = new RevverAPI( $this->getApiURL() );
		$this->api->setCurlProxy( get_option($this->pluginName . "_curl_proxy") );

		// set the text domain for this plugin
		load_plugin_textdomain($this->pluginName, $path = 'wp-content/plugins/' . $this->pluginName);

		add_action('activate_revver/revver.php',array(&$this, 'activate'));
		add_action('deactivate_revver/revver.php',array(&$this, 'deactivate'));

		add_action('posts_join', 				array(&$this, 'getPostsJoin'));
		add_action('posts_fields', 				array(&$this, 'getPostsFields'));

		add_action('the_content', 				array(&$this, 'embedVideoOnPost'));

		add_action('edit_form_advanced', 		array(&$this, 'addPostFormFields'));
		add_action('publish_post', 				array(&$this, 'savePost'));
		add_action('save_post', 				array(&$this, 'savePost'));
		add_action('edit_post', 				array(&$this, 'editPost'));
		add_action('delete_post', 				array(&$this, 'deletePost'));

		add_action('comment_post', 				array(&$this, 'saveComment'));
		add_action('wp_set_comment_status', 	array(&$this, 'updateComment'));
		add_action('delete_comment', 			array(&$this, 'deleteComment'));
		add_action('comment_form', 				'revver_addCommentFormFields'); 		// function is at the bottom of this page
		add_action('comment_text', 				'revver_embedVideoOnAdminComment'); 	// function is at the bottom of this page
		add_action('comment_text', 				'revver_embedVideoThumbOnComment'); 	// function is at the bottom of this page
		add_action('comment_excerpt', 			'revver_embedVideoLinkOnAdminComment'); // function is at the bottom of this page

		add_action('admin_menu', 				'revver_registerCustomPages'); 			// function is at the bottom of this page
		add_action('admin_head', 				'revver_setIsInAdmin'); 				// function is at the bottom of this page
		add_action('admin_head',				array(&$this, 'includeRevverWidget'));
		add_action('admin_head', 				array(&$this, 'includeAdminCss'));
		add_action('admin_head', 				array(&$this, 'includeAdminJs'));

		add_action('wp_head', 					array(&$this, 'includeRevverWidget'));
		add_action('wp_head', 					array(&$this, 'includePublicCss'));
		add_action('wp_head', 					array(&$this, 'includeAdminJs'));
		add_action('wp_footer', 				array(&$this, 'initLightbox')); // makes sure that all the comment thumbs' lightboxes work

		add_action('user_register', 			array(&$this, 'saveSubAccount'));
		add_action('profile_update', 			array(&$this, 'saveSubscriber'));
		add_action('delete_user',	 			array(&$this, 'deleteSubAccount'));
		add_action('edit_user_profile',			array(&$this, 'includeSubscribersDetails'));
		add_action('show_user_profile',			array(&$this, 'includeSubscribersDetails'));

		add_action('register_form',				array(&$this, 'includeAdminCss'));
		add_action('register_form',	 			array(&$this, 'includeRegisterAgreement'));

		add_action('rss2_ns',	 				array(&$this, 'includeRssNameSpaces'));
		add_action('rss2_item',	 				array(&$this, 'includeRssVideoLink'));

		add_action('commentsrss2_head',			array(&$this, 'flagInCommentRss')); // this is needed to get around a bug in WP that doesn't announce posts_join when showing the comments rss feed

		add_action('activity_box_end',			array(&$this, 'includeMessageCount'));

		// add an action to handle the event that will automatically check for
		// videos/posts that are set to autopublish once the revver video is approved.
		add_filter('cron_schedules', array(&$this, 'add3HourSchedule'));
		add_filter('cron_schedules', array(&$this, 'add30MinuteSchedule'));
		add_action($this->pluginName . '_hourly_autopublish', array(&$this, 'autoPublish'));
		add_action($this->pluginName . '_every3hours_autopublish', array(&$this, 'autoPublish'));
		add_action($this->pluginName . '_every30minutes_autopublish', array(&$this, 'autoPublish'));
	}

	/**
	 * activates the plugin and if needed it creates/alters the
	 * database tables that this plugin uses.
	 *
	 */
	function activate() {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

		// if ($wpdb->get_var("SHOW TABLES LIKE '$this->db_posts'") != $this->db_posts) {
			$sql = "CREATE TABLE " . $this->db_posts . " (
				post_id int(10) unsigned NOT NULL default '0',
				video_id int(10) unsigned NOT NULL default '0',
				video_owner varchar(45) NOT NULL,
				video_status varchar(20) NOT NULL,
				auto_publish tinyint(1) NOT NULL,
				is_auto_published tinyint(1) NOT NULL,
				allow_video_comments tinyint(1) NOT NULL,
				collection_id int(10) unsigned NOT NULL default '0',
				PRIMARY KEY  (post_id)
			);";
			dbDelta($sql);
		// }

		// if ($wpdb->get_var("SHOW TABLES LIKE '$this->db_comments'") != $this->db_comments) {
			$sql = "CREATE TABLE " . $this->db_comments . " (
				comment_id int(10) unsigned NOT NULL default '0',
				video_id int(10) unsigned NOT NULL default '0',
				video_owner varchar(45) NOT NULL,
				video_status varchar(20) NOT NULL,
				is_auto_published tinyint(1) NOT NULL,
				PRIMARY KEY  (comment_id)
			);";
			dbDelta($sql);
		// }

		// add all of the options that this plugin will use.
		// add_option(name, default, description, autoload);
		// BASIC OPTIONS
		add_option($this->pluginName . "_plugin_version", $this->pluginVersion, __("Revver Plugin Version", $this->pluginName), "yes");
		add_option($this->pluginName . "_username", "", __("Revver Username", $this->pluginName), "yes");
		add_option($this->pluginName . "_password", "", __("Revver Password", $this->pluginName), "yes");
		add_option($this->pluginName . "_user_id", "", __("Revver User Id", $this->pluginName), "yes");
		add_option($this->pluginName . "_use_staging", "no", __("Use Revver Staging API", $this->pluginName), "yes");
		add_option($this->pluginName . "_curl_proxy", "", __("Revver cURL Proxy", $this->pluginName), "yes");
		add_option($this->pluginName . "_anon_video_response", "no", __("Allow Anonymous User Video Responses", $this->pluginName), "yes");

		// AUTO PLAYLIST OPTIONS
		add_option($this->pluginName . "_playlist_id", "", __("Revver Auto Playlist Id", $this->pluginName), "yes");

		// FLASH PLAYER OPTIONS
		add_option($this->pluginName . "_flash_width", "480", __("Revver Flash Player Width", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_height", "360", __("Revver Flash Player Height", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_logo", "", __("Revver Flash Logo Option", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_logo_uri", "", __("Revver Flash Logo Custom URI", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_logo_updategrab", "yes", __("Revver Flash Logo Option - Update Grab code", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_autoplay", "yes", __("Revver Flash AutoPlay on Single Post View", $this->pluginName), "yes");
		add_option($this->pluginName . "_flash_allowfullscreen", "yes", __("Revver Flash Allow Full Screen", $this->pluginName), "yes");

		// SHARE OPTIONS
		add_option($this->pluginName . "_share_displayshare", "yes", __("Revver Show Video Share Button", $this->pluginName), "yes");
		add_option($this->pluginName . "_share_displaydetails", "yes", __("Revver Show Video Details Button", $this->pluginName), "yes");

		// add capabilities to the administrator role so we can use
		// current_user_can function in some areas to protect the precious videos.  :)
		$role = get_role('administrator');
		$role->add_cap($this->pluginName . '_delete_videos');

		// schedule the event that will automatically check for videos/posts
		// that are set to autopublish once the revver video is approved.
		// it also autopublishes the video comments that have been uploaded.
		// wp_schedule_event(time(), 'hourly', $this->pluginName . '_hourly_autopublish');
		// wp_schedule_event(time(), 'every3hours', $this->pluginName . '_every3hours_autopublish');
		wp_schedule_event(time(), 'every30minutes', $this->pluginName . '_every30minutes_autopublish');
	}

	/**
	 * de-activates the plugin
	 *
	 */
	function deactivate() {
		wp_clear_scheduled_hook($this->pluginName . '_hourly_autopublish');
		wp_clear_scheduled_hook($this->pluginName . '_every3hours_autopublish');
		wp_clear_scheduled_hook($this->pluginName . '_every30minutes_autopublish');
	}

	/**
	 * add the 3 hour schedule to wordpress schedules
	 * @schedule - an array from wordpress containing the existing schedules
	 *
	 */
	function add3HourSchedule($schedules) {
		$my_schedule = array('every3hours' => array('interval' => 10800, 'display' => __('Once Every 3 Hours')));
		return array_merge($my_schedule, $schedules);
	}

	/**
	 * add the 30 minute schedule to wordpress schedules
	 * @schedule - an array from wordpress containing the existing schedules
	 *
	 */
	function add30MinuteSchedule($schedules) {
		$my_schedule = array('every30minutes' => array('interval' => 1800, 'display' => __('Once Every 30 Minutes')));
		return array_merge($my_schedule, $schedules);
	}

	/**
	 * writes a css file to the page that is used in the public area.
	 *
	 */
	function includePublicCss() {
		echo "<link rel='stylesheet' type='text/css' href='" . get_option('siteurl') . "/wp-content/plugins/$this->pluginName/css/wp-public.css' />";
	}

	/**
	 * writes a css file to the page that is used in the admin area.
	 *
	 */
	function includeAdminCss() {
		echo "<link rel='stylesheet' type='text/css' href='" . get_option('siteurl') . "/wp-content/plugins/$this->pluginName/css/wp-admin.css' />";
	}

	/**
	 * writes a js file to the page that is used in the admin area.
	 *
	 */
	function includeAdminJs() {
		echo "<script src='" . get_option('siteurl') . "/wp-content/plugins/$this->pluginName/js/revverWP-js.php' type='text/javascript'></script>";
	}

	/**
	 * runs the initLightBox js function to ensure the video comment thumbs work
	 *
	 */
	function initLightbox() {
		echo "<script type='text/javascript'> initLightbox(); </script>";
	}

	/**
	 * includes a checkbox and link to the revver terms of service agreement.
	 *
	 */
	function includeRegisterAgreement() {
		echo '<script type="text/javascript">' . chr(13);
		echo 'var submitBtnElement;' . chr(13);
		echo 'function revver_disableSubmit() { submitBtnElement = document.getElementById("wp-submit") || document.getElementById("submit"); submitBtnElement.disabled = true; }' . chr(13);
		echo 'function revver_toggleSubmit() { if ( document.getElementById("agreeToTerms").checked ) { submitBtnElement.disabled = false; } else { submitBtnElement.disabled = true; } }' . chr(13);
		echo 'setTimeout("revver_disableSubmit();", 500);' . chr(13);
		echo '</script>' . chr(13);
		echo '<input type="checkbox" name="agree_to_terms" id="agreeToTerms" value="1" onclick="revver_toggleSubmit();" />' . chr(13);
		echo '<label for="agreeToTerms">&nbsp;' . __('I agree to', $this->pluginName) . ' <a href="http://revver.com/go/tou" target="_blank" class="revver-tos">' . __('Revver\'s Terms of Service', $this->pluginName) . '</a></label>' . chr(13);
	}

	/**
	 * returns true if the plugin config option
	 * is set to staging mode.
	 *
	 */
	function isStagingMode() {
		return $this->useStaging;
	}

	/**
	 * sets the staging mode of the plugin.
	 * @mode - yes or no
	 *
	 */
	function setStagingMode($mode) {
		if ( $mode == "yes" ) {
			$this->useStaging = true;
		} else {
			$this->useStaging = false;
		}
	}

	/**
	 * sets the api url based on the staging
	 * mode of the plugin.
	 *
	 */
	function setApiURL() {
		if ( $this->isStagingMode() ) {
			$this->apiURL = $this->apiBaseURLStaging . "login=" . $this->username . "&passwd=" . $this->password;
		} else {
			$this->apiURL = $this->apiBaseURL . "login=" . $this->username . "&passwd=" . $this->password;
		}
	}

	/**
	 * gets the api url that should be used for the revverApi.
	 *
	 */
	function getApiURL() {
		return $this->apiURL;
	}

	/**
	 * writes the Revver Widget to the page.
	 *
	 */
	function includeRevverWidget() {
		if ( $this->isStagingMode() ) {
			echo "<script src='http://widget.staging.revver.com/js/1.0/revver.js' type='text/javascript'></script>";
		} else {
			echo "<script src='http://widget.revver.com/js/1.0/revver.js' type='text/javascript'></script>";
		}
	}

	/**
	 * includes the configuration options form for the
	 * revver plugin.  if we have the post vars then
	 * run the update on the options and then
	 * include the form.
	 *
	 */
	function includeConfigForm() {
		global $PHP_SELF;
		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-config";
		$update_success = true;
		if (!isset($_POST['revver_updating_config'])) {
			// BASIC OPTIONS
			$revver_username      = get_option($this->pluginName . "_username");
			$revver_password      = get_option($this->pluginName . "_password");
			$revver_use_staging   = get_option($this->pluginName . "_use_staging");
			$revver_curl_proxy    = get_option($this->pluginName . "_curl_proxy");
			$revver_anon_response = get_option($this->pluginName . "_anon_video_response");

			// AUTO PLAYLIST OPTIONS
			$revver_playlist_id = get_option($this->pluginName . "_playlist_id");

			// FLASH PLAYER OPTIONS
			$revver_flash_width    		  = get_option($this->pluginName . "_flash_width");
			$revver_flash_height   		  = get_option($this->pluginName . "_flash_height");
			$revver_flash_logo     		  = get_option($this->pluginName . "_flash_logo");
			$revver_flash_logo_uri 		  = get_option($this->pluginName . "_flash_logo_uri");
			$revver_flash_logo_updategrab = get_option($this->pluginName . "_flash_logo_updategrab");
			$revver_flash_autoplay   	  = get_option($this->pluginName . "_flash_autoplay");
			$revver_flash_allowfullscreen = get_option($this->pluginName . "_flash_allowfullscreen");

			// SHARE OPTIONS
			$revver_share_displayshare   = get_option($this->pluginName . "_share_displayshare");
			$revver_share_displaydetails = get_option($this->pluginName . "_share_displaydetails");
		} else {
			// BASIC OPTIONS
			$revver_username      = (!isset($_POST['revver_username']) ? '' : $_POST['revver_username']);
			$revver_password      = (!isset($_POST['revver_password']) ? '' : $_POST['revver_password']);
			$revver_use_staging   = (!isset($_POST['revver_use_staging']) ? 'no' : $_POST['revver_use_staging']);
			$revver_curl_proxy    = (!isset($_POST['revver_curl_proxy']) ? '' : $_POST['revver_curl_proxy']);
			$revver_anon_response = (!isset($_POST['revver_anon_response']) ? 'no' : $_POST['revver_anon_response']);

			// AUTO PLAYLIST OPTIONS
			$revver_playlist_id = (!isset($_POST['revver_playlist_id']) ? '' : $_POST['revver_playlist_id']);

			// FLASH PLAYER OPTIONS
			$revver_flash_width    		  = (int) (!isset($_POST['revver_flash_width']) ? 480 : $_POST['revver_flash_width']);
			$revver_flash_height   		  = (int) (!isset($_POST['revver_flash_height']) ? 360 : $_POST['revver_flash_height']);
			$revver_flash_logo     		  = (!isset($_POST['revver_flash_logo']) ? '' : $_POST['revver_flash_logo']);
			$revver_flash_logo_uri 		  = (!isset($_POST['revver_flash_logo_uri']) ? '' : $_POST['revver_flash_logo_uri']);
			$revver_flash_logo_updategrab = (!isset($_POST['revver_flash_logo_updategrab']) ? 'no' : $_POST['revver_flash_logo_updategrab']);
			$revver_flash_autoplay   	  = (!isset($_POST['revver_flash_autoplay']) ? 'no' : $_POST['revver_flash_autoplay']);
			$revver_flash_allowfullscreen = (!isset($_POST['revver_flash_allowfullscreen']) ? 'no' : $_POST['revver_flash_allowfullscreen']);

			// SHARE OPTIONS
			$revver_share_displayshare   = (!isset($_POST['revver_share_displayshare']) ? 'no' : $_POST['revver_share_displayshare']);
			$revver_share_displaydetails = (!isset($_POST['revver_share_displaydetails']) ? 'no' : $_POST['revver_share_displaydetails']);

			$this->updateConfigOptions(
						$revver_username,
						$revver_password,
						$revver_use_staging,
						$revver_curl_proxy,
						$revver_anon_response,
						$revver_playlist_id,
						$revver_flash_width,
						$revver_flash_height,
						$revver_flash_logo,
						$revver_flash_logo_uri,
						$revver_flash_logo_updategrab,
						$revver_flash_autoplay,
						$revver_flash_allowfullscreen,
						$revver_share_displayshare,
						$revver_share_displaydetails
					);
			if ($this->userId == 0) $update_success = false;
		}
		include(REVVER_ABSPATH . "includes/configure-form.php");
		return;
	}

	/**
	 * renders a list of messages for the blog owner from
	 * the revver system.  it's like a mini email system
	 * but it's read only.
	 *
	 */
	function includeMessages() {
		global $PHP_SELF;
		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-messages";

		$query = array(
				'targets' => array('_self'),
				'statuses' => array(0, 1)
			);

		$messages = $this->api->callRemote('message.find', $query);

		include(REVVER_ABSPATH . "includes/messages.php");
		return;
	}

	/**
	 * renders a link with the count of messages on the admin
	 * dashboard.  only to users who can manage_options
	 *
	 */
	function includeMessageCount() {
		global $PHP_SELF;
		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-messages";

		// only show the messages to the blog owner.
		// if ( $this->getCurrentRevverUsername() != $this->username ) return;

		if ( !current_user_can('manage_options') ) return;

		$query = array(
				'targets' => array('_self'),
				'statuses' => array(0),
			);
		$options = array('count' => true);
		$messages = $this->api->callRemote('message.find', $query, $options);

		if ($messages[0] == 0) return;

		echo "<div>";
		echo "<h3>" . __("Revver Messages", $this->pluginName) . "</h3>";
		echo "<p>" . __("There are currently", $this->pluginName) . " <a href=\"" . $basePage . "\">" . $messages[0] . __(" unread messages</a> from the Revver system.", $this->pluginName) . "</p>";
		echo "</div>";
	}

	/**
	 * includes the subscribers search page
	 *
	 */
	function includeSubscribersSearch() {
		global $PHP_SELF;

		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-subscribers";

		$postback           = (int) (!isset($_REQUEST['postback']) ? 1 : $_REQUEST['postback']);
		$revver_keywords    = (!isset($_REQUEST['revver_keywords']) ? '' : $_REQUEST['revver_keywords']);
		$revver_orderby     = (!isset($_REQUEST['revver_orderby']) ? 'createdDate' : $_REQUEST['revver_orderby']);
		$revver_orderby_dir = (boolean) (!isset($_REQUEST['revver_orderby_dir']) ? true : $_REQUEST['revver_orderby_dir']);

		$resultsPerPage = 25;
		$pageNum = (int) (!isset($_REQUEST['pageNum']) ? 1 : $_REQUEST['pageNum']);

		if ( $postback == 1 ) {
			$query = array('search' => $revver_keywords);
			if ( $pageNum == 1 ) {
				$offset = 0;
			} else {
				$offset = $resultsPerPage * ($pageNum - 1);
			}
			$results     = $this->searchSubscribers($query, $offset, $revver_orderby, $revver_orderby_dir, $resultsPerPage);
			$count       = $results[0];
			$subscribers = $results[1];

			$baseURL = $basePage . "&revver_keywords=" . htmlspecialchars($revver_keywords) . "&revver_orderby=" . htmlspecialchars($revver_orderby) . "&revver_orderby_dir=" . $revver_orderby_dir . "&postback=1";
		}
		include(REVVER_ABSPATH . "includes/subscribers.php");
	}

	/**
	 * includes the subscribers details page.  it
	 * will check to see if the user has permission
	 * to load the details if they are looking
	 * at details that are not their own.
	 *
	 */
	function includeSubscribersDetails() {
		global $PHP_SELF;
		global $userdata;
		global $profileuser;

		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-subscribers";

		get_currentuserinfo();
		if ( !current_user_can('edit_user', $profileuser->ID) && $profileuser->ID != $userdata->ID ) {
			return;
		}

		$login = get_usermeta($profileuser->ID, $this->pluginName . '_login');

		// create the account if it doesn't exist yet.
		if ( empty($login) ) {
			$this->saveSubAccount($profileuser->ID);
			$login = get_usermeta($profileuser->ID, $this->pluginName . '_login');
		}

		if ( $login == $this->username ) return; // we can't edit the parent user from the api.

		$subscriber = $this->getSubscriberByLogin($login);
		if (!$subscriber) {
			// there's a chance the user was in the WP database but not in revver.
			// this can happen on migration and weird staging issues.
			// so if we didn't get it just create it now.
			$this->saveSubAccount($profileuser->ID, true);
			$subscriber = $this->getSubscriberByLogin($login);
		}

		$postback = (int) (!isset($_REQUEST['postback']) ? 0 : $_REQUEST['postback']);
		include(REVVER_ABSPATH . "includes/subscriber-detail.php");
	}

	/**
	 * saves a subscriber if the current user has the right
	 * to update the given wp user.
	 *
	 * @user_id - the wordpress user id of the account being updated.
	 *
	 */
	function saveSubscriber($user_id) {
		global $userdata;

		$this->saveSubAccount($user_id); // ensure a revver sub account exists
		$login = get_usermeta($user_id, $this->pluginName . '_login');

		if ($login == $this->username) return; // can't edit the blog owner revver info

		get_currentuserinfo();
		if ( !current_user_can('edit_user', $user_id) && $user_id != $userdata->ID ) {
			return;
		}

		$email             = (!isset($_REQUEST['email']) ? '' : $_REQUEST['email']);
		$revver_broadcast  = (boolean) (!isset($_REQUEST['revver_broadcast']) ? true : $_REQUEST['revver_broadcast']);
		$revver_mobile     = (boolean) (!isset($_REQUEST['revver_mobile']) ? false : $_REQUEST['revver_mobile']);
		$revver_paypal     = (!isset($_REQUEST['revver_paypal']) ? '' : $_REQUEST['revver_paypal']);
		$revver_address1   = (!isset($_REQUEST['revver_address1']) ? '' : $_REQUEST['revver_address1']);
		$revver_address2   = (!isset($_REQUEST['revver_address2']) ? '' : $_REQUEST['revver_address2']);
		$revver_city       = (!isset($_REQUEST['revver_city']) ? '' : $_REQUEST['revver_city']);
		$revver_state      = (!isset($_REQUEST['revver_state']) ? '' : $_REQUEST['revver_state']);
		$revver_postalcode = (!isset($_REQUEST['revver_postalcode']) ? '' : $_REQUEST['revver_postalcode']);
		$revver_country    = (!isset($_REQUEST['revver_country']) ? 'United States' : $_REQUEST['revver_country']);

		$options = array('allowBroadcast' => $revver_broadcast, 'allowMobile' => $revver_mobile);

		// since the revver api requires values for the address we have to
		// check for the address before updating that info
		if ( empty($revver_country) ) $revver_country = "United States";
		if ( !empty($revver_address1) && !empty($revver_city) && !empty($revver_state) && !empty($revver_postalcode) && !empty($revver_country) ) {
			$options['address'] = array(
					'address1' => $revver_address1,
					'address2' => $revver_address2,
					'city' => $revver_city,
					'state' => $revver_state,
					'postcode' => $revver_postalcode,
					'country' => $revver_country
				);
		}

		if ( !empty($email) ) {
			$options['email'] = $email;
		}

		if ( !empty($revver_paypal) ) {
			$options['paypal'] = $revver_paypal;
		}

		$this->api->callRemote('user.update', $login, $options);
		return;
	}

	/**
	 * includes the videos owned by the given revver login
	 *
	 * @login - the revver sub accout name
	 *
	 */
	function includeSubscribersVideos($login) {
		global $PHP_SELF;

		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-videos&revver_subscriber=" . $login;
		$webPathToPlugin = get_option('siteurl') . '/wp-content/plugins/' . $this->pluginName . '/';

		$postback           = (int) (!isset($_REQUEST['postback']) ? 1 : $_REQUEST['postback']);
		$revver_keywords    = (!isset($_REQUEST['revver_keywords']) ? '' : $_REQUEST['revver_keywords']);
		$revver_orderby     = (!isset($_REQUEST['revver_orderby']) ? 'publicationDate' : $_REQUEST['revver_orderby']);
		$revver_orderby_dir = (boolean) (!isset($_REQUEST['revver_orderby_dir']) ? true : $_REQUEST['revver_orderby_dir']);

		$resultsPerPage = 20;
		$pageNum = (int) (!isset($_REQUEST['pageNum']) ? 1 : $_REQUEST['pageNum']);

		$the_login = $login;
		if ( $this->username != $login ) {
			$the_login = $login . '@' . $this->username;
		}

		$video_stats = $this->getSubscribersVideoStats($the_login);

		if ( $postback == 1 ) {
			$query = array(
					'owners'   => array($the_login),
					'statuses' => array('online', 'offline'), // , 'going_online', 'uploading', 'processing', 'rejected', 'review', 'failed'
				);

			if (!empty($revver_keywords)) $query['search'] = explode(" ", $revver_keywords);

			if ( $pageNum == 1 ) {
				$offset = 0;
			} else {
				$offset = $resultsPerPage * ($pageNum - 1);
			}
			$results = $this->searchVideos($query, $offset, $revver_orderby, $revver_orderby_dir, $resultsPerPage);
			$count = $results[0];
			$videos = $results[1];

			$baseURL = $basePage . "&revver_keywords=" . htmlspecialchars($revver_keywords) . "&revver_orderby=" . htmlspecialchars($revver_orderby) . "&revver_orderby_dir=" . $revver_orderby_dir . "&postback=1";
		}

		include(REVVER_ABSPATH . "includes/subscriber-videos.php");
		return;
	}

	/**
	 * returns an object with the video stats for a given
	 * revver login.
	 * # of online videos (online)
	 * # of offline videos (offline)
	 * # of videos (sum of online and offline)
	 * todo: get Revver Api to return a report of the count
	 * of videos on all statuses.
	 *
	 * @login - the revver accout name
	 *
	 */
	function getSubscribersVideoStats($login) {
		$video_stats = array(
				'online'  => 0,
				'offline' => 0,
				'total'   => 0
			);

		// we have to make two calls to the api...
		// note that the total isn't technically accurate
		// as there are videos on statuses other than
		// online and offline but we're not going to do
		// 6+ api calls to get the correct number right now.
		// statuses: 'online', 'offline', 'going_online', 'uploading', 'processing', 'rejected', 'review', 'failed'
		$query = array(
				'owners'   => array($login),
				'statuses' => array('online')
			);
		$video_stats['online'] = (int) $this->api->callRemote('video.count', $query);

		$query = array(
				'owners'   => array($login),
				'statuses' => array('offline')
			);
		$video_stats['offline'] = (int) $this->api->callRemote('video.count', $query);

		$video_stats['total'] = $video_stats['online'] + $video_stats['offline'];
		return $video_stats;
	}

	/**
	 * includes a form to show all of the details
	 * of a revver video so the user can edit it.
	 *
	 * @video_id - the id of the video on revver
	 * @login - the revver sub account name
	 *
	 */
	function includeSubscribersVideo($video_id, $login) {
		global $PHP_SELF;
		global $revver_message;

		$the_login = $login;
		if ( $this->username != $login ) {
			$the_login = $login . '@' . $this->username;
		}

		$basePage = $PHP_SELF ."?page=" . $this->pluginName . "-videos&revver_subscriber=" . $login;

		$postback = (int) (!isset($_REQUEST['postback']) ? 0 : $_REQUEST['postback']);
		$video = $this->getVideoById($video_id);

		// make sure the user has access to edit/view this video.
		// if they can edit users or if they are the owner then it's okay, otherwise boot em out.
		if ( !current_user_can('edit_users') && $video['owner'] != $the_login ) {
			header("location: " . $basePage);
			return;
		}

		// run the save if we've posted back
		if ( $postback == 1 ) {
			$this->saveSubscriberVideo($video);
		}

		include(REVVER_ABSPATH . "includes/video-form.php");
		return;
	}

	/**
	 * updates a video object and then commits those
	 * changes to the revver api.
	 *
	 * @video - a video object that was returned from the api
	 *
	 */
	function saveSubscriberVideo(&$video) {
		global $revver_message;
		global $wpdb;

		$title    	 = (!isset($_REQUEST['revver_video_title']) ? '' : $_REQUEST['revver_video_title']);
		$keywords 	 = (!isset($_REQUEST['revver_video_keywords']) ? '' : $_REQUEST['revver_video_keywords']);
		$description = (!isset($_REQUEST['revver_video_description']) ? '' : $_REQUEST['revver_video_description']);
		$credits     = (!isset($_REQUEST['revver_video_credits']) ? '' : $_REQUEST['revver_video_credits']);
		$url         = (!isset($_REQUEST['revver_video_url']) ? '' : $_REQUEST['revver_video_url']);
		// $ageRestriction = (int) (!isset($_REQUEST['revver_video_ageRestriction']) ? 1 : $_REQUEST['revver_video_ageRestriction']);
		$status		 = (!isset($_REQUEST['revver_video_status']) ? 'online' : $_REQUEST['revver_video_status']);

		if ( $status != 'online' ) $status = 'offline';
		$video_id = $video['id'];

		$video['title']       = $title;
		$video['keywords']    = explode(",", $keywords);
		$video['description'] = $description;
		$video['credits']     = $credits;
		$video['url']         = $url;
		// $video['ageRestriction'] = $ageRestriction;
		$video['status']      = $status;
		$thumbnail			  = $video['chosenThumbnail'] + 1; // (Thumbnails are offset by +1, so thumbnail[1] -> thumb_0)

		// save the video on revver
		$options = array(
				'title' 	  => $title,
				'keywords'    => explode(",", $keywords),
				'credits'     => $credits,
				'url' 		  => $url,
				'description' => $description,
				'author' 	  => $video['author'],
				'status'      => $video['status'],
				'thumbnail'   => $thumbnail
			);

		$result = $this->api->callRemote('video.update', $video['id'], $options);

		if ($result['faultCode']) {
			$revver_message = __('An error occurred when trying to save the video on Revver.<br />Reason: ', $this->pluginName) . $result['faultString'];
			return;
		}

		// update the video status we have on file.
		$wpdb->query("
				UPDATE $this->db_posts SET
					video_status = '$status'
				WHERE
					video_id = $video_id
			");

		if ( $status == 'online' ) {
			$wpdb->query("
					UPDATE $this->db_comments SET
						video_status = '$status',
						is_auto_published = 1
					WHERE
						video_id = $video_id
				");
		} else {
			$wpdb->query("
					UPDATE $this->db_comments SET
						video_status = '$status'
					WHERE
						video_id = $video_id
				");
		}

		$revver_message = __('The video details have been updated.<br />Please note that it can take 90 minutes or more for the video updates to be live, especially if you\'ve changed the status.', $this->pluginName);
		return;
	}

	/**
	 * updates the options for the plugin.
	 * this should be called after the
	 * ConfigForm is submitted.
	 *
	 * also, this thing has to get the user id
	 * from revver for the username that was
	 * submitted.  in order to get this we
	 * have to call the revver api.
	 *
	 */
	function updateConfigOptions(
			$username, $password, $use_staging, $curl_proxy, $anon_response,
			$playlist_id, $flash_width, $flash_height, $flash_logo, $flash_logo_uri,
			$flash_logo_updategrab, $flash_autoplay, $flash_allowfullscreen,
			$share_displayshare, $share_displaydetails
		) {

		update_option($this->pluginName . "_username", $username);
		update_option($this->pluginName . "_password", $password);
		update_option($this->pluginName . "_use_staging", $use_staging);
		update_option($this->pluginName . "_curl_proxy", $curl_proxy);
		update_option($this->pluginName . "_anon_video_response", $anon_response);
		update_option($this->pluginName . "_playlist_id", $playlist_id);
		update_option($this->pluginName . "_flash_width", $flash_width);
		update_option($this->pluginName . "_flash_height", $flash_height);
		update_option($this->pluginName . "_flash_logo", $flash_logo);
		update_option($this->pluginName . "_flash_logo_uri", $flash_logo_uri);
		update_option($this->pluginName . "_flash_logo_updategrab", $flash_logo_updategrab);
		update_option($this->pluginName . "_flash_autoplay", $flash_autoplay);
		update_option($this->pluginName . "_flash_allowfullscreen", $flash_allowfullscreen);
		update_option($this->pluginName . "_share_displayshare", $share_displayshare);
		update_option($this->pluginName . "_share_displaydetails", $share_displaydetails);

		$has_username_changed = ($this->username != $username) ? true : false;
		$has_password_changed = ($this->password != $password) ? true : false;

		// update the object's properties
		$this->username = $username;
		$this->password = $password;

		// reset the $revverAPI's url
		$this->setStagingMode($use_staging);
		$this->setApiURL();
		$this->api->resetURL( $this->getApiURL() );
		$this->api->setCurlProxy($curl_proxy);

		// now get the user id from the revver api,
		// only if the username has changed.
		// if ($has_username_changed || $has_password_changed) {
			// now these seems rather putrid but the revver api doesn't
			// offer an easy way to get the id # of a user and we need
			// this because the id is used when embedding the video player.
			// so we'll search for one video with the revver keyword and then
			// grab the affiliate id that comes back.  awesome eh.
			$results = $this->searchVideos(array('search' => array('revver')), 0, 'publicationDate', true, 1);
			$videos = $results[1];
			$userId = (int) $videos[0]["affiliateId"];
			$this->userId = $userId;
			update_option($this->pluginName . "_user_id", $userId);
		// }

		// update the revver_login user metakey for the admin account.
		update_usermeta(1, $this->pluginName . '_login', $username);
	}

	/**
	 * this looks for posts that have videos and those videos are
	 * set to autopublish the post they are contained in once
	 * the video is approved by revver.
	 *
	 * this is run automatically daily by wordpress using its
	 * builtin cron functionality.
	 *
	 */
	function autoPublish() {
		global $wpdb;

		$gmnow = gmdate("Y-m-d H:i:59");
		$now   = date("Y-m-d H:i:59");

		$posts = $wpdb->get_results("
				SELECT
					post_id,
					video_id
				FROM
					$this->db_posts
				WHERE
					auto_publish = 1
					AND is_auto_published = 0
					AND video_id > 0
			");

		foreach ($posts as $post) {
			$video_id = (int) $post->video_id;
			$video = $this->getVideoById($video_id);
			$status = $video['status'];

			// while we're here... might as well update the video status we have on file.
			$wpdb->query("
					UPDATE $this->db_posts SET
						video_status = '$status'
					WHERE
						video_id = $video_id
				");

			$wpdb->query("
					UPDATE $this->db_comments SET
						video_status = '$status'
					WHERE
						video_id = $video_id
				");

			if ( $status == 'online' ) {
				$wpdb->query("
						UPDATE $this->db_posts SET
							is_auto_published = 1,
							auto_publish = 0
						WHERE
							post_id = $post->post_id
					");
				$wpdb->query("
						UPDATE $wpdb->posts SET
							post_status = 'publish',
							post_date = '$now',
							post_date_gmt = '$gmnow',
							post_modified = '$now',
							post_modified_gmt = '$gmnow'
						WHERE
							ID = $post->post_id
					");
			}
			if ( $status == 'rejected' || $status == 'failed' ) {
				$wpdb->query("
						UPDATE $this->db_posts SET
							is_auto_published = 0,
							auto_publish = 0
						WHERE
							post_id = $post->post_id
					");
			}
		}

		$comments = $wpdb->get_results("
				SELECT
					comment_id, video_id
				FROM
					$this->db_comments
				WHERE
					is_auto_published = 0
					AND video_id > 0
					AND video_status != 'online'
			");

		foreach ($comments as $comment) {
			$video_id = (int) $comment->video_id;
			$video = $this->getVideoById($video_id);
			$status = $video['status'];

			// while we're here... might as well update the video status we have on file.
			$wpdb->query("
					UPDATE $this->db_posts SET
						video_status = '$status'
					WHERE
						video_id = $video_id
				");

			$wpdb->query("
					UPDATE $this->db_comments SET
						video_status = '$status'
					WHERE
						video_id = $video_id
				");

			if ( $status == 'online' ) {
				$wpdb->query("
						UPDATE $this->db_comments SET
							is_auto_published = 1
						WHERE
							video_id = $video_id
					");

				// ensure the comment is in the collection for associated post.
				// we do this because it's possible that a timeout occurred on
				// the original submission or some other unrelated error.
				// this is to catch those odd scenarios and recover.
				$collection_id = $this->getCollectionIdByCommentId($comment->comment_id);
				if ($collection_id > 0) {
					$this->addVideoToCollection($video_id, $collection_id);
				}

			}
			if ( $status == 'rejected' || $status == 'failed' ) {
				$wpdb->query("
						UPDATE $this->db_comments SET
							is_auto_published = 1
						WHERE
							video_id = $video_id
					");
			}
		}
	}

	/**
	 * set a flag to true if the request is for the comments rss feed.
	 * this is because we can't attempt a join on tables or insert
	 * video data on the comment rss.  WP has a bug where it doesn't
	 * announce the posts_join filter on the comments rss.
	 *
	 */
	function flagInCommentRss() {
		global $revver_is_in_comment_rss;
		$revver_is_in_comment_rss = true;
	}

	/**
	 * returns the sql join code needed to get the custom post fields
	 * correctly in the "post" which is then available in the loop.
	 *
	 */
	function getPostsJoin($join) {
		global $wpdb;
		global $revver_is_in_comment_rss;
		if ( $revver_is_in_comment_rss ) return $join; // needed to get around WP not announcing posts_join in comments rss
		return $join . " LEFT JOIN $this->db_posts ON $wpdb->posts.ID = $this->db_posts.post_id";
	}

	/**
	 * returns the custom post fields that should be returned with
	 * the main loop query.
	 *
	 */
	function getPostsFields($posts_fields) {
		global $revver_is_in_comment_rss;
		if ( $revver_is_in_comment_rss ) return $posts_fields; // needed to get around WP not announcing posts_join in comments rss
		return $posts_fields . "," . $this->posts_fields;
	}

	/**
	 * embeds a revver video player into the page.
	 * @post_id - the word press post id
	 * @video_id - the id of the video to put embedded
	 *
	 */
	function embedVideo($post_id, $video_id) {
		if ($video_id == 0) return;
		$divId = "revverVideo-" . $post_id . "-" . $video_id;
		$flash_width    	   = (int) get_option($this->pluginName . "_flash_width");
		$flash_height   	   = (int) get_option($this->pluginName . "_flash_height");
		$flash_logo     	   = get_option($this->pluginName . "_flash_logo");
		$flash_logo_uri 	   = get_option($this->pluginName . "_flash_logo_uri");
		$flash_autoplay   	   = get_option($this->pluginName . "_flash_autoplay");
		$flash_allowfullscreen = get_option($this->pluginName . "_flash_allowfullscreen");

		echo "<div id='" . $divId . "' class='revver-video'></div>";
		echo "<script type='text/javascript'>\n";
		echo "//<![CDATA[\n";
		echo 'revverVideo.embed( {"divId": "' . $divId . '", "mediaId": ' . $video_id;
		echo ', "width": ' . $flash_width . ', "height": ' . $flash_height;
		if ($flash_logo == "custom") {
			echo ', "pngLogo": "' . $flash_logo_uri . '"';
		} else {
			echo ', "pngLogo": "' . $flash_logo . '"';
		}
		if (is_single() && $flash_autoplay == "yes")  echo ', "autoStart": true';
		if ($flash_allowfullscreen == "yes") {
			echo ', "allowFullScreen": true';
		} else {
			echo ', "allowFullScreen": false';
		}
		echo ', "affiliateId": ' . $this->userId . ' })' . "\n";
		echo "//]]>\n";
		echo "</script>";
	}

	/**
	 * embeds a revver video player on a post.  this is called by a filter.
	 * @content - the text of the post this is embedding into
	 *
	 */
	function embedVideoOnPost($content) {
		global $wp_query;
		global $revver_is_in_rss;
		$post_id = $wp_query->post->post_id;
		$video_id = $wp_query->post->video_id;

		if ($video_id == 0) return $content . $this->getVideoCommentsWidget($wp_query->post);
		$divId = "revverVideo-" . $post_id . "-" . $video_id;

		// if we're in rss mode just embed a thumbnail
		// which will link to the post. this makes the
		// video thumb visible right in the feed.
		if ( $revver_is_in_rss ) {
			echo '<div class="revver-video-thumb"><a href="';
			the_permalink();
			echo '" rel="bookmark" title="' . __('Watch Video for: ', $this->pluginName) . the_title('', '', false);
			echo '"><img src="http://frame.revver.com/frame/170x128/' . $video_id . '.jpg" width="170" /></a>';
			echo '</div>';
		} else {
			$flash_width    	   = (int) get_option($this->pluginName . "_flash_width");
			$flash_height   	   = (int) get_option($this->pluginName . "_flash_height");
			$flash_height 		  += 32; // 32 pixels is for the flash player controls
			$flash_logo     	   = get_option($this->pluginName . "_flash_logo");
			$flash_logo_uri 	   = get_option($this->pluginName . "_flash_logo_uri");
			$flash_autoplay   	   = get_option($this->pluginName . "_flash_autoplay");
			$flash_allowfullscreen = get_option($this->pluginName . "_flash_allowfullscreen");
			$share_displayshare    = get_option($this->pluginName . "_share_displayshare");
			$share_displaydetails  = get_option($this->pluginName . "_share_displaydetails");

			echo "<div id='" . $divId . "' class='revver-video'></div>";
			echo "<script type='text/javascript'>\n";
			echo "//<![CDATA[\n";
			echo 'revverVideo.embed( {"divId": "' . $divId . '", "mediaId": ' . $video_id;
			echo ', "width": ' . $flash_width . ', "height": ' . $flash_height;
			if ($flash_logo == "custom") {
				echo ', "pngLogo": "' . $flash_logo_uri . '"';
			} else {
				echo ', "pngLogo": "' . $flash_logo . '"';
			}
			if (is_single() && $flash_autoplay == "yes")  echo ', "autoStart": "true"';
			if ($flash_allowfullscreen == "yes") {
				echo ', "allowFullScreen": true';
			} else {
				echo ', "allowFullScreen": false';
			}
			echo ', "affiliateId": ' . $this->userId . ' })' . "\n";
			echo "//]]>\n";
			echo "</script>";

			if ($share_displayshare == "yes" || $share_displaydetails == "yes") {
				include(REVVER_ABSPATH . "includes/video-details-and-sharing.php");
			}
		}
		return $content . $this->getVideoCommentsWidget($wp_query->post) . "<span class='revver-after-video'></span>";
	}



	/**
	 * embeds a thumb into the comment on the page.
	 * @post_id - the word press post id
	 * @video_id - the id of the video to put embedded
	 *
	 */
	function embedVideoThumb($post_id, $video_id) {
		if ($video_id == 0) return "";
		$anchorId = "revverVideoThumb-" . $post_id . "-" . $video_id;
		$link = 'http://revver.com/watch/' . $video_id . '/affiliate/' . $this->userId;
		return '<div class="revver-video-comment-thumb-container"><a href="' . $link . '" class="revver-video-comment-thumb" id="' . $anchorId . '" render="renderVideoFromThumb(' . $video_id . ', \'' . $anchorId . '\', ' .  $this->userId . ');" rel="lightbox" lightboxWidth="480" lightboxHeight="415"><img src="http://frame.revver.com/frame/94x68/' . $video_id . '.jpg" width="94" height="68" /></a></div>';
	}

	/**
	 * embeds a link to a revver video into the page.
	 * @video_id - the id of the video to put embedded
	 *
	 */
	function embedVideoLink($video_id) {
		if ($video_id == 0) return;
		return '<a href="http://revver.com/watch/' . $video_id . '/affiliate/' . $this->userId . '" target="_blank">' . __('Watch Video', $this->pluginName) . ' [' . __('id', $this->pluginName) . ': ' . $video_id . ']</a>';
	}

	/**
	 * includes the rss name spaces needed so the video links will work.
	 *
	 */
	function includeRssNameSpaces() {
		global $revver_is_in_rss;
		$revver_is_in_rss = true;
		echo ' xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule" ';
		echo ' xmlns:media="http://search.yahoo.com/mrss/" ';
	}

	/**
	 * includes a link element into the rss feed.
	 *
	 */
	function includeRssVideoLink() {
		global $wp_query;
		$id = $wp_query->post->video_id;
		if ($id == 0) return;
		echo '<enclosure url="http://flash.revver.com/player/1.0/player.swf?mediaId=' . $id . '&amp;affiliateId=' . $this->userId . '" type="application/x-shockwave-flash" length="21789409" ></enclosure>';
		echo '<media:player url="http://revver.com/watch/' .  $id . '/flv/affiliate/' . $this->userId . '"></media:player>';
		echo '<media:content url="http://flash.revver.com/player/1.0/player.swf?mediaId=' . $id . '&amp;affiliateId=' . $this->userId . '" duration="187" medium="video" type="application/x-shockwave-flash"></media:content>';
	}

	/**
	 * returns the html for a revver widget that renders
	 * all of the video comments to a word press post.
	 * @post - the word press post object that this
	 * widget will return the video comments for
	 *
	 */
	function getVideoCommentsWidget($post) {
		if ($post->collection_id == 0) return;
		$video_comments_online = $this->getNumberOfVideoCommentsOnPost($post->ID, "'online'");
		$video_comments_pending = $this->getNumberOfVideoCommentsOnPost($post->ID, "'processing','review','review_verify','going_online'");
		if ( $video_comments_online > 0 ) {
			$flash_width    	   = (int) get_option($this->pluginName . "_flash_width");
			$flash_height   	   = (int) get_option($this->pluginName . "_flash_height");
			$flash_height 		  += 32; // 32 pixels is for the flash player controls
			$flash_logo     	   = get_option($this->pluginName . "_flash_logo");
			$flash_logo_uri 	   = get_option($this->pluginName . "_flash_logo_uri");
			$flash_autoplay   	   = get_option($this->pluginName . "_flash_autoplay");
			$flash_allowfullscreen = get_option($this->pluginName . "_flash_allowfullscreen");

			$divId = "revverVideoComments-" . $post->ID;
			ob_start();
			include(REVVER_ABSPATH . "includes/comments-widget.php");
			$content = ob_get_contents();
			ob_end_clean();
		} else {
			if (!$post->allow_video_comments) return;
			$content = '<h3>' . __("No Video Responses have been posted yet.", $this->pluginName) . '</h3>';
			if ( $video_comments_pending == 1 ) {
				$content .= '<p><em>' . __("1 video response is waiting to be posted, pending approval.", $this->pluginName) . '</em></p>';
			} elseif ($video_comments_pending > 1) {
				$content .= '<p><em>' . $video_comments_pending . __(" video responses are waiting to be posted, pending approval.", $this->pluginName) . '</em></p>';
			}
			$content .= '<p><a href="' . get_permalink() . '#respond">' . __("Click here to post a video response.", $this->pluginName) . '</a></p>';
		}
		return $content;
	}

	/**
	 * adds additional fields to the edit-form-advanced
	 * admin page.
	 *
	 */
	function addPostFormFields() {
		global $post;
		$revverPost = $this->getPost($post->ID);
		$revver_playlist_id = (int) get_option($this->pluginName . "_playlist_id");
		include(REVVER_ABSPATH . "includes/post-form-fields.php");
		return;
	}

	/**
	 * adds the comment fields needed for users
	 * to be able to post videos in a comment.
	 *
	 */
	function addCommentFormFields($post, $user_id = false) {
		if (!$post->allow_video_comments) return;
		// if (!$user_id && get_option($this->pluginName . "_anon_video_response") == "no") return;
		$allow_manual_video_id = false;
		if (get_option($this->pluginName . "_anon_video_response") == "yes") $allow_manual_video_id = true;
		include(REVVER_ABSPATH . "includes/comment-form-fields.php");
		return;
	}

	/**
	 * gets the revver specific data for a post
	 * @post_id
	 *
	 */
	function getPost($post_id) {
		global $wpdb;
		if (!$post_id) $post_id = -1;
		if (!is_numeric($post_id)) $post_id = -1;
		$revverPost = $wpdb->get_row("SELECT post_id, video_id, video_owner, video_status, auto_publish, is_auto_published, allow_video_comments, collection_id FROM $this->db_posts WHERE post_id = $post_id");
		if ($post_id < 0) $revverPost->auto_publish = 1;
		return $revverPost;
	}

	/**
	 * saves the revver specific data for a post
	 * @post_id
	 *
	 * the rest of the params are pulled from the post
	 * since wordpress doesn't announce the form data
	 * to this event.
	 *
	 */
	function savePost($post_id) {
		global $wpdb;
		global $revver_last_collection;
		$video_id = (int) (!isset($_POST['revver_video_id']) ? 0 : $_POST['revver_video_id']);
		$video_owner = (!isset($_POST['revver_video_owner']) ? '' : $_POST['revver_video_owner']);
		$auto_publish = (int) (!isset($_POST['revver_auto_publish']) ? 0 : $_POST['revver_auto_publish']);
		$allow_video_comments = (int) (!isset($_POST['revver_allow_video_comments']) ? 0 : $_POST['revver_allow_video_comments']);
		$collection_id = (int) (!isset($_POST['revver_collection_id']) ? 0 : $_POST['revver_collection_id']);
		$add_to_playlist = (int) (!isset($_POST['revver_add_to_playlist']) ? 0 : $_POST['revver_add_to_playlist']);

		if ( !is_numeric($video_id) ) $video_id = 0;
		if ( !is_numeric($auto_publish) ) $auto_publish = 0;
		if ( !is_numeric($allow_video_comments) ) $allow_video_comments = 0;
		if ( !is_numeric($collection_id) ) $collection_id = 0;

		// automatically generate a collection id using the revver api if
		// a collection id was not entered and the post allows for video comments
		if ( $collection_id == 0 && $allow_video_comments ) {
			if ( $revver_last_collection != 0 ) {
				$collection_id = $revver_last_collection;
			} else {
				$collection_name = "'" . (!isset($_POST['post_title']) ? __('Post Id #', $this->pluginName) . $post_id : $_POST['post_title']) . "' " . __('WP Video Responses', $this->pluginName);
				$collection_id = $this->createCollection($collection_name);
				$revver_last_collection = $collection_id;
			}
		} else {
			$collection_id = 0;
		}

		$video = $this->getVideoById($video_id);
		$video_owner = $video['owner'];
		$video_status = $video['status'];

		$wpdb->query("
				INSERT IGNORE INTO $this->db_posts
					(post_id, video_id, video_owner, video_status, auto_publish, is_auto_published, allow_video_comments, collection_id)
				VALUES
					($post_id, $video_id, '$video_owner', '$video_status', $auto_publish, 0, $allow_video_comments, $collection_id)
			");

		if ( $add_to_playlist && $video_id > 0 ) {
			$playlist_id = (int) get_option($this->pluginName . "_playlist_id");
			if ($playlist_id > 0) {
				$this->addVideoToCollection($video_id, $playlist_id);
			}
		}
	}

	/**
	 * updates the revver specific data for a post
	 * @post_id
	 *
	 * the rest of the params are pulled from the post
	 * since wordpress doesn't announce the form data
	 * to this event.
	 *
	 */
	function editPost($post_id) {
		global $wpdb;
		global $revver_last_collection;

		// this prevents the post from being updated when comments are added
		// for some annoying reason editPost is called when a comment is added.
		// probably doing so to update the comment count.  son of a!
		if ( !isset($_POST['revver_editting_post']) ) return;

		$video_id = (int) (!isset($_POST['revver_video_id']) ? 0 : $_POST['revver_video_id']);
		$video_owner = (!isset($_POST['revver_video_owner']) ? '' : $_POST['revver_video_owner']);
		$auto_publish = (int) (!isset($_POST['revver_auto_publish']) ? 0 : $_POST['revver_auto_publish']);
		$allow_video_comments = (int) (!isset($_POST['revver_allow_video_comments']) ? 0 : $_POST['revver_allow_video_comments']);
		$collection_id = (int) (!isset($_POST['revver_collection_id']) ? 0 : $_POST['revver_collection_id']);
		$add_to_playlist = (int) (!isset($_POST['revver_add_to_playlist']) ? 0 : $_POST['revver_add_to_playlist']);

		if ( !is_numeric($video_id) ) $video_id = 0;
		if ( !is_numeric($auto_publish) ) $auto_publish = 0;
		if ( !is_numeric($allow_video_comments) ) $allow_video_comments = 0;
		if ( !is_numeric($collection_id) ) $collection_id = 0;

		// automatically generate a collection id using the revver api if
		// the collection id is 0 and the post allows for video comments
		if ( $collection_id == 0 && $allow_video_comments ) {
			if ( $revver_last_collection != 0 ) {
				$collection_id = $revver_last_collection;
			} else {
				$collection_name = "'" . (!isset($_POST['post_title']) ? __('Post Id #', $this->pluginName) . $post_id : $_POST['post_title']) . "' " . __('WP Video Responses', $this->pluginName);
				$collection_id = $this->createCollection($collection_name);
				$revver_last_collection = $collection_id;
			}
		}

		$video = $this->getVideoById($video_id);
		$video_owner = $video['owner'];
		$video_status = $video['status'];

		$wpdb->query("
				UPDATE $this->db_posts SET
					video_id = $video_id,
					video_owner = '$video_owner',
					video_status = '$video_status',
					auto_publish = $auto_publish,
					allow_video_comments = $allow_video_comments,
					collection_id = $collection_id
				WHERE
					post_id = $post_id
			");

		if ( $add_to_playlist && $video_id > 0 ) {
			$playlist_id = (int) get_option($this->pluginName . "_playlist_id");
			if ($playlist_id > 0) {
				$this->addVideoToCollection($video_id, $playlist_id);
			}
		}
	}

	/**
	 * deletes the revver specific data for a post
	 * @post_id
	 *
	 */
	function deletePost($post_id) {
		global $wpdb;
		$wpdb->query("DELETE FROM $this->db_posts WHERE post_id = $post_id");
	}

	/**
	 * creates a collection using the revver api and returns the
	 * id of the new collection
	 * @collection_name - the name you want to give the collection
	 *
	 */
	function createCollection($collection_name) {
		$collection_id = 0;
		$collection_owner = $this->getCurrentRevverUsername();

		$type = 'video';
		$query = array('ids' => array());
		$options = array('restrictions' => 0, 'subtype' => 'video_response');

		// we are not going to allow sub accounts to have their own collections as of 24 aug 2007
		/*
		if ($collection_owner != $this->username) {
			$options['owner'] = $collection_owner;
		}
		*/

		$collection_id = $this->api->callRemote('collection.create', $type, $collection_name, $query, $options);

		if ( is_numeric($collection_id) ) {
			return $collection_id;
		} else {
			// todo: figure out how WP handles exceptions or messaging
			return 0;
		}
	}

	/**
	 * gets an array of the collections that the user has.
	 * it also includes the revver featured collections
	 * by default.
	 * @include_revver_collections - default to true, if set
	 * it will pull back the revver featured collections along
	 * with the users collections.
	 * @limit - how many collections to bring back in the request
	 *
	 */
	function getCollections($include_revver_collections = true, $limit = 100) {
		$query = array('types' => array('video'));
		if ($include_revver_collections) {
			$query['owners'] = array($this->getCurrentRevverUsername(), 'editor');
		}
		$return = array('id', 'owner', 'name', 'type', 'subtype');
		$options = array('limit' => $limit);

		return $this->api->callRemote('collection.find', $query, $return, $options);
	}

	/**
	 * gets a revver collection by its id
	 * @collection_id
	 *
	 */
	function getCollectionById($collection_id) {
		$query = array('ids' => array($collection_id));
		$return = array('id', 'owner', 'name', 'type', 'query', 'subtype');
		$results = $this->api->callRemote('collection.find', $query, $return);
		return $results[0];
	}

	/**
	 * gets an array of videos from the Revver API.
	 * @query - an object containing the params to search by
	 * @offset - which record to start at.
	 * @limit - how many videos to bring back in the request
	 * @orderby - the field to order the results by
	 * @orderbyAsc - a bit that determines what direction the order by
	 * should be in (true = asc, false = desc)
	 *
	 */
	function searchVideos($query, $offset = 0, $orderby = 'publicationDate', $orderbyAsc = true, $limit = 8) {
		if (is_null($query)) $query = array('minAgeRestriction' => 1); // hack for revver api... doesn't allow null or empty arrays yet
		$returnFields = array('id', 'owner', 'title', 'description', 'createdDate', 'thumbnailUrl', 'status', 'affiliateId', 'views');
		$options = array('limit' => $limit, 'offset' => $offset, 'count' => true, 'orderBy' => array($orderby, $orderbyAsc));

		return $this->api->callRemote('video.find', $query, $returnFields, $options);
	}

	/**
	 * gets a video from the Revver API by its id.
	 * @video_id - the id of the video
	 *
	 */
	function getVideoById($video_id) {
		$returnFields = array('id', 'owner', 'title', 'author', 'description', 'keywords', 'url', 'credits', 'createdDate', 'thumbnailUrl', 'status', 'duration', 'ageRestriction', 'views', 'clicks', 'flashJsUrl', 'publicationDate', 'revenue', 'chosenThumbnail');
		$query = array('ids' => array($video_id), 'statuses' => array('online', 'offline', 'going_online', 'uploading', 'processing', 'rejected', 'review', 'failed'));
		$options = array();

		$result = $this->api->callRemote('video.find', $query, $returnFields);
		$video = $result[0];

        $durationMinutes = floor($video['duration']/60);
        $durationSeconds = $video['duration'] - $durationMinutes * 60;
        $video['duration'] = $durationMinutes . ':' . $durationSeconds;

		// make sure the time is valid
		if (empty($video['publicationDate'])) $video['publicationDate'] = time();
        $video['publicationDate'] = date('j-M-Y H:i', strtotime($video['publicationDate']));

        switch ($video['ageRestriction']) {
        	case 1:
        		$video['ageRestriction'] = __("General", $this->pluginName);
        		break;

        	case 2:
        		$video['ageRestriction'] = __("General", $this->pluginName);
        		break;

        	case 3:
        		$video['ageRestriction'] = __("13+", $this->pluginName);
        		break;

        	case 4:
        		$video['ageRestriction'] = __("17+", $this->pluginName);
        		break;

        	case 5:
        		$video['ageRestriction'] = __("17+ explicit", $this->pluginName);
        		break;
        }

		return $video;
	}

	/**
	 * gets tokens for video upload from the revver api.
	 * @count the number of tokens you want to get
	 *
	 */
	function getUploadTokens($count = 1) {
		if ( $this->username !== $this->getCurrentRevverUsername() ) {
			$options = array('owner' => $this->getCurrentRevverUsername());
			return $this->api->callRemote('video.getUploadTokens', $count, $options);
		} else {
			return $this->api->callRemote('video.getUploadTokens', $count);
		}
	}

	/**
	 * sets the meta data on a video and then returns the id of the video
	 * @token - the upload token that was used to upload the video
	 * @title - the title of the video
	 * @description - the description of the video
	 * @keywords - an array of strings
	 * @credits - a string containing the credits for the video
	 * @website - a url relating to the video
	 * @ageRestriction - mpaa rating for the video
	 *
	 */
	function createVideo($token, $title, $description, $keywords, $credits, $url, $ageRestriction) {
		$options = array(
				'description' => $description,
				'credits'     => $credits,
				'url'         => $url
			);
		return $this->api->callRemote('video.create', $token, $title, $keywords, $ageRestriction, $options);
	}

	/**
	 * deletes the videos from the revver system and also deletes
	 * the videos from any posts or comments.
	 *
	 * note: the user must have revver_delete_videos capability
	 * in order to actually delete the video from the revver
	 * system.  if they don't then the video will not be
	 * removed from revver although it is removed from the
	 * posts or the comments.  currently the administrator
	 * role has revver_delete_videos capability.
	 *
	 * @video_ids - array of video ids
	 *
	 */
	function deleteVideos($video_ids) {
		global $wpdb;

		// clean the input, cast them to ints
		for ($i = 0; $i < count($video_ids); $i++) {
			$video_ids[$i] = (int) $video_ids[$i];
		}

		if ( current_user_can($this->pluginName . '_delete_videos') ) {
			$this->api->callRemote('video.delete', $video_ids);
		}

		foreach($video_ids as $video_id) {
			if ( current_user_can('edit_posts') ) {
				$wpdb->query("UPDATE $this->db_posts SET video_id = 0 WHERE video_id = $video_id");
			}
			if ( current_user_can('edit_posts') || current_user_can('moderate_comments') ) {
				$wpdb->query("DELETE FROM $this->db_comments WHERE video_id = $video_id");
			}
		}
	}

	/**
	 * deletes the videos from the revver system and also deletes
	 * the videos from any posts or comments by only for the
	 * current user.  this method will check to see if the
	 * currently logged in user is the owner of the video
	 * they are trying to delete.  if they are not it will
	 * simply ignore their request.
	 *
	 * @video_ids - array of video ids
	 *
	 */
	function deleteVideosOfCurrentUser($video_ids) {
		global $wpdb;
		global $userdata;

		// if the current user has the ultimate capability then
		// just call deleteVideos and get outta here.
		if ( current_user_can($this->pluginName . '_delete_videos') ) {
			$this->deleteVideos($video_ids);
			return;
		}

		// clean the input, cast them to ints
		for ($i = 0; $i < count($video_ids); $i++) {
			$video_ids[$i] = (int) $video_ids[$i];
		}

		get_currentuserinfo();
		$login = get_usermeta($userdata->ID, $this->pluginName . '_login');
		if (empty($login)) return;

		// check to see if the current user is the blog owner
		$the_login = $login;
		if ( $this->username != $login ) {
			$the_login = $login . '@' . $this->username;
		}

		$video_ids_to_delete = array();

		// loop through all of the video ids we have and do the following:
		// 1.) get the video from the revver api.
		// 2.) check to see if the current user is the owner.
		// 3.) if yes, delete the video from revver and then update the db.
		foreach($video_ids as $video_id) {
			if ( $video_id != 0 ) {
				$video = $this->getVideoById($video_id);
				if ( $video['owner'] == $the_login ) {
					$video_ids_to_delete[] = $video_id;
					$wpdb->query("UPDATE $this->db_posts SET video_id = 0 WHERE video_id = $video_id");
					$wpdb->query("DELETE FROM $this->db_comments WHERE video_id = $video_id");
				}
			}
		}
		if ( count($video_ids_to_delete) ) {
			$this->api->callRemote('video.delete', $video_ids_to_delete);
		}
	}

	/**
	 * gets the Revver username of the currently logged in user
	 * that is using the admin.  If the user doesn't have a Revver
	 * username then it returns the blog owner's Revver username.
	 *
	 */
	function getCurrentRevverUsername() {
		global $userdata;
		get_currentuserinfo();
		$login = get_usermeta($userdata->ID, $this->pluginName . '_login');
		if (empty($login)) {
			$this->saveSubAccount($userdata->ID);
			$login = get_usermeta($userdata->ID, $this->pluginName . '_login');
		}
		return $login;
	}

	/**
	 * creates a revver sub account for the new user
	 * that just registered on the wordpress system.
	 *
	 * it will not create an account for WP userid #1
	 * aka the blog owner as that account automatically
	 * get the parent revver account (blog owner)
	 *
	 * @user_id - the user id of the wordpress user
	 * @bypass_check - ignores the subAccountExists check
	 * which looks at the WP db to see if the account
	 * is already created.
	 *
	 */
	function saveSubAccount($user_id, $bypass_check = false) {
		if ( $user_id == 1 ) return;
		if ( !$this->subAccountExists($user_id) || $bypass_check ) {
			// first get the user login and then
			// make sure the format is correct
			// for revver.
			$user = get_userdata($user_id);
			$login = substr(ereg_replace('[^a-zA-Z0-9]', '', $user->user_login), 0, 13);
			if ( !preg_match('/^[a-zA-Z][a-zA-Z0-9]{1,13}$/', $login) ) {
				// silly i know but revver needs a letter to start the login
				// and word press allows for friggin anything in usernames.
				// so in this putrid case we'll add a letter at the front.
				// we also have to clip the login at 12 since we're adding
				// a letter at the front.
				$login = 'r' . substr($login, 0, 12);
			}

			if ( $this->username != $login ) {
				// now create the account on revver.
				if ( !empty($user->user_email) ) {
					$results = $this->api->callRemote('user.create', $login, array('email' => $user->user_email, 'allowBroadcast' => true, 'allowMobile' => false));
				} else {
					$results = $this->api->callRemote('user.create', $login, array('allowBroadcast' => true, 'allowMobile' => false));
				}
			}

			// all is good now, update the wordpress meta key.
			update_usermeta($user_id, $this->pluginName . '_login', $login);
		}
	}

	/**
	 * deletes a revver sub account and also deletes
	 * any videos they had on posts or comments
	 *
	 * @user_id - the user id of the wordpress user
	 *
	 */
	function deleteSubAccount($user_id) {
		global $wpdb;
		$login = get_usermeta($user_id, $this->pluginName . '_login');
		if (!empty($login) && $this->username != $login) {
			$the_login = $login . '@' . $this->username;
			$this->api->callRemote('user.delete', array($login));
			$wpdb->query("UPDATE $this->db_posts SET video_id = 0 WHERE video_owner = '$the_login'");
			$wpdb->query("DELETE FROM $this->db_comments WHERE video_owner = '$the_login'");
		}
	}

	/**
	 * returns true if a sub account has already
	 * been created for the given wordpress user id.
	 *
	 * @user_id - the user id of the wordpress user
	 *
	 */
	function subAccountExists($user_Id) {
		$login = get_usermeta($user_Id, $this->pluginName . '_login');
		if (empty($login)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * gets an array of subscribers/users from the Revver API.
	 * @query - an object containing the params to search by
	 * @offset - which record to start at.
	 * @limit - how many subscribers to bring back in the request
	 * @orderby - the field to order the results by
	 * @orderbyAsc - a bit that determines what direction the order by
	 * should be in (true = asc, false = desc)
	 *
	 */
	function searchSubscribers($query, $offset = 0, $orderby = 'createdDate', $orderbyAsc = true, $limit = 50) {
		$returnFields = array('login', 'email', 'paypal', 'status', 'balance', 'address', 'allowBroadcast', 'allowMobile');
		$options = array('limit' => $limit, 'offset' => $offset, 'count' => true, 'orderBy' => array($orderby, $orderbyAsc));

		return $this->api->callRemote('user.find', $query, $returnFields, $options);
	}

	/**
	 * gets a revver subscriber by the login name
	 * if there is no subscriber for the given login
	 * the method will return false.
	 *
	 * @login - the revver login name
	 *
	 */
	function getSubscriberByLogin($login) {
		$subscribers = $this->searchSubscribers(array('logins' => array($login)), 0, 'createdDate', true, 1);
		if ( $subscribers[0] == 0 ) return false;
		return $subscribers[1][0];
	}

	/**
	 * generates a paging menu for the page.
	 *
	 * @results - the associative array with the results to page
	 * @currentPage - the page of results you're on now
	 * @pagerLink - the base url that the paging links start with
	 * @resultsPerPage - the number of results to show per page
	 *
	 */
	function genPager($results, $currentPage, $pagerLink, $resultsPerPage=8) {
	    $padding = 2;

    	$firstPage = 1;
    	$lastPage = ceil($results[0] / $resultsPerPage);

    	if ($currentPage < $firstPage + $padding) {
	        $leftPad = $currentPage - $firstPage;
        	$rightPad = 2 * $padding - ($currentPage - $firstPage);
    	} elseif ($currentPage > $lastPage - $padding) {
	        $leftPad = 2 * $padding - ($lastPage - $currentPage);
        	$rightPad = $lastPage - $currentPage;
    	} else {
	        $leftPad = $padding;
        	$rightPad = $padding;
    	}

    	$previousPage = $currentPage - 1;
    	if ($previousPage < $firstPage) $previousPage = $firstPage;

    	$nextPage = $currentPage + 1;
    	if ($nextPage > $lastPage) $nextPage = $lastPage;

    	$linkPages = array();
    	$pageBreak = false;
    	for ($i = $firstPage; $i <= $lastPage; $i++) {
	        if ($i == $firstPage || $i == $lastPage || ($i >= $currentPage - $leftPad && $i <= $currentPage + $rightPad)) {
            	$linkPages[] = $i;
            	$pageBreak = false;
        	} else {
	            if (!$pageBreak) {
                	$linkPages[] = -1;
                	$pageBreak = true;
            	}
        	}
    	}

    	$finalResult = array(
        	'resultsPerPage' => $resultsPerPage,
        	'resultsThisPage' => count($results[1]),
        	'resultsTotal' => $results[0],
        	'firstPage' => $firstPage,
        	'previousPage' => $previousPage,
        	'currentPage' => $currentPage,
        	'nextPage' => $nextPage,
        	'lastPage' => $lastPage,
        	'linkPages' => $linkPages,
        	'pagerLink' => $pagerLink,
        	'firstResultThisPage' => ($currentPage - 1) * $resultsPerPage + 1,
        	'lastResultThisPage' => ($currentPage - 1) * $resultsPerPage + count($results[1])
    	);

    	foreach ($finalResult['linkPages'] as $linkPage) {
			if ($linkPage == -1) {
				echo "...";
			} else {
				if ($linkPage == $finalResult['currentPage']) {
					echo " <span class='current-page'>" . $linkPage . "</span>";
				} else {
					echo ' <a href="' . $finalResult['pagerLink'] . '&pageNum=' . $linkPage .'">' . $linkPage . '</a> ';
				}
			}
		}
	}

	/**
	 * saves the revver specific data for a comment
	 * @comment_id
	 *
	 * the rest of the params are pulled from the post
	 * since wordpress doesn't announce the form data
	 * to this event.
	 *
	 * also note, although word press docs state that
	 * the status is posted to this function that is
	 * in fact no true.  so we manually get the comment
	 * status in the function itself.  we need to do
	 * this so that we don't add videos to the collection
	 * until the comment itself is approved.
	 *
	 */
	function saveComment($comment_id) {
		global $wpdb;

		$video_id = (int) (!isset($_POST['revver_video_id']) ? 0 : $_POST['revver_video_id']);
		$video_owner = (!isset($_POST['revver_video_owner']) ? '' : $_POST['revver_video_owner']);

		if ( !is_numeric($video_id) ) $video_id = 0;
		if ($video_id == 0) return;

		$video = $this->getVideoById($video_id);
		$video_owner = $video['owner'];
		$video_status = $video['status'];

		$is_auto_published = 0;
		if ( $video_status == 'online' ) $is_auto_published = 1;

		$wpdb->query("
				INSERT IGNORE INTO $this->db_comments
					(comment_id, video_id, video_owner, video_status, is_auto_published)
				VALUES
					($comment_id, $video_id, '$video_owner', '$video_status', $is_auto_published)
			");

		if ( $this->isCommentApproved($comment_id) ) {
			$collection_id = $this->getCollectionIdByCommentId($comment_id);
			if ($collection_id == 0) return;
			$this->addVideoToCollection($video_id, $collection_id);
		}
	}

	/**
	 * updates the revver collection that the post
	 * is apart of.  If no video id exists for the
	 * comment then we just abort.
	 *
	 * by this time if there was a video that was
	 * posted along with this comment then we'd
	 * be able to get the video id by the comment id.
	 *
	 * @comment_id
	 *
	 */
	function updateComment($comment_id) {
		global $wpdb;

		$collection_id = $this->getCollectionIdByCommentId($comment_id);
		if ($collection_id == 0) return;

		$video_id = $this->getVideoIdByCommentId($comment_id);
		if ($video_id == 0) return;

		if ( $this->isCommentApproved($comment_id) ) {
			$this->addVideoToCollection($video_id, $collection_id);
		} else {
			$this->removeVideoFromCollection($video_id, $collection_id);
		}
	}

	/**
	 * deletes the video related comment data from
	 * the system.
	 *
	 * @comment_id
	 *
	 */
	function deleteComment($comment_id) {
		global $wpdb;

		$video_id = $this->getVideoIdByCommentId($comment_id, false);
		if ($video_id == 0) return;

		$wpdb->query("DELETE FROM $this->db_comments WHERE comment_id = $comment_id");
	}

	/**
	 * returns true if the word press comment is approved.
	 *
	 * @comment_id
	 *
	 */
	function isCommentApproved($comment_id) {
		global $wpdb;

		if ( !is_numeric($comment_id) ) return 0;
		$status = (int) $wpdb->get_var("
				SELECT
					c.comment_approved
				FROM
					$wpdb->comments c
				WHERE
					c.comment_ID = $comment_id
			");
		return $status;
	}

	/**
	 * returns the video id that was posted along with the
	 * give comment id.  if no video id is present then
	 * this function will return 0.  also, if no comment
	 * is found it will return 0.
	 *
	 * @comment_id
	 * @online_only - tells the function to only return the
	 * video id if it's status is online
	 *
	 */
	function getVideoIdByCommentId($comment_id, $online_only = true) {
		global $wpdb;

		if ( !is_numeric($comment_id) ) return 0;
		if ($online_only) {
			$video_id = (int) $wpdb->get_var("
					SELECT
						c.video_id
					FROM
						$this->db_comments c
					WHERE
						c.comment_id = $comment_id
						AND c.video_status = 'online'
				");
		} else {
			$video_id = (int) $wpdb->get_var("
					SELECT
						c.video_id
					FROM
						$this->db_comments c
					WHERE
						c.comment_id = $comment_id
				");
		}
		return $video_id;
	}

	/**
	 * returns the video id that was posted along with the
	 * give post id.  if no video id is present then
	 * this function will return 0.  also, if no post
	 * is found it will return 0.
	 *
	 * @post_id
	 * @online_only - tells the function to only return the
	 * video id if it's status is online
	 *
	 */
	function getVideoIdByPostId($post_id, $online_only = true) {
		global $wpdb;

		if ( !is_numeric($post_id) ) return 0;
		if ($online_only) {
			$video_id = (int) $wpdb->get_var("
					SELECT
						p.video_id
					FROM
						$this->db_posts p
					WHERE
						p.post_id = $post_id
						AND p.video_status = 'online'
				");
		} else {
			$video_id = (int) $wpdb->get_var("
					SELECT
						p.video_id
					FROM
						$this->db_posts p
					WHERE
						p.post_id = $post_id
				");
		}
		return $video_id;
	}

	/**
	 * return the number of video comments in
	 * the given video status list that exist for
	 * the given post id.  also, the comment must
	 * be in an approved state as well.
	 *
	 * @post_id
	 *
	 */
	function getNumberOfVideoCommentsOnPost($post_id, $statuses = '\'online\'') {
		global $wpdb;

		if ( !is_numeric($post_id) ) return false;
		$videos = (int) $wpdb->get_var("
				SELECT
					count(distinct cc.video_id) as videos
				FROM
					$this->db_comments cc inner join $wpdb->comments c on c.comment_ID = cc.comment_id
				WHERE
					cc.video_status in ($statuses)
					AND c.comment_post_ID = $post_id
					AND c.comment_approved = '1'
			");
		return $videos;
	}

	/**
	 * returns the collection id by a comment id
	 * this is useful when a person adds a video comment
	 * because you can get the revver collection id that
	 * the video should be posted to by the comment
	 * id.
	 * @comment_id
	 *
	 */
	function getCollectionIdByCommentId($comment_id) {
		global $wpdb;

		if ( !is_numeric($comment_id) ) return 0;
		$collection_id = (int) $wpdb->get_var("
				SELECT
					p.collection_id
				FROM
					$wpdb->comments c INNER JOIN $this->db_posts p ON c.comment_post_ID = p.post_id
				WHERE
					c.comment_ID = $comment_id
			");
		return $collection_id;
	}

	/**
	 * adds a video to an existing collection using the
	 * revver api.
	 * @video_id
	 * @collection_id
	 *
	 */
	function addVideoToCollection($video_id, $collection_id) {
		$collection = $this->getCollectionById($collection_id);
		$ids = $collection['query']['ids'];

		$hasVideoId = false;
		$hasVideoId = in_array($video_id, $ids);

		if (!$hasVideoId) {
			$ids[] = $video_id;
			$new_settings = array(
					'name' => $collection['name'],
					'query' => array(
						'ids' => $ids
					),
					'subtype' => $collection['subtype']
				);

			$results = $this->api->callRemote('collection.update', $collection_id, $new_settings);
		}
		return;
	}

	/**
	 * removes a video to an existing collection using the
	 * revver api.
	 * @video_id
	 * @collection_id
	 *
	 */
	function removeVideoFromCollection($video_id, $collection_id) {
		$collection = $this->getCollectionById($collection_id);
		$ids = $collection['query']['ids'];
		$new_ids = array();

		$removed_video = false;
		foreach($ids as $id) {
			if ($video_id !== $id) {
				$new_ids[] = $id;
			} else {
				$removed_video = true;
			}
		}

		if ($removed_video) {
			$new_settings = array(
					'name' => $collection['name'],
					'query' => array(
						'ids' => $new_ids
					),
					'subtype' => $collection['subtype']
				);

			$results = $this->api->callRemote('collection.update', $collection_id, $new_settings);
		}
		return;
	}

	/**
	 * returns the wordpress user id that
	 * matches up with a revver subaccount.
	 *
	 * @account - the revver login name
	 *
	 */
	function getWPUserIdBySubAccount($login) {
		global $wpdb;

		$meta_key = $this->pluginName . '_login';
		$user_id = (int) $wpdb->get_var("
				SELECT
					u.user_id
				FROM
					$wpdb->usermeta u
				WHERE
					u.meta_key = '$meta_key'
					and u.meta_value = '$login'
			");
		return $user_id;
	}

}


// initialize the RevverWP so it can be passed to the WordPress hooks and filters
$revverWP = new RevverWP();

// set a var that will let some methods know whether
// or not the current page is in the admin.
$revver_is_in_admin 	  = false;
$revver_subscriber  	  = (!isset($_REQUEST['revver_subscriber']) ? '' : $_REQUEST['revver_subscriber']);
$revver_message     	  = "";
$revver_is_in_rss   	  = false;
$revver_is_in_comment_rss = false;
$revver_last_collection   = 0; // needed a global var to keep track of a newly created collection.  WP calls savePost and PublishPost right after another when a person hits publish from the post page.  this creates a problem for us.  :)

// this tiny if is needed because WP queries for the posts prior to running 'commentsrss2_head' action
// and thus we have no way around the WP posts_join bug on comments rss.  this way we find out if
// we're in the comments rss before initialization of all components is even done.
if ( strpos($_SERVER['REQUEST_URI'], 'comments') && (strpos($_SERVER['REQUEST_URI'], 'feed') || strpos($_SERVER['REQUEST_URI'], 'rss')) ) {
	$revver_is_in_comment_rss = true;
}

function revver_test() {
	echo "test";
}

function revver_registerCustomPages() {
	global $revver_subscriber;
	global $revverWP;
	if ( function_exists('add_submenu_page') ) {
		add_submenu_page(
			'index.php',
			__('Revver Messages'),
			__('Revver Messages'),
			'manage_options',
			$revverWP->pluginName . '-messages',
			'revver_showMessagesPage'
		);

		add_submenu_page(
			'plugins.php',
			__('Revver Configuration'),
			__('Revver Configuration'),
			'manage_options',
			$revverWP->pluginName . '-config',
			'revver_showPluginConfigPage'
		);

		add_submenu_page(
			'users.php',
			__('Revver Subscribers'),
			__('Revver Subscribers'),
			'edit_users',
			$revverWP->pluginName . '-subscribers',
			'revver_showSubscribersPage'
		);

		add_submenu_page(
			'profile.php',
			__('Revver Videos'),
			__('Revver Videos'),
			'level_0',
			$revverWP->pluginName . '-videos',
			'revver_showSubscribersVideoPage'
		);
	}
}

function revver_setIsInAdmin() {
	global $revver_is_in_admin;
	$revver_is_in_admin = true;
}

function revver_showMessagesPage() {
	global $revverWP;
	$revverWP->includeMessages();
}

function revver_showPluginConfigPage() {
	global $revverWP;
	$revverWP->includeConfigForm();
}

function revver_showSubscribersPage() {
	global $revverWP;
	global $revver_subscriber;
	if ( !empty($revver_subscriber) ) {
		revver_showSubscribersVideoPage();
		return;
	}
	$revverWP->includeSubscribersSearch();
}

function revver_showSubscribersVideoPage() {
	global $revverWP;
	global $revver_subscriber;
	global $userdata;

	if ( current_user_can('edit_users') && !empty($revver_subscriber) ) {
		$login = $revver_subscriber;
	} else {
		$login = $revverWP->getCurrentRevverUsername();
	}

	$revver_video_id = (int) (!isset($_REQUEST['revver_video_id']) ? 0 : $_REQUEST['revver_video_id']);
	if ( $revver_video_id != 0 ) {
		$revverWP->includeSubscribersVideo($revver_video_id, $login);
		return;
	}

	$revverWP->includeSubscribersVideos($login);
}

function revver_addCommentFormFields() {
	global $post;
	global $revverWP;
	global $user_ID;
	$revverWP->addCommentFormFields($post, $user_ID);
}

function revver_embedVideoOnAdminComment($comment_text) {
	global $revverWP;
	global $comment;
	global $revver_is_in_admin;
	if ($revver_is_in_admin) {
		return $revverWP->embedVideo($comment->comment_post_ID, $revverWP->getVideoIdByCommentId($comment->comment_ID, false)) . $comment_text;
	}
	return $comment_text;
}

function revver_embedVideoThumbOnComment($comment_text) {
	global $revverWP;
	global $comment;
	global $revver_is_in_admin;
	$thumb = "";
	if (!$revver_is_in_admin) {
		$thumb = $revverWP->embedVideoThumb($comment->comment_post_ID, $revverWP->getVideoIdByCommentId($comment->comment_ID, true));
	}
	if (strlen($thumb)) {
		$new_text = '<div class="revver-comment-clearfix">' . $thumb . $comment_text . '</div>';
		return $new_text;
	}
	return $comment_text;
}

function revver_embedVideoLinkOnAdminComment($comment_text) {
	global $revverWP;
	global $comment;
	global $revver_is_in_admin;
	if ($revver_is_in_admin) {
		$video_id = $revverWP->getVideoIdByCommentId($comment->comment_ID, false);
		if ($video_id != 0) {
			return $revverWP->embedVideoLink($video_id) . "&nbsp&nbsp;" . $comment_text;
		}
	}
	return $comment_text;
}

function revver_currency_format($amount, $precision = 2, $use_commas = true, $show_currency_symbol = false, $parentheses_for_negative_amounts = false) {
    /*
    **    An improvement to number_format.  Mainly to get rid of the annoying behaviour of negative zero amounts.
    */
    $amount = (float) $amount;
    // Get rid of negative zero
    $zero = round(0, $precision);
    if (round($amount, $precision) == $zero) {
        $amount = $zero;
    }

    if ($use_commas) {
        if ($parentheses_for_negative_amounts && ($amount < 0)) {
            $amount = '('.number_format(abs($amount), $precision).')';
        }
        else {
            $amount = number_format($amount, $precision);
        }
    }
    else {
        if ($parentheses_for_negative_amounts && ($amount < 0)) {
            $amount = '('.round(abs($amount), $precision).')';
        }
        else {
            $amount = round($amount, $precision);
        }
    }

    if ($show_currency_symbol) {
        $amount = '$'.$amount;  // Change this to use the organization's country's symbol in the future
    }
    return $amount;
}

// test code for the autoPublish command.
// un-comment the line below to run the
// autoPublish command.  currently this
// is set to run hourly
// $revverWP->autoPublish();
?>