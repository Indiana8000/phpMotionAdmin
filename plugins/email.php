<?php

// Register all available Plugin-Functions
$GLOBALS['PLUGINS']['plugin_email_onstart'] = Array("EVENT START","Email: On Event Start");
$GLOBALS['PLUGINS']['plugin_email_randpic'] = Array("EVENT END"  ,"Email: Random Pictures (Count)");
$GLOBALS['PLUGINS']['plugin_email_bestpic'] = Array("EVENT END"  ,"Email: Picture with most changes");
$GLOBALS['PLUGINS']['plugin_email_movie']   = Array("EVENT END"  ,"Email: Movie");

function plugin_email_onstart($eCam,$eNum,$nX,$nP) {
	// Send Email
	$receiver = explode(";",$nP);
	for($i=0;$i<count($receiver);$i++) {
		$receiver[$i] = trim($receiver[$i]);
		if(filter_var($receiver[$i], FILTER_VALIDATE_EMAIL)) {
			plugin_email_sendmail(plugin_email_subject($eCam,$eNum), plugin_email_body($eCam,$eNum,"ON START"), $receiver[$i], null);
		}
	}
	return true;
}

function plugin_email_randpic($eCam,$eNum,$nX,$nP) {
	// Check Settings
	if(!($nX>0 && $nX<20)) $nX = 3;

	// Create File List
	$files = Array();
	$stmt = $GLOBALS['DB']->prepare("SELECT eFile FROM events WHERE eCam = :cid AND eNum = :eid AND eType = 'PICTURE' AND eData != '0 0 0 0'");
	$stmt->bindValue(':cid', $eCam, PDO::PARAM_INT);
	$stmt->bindValue(':eid', $eNum, PDO::PARAM_INT);
	if($stmt->execute()) {
		$step = floor($stmt->rowCount() / $nX); $i=0;
		if($step < 1) $step = 1;
		while($row = $stmt->fetch()) {
			if(++$i % $step == 0)
				$files[count($files)] = $row['eFile'];
		}

		// Send Email with File
		$receiver = explode(";",$nP);
		for($i=0;$i<count($receiver);$i++) {
			$receiver[$i] = trim($receiver[$i]);
			if(filter_var($receiver[$i], FILTER_VALIDATE_EMAIL)) {
				plugin_email_sendmail(plugin_email_subject($eCam,$eNum), plugin_email_body($eCam,$eNum,"RANDOM PICTURES"), $receiver[$i], $files);
			}
		}
	}
	return true;
}

function plugin_email_bestpic($eCam,$eNum,$nX,$nP) {
	// Create File List
	$bestFile = "";
	$bestSize = 0;
	$stmt = $GLOBALS['DB']->prepare("SELECT eFile, eData FROM events WHERE eCam = :cid AND eNum = :eid AND eType = 'PICTURE' AND eData != '0 0 0 0'");
	$stmt->bindValue(':cid', $eCam, PDO::PARAM_INT);
	$stmt->bindValue(':eid', $eNum, PDO::PARAM_INT);
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			$tmp = explode(" ",$row['eData']);
			if($tmp[0]*$tmp[1]>$bestSize) {
				$bestSize = $tmp[0] * $tmp[1];
				$bestFile = $row['eFile'];
			}
		}
		$files = Array();
		if(is_file($bestFile)) {
			$files[count($files)] = $bestFile;

			// Send Email with File
			$receiver = explode(";",$nP);
			for($i=0;$i<count($receiver);$i++) {
				$receiver[$i] = trim($receiver[$i]);
				if(filter_var($receiver[$i], FILTER_VALIDATE_EMAIL)) {
					plugin_email_sendmail(plugin_email_subject($eCam,$eNum), plugin_email_body($eCam,$eNum,"BEST PICTURE"), $receiver[$i], $files);
				}
			}
		}
	}
	return true;
}

function plugin_email_movie($eCam,$eNum,$nX,$nP) {
	// Create File List
	$files = Array();
	$stmt = $GLOBALS['DB']->prepare("SELECT eFile FROM events WHERE eCam = :cid AND eNum = :eid AND eType = 'MOVIE END'");
	$stmt->bindValue(':cid', $eCam, PDO::PARAM_INT);
	$stmt->bindValue(':eid', $eNum, PDO::PARAM_INT);
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			$files[count($files)] = $row['eFile'];
		}

		// Send Email with File
		$receiver = explode(";",$nP);
		for($i=0;$i<count($receiver);$i++) {
			$receiver[$i] = trim($receiver[$i]);
			if(filter_var($receiver[$i], FILTER_VALIDATE_EMAIL)) {
				plugin_email_sendmail(plugin_email_subject($eCam,$eNum), plugin_email_body($eCam,$eNum,"MOVIE"), $receiver[$i], $files);
			}
		}
	}
	return true;
}

// Private Functions
function plugin_email_subject($eCam,$eNum) {
	return "[Motion] Cam ".$eCam." #" . $eNum;
}

function plugin_email_body($eCam,$eNum,$eEvent) {
	return "Event: " . $eEvent . "\nCam: " . $eCam . "\nNumber: " . $eNum;
}

function plugin_email_sendmail($subject,$message,$to,$files) {
        $boundary = strtoupper(md5(uniqid(time())));
        $mail_header  = "From: " . $GLOBALS['CONFIG']['plugin_email_sender'];
        $mail_header .= "\r\nMIME-Version: 1.0";
        $mail_header .= "\r\nContent-Type: multipart/mixed; boundary=$boundary";
        $mail_body  = "--" . $boundary; 
        $mail_body .= "\r\nContent-Type: text/plain";
        $mail_body .= "\r\nContent-Transfer-Encoding: 8bit";
        $mail_body .= "\r\n\r\n" . $message;

        if($files) for($x=0;$x<count($files);$x++) {
                $file_name    = basename($files[$x]);
                $file_content = fread(fopen($files[$x],"r"),filesize($files[$x]));
                $file_content = chunk_split(base64_encode($file_content));
                $mail_body   .= "\r\n--" . $boundary;
                $mail_body   .= "\r\nContent-Type: application/octetstream; name=\"" . $file_name . "\"";
                $mail_body   .= "\r\nContent-Transfer-Encoding: base64";
                $mail_body   .= "\r\nContent-Disposition: attachment; filename=\"" . $file_name . "\"";
                $mail_body   .= "\r\n\r\n" . $file_content;
        }
        
        $mail_body .= "\r\n--" . $boundary . "--";
        mail($to,$subject,$mail_body,$mail_header);
}


?>