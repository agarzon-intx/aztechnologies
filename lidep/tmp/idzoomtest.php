<?php session_start(); ?><!DOCTYPE html>
<html><head><meta charset="utf-8">
<link href="../assets/css/material-dashboard.css" rel="stylesheet">
<script src="../assets/js/core/jquery.min.js"></script>
<script>var MSG_AJAX_GENERIC = 'err'; function mainLoadingOn(){} function mainLoadingOff(){}</script>
<script src="../javascript/main.js.php?v=3"></script>
</head><body style="padding:20px">
<div id="myIDBackground2E">
<div id="id11FrameE" class="player-photo-frame" data-out-w="700" data-out-h="438" data-upload="objects/UploadIDFE.php" data-field="myID11E" data-target="#myID11FileNameE" data-preview="#previewMyID11E" data-tools="#id11ToolsE" data-zoom="#id11ZoomE" style="width: 150px; height: 94px; background-color: #BFBFBF; margin: auto; position: relative; overflow: hidden;">
	<img id="identificacion11E" src="" width="150" height="94" style="display: none"/>
</div>
<div id="id11ToolsE" style="display: none; width: 150px; margin: 6px auto 0;">
	<div class="d-flex align-items-center" style="gap: 4px;">
		<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" onclick="playerPhotoStep('#id11FrameE', 1 / 1.15);">&minus;</button>
		<input type="range" class="form-range" id="id11ZoomE" min="0" max="100" step="1" value="0" oninput="playerPhotoSlider('#id11FrameE', this.value);" style="flex: 1;">
		<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" id="plusBtn" onclick="playerPhotoStep('#id11FrameE', 1.15);">+</button>
		<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" onclick="playerPhotoReset('#id11FrameE');">&#8634;</button>
	</div>
</div>
<input type="file" accept="image/png,image/jpeg" id="myID11E" onchange="readIDURL11E(this, 'identificacion11E');">
<input type="hidden" id="myID11FileNameE" value="">
<div id="previewMyID11E"></div>
</div>
</body></html>
