<?php
include("inc/common.php");
header('Content-type: application/json');

// include("inc/verify_user.php");

$update_dir = $_SERVER['DOCUMENT_ROOT'] . '/../db_setup/updates';
$error=null;
$res=array("status" => "ok");
$log= [];

$db_version;

$rodb->autocommit(false);
if ($rodb->begin_transaction()) {
	$log[] = "Started transaction";
} else {
	$error = "Could not start transaction: " . $rodb->error;
}

$result=$rodb->query("SELECT * FROM Configuration WHERE id = 'db_version'");

if (!$result) {
	$error = "Could not select db_version: " . $rodb->error;
} else {
	$row = $result->fetch_assoc();
	if ($row) {
		$db_version = 0 + $row['value'];
		$log[] = "Existing db_version = $db_version";
	} else {
		$error = "DB version not found in database";
	}
}

if (is_null($error) && !file_exists($update_dir)) {
	$error = "Update directory '$update_dir' does not exist";	
}

function update_filename($version){
	global $update_dir;
	return $update_dir . "/" . $version . "_to_" . ($version + 1 ) . ".sql";
}

$current_version = $db_version;


if (is_null($error)) {
	$log[] = "Check for file " . update_filename( $current_version);
	while (file_exists(update_filename( $current_version) )) {
		$filename = update_filename( $current_version);
		$log[] = "Found update in file $filename";
		$update = file_get_contents($filename);
		if ($update === false) {
			$error = "Could not read update file '$filename'";
			break;
		}
		$idx = 0;
		$dbres = $rodb->multi_query($update);
		if ($dbres === false) {
			$error = "Update from version $current_version failed: " . $rodb->error;
			$log[] = "Update " . ++$idx . " failed: " . $dbres;
			break;
		} else {
			$log[] = "Update " . ++$idx . " OK: " . $dbres;
		}
		while ($rodb->more_results()) {
			$rodb->next_result();
			if ($rodb->errno) {
				$error = "Update from version $current_version failed: " . $rodb->error;
				$log[] = "Update " . ++$idx . " FAILED";				
				// Note: No break here, because we want to empty the results buffer
			} else {
				$log[] = "Update " . ++$idx . " OK";
			}
		}
		if (! is_null($error)) {
			break;
		}
		$current_version++;
	}

}

if ($current_version > $db_version && is_null($error)) {
	$dbres = $rodb->query("UPDATE Configuration SET VALUE = '" . (int) $current_version . "' WHERE id = 'db_version'");
	if ($dbres == false) {
		$error = 'Could not update version number in database:' . $rodb->error;
	} else {
		$log[] = "Updated database to version $current_version";
	}

}


if (! is_null($error)) {
	$res['status'] = 'error';
	$res['error'] = $error;
	$res['last_good_version'] = $current_version;
	
	// Try to roll back. This probably does not completely clean up, because PHP and MySQL both suck.
	$log[] = "Rolling  back...";	
	if ($rodb->rollback()) {
		$log[] = "Rollback may have succeeded";
	} else {
		$log[] = "Rollback failed :" . $rodb->error;
	}
} else {
	if ($db_version == $current_version) {
		$res['details'] = 'No updates needed';
	} else {
		$res['details'] = 'Updated database';
		$res['updated_from'] = $db_version;
		$res['updated_to'] = $current_version;
	}
	if ($rodb->commit()) {
		$log[] = "Committed changes to database";
	} else {
		$res['error'] = "Could not commit changes: " . $rodb->error;
		$res['status'] = 'error';
	}
}

$res['log'] = $log;
echo json_encode($res,JSON_PRETTY_PRINT);
$rodb->close();


?>
