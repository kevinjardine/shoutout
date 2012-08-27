<?php

elgg_register_event_handler('init', 'system', 'shoutout_init');

/**
 * Init plugin.
 */
function shoutout_init() {

	elgg_register_library('elgg:shoutout', elgg_get_plugins_path() . 'shoutout/models/model.php');
	elgg_register_library('elgg:shoutout:uploads', elgg_get_plugins_path() . 'shoutout/models/uploads.php');

	// add a site navigation item
	//$item = new ElggMenuItem('shoutout', elgg_echo('shoutout:shoutouts'), 'shoutout/activity');
	//elgg_register_menu_item('site', $item);

	// add to the main css
	elgg_extend_view('css/elgg', 'shoutout/css');

	// add extend link js to poll plugin
	elgg_extend_view('object/poll','shoutout/add_link');

	// register the JavaScript
	$js = elgg_get_simplecache_url('js', 'shoutout/js');
	elgg_register_simplecache_view('js/shoutout/js');
	elgg_register_js('elgg.shoutout', $js);

	$js = elgg_get_simplecache_url('js', 'shoutout/link');
	elgg_register_simplecache_view('js/shoutout/link');
	elgg_register_js('elgg.shoutout_link', $js);

	$js = elgg_get_simplecache_url('js', 'shoutout/fileuploader');
	elgg_register_simplecache_view('js/shoutout/fileuploader');
	elgg_register_js('qq.fileuploader', $js);

	// routing of urls
	elgg_register_page_handler('shoutout', 'shoutout_page_handler');
	elgg_register_page_handler('activity', 'shoutout_page_handler');

	// link elgg-river-timestamp to the shoutout item page
	elgg_register_plugin_hook_handler('view', 'river/elements/layout', 'shoutout_river_add_link');

	// add edit and delete in river menu for shoutouts
	elgg_register_plugin_hook_handler('register', 'menu:river', 'shoutout_river_menu_setup');

	elgg_register_entity_type('object','shoutout');

	// override the default url to view a showout object
	elgg_register_entity_url_handler('object', 'shoutout', 'shoutout_url_handler');
	
	// optionally add wall link to owner block
	$shoutout_wall = elgg_get_plugin_setting('wall', 'shoutout');
	if ($shoutout_wall == 'yes') {
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'shoutout_owner_block_menu');
	}
	
	// optionally support video attachments
	$shoutout_video_add = elgg_get_plugin_setting('video_add', 'shoutout');
	if ($shoutout_video_add == 'yes') {
		elgg_register_library('elgg:shoutout:video', elgg_get_plugins_path() . 'shoutout/models/video.php');
		elgg_register_plugin_hook_handler('shoutout:video:preprocess', 'url', 'shoutout_video_preprocess_url');
	}

	// register actions
	$action_path = elgg_get_plugins_path() . 'shoutout/actions/shoutout';
	elgg_register_action('shoutout/edit', "$action_path/edit.php");
	elgg_register_action('shoutout/delete', "$action_path/delete.php");
	elgg_register_action('shoutout/attach/add', "$action_path/attach/add.php");
	elgg_register_action('shoutout/attach/delete', "$action_path/attach/delete.php");

	// over-ride comments action
	elgg_register_action('comments/add', elgg_get_plugins_path() . 'shoutout/actions/comments/add.php');
}

/**
 * Dispatches shoutout pages.
 * URLs take the form of
 *  All shoutouts:   	shoutout/all
 *  View shoutout:   	shoutout/view/<guid>
 *  Edit shoutout:   	shoutout/edit/<guid>
 *  Comment shoutout:   shoutout/comment/<guid>
 *  Show thumb:			shoutout/temporary_thumb/<guid>/<file_name>
 * @param array $page
 * @return bool
 */
function shoutout_page_handler($page) {

	elgg_load_library('elgg:shoutout');

	$page_type = $page[0];
	switch ($page_type) {
		case 'all':
			//echo shoutout_get_all_page();
			echo shoutout_get_activity_page();
			break;
		case 'activity':
			echo shoutout_get_activity_page();
			break;
		case 'shout_entity':
			// allows attaching entities to shoutouts, eg. polls
			echo shoutout_get_activity_page($page[1]);
			break;
		case 'owner':
			set_input('page_type','mine');
			echo shoutout_get_activity_page();
			break;
		case 'friends':
			set_input('page_type','friends');
			echo shoutout_get_activity_page();
			break;
		case 'wall':
			set_input('page_type','wall');
			set_input('origin','shoutout/wall');
			echo shoutout_get_activity_page();
			break;
		case 'activity_river_view':
			$activity = shoutout_get_activity();
			echo $activity['content'];
			break;
		case 'view':
			echo shoutout_get_view_page($page[1]);
			break;
		case 'edit':
			if (isset($page[2]) && $page[2] == 'wall') {
				set_input('origin','shoutout/wall');
			} else {
				set_input('origin','shoutout/all');
			}
			echo shoutout_get_edit_page($page[1]);
			break;
		case 'comment':
			echo shoutout_get_comment_page($page[1]);
			break;
		case 'temporary_thumb':
			echo shoutout_show_temporary_attachment($page[1],$page[2],urldecode($page[3]));
			break;
		case 'show_attachment_image':
			shoutout_show_attachment_image($page[1]);
			break;
		case 'download_attachment':
			shoutout_download_attachment($page[1]);
			break;
		case 'get_file_uploader_bit':
			echo shoutout_get_file_uploader_bit($page[1]);
			break;
		case 'watch':
			echo elgg_view('shoutout/video_watch',array('guid'=>$page[1]));
			break;
		default:
			echo shoutout_get_activity_page();
			break;
	}

	return true;
}

