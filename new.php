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
require 'templates/inputform.php';

$form = new InputForm(-1, "GET", "new.php");

// public estimate identifier code
// TODO: check identifiers in the database before using
$ident = strtoupper(base_convert(mt_rand(100000,999999999999), 10, 36));
$input = new InputText("AccessCode", "AccessCode", "Access Code", "[0-9a-zA-Z]+", $ident, $minlen=-1, $maxlen=-1, true);
$input->setInputClass("accessCode");
$input->setLabelClass("accessCode");
$form->addInput($input);
$input = new HTML("The above code is the only way to access your saved estimate (please write it down). <br />");
$form->addInput($input);


// if the user has submitted the form, handle the result
if ($form->isResult()) {
    $form->updateTable("tablenamexxx");
}

// Header file to show title, load styles, etc...
$header_title = "New Estimate";
$header_extra = '<link rel="stylesheet" href="static/forms.css" type="text/css">';

// print form
$form->printBody();

// load template
$template_body = ob_get_clean();
require 'templates/main.php'
?>