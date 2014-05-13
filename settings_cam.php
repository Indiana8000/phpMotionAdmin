<?php require_once('core.php'); require_once('core_plugins.php'); ?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>phpMotionAdmin</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-default hidden-xs" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="about.php">phpMotionAdmin</a>
				</div>
				<div>
					<ul class="nav navbar-nav">
						<li><a href=".">Overview</a></li>
						<li><a href="settings.php">Settings</a></li>
						<li class="active"><a href="settings_cam.php">Cams</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="clearfix visible-xs">&nbsp;</div>
		<div class="container col-xs-12 col-sm-8 col-sm-offset-2">
			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Events</th>
						<th>Name</th>
						<th>Detection</th>
						<th>Notification</th>
						<th><button class="btn btn-primary pull-right" id="cam_config_add"><span class="glyphicon glyphicon-plus"></span> Cam</button></th>
					</tr>
				</thead>
				<tbody id="cam_config_list">
				</tbody>
			</table>
			<button class="btn btn-danger pull-right" id="cam_config_remove"><span class="glyphicon glyphicon-remove"></span> Last Cam</button>
			
		</div>
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				var cam_count = 0;
				
				$('#cam_config_remove').click(function() {
					if(cam_count > 0 && confirm('Remove Cam #'+cam_count+'?')) {
						$.ajax({
							url: "ajax.php",
							type: "POST",
							data: {
								action: "config_cam_remove",
								cid: cam_count
							},
							error: function(jqXHR, textStatus, errorThrown) {
								alert(errorThrown);
							},
							success: function(data, textStatus, jqXHR) {
								if(data != 'OK') {
									alert(data);
								} else {
									$('tr[data-cid='+cam_count+']').remove();
									cam_count--;
								}
							}
						});
					}
				});

				$('#cam_config_add').click(function() {
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "config_cam_add",
							cid: cam_count+1
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								alert(data);
							} else {
								createCam('New Cam #'+(cam_count+1), 0, 0, 0);
							}
						}
					});
				});

				$('body').on("click", ".btn_cam_save", function() {
					var cam_id = $(this).closest('tr').attr('data-cid');
					var cam_name = $(this).closest('tr').find('input').val();
					var tmp = $(this).closest('tr').find('select');
					var cam_detect = $(tmp[0]).val();
					var cam_notify = $(tmp[1]).val();
					
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "config_cam_save",
							cid: cam_id,
							cname: cam_name,
							cdetect: cam_detect,
							cnotify: cam_notify
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								alert(data);
							} else {
								alert('Saved!');
							}
						}
					});					
				});

				$('body').on("click", ".btn_notify_add", function() {
					var cam_id = $(this).closest('table').parent().parent().attr('data-cid');
					createNotify(cam_id, 0, '', '', '');
				});

				$('body').on("click", ".btn_notify_save", function() {
					var cam_id = $(this).closest('table').parent().parent().attr('data-cid');
					var notify_id = $(this).closest('tr').attr('data-nid');
					var notify_type = $(this).closest('tr').find('select').val();
					var tmp = $(this).closest('tr').find('input');
					var notify_param = $(tmp[0]).val();
					var notify_x = $(tmp[1]).val();
					$this = $(this);
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "config_notify_save",
							cid: cam_id,
							nid: notify_id,
							ntype: notify_type,
							nparam: notify_param,
							nx: notify_x
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								if(parseInt(data) > 0) {
								$(this).find('span').removeClass('glyphicon-star').addClass('glyphicon-saved');
									alert('Created! #' + parseInt(data));
								} else {
									alert(data);
								}
							} else {
								alert('Saved!');
							}
						}
					});
				});

				$('body').on("click", ".btn_notify_remove", function() {
					var notify_id = $(this).closest('tr').attr('data-nid');
					if(notify_id > 0) {
						$this = $(this).closest('tr');
						if(confirm('Remove Notification?'))
						$.ajax({
							url: "ajax.php",
							type: "POST",
							data: {
								action: "config_notify_remove",
								nid: notify_id
							},
							error: function(jqXHR, textStatus, errorThrown) {
								alert(errorThrown);
							},
							success: function(data, textStatus, jqXHR) {
								if(data != 'OK') {
									alert(data);
								} else {
									$this.remove();
								}
							}
						});
					} else {
						$(this).closest('tr').remove();
					}
				});

function createSelect(arr, opt) {
	var html = '<select class="form-control">';
	for(var key in arr) {
		if(key == opt) html += '<option value="'+key+'" selected>'+arr[key]+'</option>';
		else html += '<option value="'+key+'">'+arr[key]+'</option>';
	}
	html += '</select>';
	return html;
}

function createCam(cam_name, cam_event, cam_detect, cam_notifiy) {
	cam_count++;
	var html = '<tr data-cid="'+cam_count+'">';
	html += '<td class="text-right"><b>'+cam_count+'.</b></td>';
	html += '<td class="text-right">'+cam_event+'</td>';
	html += '<td><input type="text" class="form-control" value="'+cam_name+'" /></td>';
	html += '<td class="text-center">'+createSelect(Array('OFF', 'ON', 'MasterSwitch'), cam_detect)+'</td>';
	html += '<td class="text-center">'+createSelect(Array('OFF', 'ON', 'MasterSwitch'), cam_notifiy)+'</td>';
	html += '<td><button class="btn btn-default pull-right btn_cam_save"><span class="glyphicon glyphicon-save text-success"></span></button></td>';
	html += '</tr>';
	
	html += '<tr data-cid="'+cam_count+'">';
	html += '<td>&nbsp;</td><td colspan="5">';
		html += '<table class="table table-condensed">';
		html += '<thead><tr><th>Type</th><th>Parameter</th><th>Count</th>';
		html += '<th><button class="btn btn-default pull-right btn_notify_add"><span class="glyphicon glyphicon-plus text-primary"></span> Notify</button></th>';
		html += '</tr></thead><tbody id="nt'+cam_count+'"></tbody></table>';
	html += '</td></tr>';
	$('#cam_config_list').append(html);
}

var plugins = Array();
function createNotify(cam_id, notify_id, notify_type, notify_param, notify_x) {
	var html = '<tr data-nid="'+notify_id+'">';
	html += '<td>'+createSelect(plugins, notify_type)+'</td>';
	html += '<td><input type="text" class="form-control" value="'+notify_param+'" /></td>';
	html += '<td class="col-xs-1"><input type="text" class="form-control" value="'+notify_x+'" /></td>';
	html += '<td><div class="btn-group pull-right"><button class="btn btn-default btn_notify_remove"><span class="glyphicon glyphicon-remove text-danger"></span></button><button class="btn btn-default btn_notify_save"><span class="glyphicon glyphicon-save text-success"></span></button></div></td>';
	html += '</tr>';
	$('#nt'+cam_id).append(html);
}

<?php
	foreach($GLOBALS['PLUGINS'] as $key => $value) {
		echo 'plugins["'.$key.'"] = "'.$value[1].'";';
	}

	$stmt = $GLOBALS['DB']->prepare("SELECT tID, tName, tEvent, tDetection, tNotification FROM threats ORDER BY tID");
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			echo 'createCam("'.$row['tName'].'", '.$row['tEvent'].', '.$row['tDetection'].', '.$row['tNotification'].');';
		}
	}
	
	$stmt = $GLOBALS['DB']->prepare("SELECT nID, nCam, nType, nX, nP FROM notify");
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			echo 'createNotify('.$row['nCam'].', '.$row['nID'].', "'.$row['nType'].'", "'.$row['nP'].'", "'.$row['nX'].'");';
		}
	}
?>
			});
		</script>
	</body>
</html>