<?php 
error_reporting(E_ALL & ~E_NOTICE);
	//
	//	array compare data
	//
	function array1Para($array1,$array2){
		$array = array_intersect($array1,$array2);
		$count = count($array);
		
		$data = array(
					"array"		=> array_values($array),
					"num"		=> $count
				);
		
		return $data;
	}
	function array2Para($array1,$array2,$array3){
		$array = array_intersect($array1,$array2,$array3);
		$count = count($array);
		
		$data = array(
					"array"		=> array_values($array),
					"num"		=> $count
				);
		
		return $data;
	}
	#
	#	compare data
	function unique_multidim_array($array, $key) {
		$temp_array = array();
		$i = 0;
		$key_array = array();
	   
		foreach($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}
?>