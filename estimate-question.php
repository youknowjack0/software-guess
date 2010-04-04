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
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
*/
ob_start();
require 'components/db.php';
require 'question/Question.php';
require 'components/utility.php';
require 'components/inputform.php';


if ($rs_estimate = validateEstimateCode($_GET, "estimate") && $rs_question = validateQuestionCode($_GET)) {
    $header_title = "Question response (estimate " . $_GET["estimate"] . ")";
    $header_extra = '<link rel="stylesheet" href="static/forms.css" type="text/css" />';
    
    $allquestions = Question::getAllQuestions($_GET["estimate"]);
    $q = $allquestions[$_GET["question"]];
    
    $form = new InputForm(-1, "GET", "estimate-question.php");
        
    $name = "megainput";    
    $column = "megainput";
    $label = $q->name;
    $validate = $q->regex;
    $default = $q->getLatestValue()    ;
    $min = $q->min;
    $max = $q->max;
    $minlen = $q->minlen;
    $maxlen = $q->maxlen;
    
    if($q->questiontemplate=="SimpleText") { //TODO: is it possible to replace this with something more OO?
        $input = new InputText($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, false);
    }          
    $input->setHelp($q->shorthelp);
    
    $form->addInput($input);
    
    $form->setButtons(array(array("savereturn","Save &amp; return"), array("savenext", "Save &amp; next")));
    
    $whichbutton = $form->setRequest($_REQUEST);
    
    if($form->isResult()) {
        if($form->isValid()) {
            print($whichbutton);
        } else {
            $template_error = $form->getError();   
        }        
    }
    
    // print form
	$form->printHeader();
	$form->printBody();
	print("<br />");
	//maintain state
	printf('<input type="hidden" name="estimate" value="%s"', $_GET['estimate']);
	printf('<input type="hidden" name="question" value="%s"', $_GET['question']);
	
	//finish printing form
	$form->printFooter();
    
    
} else {
    $header_title = "Estimate Question Error";
    $template_error = "An error occured (perhaps the estimate code or question code is invalid?) " . mysql_error();
} 

$template_body = ob_get_clean();
require 'templates/main.php';

?>