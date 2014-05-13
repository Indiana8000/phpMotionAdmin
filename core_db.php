<?php
	try {
		$GLOBALS['DB'] = new PDO($GLOBALS['CONFIG']['DB_DSN'], $GLOBALS['CONFIG']['DB_USER'], $GLOBALS['CONFIG']['DB_PASSWD']);
		$stmt = $GLOBALS['DB']->prepare("SELECT cName, cValue FROM config");
		if($stmt->execute()) {
			while($row = $stmt->fetch()) {
				$GLOBALS['CONFIG'][$row['cName']] = $row['cValue'];
			}
		}
		if(!isset($GLOBALS['CONFIG']['MasterSwitch']))
			$GLOBALS['CONFIG']['MasterSwitch'] = "OFF";
		if(!isset($GLOBALS['CONFIG']['PathToURL']))
			$GLOBALS['CONFIG']['PathToURL'] = "/var/www/motion/ => http://localhost/motion/";
		if(!isset($GLOBALS['CONFIG']['AdminURL']))
			$GLOBALS['CONFIG']['AdminURL'] = "http://user:password@localhost:8080/";
		if(!isset($GLOBALS['CONFIG']['StreamURL']))
			$GLOBALS['CONFIG']['StreamURL'] = "http://localhost:8080/";
		if(!isset($GLOBALS['CONFIG']['CheckActive']))
			$GLOBALS['CONFIG']['CheckActive'] = "FALSE";
	} catch(PDOException $e) {
		die('Connection failed: ' . $e->getMessage());
	}
?>