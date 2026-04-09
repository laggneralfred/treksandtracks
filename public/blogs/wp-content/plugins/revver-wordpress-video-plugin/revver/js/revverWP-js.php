<?php
/*
$Id: revverWP-js.php 231 2008-04-30 21:06:54Z gregbrown $
$LastChangedDate: 2008-04-30 14:06:54 -0700 (Wed, 30 Apr 2008) $
$LastChangedRevision: 231 $
$LastChangedBy: gregbrown $

All of the admin functionality for the REVVER
specific stuff is in this file.

I had to make it a php file so i could use
the __() and _e() methods for translation.
*/

$__THIS_ABSPATH = dirname(__FILE__);
$__REVVERWP_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 29);
$__REVVER_ABSPATH = substr($__THIS_ABSPATH, 0, strlen($__THIS_ABSPATH) - 3);

define('REVVERWP_ABSPATH', $__REVVERWP_ABSPATH . '/');
define('REVVER_ABSPATH', $__REVVER_ABSPATH . '/');

require_once(REVVERWP_ABSPATH . 'wp-config.php');

$flash_width    	   = (int) get_option($revverWP->pluginName . "_flash_width");
$flash_height   	   = (int) get_option($revverWP->pluginName . "_flash_height");
$flash_height 		  += 32; // 32 pixels is for the flash player controls
$flash_logo     	   = get_option($revverWP->pluginName . "_flash_logo");
$flash_logo_uri 	   = get_option($revverWP->pluginName . "_flash_logo_uri");
$flash_allowfullscreen = get_option($revverWP->pluginName . "_flash_allowfullscreen");
$flash_logo_updategrab = get_option($revverWP->pluginName . "_flash_logo_updategrab");
$share_displayshare    = get_option($revverWP->pluginName . "_share_displayshare");
$share_displaydetails  = get_option($revverWP->pluginName . "_share_displaydetails");
?>

// __revver_plugin_path 	   = "<?php echo get_option('siteurl') . '/wp-content/plugins/' . $revverWP->pluginName; ?>";
__revver_plugin_path    	   = "<?php echo substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - 18); ?>";
__revver_flash_width    	   = <?php echo $flash_width; ?>;
__revver_flash_height   	   = <?php echo $flash_height; ?>;
__revver_flash_logo     	   = "<?php echo $flash_logo; ?>";
__revver_flash_logo_uri 	   = "<?php echo $flash_logo_uri; ?>";
__revver_flash_allowfullscreen = ("<?php echo $flash_allowfullscreen; ?>" == "yes") ? true : false;
__revver_flash_logo_updategrab = ("<?php echo $flash_logo_updategrab; ?>" == "yes") ? true : false;
__revver_share_displayshare    = ("<?php echo $share_displayshare; ?>" == "yes") ? true : false;
__revver_share_displaydetails  = ("<?php echo $share_displaydetails; ?>" == "yes") ? true : false;
__revver_videos         	   = []; // this will store video object responses from the api. (prevents dup calls on the same page for the same video)
__revver_video_posts    	   = [];

if (__revver_flash_logo_updategrab) {
	switch (__revver_flash_logo) {
		case "unbranded":
				__revver_flash_logo_uri = "unbranded";
			break;
		case "custom":
			break;
		default:
				__revver_flash_logo_uri = "";
				__revver_flash_logo_updategrab = false;
			break;
	}
}

function showVideoDetails(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	// get the panel objects
	var detailsPanel = document.getElementById("revver-video-details-panel-" + objectId);
	var sharePanel   = document.getElementById("revver-video-share-panel-"   + objectId);

	// get the panel control buttons
	var detailsBtn = document.getElementById("revver-video-details-btn-" + objectId);
	var shareBtn   = document.getElementById("revver-video-share-btn-"   + objectId);

	// turn off the share panel and the button
	if (__revver_share_displayshare) {
		shareBtn.src = __revver_plugin_path + "/img/share_closed.gif";
		sharePanel.style.display = "none";
	}

	getRevverVideoFromApi(post_id, video_id);

	// toggle the details panel
	if (detailsPanel.style.display == "block") {
		detailsBtn.src = __revver_plugin_path + "/img/details_closed.gif";
		detailsPanel.style.display = "none";
	} else {
		detailsBtn.src = __revver_plugin_path + "/img/details_opened.gif";
		detailsPanel.style.display = "block";
	}
}

