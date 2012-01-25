<?php
function shoutout_get_all_page() {
	elgg_load_library('elgg:shoutout:uploads');
	elgg_load_js('elgg.shoutout');
	elgg_load_js('qq.fileuploader');
	$title = elgg_echo('shoutout:listing_title');
	$form_vars = array('id' => 'shoutout-form');
	$content = '';
	if (elgg_is_logged_in()) {
		$content .= elgg_view_form('shoutout/edit',$form_vars);
	}
	$content .= elgg_view('shoutout/content');
	$params = array('title' => $title, 'content' => $content,'filter' => '');

	$body = elgg_view_layout("content", $params);

	return elgg_view_page($title,$body);
}

function shoutout_get_view_page($guid) {
	elgg_extend_view('forms/comments/add','shoutout/add_river_flag');
	$title = elgg_echo('shoutout:view_title');
	$shoutout = get_entity($guid);
	if (elgg_instanceof($shoutout,'object','shoutout')) {
		$content = '<div class="shoutout-view-wrapper">'.parse_urls($shoutout->description).'</div>';
		$content .= shoutout_get_attachment_listing($shoutout);
		$content .= elgg_view_comments($shoutout);
	} else {
		$content = elgg_echo('shoutout:bad_shoutout');
	}
	elgg_push_breadcrumb(elgg_echo('shoutout:listing_title'),'shoutout/activity');
	$params = array('title' => $title, 'content' => $content,'filter' => '');

	$body = elgg_view_layout("content", $params);

	return elgg_view_page($title,$body);
	
}
function shoutout_get_edit_page($guid) {
	elgg_load_library('elgg:shoutout:uploads');
	elgg_load_js('elgg.shoutout');
	elgg_load_js('qq.fileuploader');
	$title = elgg_echo('shoutout:edit_title');
	elgg_push_breadcrumb(elgg_echo('shoutout:listing_title'),'shoutout/activity');
	$form_vars = array('id' => 'shoutout-form');
	$body_vars = array();
	if ($guid) {
		$entity = get_entity($guid);
		if (elgg_instanceof($entity,'object','shoutout') && $entity->canEdit()) {
			$body_vars['entity'] = $entity;
			$content = elgg_view_form('shoutout/edit',$form_vars,$body_vars);
		} else {
			$content = elgg_echo('shoutout:bad_shoutout_for_edit');
		}
	} else {
		$content = elgg_view_form('shoutout/edit',$form_vars,$body_vars);
	}
	$params = array('title' => $title, 'content' => $content,'filter' => '');

	$body = elgg_view_layout("content", $params);

	return elgg_view_page($title,$body);
	
}

function shoutout_edit($guid,$attached_guid, $text,$access_id,$attachments) {
	$user_guid = elgg_get_logged_in_user_guid();
	if ($guid) {
		$shoutout = get_entity($guid);
		if (!elgg_instanceof($shoutout,'object','shoutout')) {
			return FALSE;
		}
	} else {
		$shoutout = new ElggObject();
		$shoutout->subtype = 'shoutout';
		$shoutout->owner_guid = $user_guid;
		$shoutout->container_guid = $user_guid;
		$shoutout->title = elgg_echo('shoutout:entity_title');
	}
	$shoutout->access_id = $access_id;
	$shoutout->description = $text;
	
	if($shoutout->save()) {
		if ($guid) {
			// clear attachment annotations
			elgg_delete_annotations(array('guid' => $guid,'annotation_name'=> 'shoutout_attachment'));
			// clear attached entity
			remove_entity_relationships($guid, 'shoutout_attached_entity');
		}
		if ($attachments) {
			foreach ($attachments as $a) {
				$time_bit = $a['timeBit'];
				$file_name = urldecode($a['fileName']);
				$value = "$time_bit|$file_name";
				create_annotation($shoutout->guid, 'shoutout_attachment', $value,'',$user_guid, ACCESS_PUBLIC);
			}
		}
		if ($attached_guid) {
			add_entity_relationship($shoutout->guid, 'shoutout_attached_entity', $attached_guid);
		}
		if(!$guid) {
			add_to_river('river/object/shoutout/create', 'create', elgg_get_logged_in_user_guid(), $shoutout->guid);
		}
		return TRUE;
	} else {
		return FALSE;
	}
}

