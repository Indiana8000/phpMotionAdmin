<?php require_once('core.php');

$feedback = "Unknown Querry! \n" . json_encode($_POST);

if(isset($_POST['action'])) {

	// General Settings
	if($_POST['action'] == 'config_save' && isset($_POST['motionadminurl']) && isset($_POST['motionstreamurl']) && isset($_POST['path2url'])) {
		$stmt = $GLOBALS['DB']->prepare("REPLACE INTO config (cName,cValue) VALUES (:key, :value)");
		$stmt->bindValue(':key', 'AdminURL', PDO::PARAM_STR);
		$stmt->bindValue(':value', $_POST['motionadminurl'], PDO::PARAM_STR);
		$stmt->execute();
		$stmt->bindValue(':key', 'StreamURL', PDO::PARAM_STR);
		$stmt->bindValue(':value', $_POST['motionstreamurl'], PDO::PARAM_STR);
		$stmt->execute();
		$stmt->bindValue(':key', 'PathToURL', PDO::PARAM_STR);
		$stmt->bindValue(':value', $_POST['path2url'], PDO::PARAM_STR);
		$stmt->execute();
		$feedback = 'OK';
	}

	// Cam Settings
	if($_POST['action'] == 'config_cam_add' && isset($_POST['cid'])) {
		$stmt = $GLOBALS['DB']->prepare("INSERT INTO threats (tID, tName, tEvent, tDetection, tNotification) VALUES (:cid, :name, 0, 0, 0)");
		$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
		$stmt->bindValue(':name', 'New Cam #' . intval($_POST['cid']), PDO::PARAM_STR);
		if($stmt->execute() && $stmt->rowCount() == 1) {
			$feedback = 'OK';
		} else {
			$feedback = 'SQL Error';
		}
	}

	if($_POST['action'] == 'config_cam_remove' && isset($_POST['cid'])) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM threats WHERE tID = :cid");
		$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
		if($stmt->execute() && $stmt->rowCount() == 1) {
			$stmt = $GLOBALS['DB']->prepare("DELETE FROM notify WHERE nCam = :cid");
			$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt = $GLOBALS['DB']->prepare("UPDATE events SET eData = 'remove' WHERE eCam = :cid AND eType = 'EVENT START'");
			$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
			$stmt->execute();
			$feedback = 'OK';
		} else {
			$feedback = 'SQL Error';
		}
	}

	if($_POST['action'] == 'config_cam_save' && isset($_POST['cid']) && isset($_POST['cname']) && isset($_POST['cdetect']) && isset($_POST['cnotify'])) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE threats SET tName = :cname, tDetection = :cdetect, tNotification = :cnotify WHERE tID = :cid");
		$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
		$stmt->bindValue(':cname', $_POST['cname'], PDO::PARAM_STR);
		$stmt->bindValue(':cdetect', $_POST['cdetect'], PDO::PARAM_INT);
		$stmt->bindValue(':cnotify', $_POST['cnotify'], PDO::PARAM_INT);
		if($stmt->execute()) {
			if($stmt->rowCount() == 1) {
				$feedback = 'OK';
			} else {
				$feedback = 'Nothing Changed!';
			}
		} else {
			$feedback = 'SQL Error';
		}
	}

	// Notification Settings
	if($_POST['action'] == 'config_notify_remove' && isset($_POST['nid'])) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM notify WHERE nID = :nid");
		$stmt->bindValue(':nid', $_POST['nid'], PDO::PARAM_INT);
		if($stmt->execute() && $stmt->rowCount() == 1) {
			$feedback = 'OK';
		} else {
			$feedback = 'SQL Error';
		}
	}
	
	if($_POST['action'] == 'config_notify_save' && isset($_POST['nid']) && isset($_POST['ntype']) && isset($_POST['nparam']) && isset($_POST['nx'])) {
		if($_POST['nid'] == 0) {
			$stmt = $GLOBALS['DB']->prepare("INSERT INTO notify (nCam, nType, nX, nP) VALUES ( :cid , :ntype , :nx , :nparam )");
			$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
			$stmt->bindValue(':ntype', $_POST['ntype'], PDO::PARAM_STR);
			$stmt->bindValue(':nx', $_POST['nx'], PDO::PARAM_INT);
			$stmt->bindValue(':nparam', $_POST['nparam'], PDO::PARAM_STR);
			if($stmt->execute()) {
				$feedback = $GLOBALS['DB']->lastInsertId(); 
			} else {
				$feedback = 'SQL Error!';
			}
		} else {
			$stmt = $GLOBALS['DB']->prepare("UPDATE notify SET ntype = :ntype , nX = :nx , nP = :nparam WHERE nID = :nid");
			$stmt->bindValue(':ntype', $_POST['ntype'], PDO::PARAM_STR);
			$stmt->bindValue(':nx', $_POST['nx'], PDO::PARAM_INT);
			$stmt->bindValue(':nparam', $_POST['nparam'], PDO::PARAM_STR);
			$stmt->bindValue(':nid', $_POST['nid'], PDO::PARAM_INT);
			if($stmt->execute()) {
				if($stmt->rowCount() == 1) {
					$feedback = 'OK';
				} else {
					$feedback = 'Nothing Changed!';
				}
			} else {
				$feedback = 'SQL Error!';
			}
		}
	}

	// Event Actions
	if($_POST['action'] == 'cam_event_remove' && isset($_POST['cid']) && isset($_POST['eid'])) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE events SET eData = 'remove' WHERE eCam = :cid AND eNum = :eid AND eType = 'EVENT START'");
		$stmt->bindValue(':cid', $_POST['cid'], PDO::PARAM_INT);
		$stmt->bindValue(':eid', $_POST['eid'], PDO::PARAM_INT);
		if($stmt->execute() && $stmt->rowCount() == 1) {
			$feedback = 'OK';
		} else {
			$feedback = 'SQL Error';
		}
	}
	
	// Master Switch
	if($_POST['action'] == 'masterswitch' && isset($_POST['status'])) {
		$stmt = $GLOBALS['DB']->prepare("REPLACE INTO config (cName,cValue) VALUES (:key, :value)");
		$stmt->bindValue(':key', 'MasterSwitch', PDO::PARAM_STR);
		$stmt->bindValue(':value', $_POST['status'], PDO::PARAM_STR);

		if($stmt->execute()) {
			$feedback = 'OK';
			// Log activity to events
			$stmt = $GLOBALS['DB']->prepare("INSERT INTO events (eCam,eType,eNum, eFile, eData) VALUES (0,'MASTER SWITCH',0,'',:value)");
			$stmt->bindValue(':value', $_POST['status'], PDO::PARAM_STR);
			$stmt->execute();
		} else {
			$feedback = 'SQL Error';
		}
	}
	
} // END - if(isset($_POST['action']))

echo $feedback;

?>