function showVideoShare(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	// get the panel objects
	var detailsPanel = document.getElementById("revver-video-details-panel-" + objectId);
	var sharePanel   = document.getElementById("revver-video-share-panel-"   + objectId);

	// get the panel control buttons
	var detailsBtn = document.getElementById("revver-video-details-btn-" + objectId);
	var shareBtn   = document.getElementById("revver-video-share-btn-"   + objectId);

	// turn off the details panel and the button
	if (__revver_share_displaydetails) {
		detailsBtn.src = __revver_plugin_path + "/img/details_closed.gif";
		detailsPanel.style.display = "none";
	}

	getRevverVideoFromApi(post_id, video_id);

	// toggle the share panel
	if (sharePanel.style.display == "block") {
		shareBtn.src = __revver_plugin_path + "/img/share_closed.gif";
		sharePanel.style.display = "none";
	} else {
		shareBtn.src = __revver_plugin_path + "/img/share_opened.gif";
		sharePanel.style.display = "block";
	}
}

function showVideoShareSend(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	// get the panel objects
	var sendPanel = document.getElementById("revver-video-share-send-" + objectId);
	var grabPanel = document.getElementById("revver-video-share-grab-" + objectId);

	grabPanel.style.display = "none";
	sendPanel.style.display = "block";
}

function showVideoShareGrab(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	// get the panel objects
	var sendPanel = document.getElementById("revver-video-share-send-" + objectId);
	var grabPanel   = document.getElementById("revver-video-share-grab-" + objectId);

	sendPanel.style.display = "none";
	grabPanel.style.display = "block";
}

function getRevverVideoFromApi(post_id, video_id) {
	if (!__revver_video_posts[video_id]) __revver_video_posts[video_id] = [];
	var postLength = __revver_video_posts[video_id].length;
	__revver_video_posts[video_id][postLength] = post_id;

	if (!__revver_videos[video_id]) {
		var _package = {};
		var methodParams = [
				{"ids" : [video_id]},
				['id','title','owner','author','status','ageRestriction','publicationDate','modifiedDate','url','quicktimeMediaUrl','quicktimeJsUrl','flashMediaUrl','flashJsUrl','thumbnailUrl','description','keywords','duration','size','credits','views','affiliateId'],
				{"offset": 0, "limit": 1, "count": false, "affiliate": "<?php echo $revverWP->username; ?>"}
			];

    	_package.params  = "method=open.video.find";
    	_package.params += "&callback=handleRevverVideoReturn";
    	_package.params += "&params=" + REVVER.util.toJSONString.object(methodParams);
    	_package.elementId = "revverJsonApiCall-" + video_id;

    	var jsonRequestObj = new REVVER.util.jsonRequest(_package);
	    jsonRequestObj.send();
	    jsonRequestObj = null;
	} else {
		updateRevverPanels(video_id);
	}
}

function handleRevverVideoReturn(json) {
	if (!json[0]) return;
	var video_id = json[0]["id"];
	__revver_videos[video_id] = json[0];
	updateRevverPanels(video_id);
}

function updateRevverPanels(video_id) {
	var post_id;
	for (var i = 0; i < __revver_video_posts[video_id].length; i++) {
		post_id = __revver_video_posts[video_id][i];
		updateRevverDetailsPanel(post_id, video_id);
		updateRevverSharePanel(post_id, video_id);
	}
}

