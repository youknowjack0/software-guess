<?php
function FPComplexity($type, $comp1, $comp2, $low, $med, $high, $comp1high, $comp1med, $comp2high, $comp2med) {
    $Q =& Question::$Q;
    $C =& Calculation::$C;    
    
	if(isset($Q[$type]) && isset($Q[$comp1]) && isset($Q[$comp2])) 
	{

		$len = intval($Q[$type]);
		
		$total = 0;
		
		for ($i=0; $i<$len; $i++) 
		{
			$det = intval($Q[$comp1]);
			$ret = intval($Q[$comp2]);
			
			if ($det < 1 || $ret < 1 || $len < 1) {
				return 0;
			}
			
			if ($det >= $comp1high)
			{
				if ($ret >= $comp2med) {
					$total = $total + $high;
				} else {
					$total = $total + $med;
				}
			} else {
				if ($det >= $comp1med) {
					if ($ret >= $comp2high) {
						$total = $total + $high;
					} else {
						if ($ret >=$comp2med ) {
							$total = $total + $med;
						} else {
							$total = $total + $low;
						}
					}
				} else {
					if ($ret >= $comp2high) {
						$total = $total + $med;
					} else {
						$total = $total + $low;
					}
					
				}
			}
		}

		return $total;

		
	} else return 0;
}
?>