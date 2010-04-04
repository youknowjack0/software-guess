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

FILE INFO: new.php
$LastChangedDate: 2010-03-26 07:38:15 +0800 (Fri, 26 Mar 2010) $
$Revision: 9 $
$Author: youknowjack@gmail.com $
*/

ob_start();

//load DB vars
require 'components/db.php';
require 'components/utility.php';

// Create instance of InputForm class
require 'components/inputform.php';

$form = new InputForm(-1, "GET", "new.php");

// public estimate identifier code
// TODO: check identifiers in the database before using
$input = array();
$ident = strtoupper(base_convert(mt_rand(100000,999999999999), 10, 36));
$input["AccessCode"] = new InputText("AccessCode", "AccessCode", "Access Code", "[0-9a-zA-Z]{3,69}", $ident, -1, -1, 3, 69, true);
$input["AccessCode"]->setLabelClass("accessCode");
$input["AccessCode"]->setHelp("This access code is the only way to access your saved estimate (please write it down). The code also serves as a unique identifier for this estimate. You cannot change this value.");

$input["ProjectName"] = new InputText("ProjectName", "ProjectName", "Project Name", "[a-zA-Z0-9\-' _]*", "", -1,-1, 3, 64);
$input["ProjectName"]->setHelp("This is the name of your project. You can change this at a later time; it is for identification purposes only");

$input["ProjectOwner"] = new InputText("ProjectOwner", "ProjectOwner", "Project Owner", "[a-zA-Z\-' ]*", "", -1, -1, 3, 48);
$input["ProjectOwner"]->setHelp("This is the name of the project owner (e.g. the project manager). You can change this at a later time; it is for identification purposes only");

$input["Organisation"] = new InputText("Organisation", "Organisation", "Organisation", "[a-zA-Z0-0\-' ]*", "", -1, -1, 0, 64);
$input["Organisation"]->setHelp("This is the name of the organisation associated with this project. This field can be left blank. You can change this at a later time; it is for identification purposes only");

$input["Phase"] = new InputRadio("Phase", "Phase", "Project Phase", "[0-9]+", 0, 0, 5, 1, 1);
$input["Phase"]->setHelp("This reflects the current phase of the project. Unless the project has started, leave this as default. This value affects confidence intervals for estimates generated.");
$input["Phase"]->add(0, "No Work Undertaken");
$input["Phase"]->add(1, "Requirements Analysis Complete");
$input["Phase"]->add(2, "Design Complete");
$input["Phase"]->add(3, "Alpha Release");
$input["Phase"]->add(4, "Beta Release");
$input["Phase"]->add(5, "Final Release");

$input["LastIteration"] = new InputText("LastIteration", "LastIteration", "Estimate Version", "[0-9]+", 0, 0, 32000, -1, -1, true);
$input["LastIteration"]->setHelp("this is the estimate version - it always begins at zero. This number will be iterated automatically as the estimate is revised. You cannot change this number manually.");

foreach($input as $i) {
    $form->addInput($i);
}

// if the user has submitted the form, handle the result
$form->setButtons();
$form->setRequest($_REQUEST);
if ($form->isResult()) {
    if ($form->isValid()) {        
        // update table & forward
        if(mysql_query(buildInsertQuery($input, "Estimates"))) {
            header("Location: estimatehome.php?code=".$input["AccessCode"]->getValue());
            exit;
        } else {
            $template_error = "MySQL threw an error: " . mysql_error();
        }
    } else {
        // print error, show form again
        $template_error = $form->getError();
    }
}

// Header file to show title, load styles, etc...
$header_title = "New Estimate";
$header_extra = '<link rel="stylesheet" href="static/forms.css" type="text/css" />';

// print form
$form->printHeader();
$form->printBody();
print("<br /><br />");
$form->printFooter();
print("<br />");

// load template
$template_body = ob_get_clean();
require 'templates/main.php'
?>