function updateRevverDetailsPanel(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	var desc = document.getElementById("revver-video-details-desc-" + objectId);
	var tags = document.getElementById("revver-video-details-tags-" + objectId);
	var credits = document.getElementById("revver-video-details-credits-" + objectId);
	var website = document.getElementById("revver-video-details-website-" + objectId);

	if (!__revver_videos[video_id]) {
		desc.innerHTML = "<?php echo addslashes( __('Error loading video data from Revver.', $revverWP->pluginName)); ?>";
		return;
	}

	desc.innerHTML    = __revver_videos[video_id]["description"];
	tags.innerHTML    = __revver_videos[video_id]["keywords"].join(", ");
	credits.innerHTML = __revver_videos[video_id]["credits"];
	website.innerHTML = __revver_videos[video_id]["url"];

	// hide the credits and/or website elements if they are empty
	if ( __revver_videos[video_id]["credits"] == '' ) {
		credits.parentNode.style.display = "none";
	} else {
		credits.parentNode.style.display = "block";
	}

	if ( __revver_videos[video_id]["url"] == '' || __revver_videos[video_id]["url"] == 'http://' ) {
		website.parentNode.style.display = "none";
	} else {
		website.parentNode.style.display = "block";
	}

	var myUrl = __revver_videos[video_id]["url"];
	if ( myUrl.indexOf('http') != 0 ) myUrl = "http://" + __revver_videos[video_id]["url"];
	website.href = myUrl;
}

function updateRevverSharePanel(post_id, video_id) {
	var objectId = "p" + post_id + "v" + video_id;

	var grab = document.getElementById("revver-video-share-grabcode-" + objectId);
	if (__revver_flash_logo_updategrab) {
		grab.value = '<script type="text/javascript" src="http://flash.revver.com/player/1.0/player.js?mediaId:' + __revver_videos[video_id]['id'] + ';affiliateId:' + __revver_videos[video_id]['affiliateId'] + ';pngLogo:' + escape(__revver_flash_logo_uri) + '"></script>';
	} else {
		grab.value = '<script type="text/javascript" src="http://flash.revver.com/player/1.0/player.js?mediaId:' + __revver_videos[video_id]['id'] + ';affiliateId:' + __revver_videos[video_id]['affiliateId'] + '"></script>';
	}
}

function updateRevverGrabFormat(selectBox, post_id, video_id) {
	var format = selectBox[selectBox.selectedIndex].value;
	var objectId = "p" + post_id + "v" + video_id;
	var grab = document.getElementById("revver-video-share-grabcode-" + objectId);
	var randomId = "revver" + objectId + (new Date()).getTime() + Math.floor(Math.random()*20000);

	switch (format) {
		case "quicktime":
				grab.value = '<embed id="' + randomId + '" type="video/quicktime" src="' + __revver_videos[video_id]["quicktimeMediaUrl"] + '" pluginspage="http://www.apple.com/quicktime/download/" scale="aspect" cache="False" height="376" width="480" autoplay="False"></embed>';
			break;

		case "flash":
				if (__revver_flash_logo_updategrab) {
					grab.value = '<object width="480" height="392" data="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '&pngLogo=' + escape(__revver_flash_logo_uri) + '" type="application/x-shockwave-flash" id="' + randomId + '"><param name="Movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '&pngLogo=' + escape(__revver_flash_logo_uri) + '"></param><param name="FlashVars" value="allowFullScreen=true"></param><param name="AllowFullScreen" value="true"></param><param name="AllowScriptAccess" value="always"></param><embed type="application/x-shockwave-flash" src="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '&pngLogo=' + escape(__revver_flash_logo_uri) + '" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="always" flashvars="allowFullScreen=true" allowfullscreen="true" height="392" width="480"></embed></object>';
				} else {
					grab.value = '<object width="480" height="392" data="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '" type="application/x-shockwave-flash" id="' + randomId + '"><param name="Movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '"></param><param name="FlashVars" value="allowFullScreen=true"></param><param name="AllowFullScreen" value="true"></param><param name="AllowScriptAccess" value="always"></param><embed type="application/x-shockwave-flash" src="http://flash.revver.com/player/1.0/player.swf?mediaId=' + __revver_videos[video_id]['id'] + '&affiliateId=' + __revver_videos[video_id]['affiliateId'] + '" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="always" flashvars="allowFullScreen=true" allowfullscreen="true" height="392" width="480"></embed></object>';
                }
			break;

		case "quicktimejs":
				grab.value = '<script type="text/javascript" src="' + __revver_videos[video_id]["quicktimeJsUrl"] + '"></script>';
			break;

		case "thumbnail":
				var postUrl = document.getElementById("permalink-" + objectId).value;
				grab.value = '<a href="' + postUrl + '"><img src="' + __revver_videos[video_id]["thumbnailUrl"] + '" /></a>';
			break;

		default:
				if (__revver_flash_logo_updategrab) {
					grab.value = '<script type="text/javascript" src="http://flash.revver.com/player/1.0/player.js?mediaId:' + __revver_videos[video_id]['id'] + ';affiliateId:' + __revver_videos[video_id]['affiliateId'] + ';pngLogo:' + escape(__revver_flash_logo_uri) + '"></script>';
				} else {
					grab.value = '<script type="text/javascript" src="http://flash.revver.com/player/1.0/player.js?mediaId:' + __revver_videos[video_id]['id'] + ';affiliateId:' + __revver_videos[video_id]['affiliateId'] + '"></script>';
				}
			break;
	}
}