function shoutout_attach_add($original_name) {
	elgg_load_library('elgg:shoutout:uploads');
	$owner_guid = elgg_get_logged_in_user_guid();
	if ($owner_guid) {	
		$user_dir = elgg_get_data_path() . shoutout_attach_make_file_matrix($owner_guid);
		if (shoutout_attach_setup_directory($user_dir)) {
			$attachment_dir = $user_dir .'/attachments/'.time().'/';
			if (shoutout_attach_setup_directory($attachment_dir)) {
				//$location = $attachment_dir . $filestorename;
				// list of valid extensions, ex. array("jpeg", "xml", "bmp")
				$whitelist = elgg_get_plugin_setting('whitelist','shoutout');
				if ($whitelist) {
					$allowedExtensions = array();
					foreach(explode(',',$whitelist) as $ext) {
						$allowedExtensions[] = trim($ext);
					}
				} else {
					$allowedExtensions = array('png','gif','jpg','jpeg');
				}
				// max file size in bytes
				$sizeLimit = elgg_get_plugin_setting('size_limit','shoutout');
				if (!$sizeLimit) {
					$sizeLimit = 2 * 1024 * 1024;
				}
				$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
				$result = $uploader->handleUpload($attachment_dir);
				
				// create thumb if this is an image
				$pathinfo = pathinfo($original_name);
        		$ext = $pathinfo['extension'];
        		if (in_array($ext,array('png','jpg','gif','jpeg','jpe'))) {
        			shoutout_attach_write_thumbs($attachment_dir,$original_name,$pathinfo['filename'],"40x40");
        		}
				// to pass data through iframe you will need to encode all html tags
				return htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			} 
		}
	}
	return '';
}

// shows an attachment list for attachments created before the object they are attached to
// TODO: make this a view

/*function shoutout_attach_show_temporary_attachment_listing($dir,$fn,$ofn,$mime_type,$guid) {
	$root = elgg_get_site_url();
	
	$title_bit = " title = \"$ofn\" alt=\"$ofn\" ";
	
	if (substr_count($mime_type,'image/')) {
		$image = '<img class="attachment_image" '.$title_bit.'src="'.urlencode($root."shoutout/attach/show_temporary_image/$guid/$fn").'">';
	} else {
		//TODO: make this less generic based on the mime type
		$image = '<img class="attachment_image" '.$title_bit.'src="'.$root.'mod/file/graphics/icons/general.gif">';
	}
	$delete_link = urlencode(elgg_add_action_tokens_to_url($root."shoutout/attach/remove/$guid/$fn"));
	$result = array(
		'image'=>$image,
		'delete_link' => $delete_link,
		'token' => implode(',',array('local',$fn,$ofn,$mime_type,$guid)),
	);
	return json_encode($result);
}*/

function shoutout_attach_delete($guid,$time_bit,$ofn) {
	$pathinfo = pathinfo($ofn);
    $fn = $pathinfo['filename'];
    $path_bit = elgg_get_data_path().shoutout_attach_make_file_matrix($guid).'/attachments/'.$time_bit.'/';
    $original = $path_bit.$ofn;
	$thumb = $path_bit.'thumb'.$fn.'.jpg';
	unlink($original);
	unlink($thumb);
}

// TODO - probably only need a single thumb size for shoutouts
function shoutout_attach_write_thumbs($dir,$ofn,$fn,$attachment_image_size) {
	if ($attachment_image_size) {
		$a = explode('x',$attachment_image_size);
		$thumb_width = (int) $a[0];
		if (count($a) == 2) {
			$thumb_height = (int) $a[1];
		} else {
			$thumb_height = $thumb_width;
		}
		$thumbnail = get_resized_image_from_existing_file($dir.$ofn,$thumb_width,$thumb_height,false);
		$fd = fopen($dir.'thumb'.$fn.".jpg",'wb');
		fwrite($fd,$thumbnail);
		fclose($fd);
	} else {
		$thumbnail = get_resized_image_from_existing_file($dir.$fn,46,46, true);
		$fd = fopen($dir.'thumb'.$fn,'wb');
		fwrite($fd,$thumbnail);
		fclose($fd);
		
		$thumbsmall = get_resized_image_from_existing_file($dir.$fn,60,60, true);
		$fd = fopen($dir.'smallthumb'.$fn,'wb');
		fwrite($fd,$thumbsmall);
		fclose($fd);
		
		$thumbmedium = get_resized_image_from_existing_file($dir.$fn,130,130, false);
		$fd = fopen($dir.'mediumthumb'.$fn,'wb');
		fwrite($fd,$thumbmedium);
		fclose($fd);
		
		$thumblarge = get_resized_image_from_existing_file($dir.$fn,195,130, false);
		$fd = fopen($dir.'largethumb'.$fn,'wb');
		fwrite($fd,$thumblarge);
		fclose($fd);
		
		$thumbxlarge = get_resized_image_from_existing_file($dir.$fn,267,178, false);
		$fd = fopen($dir.'xlargethumb'.$fn,'wb');
		fwrite($fd,$thumbxlarge);
		fclose($fd);
	}
}

