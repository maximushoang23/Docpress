<?php
function documentor_convert_int_to_roman($integer, $upcase = true) { 
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
    $return = ''; 
    while($integer > 0) 
    { 
        foreach($table as $rom=>$arb) 
        { 
            if($integer >= $arb) 
            { 
                $integer -= $arb; 
				if($upcase==false) {
					$return .= strtolower($rom);
				}
				else{
					$return .= $rom; 
				}
                break; 
            } 
        } 
    } 
    return $return; 
} 
function documentor_convert_int_to_alpha($integer, $upcase = true) { 
    $table = array('','A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $return = ''; 
    if($integer > 0) { 
        if($upcase==false) {
			$return .= strtolower($table[$integer]);
		}
		else{
			$return .= $table[$integer]; 
		}
    } 
    return $return; 
}
?>