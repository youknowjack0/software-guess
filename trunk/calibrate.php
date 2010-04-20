<?php /*
Copyright (c) 2010 Jack Langman, Daniel Fozdar, Nelson Yiap, Zhihua Guo,
Vivek Koul & Aaron Taylor
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.
* Neither the name(s) of the authors nor the names of its contributors
may be used to endorse or promote products derived from this software
without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

FILE INFO: calibrate.php
$LastChangedDate$
$Revision$
$Author$

*/

// TODO: set up user level calibration, multiple org calibration, etc...
// probably wont happen....

// buffer output
ob_start();

require 'components/utility.php';
require 'components/db.php';
require 'Calculation/Calculation.php';
require 'question/Question.php';

$header_title = "Calibration";
$template_breadcrumbs = getBreadcrumbs('calibration.php', array());

// loop all calibration pairs

$sql = "SELECT * FROM CalibrationPairs";
$rs_pairs = mysql_query($sql);

while($rowp = mysql_fetch_assoc($rs_pairs)) { // iterate over pairs
    $c1code = $rowp["Calc1"];
    $c2code = $rowp["Calc2"];
    
    $allcalcs = Calculation::getAllCalculations();    
    
    $arr1 = $allcalcs[$c1code]->getAllResults();
    $arr2 = $allcalcs[$c2code]->getAllResults();
    
    $result = array();
    
    foreach($arr1 as $k => $v1) {
        if(isset($arr2[$k])) {
            $v2 = $arr2[$k];          
            print($v1 . " - " . $v2);
        }
    }
    
}

// Footer file to show some copyright info etc...
$template_body = ob_get_clean();
require 'templates/main.php';
?>