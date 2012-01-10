.qq-uploader { 
	position:relative; width: 100%;
}

/*.qq-upload-button {
    display:block; /* or inline-block */
    width: 105px; padding: 7px 0; text-align:center;    
    background:#880000; border-bottom:1px solid #ddd;color:#fff;
}*/

.qq-upload-drop-area {
    position:absolute; top:0; left:0; width:100%; height:100%; min-height: 70px; z-index:2;
    background:#FF9797; text-align:center; 
}
.qq-upload-drop-area span {
    display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px;
}
.qq-upload-drop-area-active {background:#FF7171;}

.qq-upload-list {margin:15px 35px; padding:0; list-style:disc;}
.qq-upload-list li { margin:0; padding:0; line-height:15px; font-size:12px;}
.qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text {
    margin-right: 7px;
}

.qq-upload-file { margin-left: 10px; }
.qq-upload-spinner {display:inline-block; background: url("<?php echo $vars['url']; ?>mod/shoutout/images/loading.gif"); width:15px; height:15px; vertical-align:text-bottom;}
.qq-upload-size,.qq-upload-cancel {font-size:11px;}

.qq-upload-failed-text {display:none;}
.qq-upload-fail .qq-upload-failed-text {display:inline;}

#shoutout-form {
	clear: both;
	margin: 10px;
}

.shoutout-countdown {
	width: 400px;
	height: 100px;
}

#shoutout-bottom-wrapper {
	width: 400px;
	padding-bottom: 10px;
}

#shoutout-countdown-wrapper {
	width: 300px;
	float: left;
}

#shoutout-post-wrapper {
	width: 50px;
	float: right;
}

.shoutout-attachment-listing {
	margin-top: 5px;
}

.shoutout-attachment-listing-item {
	padding-left: 20px;
	padding-bottom: 0;
	padding-top: 0;
	margin-bottom: 0;
	margin-top: 0;
}

.shoutout-attachment-image {
	padding-bottom: 0;
	padding-top: 0;
	margin-bottom: 0;
	margin-top: 0;
}