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

FILE INFO: newversion.php
$LastChangedDate: 2010-04-17 18:28:32 +0800 (Sat, 17 Apr 2010) $
$Revision: 37 $
$Author: youknowjack@gmail.com $
*/

require 'components/db.php';
require 'components/utility.php';
require 'question/Question.php';
require 'Calculation/Calculation.php';

ob_start();
$header_title = "Confirm New Version";

if ($result = validateEstimateCode($_GET, "estimate")) {
    $estimate_code = strtoupper($_GET['estimate']);
    if(isset($_GET['cancel'])) {
        header(sprintf("Location: estimate.php?estimate=%s&error=%s",$estimate_code,"New version action was cancelled."));
    } elseif(isset($_GET['confirm'])) {
        // iterate version number
        $est_row = mysql_fetch_assoc($result);
        $current_version = $est_row['LastIteration'] + 1;
        
        $fieldmapping = array(
            "LastIteration" => $current_version
        );
        $sql = buildUpdateQuery($fieldmapping, "AccessCode", $estimate_code);
        mysql_query($sql);        
        
        // grab calculation results        
        Question::getAllQuestions($estimate_code); //called for effect; setting $Q    
	    $Q =& Question::$Q;
	    $allcalcs = Calculation::getAllCalculations();
	    $C =& Calculation::getC($Q, $allcalcs); //this also sets error codes and results for $allcalcs      
       
	    // committ calculation results
        foreach($C as $k => $c) {
            $fieldmapping = array(
	            "CalculationCode" => $k,
                "Data" => serialize($c),
                "EstimateCode" => $estimate_code,
                "Version" => $current_version
	        );            
            $sql = buildInsertQuery2($fieldmapping, "CalculationResults");         
            mysql_query($sql);
        }

        //done, redirect
        header(sprintf("Location: estimatehome.php?estimate=%s&success=%s",$estimate_code,"New version action was successful."));
    } else {
	    
	    printf("<strong>Are you sure you want to create a new version?</strong>");
	    printf("<form action=\"newversion.php\" method=\"GET\">");
	    printf("<input type=\"hidden\" name=\"estimate\" value=\"%s\" />", $estimate_code);
	    printf("<input type=\"submit\" name=\"confirm\" value=\"Confirm\" />");
	    printf("<input type=\"submit\" name=\"cancel\" value=\"Cancel\" />");
	    printf("<br /><span style=\"font-size:small;\">Please only click once.</span>");
    }
} else {
    $template_error = "Error validating estimate code";
}

// Footer file to show some copyright info etc...
$template_body = ob_get_clean();
require 'templates/main.php';

?>