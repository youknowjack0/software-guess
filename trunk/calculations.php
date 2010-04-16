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

FILE INFO: calculations.php
$LastChangedDate$
$Revision$
$Author$
*/
ob_start();
require 'components/db.php';
require 'question/Question.php';
require 'components/utility.php';
require 'Calculation/Calculation.php';

if (($rs_estimate = validateEstimateCode($_REQUEST, "estimate"))) {
  
    $header_title = "Calculations";    
    $estimate_code = strtoupper($_REQUEST["estimate"]);
    
    // include tooltip dependencies
    $header_extra = "<link rel=\"stylesheet\" href=\"static/tooltip.css\" type=\"text/css\" />
					 <script language=\"javascript\" src=\"static/tooltip.js\"></script>";
    
    Question::getAllQuestions($estimate_code); //called for effect; setting $Q    
    $Q =& Question::$Q;
    $allcalcs = Calculation::getAllCalculations();
    $C =& Calculation::getC($Q, $allcalcs); //this also sets error codes and results for $allcalcs
    
    $lastgroup = "";

    printf("<table class=\"estimates\">");
    printf("<tr class=\"estimatemainheader\"><th></th><th>Calculation</th><th>Result</th><th></th></tr>");    
    
    $cnum = 1;
    foreach($allcalcs as $k => $c) {
        
        //print group header if required
        if($lastgroup != $c->GroupName) {            
            printf("<tr><th class=\"estimategroupheader\" colspan=\"4\">%s</th></tr>", $c->GroupName);
            $lastgroup = $c->GroupName;
        }
        
        //build error tooltip if needed
        $errorstr = "";
        if($c->error != "" && isset($c->error)) {
            $errorstr = sprintf('<image src="copyrightimages/smallexclaim.png" alt="Error" onmouseover="tooltip.show(\'%s\');" onmouseout="tooltip.hide();" />', preg_replace(array("/\n/","/\r/"),array("\\n",""),str_replace("'","\'", $c->error)));
            
        }
        
        //tooltip to show code being executed
        $infostr = "";
        $infostr = sprintf('<image src="copyrightimages/smallinfo.png" alt="Info" onmouseover="tooltip.show(\'%s\');" onmouseout="tooltip.hide();" />', "<pre>".htmlspecialchars(preg_replace(array("/\n/","/\r/"),array("\\n",""),str_replace("'","\'",$c->PHP)))."</pre>");
        
        printf("<th>%d</th><td>%s (%s)</td><td>%s</td><td>%s%s</td></tr>", $cnum, $c->Name, $k, $c->getHTMLResult(), $infostr, $errorstr);

        
        $cnum++;
    }
    printf("</table>");
    
} else {
    $header_title = "Calculations";
    $template_error = "An error occured (perhaps the estimate code is invalid?) " . mysql_error();
}


$template_body = ob_get_clean();
require 'templates/main.php';
?>