<?php

function MergeArrays($Arr1, $Arr2) {
	foreach($Arr2 as $key => $Value) {
		if (array_key_exists($key, $Arr1) && is_array($Value)) {
			$Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);
		} else {
			else $Arr1[$key] = $Value;
		}
	}
	return $Arr1;
}

