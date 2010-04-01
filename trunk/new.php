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

// Create instance of InputForm class
require 'components/inputform.php';

$form = new InputForm(-1, "GET", "new.php");

// public estimate identifier code
// TODO: check identifiers in the database before using
$ident = strtoupper(base_convert(mt_rand(100000,999999999999), 10, 36));
$input = new InputText("AccessCode", "AccessCode", "Access Code", "[0-9a-zA-Z]{3,69}", $ident, $minlen=-1, $maxlen=-1, true);
$input->setLabelClass("accessCode");
$input->setHelp("This access code is the only way to access your saved estimate (please write it down). The code also serves as a unique identifier for this estimate. You cannot change this value.");
$form->addInput($input);
$input = new InputText("ProjectName", "ProjectName", "Project Name", "[a-zA-Z0-9\-' _]*", "", 3, 64);
$input->setHelp("This is the name of your project. You can change this at a later time; it is for identification purposes only");
$form->addInput($input);
$input = new InputText("ProjectOwner", "ProjectOwner", "Project Owner", "[a-zA-Z\-' ]*", "", 3, 48);
$input->setHelp("This is the name of the project owner (e.g. the project manager). You can change this at a later time; it is for identification purposes only");
$form->addInput($input);
$input = new InputText("LastIteration", "LastIteration", "Estimate Version", "[0-9]+", 0, -1, -1, true);
$input->setHelp("this is the estimate version - it always begins at zero. This number will be iterated automatically as the estimate is revised. You cannot change this number manually.");
$form->addInput($input);


// if the user has submitted the form, handle the result
$form->setRequest($_REQUEST);
if ($form->isResult()) {
    if ($form->isValid()) {
        // update table & forward
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