function shoutout_attach_setup_directory($dir) {
	if (!file_exists($dir)) {
		return @mkdir($dir, 0700, true); 
	} else {
		return true;
	}
}

function shoutout_attach_make_file_matrix($guid) {
	// lookup the entity
	$user = get_entity($guid);
	if (!elgg_instanceof($user,'user'))
	{
		// only to be used for user directories
		return FALSE;
	}

	$time_created = date('Y/m/d', $user->time_created);
	return "$time_created/$guid";
}

function shoutout_show_temporary_attachment($guid,$time_bit,$ofn) {
	// security - only admins and that attachment owner get to see this
	if (elgg_is_admin_logged_in() || (elgg_get_logged_in_user_guid() == $guid)) {
		$pathinfo = pathinfo($ofn);
        $fn = $pathinfo['filename'];
        $ext = $pathinfo['extension'];
        header("Content-Type: image/jpg");
		header("Content-Disposition: inline; filename=\"$fn.jpg\"");
        if (in_array($ext, array('png','jpg','jpeg','gif'))) {		
			$content = file_get_contents(elgg_get_data_path().shoutout_attach_make_file_matrix($guid).'/attachments/'.$time_bit.'/thumb'.$fn.'.jpg');
        }
		//echo elgg_get_data_path().shoutout_attach_make_file_matrix($guid).'/attachments/'.$time_bit.'/thumb'.$fn;
		$splitString = str_split($content, 8192);
		foreach($splitString as $chunk) {
			echo $chunk;
		}
	}
	exit;
}

function shoutout_get_activity() {
	elgg_extend_view('forms/comments/add','shoutout/add_river_flag');
	$options = array();
	
	$override_activity = elgg_get_plugin_setting('override_activity','shoutout');
	if ($override_activity == 'yes') {
		$type = preg_replace('[\W]', '', get_input('type', 'object'));
		$subtype = preg_replace('[\W]', '', get_input('subtype', 'shoutout'));
	} else {
		$type = preg_replace('[\W]', '', get_input('type', 'all'));
		$subtype = preg_replace('[\W]', '', get_input('subtype', ''));
	}
	
	$page_type = preg_replace('[\W]', '', get_input('page_type', 'all'));

	if ($subtype) {
		$selector = "type=$type&subtype=$subtype";
	} else {
		$selector = "type=$type";
	}	
	
	if (($override_activity == 'yes') && ($subtype == 'shoutout')) {
		$river_mine_title = elgg_echo('shoutout:river:mine');
		$river_friends_title = elgg_echo('shoutout:river:friends');
		$river_all_title = elgg_echo('shoutout:river:all');
	} else {
		$river_mine_title = elgg_echo('river:mine');
		$river_friends_title = elgg_echo('river:friends');
		$river_all_title = elgg_echo('river:all');
	}
	
	if ($type != 'all') {
		$options['type'] = $type;
		if ($subtype) {
			$options['subtype'] = $subtype;
		}
	}
	
	switch ($page_type) {
		case 'mine':
			$title = $river_mine_title;
			$page_filter = 'mine';
			$options['subject_guid'] = elgg_get_logged_in_user_guid();
			break;
		case 'friends':
			$title = $river_friends_title;
			$page_filter = 'friends';
			$options['relationship_guid'] = elgg_get_logged_in_user_guid();
			$options['relationship'] = 'friend';
			break;
		default:
			$title = $river_all_title;
			$page_filter = 'all';
			break;
	}	
	
	$activity = elgg_list_river($options);
	
	return array('title'=> $title, 'content' => $filter . $activity, 'page_filter'=>$page_filter,'selector'=>$selector);
	
}