function sendRevverShareEmail(post_id, video_id) {
	var objectId   = "p" + post_id + "v" + video_id;
	var theFormId  = 'revver-video-share-send-' + objectId;
	var msgElement = $('revver-video-share-send-result-' + objectId);

	msgElement.innerHTML = "<?php addslashes(__('Sending message...', $revverWP->pluginName)); ?>";
	msgElement.show();

	var request = new Ajax.Request(
            __revver_plugin_path + '/includes/share-video-email.php',
            {
                method: 'post',
                parameters: Form.serialize(theFormId),
                onSuccess: function (request) {
                    var json = eval( '(' + request.responseText + ')' );
					msgElement.innerHTML = json.msg;
                }
            }
        );
}

function renderVideoFromThumb(id, anchorId, affiliateId) {
	var parentContainer = document.createElement("div");
	var videoContainer = document.createElement("div");
	videoContainer.id = anchorId + "-video";
	parentContainer.appendChild(videoContainer);

	var closeLink = document.createElement("p");
	closeLink.style.textAlign = "center";
	closeLink.innerHTML = "<a href='#' onclick='hideLightbox(); return false;'><strong><?php _e('Close Video', $revverWP->pluginName); ?></strong></a>";
	parentContainer.appendChild(closeLink);

	flashParams = {};

    REVVER.widget.videoIntervals[videoContainer.id] = setInterval(function() {
        revverVideo.embed(
            {
                "divId" : videoContainer.id,
                "mediaId" : id,
                "affiliateId" : affiliateId,
                "width" : flashParams.width || null,
                "height" : flashParams.height || null,
                "bgColor" : flashParams.bgColor || null,
                "skinURL" : flashParams.skinURL || null,
                "flashvars" : flashParams.flashvars || null
            }
        );
        clearInterval(REVVER.widget.videoIntervals[videoContainer.id]);
	}, 1000);

	return parentContainer;
}

function clearDefault(field, defaultText) {
    if ( field.value == defaultText ) {
        field.value = "";
    }
    return;
}

function setPostVideoId(id) {
    parent.$('revverVideoId').value = id;
    parent.Element.toggle('revverVideoSelector');
}

function revver_uploadStart() {
    $('revver-video-metaform-container').style.display = 'block';
    __video_upload_inprogress = true;
}

function revver_uploadSuccess() {
    __video_upload_complete = true;
    __video_upload_inprogress = false;
    showSuccessScreen();
}

function revver_uploadFail() {
    __video_upload_complete = false;
    __video_upload_inprogress = false;
}

function revver_uploadCancel() {
    __video_upload_complete = false;
    __video_upload_inprogress = false;
}

function setPostVideoIdPostUpload(id, closeWin) {
    if (!__upload_only) {
        if (__auto_publish) {
            parent.$('revverAutoPublish').checked = true;
        }
        parent.$('revverVideoId').value = id;
    }
    if (closeWin) {
    	parent.Element.toggle('revverVideoSelector');
    }
}