/**
 * Format and return the URL for shoutouts.
 *
 * @param ElggObject $entity Shoutout object
 * @return string URL of shoutout.
 */
function shoutout_url_handler($entity) {
	if (!$entity->getOwnerEntity()) {
		// default to a standard view if no owner.
		return FALSE;
	}

	return "shoutout/view/{$entity->guid}";
}

/**
 * Add edit and delete to river actions
 */
function shoutout_river_menu_setup($hook, $type, $return, $params) {
	$item = $params['item'];
	if ($item) {
		$page_type = get_input('page_type');
		$object = $item->getObjectEntity();
		if (elgg_instanceof($object,'object','shoutout') && $object->canEdit()) {
			// edit link
			$href = "shoutout/edit/{$object->guid}";
			if ($page_type == 'wall') {
				$href .= '/wall';
			}
			$options = array(
				'name' => 'edit',
				'text' => elgg_echo('edit'),
				'title' => elgg_echo('edit:this'),
				'href' => $href,
				'priority' => 200,
			);
			$return[] = ElggMenuItem::factory($options);
			// delete link
			$href = "action/shoutout/delete?guid={$object->guid}";
			if ($page_type == 'wall') {
				$href .= '&origin=wall';
			}
			$options = array(
				'name' => 'delete',
				'text' => elgg_view_icon('delete'),
				'title' => elgg_echo('delete:this'),
				'href' => $href,
			//				'confirm' => elgg_echo('deleteconfirm'),
				'link_class' => 'shoutout-delete-link',
				'priority' => 300,
			);
			$return[] = ElggMenuItem::factory($options);
		}
	}

	return $return;
}

/**
 * Add a menu item to an ownerblock
 */
function shoutout_owner_block_menu($hook, $type, $return, $params) {
	$e = $params['entity'];
	// show the wall link if the user is looking at his/her own owner block
	if (elgg_instanceof($e, 'user') && ($e->guid == elgg_get_logged_in_user_guid())) {
		$url = "shoutout/wall";
		$item = new ElggMenuItem('shoutout:wall', elgg_echo('shoutout:wall'), $url);
		$return[] = $item;
	}

	return $return;
}

/**
 * Prepend HTTP scheme if missing
 * @param string $hook
 * @param string $type
 * @param string $returnvalue
 * @param array $params
 * @return string
 */
function shoutout_video_preprocess_url($hook, $type, $returnvalue, $params) {
    $parsed = parse_url($returnvalue);
    if (empty($parsed['host']) && ! empty($parsed['path']) && $parsed['path'][0] !== '/') {
        // user probably forgot scheme
        $returnvalue = 'http://' . $returnvalue;
    }
    return $returnvalue;
}

function shoutout_river_add_link($hook, $type, $return, $params) {
	global $shoutout_closure_hack_url;
	if (isset($params['vars']['item'])) {
		$item = $params['vars']['item'];
		if ($item instanceof ElggRiverItem) {
			/* @var ElggRiverItem $item  */
			$entity = $item->getObjectEntity();
			if (elgg_instanceof($entity, 'object', 'shoutout')) {
				$shoutout_closure_hack_url = $entity->getURL();
				$return = preg_replace_callback(
	                '@(<span class="elgg-river-timestamp">)([^/]+)(</span>)@',
	            	'shoutout_add_link',
				$return);
				// allow styling shoutouts differently
				$return = str_replace(
	                ' elgg-river-item',
	                ' shoutout-river-item elgg-river-item',
				$return);
			}
		}
	}
	return $return;
}

function shoutout_add_link ($m) {
	global $shoutout_closure_hack_url;
	$link = elgg_view('output/url', array(
                        'href' => $shoutout_closure_hack_url,
                        'text' => $m[2],
	));
	return $m[1] . $link . $m[3];
}