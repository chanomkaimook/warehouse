<?php 
error_reporting(E_ALL & ~E_NOTICE);

	function get_columnExcelNameFromNumber($num) {//(Example 0 = A, 1 = B)
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return get_columnExcelNameFromNumber($num2 - 1) . $letter;
		} else {
			return $letter;
		}
	}
?>