function setCommentVideoIdPostUpload(id, closeWin) {
	window.opener.$('revver-comment-upload-msg').show();
    window.opener.$('revver_video_id').value = id;

    var commentField = window.opener.$('comment');
    if ( commentField.value == "" ) {
    	commentField.value = $F('title') + '\n' + $F('description');
    }
    if (closeWin) {
    	window.opener.focus();
    	self.close();
    }
}

function validateMetaForm(isComment) {

    if ( $F('title') == '' ) {
        alert("<?php echo addslashes( __('Please enter a title for this video.', $revverWP->pluginName)); ?>");
        $('title').focus();
        return false;
    }

    if ( $F('description') == '' ) {
        alert("<?php echo addslashes( __('Please enter a description for this video.', $revverWP->pluginName)); ?>");
        $('description').focus();
        return false;
    }

    if ( $F('keywords') == '' ) {
        alert("<?php echo addslashes( __('Please enter some keywords for this video.', $revverWP->pluginName)); ?>");
        $('keywords').focus();
        return false;
    }

    if ( !$F('agreeToTerms') ) {
        alert("<?php echo addslashes( __('You must agree to the Revver Terms of Service.', $revverWP->pluginName)); ?>");
        return false;
    }

    if (!isComment) {
        if (parent.$F('revverAutoPublish') == 1) {
            __auto_publish = true;
        }

		/*
        if ($F('upload_only') == 1) {
            __upload_only = true;
        }
        */
        createVideo();
    } else {
        createVideoComment();
    }
}

function createVideo() {
	var request = new Ajax.Request(
            'create-video.php',
            {
                method: 'post',
                parameters: Form.serialize('revver-video-metaform'),
                onSuccess: function (request) {
                    var json = eval( '(' + request.responseText + ')' );
                    __video_id = json.id;
					setPostVideoIdPostUpload(__video_id, false);
					if (__video_id == 0) {
            			alert("<?php echo addslashes( __('The video creation process failed.  Please try again.', $revverWP->pluginName)); ?>");
					} else {
                    	__meta_data_complete = true;
                    	showSuccessScreen();
					}
                }
            }
        );
}

function createVideoComment() {
	var request = new Ajax.Request(
            'create-video.php',
            {
                method: 'post',
                parameters: Form.serialize('revver-video-metaform'),
                onSuccess: function (request) {
                    var json = eval( '(' + request.responseText + ')' );
                    __video_id = json.id;
					setCommentVideoIdPostUpload(__video_id, false);
					if (__video_id == 0) {
            			alert("<?php echo addslashes( __('The video creation process failed.  Please try again.', $revverWP->pluginName)); ?>");
					} else {
        	            __meta_data_complete = true;
    	                showSuccessScreen();
	                   	$('revverVideoId').innerHTML = __video_id;
					}
                }
            }
        );
}

function showSuccessScreen() {
    if (!__video_upload_complete && __meta_data_complete) {
        if (__video_upload_inprogress) {
            alert("<?php echo addslashes( __('The settings have been saved but the video file has not been uploaded yet or is still in progress.', $revverWP->pluginName)); ?>");
        }
        if (!__video_upload_inprogress) {
            alert("<?php echo addslashes( __('The settings have been saved but you still need to upload a video file.', $revverWP->pluginName)); ?>");
        }
        return;
    }

    if (__video_upload_complete && !__meta_data_complete) {
        return;
    }

    $('revver-uploader').hide();
    $('revver-video-metaform-container').hide();
    $('revver-video-uploadsuccess').show();
}

function loadEditUserBySubscriber(subscriber) {
	var request = new Ajax.Request(
            __revver_plugin_path + '/includes/get-wpuserid-by-subscriber.php',
            {
                method: 'post',
                parameters: "subscriber=" + subscriber,
                onSuccess: function (request) {
                    var json = eval( '(' + request.responseText + ')' );
					if (json.id == 0) {
            			alert("<?php echo addslashes( __('There is no associated WordPress user for this subscriber.', $revverWP->pluginName)); ?>");
					} else {
						location.href = "<?php echo get_option('siteurl') . '/wp-admin/user-edit.php?user_id='; ?>" + json.id;
					}
                }
            }
        );
}