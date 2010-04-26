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

FILE INFO: estimate-question.php
$LastChangedDate: 2010-04-15 16:43:28 +0800 (Thu, 15 Apr 2010) $
$Revision: 30 $
$Author: youknowjack@gmail.com $
*/
ob_start();
require 'components/db.php';
require 'question/Question.php';
require 'components/utility.php';
require 'components/inputform.php';


if (($rs_estimate = validateEstimateCode($_REQUEST, "estimate")) && ($allquestions = validateQuestionCode($_REQUEST))) {
    
    $row_estimate = mysql_fetch_assoc($rs_estimate);   
    $latestversion = $row_estimate["LastIteration"] +1;
    $estimatecode = $row_estimate["AccessCode"];
    
    $header_title = "Question response (estimate " . $estimatecode . ")";
    $header_extra = '<link rel="stylesheet" href="static/forms.css" type="text/css" />';
    $template_breadcrumbs = getBreadcrumbs('estimate-question.php', array("estimate" => $_GET["estimate"])); //note: using the other estimate question page here is OK
    
    
    $masterq = $allquestions[$_REQUEST["question"]];
    
    $form = new InputForm(-1, "POST", sprintf("estimate-question.php?estimate=%s&question=%s", $estimatecode, $_REQUEST["question"]));

    $inputs = array();
    foreach($allquestions as $q) {
        
        if($q->displaywith != $masterq->code) { //only add slave questions
            continue;
        }
        
	    $name = $q->code;    
	    $column = $q->code;
	    $label = $q->name;
	    $validate = $q->regex;
	    $default = $q->getLatestValue()    ;
	    $min = $q->min;
	    $max = $q->max;
	    $minlen = $q->minlen;
	    $maxlen = $q->maxlen;
	    
        $input = Input::getInput($q->questiontemplate, $q->questiontemplateparameters, $name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, false);          	       
	    $input->setHelp($q->shorthelp);
	    $input->setLongHelp($q->longhelp);
	    $input->setTemplate('compact.php');
	    $inputs[$q->code] = $input;
    }
    
    foreach($inputs as $input) { //recycling the $input var
        $form->addInput($input);
    }
    
    $form->setButtons(array(array("savereturn","Save &amp; return"))); //removed save and next button: , array("savenext", "Save &amp; next")
    
    
    $whichbutton = $form->setRequest($_REQUEST);
    
    if($form->isResult()) {
        if($form->isValid()) {
            foreach($inputs as $inputkey => $input) { 
	            $allquestions[$inputkey]->setResponse($latestversion, $input->getValue());
	            $allquestions[$inputkey]->committResponse($latestversion, $estimatecode);
            }
            $success = urlencode(sprintf("Updated %s successfully!", $masterq->name));
            if($whichbutton == "savereturn") {
                header(sprintf("Location: estimate.php?estimate=%s&success=%s",$estimatecode,$success));
                exit;
            } /*elseif($whichbutton == "savenext") {
                //update Q so the lock calculation will be correct:
                Question::$Q[$_REQUEST["question"]]=$q->getLatestValue();
                
                //figure out which question is next
                $next = false;
                $nextq;
                foreach($allquestions as $ak => $aq) {                    
                    if($next) {
                        $nextq = $aq; 
                        break;
                    }
                    if(strtoupper($ak) == strtoupper($_REQUEST['question'])) {
                        $next = true;
                    }
                }
                                
                //test its condition
                if(isset($nextq) && $nextq->canAnswer()) {
                    $location = sprintf("estimate-question.php?estimate=%s&question=%s", $estimatecode, $nextq->code);
                    $error = "";
                } elseif(!isset($nextq)) {
                    $error = urlencode("Could not go to next question, you just answered the last question.");
                    $location = sprintf("estimate.php?estimate=%s", $estimatecode);
                } elseif(!$nextq->canAnswer()) {
                    $error = urlencode("Could not go to the next question, it seems to be locked. Please pick another question");
                    $location = sprintf("estimate.php?estimate=%s", $estimatecode);
                }
                
                header(sprintf("Location: %s&error=%s&success=%s",$location,$error,$success));
                exit;
            }*/
        } else {
            $template_error = $form->getError();   
        }        
    }
    
    // print form
	$form->printHeader();
	$form->printBody();
	print("<br />");
	//maintain state (changed action instead)
	//printf('<input type="hidden" name="estimate" value="%s"', $_REQUEST['estimate']);
	//printf('<input type="hidden" name="question" value="%s"', $_REQUEST['question']);
	
	//finish printing form
	$form->printFooter();
	printf("<br /><a href=\"estimate.php?estimate=%s\">Discard Changes</a>", $estimatecode);
    
    
} else {
    $header_title = "Estimate Question Error";
    $template_error = "An error occured (perhaps the code is invalid, or the question is locked?) " . mysql_error(); //TODO: Better error here
} 

$template_body = ob_get_clean();
require 'templates/main.php';

?>