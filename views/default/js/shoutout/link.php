elgg.provide('elgg.shoutout_link');

elgg.shoutout_link.init = function () {
	if ($(".elgg-menu-longtext").length > 0) {
		// back up from TinyMCE widget
		$(".elgg-menu-longtext").prev().append(' | <a id="shout-poll-link" href="javascript:void(0)">'+elgg.echo('shoutout:add_poll')+'</a>');
	} else {
		// back up from plain text
		$('[name="generic_comment"]').prev().append(' | <a id="shout-poll-link" href="javascript:void(0)">'+elgg.echo('shoutout:add_poll')+'</a>');
	}
	$('#shout-poll-link').live('click',elgg.shoutout_link.handleLink);
}

elgg.shoutout_link.handleLink = function(e) {
	var guid = $('[name="entity_guid"]').val();
	location.href = elgg.get_site_url() + "shoutout/shout_entity/"+guid;
}
elgg.register_hook_handler('init', 'system', elgg.shoutout_link.init);
