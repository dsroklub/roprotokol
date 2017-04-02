<?php 

function csvParse( $input, $delimiter = ';', $textseperator = '"', $escape = false) {
	$res = [];
	$line = [];
	$field = '';
	
	$length = strlen($input);
	$cur;
	$next;
	$inQuote = false;
	$prevDelim = false;
	for ($i = 0; $i < $length; $i++) {
		$cur = $input[$i];
		$next = ($i == $length -1) ? null :$input[$i +1];
		
		if ( $cur == $textseperator ) {
			if ($inQuote) {
				if ($escape === false && $next == $textseperator) {
					// Double quote is escaped quote
					$field .= $cur;
					$i++;
				} else {
					$inQuote = false;
				}
			} else {				
				$inQuote = true;
			}
			$prevDelim = false;
		} elseif ( $escape !== false && $cur == $escape ) {
			$field .= $next;
			$i++;
			$prevDelim = false;
		} elseif ( $cur == $delimiter ) {
			if ($inQuote) {
				$field .= $cur;
				$prevDelim = false;
			} else {
				array_push($line, $field);
				$field = '';
				$prevDelim = true;
			}
		} elseif ( $cur == "\n" ) {
			if ($inQuote) {
				$field .= $cur;
			} else {
				if ($prevDelim || strlen($field) ) {
					array_push($line, $field);
				}
				$field = '';
				array_push($res, $line);
				$line = [];
			}
			if ($next == "\r") {
				$i++;
			}
			$prevDelim = false;
		} else {
			$field .= $cur;
			$prevDelim = false;
		}
	}
	
	if ($inQuote) {
		error_log("Parser error: Unbalanced quotes");
		return false;
	}
	
	if (strlen($field) || $prevDelim ) {
		array_push($line, $field);
	}
	if (count($line)) {
		array_push($res, $line);
	}
	
	
	
	return $res;
}

?>