function shoutout_get_activity_page($attached_guid = 0) {
	/**
	 * Main activity stream list page
	 */
	
	elgg_load_library('elgg:shoutout:uploads');
	elgg_load_js('elgg.shoutout');
	elgg_load_js('qq.fileuploader');
	
	$activity = shoutout_get_activity();

	$activity_bit = '<div id="shoutout-content-area">'.$activity['content'].'</div>';
	
	if (elgg_is_logged_in()) {
		$form_vars = array('id' => 'shoutout-form');
		$body_vars = array('attached_guid'=>$attached_guid);
	
		$content .= elgg_view_form('shoutout/edit',$form_vars,$body_vars);
	} else {
		$content .= '<p>'.elgg_echo('shoutout:login').'</p>';
	}
	
	$sidebar = elgg_view('core/river/sidebar');
	
	$filter_tabs = elgg_view('page/layouts/content/filter',array('filter_context' => $activity['page_filter']));
	$filter_dropdown = $filter = elgg_view('core/river/filter', array('selector' => $activity['selector']));
	
	$params = array(
		'content' =>  $content . $filter_dropdown . $filter_tabs . $activity_bit,
		'title' => $activity['title'],
		'sidebar' => $sidebar,
		'filter' => '',
		'class' => 'elgg-river-layout',
	);
	
	$body = elgg_view_layout('content', $params);
	
	return elgg_view_page($title, $body);
	
}

function shoutout_get_attachment_listing($entity) {
	$listing = '';
	$attachments = elgg_get_annotations(array('guid' => $entity->guid,'annotation_name' => 'shoutout_attachment'));
	if ($attachments) {
		$listing .= '<div class="shoutout-attachment-listing">';
		foreach ($attachments as $attachment) {
			$listing .= shoutout_attachment_listing($entity->guid,$attachment);
		}
		$listing .= '</div>';
	}
	return $listing;
}

function shoutout_attachment_listing($entity_guid,$annotation) {
	$body = '';
	$token = $annotation->value;
	
	if ($token) {
		$url = elgg_get_site_url();
		list($time_bit,$ofn) = explode('|',$annotation->value);
			
		$pathinfo = pathinfo($ofn);
    	$fn = $pathinfo['filename'];
    	$ext = $pathinfo['extension'];
	
		$title_bit = " title = \"$ofn\" alt=\"$ofn\" ";
		$body = '<div class="shoutout-attachment-listing-item">';
		
		if (in_array($ext, array('png','jpg','jpeg','gif'))) {
			$thumb = $url.'shoutout/show_attachment_image/'.$annotation->id;
		} else {
			$mime_type = shoutout_determine_mime_type($ofn);
			$thumb = shoutout_get_file_icon_url($mime_type);
		}			
		$image = '<img class="shoutout-attachment-image" '.$title_bit.'src="'.$thumb.'">';
		
		$body .= '<a href="'.$url.'shoutout/download_attachment/'.$annotation->id.'">'.$image.' '.$ofn.'</a>';
		$body .= '</div>';		
	}
	
	return $body;
}

function shoutout_show_attachment_image($annotation_id) {
	$annotation = elgg_get_annotation_from_id($annotation_id);
	if ($annotation) {
		$entity_guid = $annotation->entity_guid;	
		if (get_entity($entity_guid)) {
			list($time_bit,$ofn) = explode('|',$annotation->value);
			$pathinfo = pathinfo($ofn);
    		$fn = $pathinfo['filename'];
    		header("Content-Type: image/jpeg");
			header("Content-Disposition: inline; filename=\"$fn.jpg\"");
			$content = file_get_contents(elgg_get_data_path().shoutout_attach_make_file_matrix($annotation->owner_guid).'/attachments/'.$time_bit.'/thumb'.$fn.'.jpg');
			$splitString = str_split($content, 8192);
			foreach($splitString as $chunk) {
				echo $chunk;
			}
		}
	}
	exit;
}

function shoutout_download_attachment($annotation_id) {
	$annotation = elgg_get_annotation_from_id($annotation_id);
	if ($annotation) {
		$entity_guid = $annotation->entity_guid;	
		if (get_entity($entity_guid)) {
			list($time_bit,$ofn) = explode('|',$annotation->value);
			$full_file_path = elgg_get_data_path().shoutout_attach_make_file_matrix($annotation->owner_guid).'/attachments/'.$time_bit.'/'.$ofn;
			// determine mime type
			$mime_type = shoutout_determine_mime_type($full_file_path);
			header("Content-Type: $mime_type");
			header("Content-Disposition: attachment; filename=\"$ofn\"");
			$content = file_get_contents($full_file_path);
			$splitString = str_split($content, 8192);
			foreach($splitString as $chunk) {
				echo $chunk;
			}
		}
	}
	exit;
}

