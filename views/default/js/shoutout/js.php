//<script>
elgg.provide('elgg.shoutout');

elgg.shoutout.init = function () {

	// support a Twitter-like countdown field
	$('.shoutout-countdown').each(elgg.shoutout.handleCountdownFieldsUpdate);
	$('.shoutout-countdown').change(elgg.shoutout.handleCountdownFieldsUpdate);
	$('.shoutout-countdown').keyup(elgg.shoutout.handleCountdownFieldsUpdate);

	$('.shoutout-post-button').click(elgg.shoutout.handlePost);
	$('.shoutout-delete-link').live('click',elgg.shoutout.handleDelete);
	$('.shoutout-attachment-delete').live('click',elgg.shoutout.handleAttachmentDelete);

	elgg.shoutout.resetFileUploader();
		
	var guid = $('#shoutout-guid').val();
	if (guid > 0) {
		$(".qq-upload-list").load(elgg.get_site_url()+'shoutout/get_file_uploader_bit/'+guid);
	}
}

elgg.shoutout.resetFileUploader = function() {
	var uploader = new qq.FileUploader({
	    // pass the dom node (ex. $(selector)[0] for jQuery users)
	    element: document.getElementById('shoutout-file-uploader'),
	    // path to server-side upload script
	    action: elgg.get_site_url()+'action/shoutout/attach/add',
	    debug: true,
	    template: '<div class="qq-uploader">' + 
			        '<div style="display:none;" class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
			        '<div class="elgg-button elgg-button-action">Attach a file</div>' +
			        '<ul class="qq-upload-list"></ul>' + 
			     '</div>',

	     classes: {
	            // used to get elements from templates
	            button: 'elgg-button',
	            drop: 'qq-upload-drop-area',
	            dropActive: 'qq-upload-drop-area-active',
	            list: 'qq-upload-list',
	                        
	            file: 'qq-upload-file',
	            spinner: 'qq-upload-spinner',
	            size: 'qq-upload-size',
	            cancel: 'qq-upload-cancel',

	            // added to list item when upload completes
	            // used in css to hide progress spinner
	            success: 'qq-upload-success',
	            fail: 'qq-upload-fail'
	        }
	});
}

elgg.shoutout.handleAttachmentDelete = function(event) {
	elgg.action($(this).attr('href'));
	$(this).parent().parent().hide();
	event.preventDefault();
	return false;
}

elgg.shoutout.handleDelete = function(event) {
	var confirmText = $(this).attr('rel') || elgg.echo('question:areyousure');
	if (confirm(confirmText)) {
		elgg.action($(this).attr('href'),{success: 
			function(response) {
				if (response.success) {
					$('#shoutout-content-area').load(elgg.get_site_url()+'shoutout/activity_river_view');
					elgg.system_message(response.msg);
				} else {
					elgg.register_error(response.msg);
				}
			}
		});
	}	
	
	event.preventDefault();
	return false;
}

elgg.shoutout.handleCountdownFieldsUpdate = function() {
	var max = $("#shoutout-countdown-max").val();
	var current_length = $(this).val().length;
	var remaining = max-current_length;
	$("#shoutout-countdown-remaining").html(remaining);
}

elgg.shoutout.handlePost = function() {
	var attachments = [];
	$.each($('.qq-upload-list').children().filter(":visible"), function() {
		var c = $(this).children();
		if (c.filter('.qq-upload-failed-text').is(':hidden')) {
			attachments.push({timeBit: c.filter('.qq-upload-dir').html(), fileName: c.filter('.qq-upload-file').html()});
		}
	});
	var content = {attachments: attachments, text: $("[name='shoutout_text']").val(), guid: $('#shoutout-guid').val()};
	elgg.action('action/shoutout/edit', {data: content, success : 
			function (response) {
				if (response.success) {
					var guid = $('#shoutout-guid').val();
					if (guid > 0) {
						elgg.forward('shoutout/all');
					} else {

						// reload activity view
						$('#shoutout-content-area').load(elgg.get_site_url()+'shoutout/activity_river_view');
						
						// reset form
						$('[name="shoutout_text"]').val('');
						$("#shoutout-countdown-remaining").html('500');
						$('.qq-upload-list').children().remove();
						$('.shoutout-number-of-attachments').val(0);
						elgg.shoutout.resetFileUploader();

						// show success message					
						elgg.system_message(response.msg);
					}
				} else {
					elgg.register_error(response.msg);
				}
			}
	});
}

elgg.register_hook_handler('init', 'system', elgg.shoutout.init);
//</script>
