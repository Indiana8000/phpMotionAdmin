<?php

function urlOfCam($id) {
	$p1 = strrpos($GLOBALS['CONFIG']['StreamURL'], ":") +1;
	$p2 = strrpos($GLOBALS['CONFIG']['StreamURL'], "/");
	$port = intval( substr($GLOBALS['CONFIG']['StreamURL'], $p1, $p2 - $p1) );

	return str_replace($port, $port + intval($id), $GLOBALS['CONFIG']['StreamURL']);
}

function getMotionStatus() {
	ini_set('default_socket_timeout',15); 
	$tmp = @file_get_contents($GLOBALS['CONFIG']['AdminURL']);
	if(strlen($tmp)>0) {
		preg_match("/ (.+\..+\..+) .+\[(.+)\]/",$tmp,$regs);
		if($regs[2]>0) {
			$status['Version'] = $regs[1];
			$status['Cams'] = $regs[2]-1;
			$status['Online'] = "ON";
		} else {
			$status['Version'] = "Offline";
			$status['Cams'] = 1;
			$status['Online'] = "OFF";
		}

	} else {
		$status['Version'] = "Error";
		$status['Cams'] = 0;
		$status['Online'] = "OFF";
	}
	return $status;
}

function getMotionThreat($id) {
	$tmp = file_get_contents($GLOBALS['CONFIG']['AdminURL'] . $id . "/detection/status");
	preg_match("/status (.+)\n/",$tmp,$regs);
	return $regs[1];
}

function setMotionThreat($id,$status) {
	$tmp = file_get_contents($GLOBALS['CONFIG']['AdminURL'] . $id . "/detection/" . $status);
	return true;
}

function checkMotionStatus() {
	$motion_status = getMotionStatus();
	if($motion_status['Online']=='ON') {
		$stmt = $GLOBALS['DB']->prepare("SELECT tID, tStatus, tDetection FROM threats ORDER BY tID");
		if($stmt->execute()) {
			while($row = $stmt->fetch()) {
				if($row['tID'] <= $motion_status['Cams']) {
					$motion_thread_status = getMotionThreat($row['tID']);
					if($motion_thread_status != "ACTIVE" && ($row['tDetection'] == 1 || ($row['tDetection'] == 2 && $GLOBALS['CONFIG']['MasterSwitch'] == "ON"))) {
						setMotionThreat($row['tID'],"start");
						$motion_thread_status = getMotionThreat($row['tID']);
						if($motion_thread_status == "ACTIVE") {
							if($row['tStatus'] != 1)
								$GLOBALS['DB']->query("UPDATE threats SET tStatus = 1 WHERE tID = " . $row['tID']);
						} else {
							if($row['tStatus'] != 0)
								$GLOBALS['DB']->query("UPDATE threats SET tStatus = 0 WHERE tID = " . $row['tID']);
						}
					}
					if($motion_thread_status != "PAUSE" && ($row['tDetection'] == 0 || ($row['tDetection'] == 2 && $GLOBALS['CONFIG']['MasterSwitch'] == "OFF"))) {
						setMotionThreat($row['tID'],"pause");
						$motion_thread_status = getMotionThreat($row['tID']);
						if($motion_thread_status == "PAUSE") {
							if($row['tStatus'] != 2)
								$GLOBALS['DB']->query("UPDATE threats SET tStatus = 2 WHERE tID = " . $row['tID']);
						} else {
							if($row['tStatus'] != 0)
								$GLOBALS['DB']->query("UPDATE threats SET tStatus = 0 WHERE tID = " . $row['tID']);
						}
					}
				}
			}
		}
	}
}


?>