function shoutout_get_file_icon_url($mime) {
	error_log("mime type: $mime");
	$mapping = array(
		'application/excel' => 'excel',
		'application/msword' => 'word',
		'application/pdf' => 'pdf',
		'application/powerpoint' => 'ppt',
		'application/vnd.ms-excel' => 'excel',
		'application/vnd.ms-powerpoint' => 'ppt',
		'application/vnd.oasis.opendocument.text' => 'openoffice',
		'application/x-gzip' => 'archive',
		'application/x-rar-compressed' => 'archive',
		'application/x-stuffit' => 'archive',
		'application/zip' => 'archive',

		'text/directory' => 'vcard',
		'text/v-card' => 'vcard',

		'application' => 'application',
		'audio' => 'music',
		'text' => 'text',
		'video' => 'video',
	);
	
	if ($mime) {
		$base_type = substr($mime, 0, strpos($mime, '/'));
	} else {
		$mime = 'none';
		$base_type = 'none';
	}

	if (isset($mapping[$mime])) {
		$type = $mapping[$mime];
	} elseif (isset($mapping[$base_type])) {
		$type = $mapping[$base_type];
	} else {
		$type = 'general';
	}

	if ($size == 'large') {
		$ext = '_lrg';
	} else {
		$ext = '';
	}
	
	$url = "mod/file/graphics/icons/{$type}{$ext}.gif";
	
	return $url;
}

function shoutout_determine_mime_type($full_file_path) {
	$mime_type = '';
	error_log("Full file path: $full_file_path");
	if (function_exists('finfo_file') && defined('FILEINFO_MIME_TYPE')) {
		error_log('using finfo_file');
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		if ($finfo) {
			$mime_type = finfo_file($finfo, $full_file_path, FILEINFO_MIME);
		}		
		finfo_close($finfo);
	} else if (function_exists('mime_content_type')) {
		error_log('using mime_content_type');
		$mime_type = mime_content_type($full_file_path);
	} else {
		error_log('failed to find mime type functions');
		$pathinfo = pathinfo($full_file_path);
		$ext = $pathinfo['extension'];
		if (in_array($ext, array('png','jpg','jpeg','gif'))) {
			$mime_type = "image/$ext";
		}
	}
	if (!$mime_type) {
		$mime_type = 'application/octet-stream';
	}
	
	return $mime_type;
}

function shoutout_get_file_uploader_bit($guid) {
	$url = elgg_get_site_url();
	$listing = '';
	$template = <<< __HTML
<li class="qq-upload-success">
	<span class="qq-upload-thumb">%s</span>
	<span class="qq-upload-file">%s</span>
    <span class="qq-upload-size">%s</span>
    <span class="qq-upload-dir" style="display:none;">%s</span>
    <span class="qq-upload-delete">%s</span>
</li>
__HTML;
	$attachments = elgg_get_annotations(array('guid' => $guid,'annotation_name' => 'shoutout_attachment'));
	if ($attachments) {
		foreach($attachments as $a) {
			list($time_bit,$ofn) = explode("|",$a->value);
			$pathinfo = pathinfo($ofn);
    		$ext = $pathinfo['extension'];
			if (in_array($ext, array('png','jpg','jpeg','gif'))) {
				$image = '<img class="shoutout-attachment-image" '.$title_bit.'src="'.$url.'shoutout/show_attachment_image/'.$a->id.'">';
			} else {
				//TODO: make this less generic based on the extension
				$image = '<img class="shoutout-attachment-image" '.$title_bit.'src="'.$url.'mod/file/graphics/icons/general.gif">';
			}
			
			$delete_link =  'action/shoutout/attach/delete?guid='.$guid.'&time_bit='.$time_bit.'&filename='.$ofn;
			$delete = '<a class="shoutout-attachment-delete" href="'.$delete_link.'">Delete</a>';
			$listing .= sprintf($template,$image,$ofn,'',$time_bit,$delete);
		}
	}

   echo $listing;
}

function shoutout_delete($guid) {
	$shoutout = get_entity($guid);
	if (!elgg_instanceof($shoutout,'object','shoutout')) {
		return FALSE;
	}
	
	// delete attachments
	$attachments = elgg_get_annotations(array('guid' => $guid,'annotation_name' => 'shoutout_attachment'));
	if ($attachments) {
		foreach($attachments as $a) {
			list($time_bit,$ofn) = explode("|",$a->value);
			shoutout_attach_delete($a->owner_guid,$time_bit,$ofn);
		}
	}
	return $shoutout->delete();
}

// hack for some systems

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}