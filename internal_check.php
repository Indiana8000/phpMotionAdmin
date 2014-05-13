<?php require_once('core.php'); require_once('core_motion.php'); 

checkMotionStatus();

// if CLI remove files
if(isset($argv) && $GLOBALS['CONFIG']['CheckActive'] == "FALSE") {
	$stmt = $GLOBALS['DB']->prepare("SELECT eCam, eNum FROM events WHERE eType = 'EVENT START' AND eData = 'remove'");
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			if($GLOBALS['CONFIG']['CheckActive'] == "FALSE") {
				$GLOBALS['DB']->query("REPLACE INTO config (cName,cValue) VALUES ('CheckActive', 'TRUE')");
				$GLOBALS['CONFIG']['CheckActive'] = "TRUE";
			}
			$stmt2 = $GLOBALS['DB']->prepare("SELECT eFile FROM events WHERE eCam = :cid AND eNUm = :eid AND (eType = 'PICTURE' OR eType = 'MOVIE END')");
			$stmt2->bindValue(':cid', $row['eCam'], PDO::PARAM_INT);
			$stmt2->bindValue(':eid', $row['eNum'], PDO::PARAM_INT);
			if($stmt2->execute()) {
				while($row2 = $stmt2->fetch()) {
					@unlink($row2['eFile']);
				}
			}
			$stmt2 = $GLOBALS['DB']->prepare("DELETE FROM events WHERE eCam = :cid AND eNUm = :eid");
			$stmt2->bindValue(':cid', $row['eCam'], PDO::PARAM_INT);
			$stmt2->bindValue(':eid', $row['eNum'], PDO::PARAM_INT);
			$stmt2->execute();
		}
		if($GLOBALS['CONFIG']['CheckActive'] == "TRUE")
			$GLOBALS['DB']->query("REPLACE INTO config (cName,cValue) VALUES ('CheckActive', 'FALSE')");
	}
}

?>