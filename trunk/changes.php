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

FILE INFO: changes.php
$LastChangedDate$
$Revision$
$Author$
*/
ob_start();
require 'components/db.php';
require 'components/utility.php';
require 'question/Question.php';
require 'Calculation/Calculation.php';

if ($rs_estimate = validateEstimateCode($_GET, 'estimate')) {
    $row_estimate = mysql_fetch_assoc($rs_estimate);
    $estimatecode = $row_estimate["AccessCode"];
    $version = $row_estimate["LastIteration"] + 1;
    
    // include tooltip dependencies
    $header_extra = "<link rel=\"stylesheet\" href=\"static/tooltip.css\" type=\"text/css\" />
					 <script language=\"javascript\" src=\"static/tooltip.js\"></script>";
    

    $header_title = "Change History (estimate " . $estimatecode . ")";

    $template_breadcrumbs = getBreadcrumbs('changes.php', array("estimate" => $estimatecode));

    $questions = Question::getAllQuestions($estimatecode, $version);

    $lastgroup = -1;

    printf("<image align=\"bottom\" src=\"copyrightimages/newsmall.png\" /> = new value (mouseover to view)");
    
    // print questions table
    printf("<table class=\"estimates\">");
    printf("<tr class=\"estimatemainheader\"><th></th><th>Question</th>");
    
    for($i=1;$i<=$version;$i++) {        
        printf("<th>%d</th>", $i);        
    }
    
    printf("</tr>");
    
    $qnum=1;
    foreach($questions as $k => $v) {

        //print group header if required
        if($lastgroup != $v->groupid) {

            $sql = sprintf("SELECT * FROM `QuestionGroups` WHERE `id` = %s", $v->groupid);
            $r_group = mysql_query($sql);
            $row_group = mysql_fetch_assoc($r_group);

            printf("<tr><th class=\"estimategroupheader\" colspan=\"%d\">%s</td>",$version + 2, $row_group["GroupName"]);

            $lastgroup = $v->groupid;
        }

        printf("<tr>");
        
        printf("<th>%d</th>", $qnum);
        printf("<td>%s</td>", $v->name);

        $lastval;
        for($i=1;$i<=$version;$i++) {        
            if(isset($v->value[$i])) {
                if (!isset($lastval) || ($v->value[$i] != $lastval)) {
                    printf('<td><image src="copyrightimages/newsmall.png" alt="New Version" onmouseover="tooltip.show(\'%s\');" onmouseout="tooltip.hide();" /></td>', tooltipify($v->getValueHtml($i)));
                } else {
                    print("<td></td>");    
                }
                $lastval = $v->value[$i];
            } else {
                print("<td></td>");
            }                    
        }
        
        printf("</tr>");
        $qnum++;
    }
    printf("</table>");
    
    //print calculations table
    printf("<br /><image align=\"bottom\" src=\"copyrightimages/newsmall.png\" /> = new value (mouseover to view)");
    printf("<table class=\"estimates\">");
    printf("<tr class=\"estimatemainheader\"><th></th><th>Calculation</th><th>Code</th>");
    
    for($i=1;$i<$version;$i++) {        
        printf("<th>%d</th>", $i);        
    }

    printf("</tr>");
    
    $Q =& Question::$Q;
    $allcalcs = Calculation::getAllCalculations();
    $C =& Calculation::getC($Q, $allcalcs); //this also sets error codes and results for $allcalcs
    
    $qnum=1;
    $lastgroup=-1;
    foreach($allcalcs as $k => $v) {

        //print group header if required
        if($lastgroup != $v->GroupID) {

            $sql = sprintf("SELECT * FROM `QuestionGroups` WHERE `id` = %s", $v->GroupID);
            $r_group = mysql_query($sql);
            $row_group = mysql_fetch_assoc($r_group);

            printf("<tr><th class=\"estimategroupheader\" colspan=\"%d\">%s</td>",$version + 2, $row_group["GroupName"]);

            $lastgroup = $v->GroupID;
        }

        printf("<tr>");
        
        printf("<th>%d</th>", $qnum);
        printf("<td>%s</td>", $v->Name);
        printf("<td>%s</td>", $v->Code);

        $lastval = null;
        $sql = sprintf("SELECT * FROM `CalculationResults` WHERE `EstimateCode` = '%s' AND `CalculationCode` = '%s'", $estimatecode, $v->Code);
        $rs_rslts = mysql_query($sql);
        $therow;
        $found;
        for($i=1;$i<$version;$i++) {
            if(mysql_num_rows($rs_rslts) != 0) {
	            mysql_data_seek($rs_rslts, 0); //reset pointer
	            $found=false;            
	            while($row = mysql_fetch_assoc($rs_rslts)) {
	                if($row["Version"] == $i) {
	                    $therow = $row;
	                    $found = true;
	                    break;
	                }
	            }
            } else {
                $found=false;
            }
            if(!$found) {
                print("<td></td>");
            } else {
                $data = unserialize($row["Data"]);
                if ((!isset($lastval) || ($data != $lastval)) && isset($data) && $data != "" ) { //check if has changed since last version
                    printf('<td><image src="copyrightimages/newsmall.png" alt="New Version" onmouseover="tooltip.show(\'%s\');" onmouseout="tooltip.hide();" /></td>', tooltipify($v->getHTMLResult($data)));
                } else {
                    print("<td></td>");    
                }
                $lastval = $data;               
            }
                        
                                
        }
        
        printf("</tr>");
        $qnum++;
    }
    printf("</table>");

} else {
    $header_title = "Change History";
    $template_error = "An error occured (perhaps the Access Code is invalid?) " . mysql_error();
}

$template_body = ob_get_clean();
require 'templates/main.php';
?>