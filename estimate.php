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

FILE INFO: estimate.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
*/
ob_start();
require 'components/db.php';
require 'question/Question.php';
require 'components/utility.php';

if ($result = validateEstimateCode($_GET, "estimate")) {
    
    $row = mysql_fetch_assoc($result);
    $version = $row["LastIteration"]+1;
    $header_title = sprintf("Estimate (%s version %d)", $_GET["estimate"], $version);
    $questions = Question::getAllQuestions(strtoupper($_GET["estimate"]), $version);
    
    $template_breadcrumbs = getBreadcrumbs('estimate.php', array("estimate" => $_GET["estimate"]));
    
    
    //$lastq = $row["LastQuestionAnswered"];
    
    //print a list of questions TODO: images
    $outofdatestr = "out of date";
    $notansweredstr = "No";
    $answeredstr = "Yes";
    $locked = true;
    
    //prepare teh group 
    $lastgroup = -1;
    
    //$currentstr = " [current]"
    printf("<table class=\"estimates\">");
    printf("<tr class=\"estimatemainheader\"><th></th><th>Question</th><th>Answered?</th><th>Out of Date?</th><th>Locked?</th></tr>");
    $i=1;
    foreach($questions as $k => $v) {
        
        //print group header if required
        if($lastgroup != $v->groupid) {
            
            $sql = sprintf("SELECT * FROM `questiongroups` WHERE `id` = %s", $v->groupid);
            $r_group = mysql_query($sql);
            $row_group = mysql_fetch_assoc($r_group);            
            
            printf("<tr><th class=\"estimategroupheader\" colspan=\"5\">%s</td>", $row_group["GroupName"]);
            
            $lastgroup = $v->groupid;
        }
        
        printf("<tr>");
        if(!$v->canAnswer()) {
            $lockedstr = sprintf("locked! condition: <span class=\"code\">%s</span>", $v->conditions);
            $regex = '/(\\$Q\\["|\\$Q\[\')([a-zA-Z0-9_]+)("\]|\'\])/';
            //printf($regex); //debug            
            $lockedstr = preg_replace($regex, sprintf('<a href="estimate-question.php?estimate=%s&question=%s">$2</a>', $_GET["estimate"], $v->code) , $lockedstr); //replace $Q["CODE"]/$Q['CODE'] with a link
            $locked = true;
            //TODO: don't show a link if the target is locked            
        } else {
            $locked = false;
            $lockedstr = "";
        }
        if(!$locked) {
            printf('<td>%d.</td><td><a href="estimate-question.php?estimate=%s&question=%s">%s</a></td><td>%s</td><td>%s</td><td></td>',$i, $_GET["estimate"], $v->code, $v->name, ($v->hasValue()?$answeredstr:$notansweredstr), ($v->isUpToDate($version)?"":$outofdatestr) );
        } else {
            printf('<td>%d.</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>',$i, $v->name, ($v->hasValue()?$answeredstr:$notansweredstr), ($v->isUpToDate($version)?"":$outofdatestr), $lockedstr);
        }
        printf("</tr>");
        $i++;
    }
    printf("</table>");
} else {
    $header_title = "Estimate";
    $template_error = "An error occured (perhaps the Access Code is invalid?) " . mysql_error();
}
    
$template_body = ob_get_clean();
require 'templates/main.php';

?>