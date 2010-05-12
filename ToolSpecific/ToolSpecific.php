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

function combinedMean($means, $stdevs) {
    $numerator = 0;
    $denominator = 0;
    for($i=0;$i<count($means);$i++) {
        if(abs($stdevs[$i]) > 0.01) {
            $x = $stdevs[$i];
        } else {
            $x = 1;
        }
        $numerator += $means[$i] / $x;
        $denominator += 1 / $x;
    }
    if($denominator==0) {
        return $numerator;
    } else {
        return $numerator / $denominator;
    }
}

function combinedStDev($stdevs) {
    $sum = 0;
    for($i=0;$i<count($stdevs);$i++) {
        if(abs($stdevs[$i]) > 0.01) {
            $sum += 1 / ($stdevs[$i] ^ 2);
        } 
    }    
    if($sum==0) {
        return 0;
    } else {
        return (1 / (sqrt($sum)));
    }    
}
?>