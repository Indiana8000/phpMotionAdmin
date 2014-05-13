<?php require_once('core.php');

if($argc>2) {
	// On Event Start increase internal EventID
	if($argv[2]=="EVENT START") {
		$GLOBALS['DB']->query("UPDATE threats SET tEvent = tEvent + 1 WHERE tID = " . $argv[1]);
	}

	// Get internal EventID (Motion EventIDs start from 1 after every restart!)
	$stmt = $GLOBALS['DB']->query("SELECT tEvent, tNotification FROM threats WHERE tID = " . $argv[1]);
	foreach($stmt as $row) {
		$eNum = $row['tEvent'];
		$tNotification = $row['tNotification'];
	}
	
	if($argc<4) $argv[3] = "";
	if($argc<5) $argv[4] = "";
	
	// Insert Event.
	$GLOBALS['DB']->query("INSERT INTO events (eCam,eType,eNum, eFile, eData) VALUES (".$argv[1].",'".$argv[2]."',$eNum,'".$argv[3]."','".$argv[4]."')");
	
	// Limit Notifications to EVENT START/END to save performance.
	// With Plugin's which need PICTURE Events this should be extended.
	if($argv[2]=="EVENT START" || $argv[2]=="EVENT END") {
		// Notify!?
		if($tNotification == 1 || ( $tNotification == 2 && $GLOBALS['CONFIG']['MasterSwitch'] == "ON" )) {
			// Load Plugins
			require_once('core_plugins.php');
			
			$stmt = $GLOBALS['DB']->query("SELECT nType, nX, nP FROM notify WHERE nCam = " . $argv[1]);
			foreach($stmt as $row) {
				if($GLOBALS['PLUGINS'][$row['nType']][0] == $argv[2]) {
					//$row['nType']($eNum,$row['nX'],$row['nP']);
					call_user_func($row['nType'], $argv[1], $eNum, $row['nX'], $row['nP']);
				}
			}
		}
	}
}

?>