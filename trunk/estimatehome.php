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

FILE INFO: estimatehome.php
$LastChangedDate$
$Revision$
$Author$
*/
ob_start();
require 'components/db.php';
require 'components/utility.php';

// Create instance of InputForm class
require 'components/inputform.php';

if ($result = validateEstimateCode($_REQUEST, 'estimate')) {
            
    $estimate_code = strtoupper($_REQUEST['estimate']);

    //show links across the top; 'start estimating', 'reports' & 'change history'
    $row = mysql_fetch_assoc($result);
    $header_title = sprintf("Estimate Home (%s)", $row["AccessCode"]);
    $template_breadcrumbs = getBreadcrumbs('estimatehome.php', array("estimate" => $estimate_code));
    $biglinks_items = array(
        array("name" => "Start Estimating", "file" => "estimate.php?estimate=".$estimate_code, "image" => "copyrightimages/pencil.png", "type" => "button"),
        array("name" => "Simple Report", "file" => "report_simple.php?estimate=".$estimate_code, "image" => "copyrightimages/reports.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true)),
        array("name" => "Extended Report", "file" => "report_extended.php?estimate=".$estimate_code, "image" => "copyrightimages/largereport.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true)),
        array("name" => "Raw Calculations", "file" => "calculations.php?estimate=".$estimate_code, "image" => "copyrightimages2/math.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true)),
        array("name" => "Change History", "file" => "changes.php?estimate=".$estimate_code, "image" => "copyrightimages/cert.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true))
    );
    $biglinks_height = 80;
    require 'components/biglinks.php';
    echo "<hr />";
    echo "<h3>View/Update Estimate Details</h3>";
	$form = new InputForm(-1, "POST", "estimatehome.php?estimate=".$estimate_code);
	
	// public estimate identifier code
	$input = array();
	$ident = $estimate_code;
	$input["AccessCode"] = new InputText("AccessCode", "AccessCode", "Access Code", "[0-9a-zA-Z]{3,69}", $ident, -1, -1, 3, 69, true);
	$input["AccessCode"]->setLabelClass("accessCode");
	$input["AccessCode"]->setHelp("This access code is the only way to access your saved estimate (please write it down). The code also serves as a unique identifier for this estimate. You cannot change this value.");
	
	$input["ProjectName"] = new InputText("ProjectName", "ProjectName", "Project Name", "[a-zA-Z0-9\-' _]*", $row["ProjectName"], -1,-1, 3, 64);
	$input["ProjectName"]->setHelp("This is the name of your project. You can change this at a later time; it is for identification purposes only");
	
	$input["ProjectOwner"] = new InputText("ProjectOwner", "ProjectOwner", "Project Owner", "[a-zA-Z\-' ]*", $row["ProjectOwner"], -1, -1, 3, 48);
	$input["ProjectOwner"]->setHelp("This is the name of the project owner (e.g. the project manager). You can change this at a later time; it is for identification purposes only");
	
	$input["Organisation"] = new InputText("Organisation", "Organisation", "Organisation", "[a-zA-Z0-0\-' ]*", $row["Organisation"], -1, -1, 0, 64);
	$input["Organisation"]->setHelp("This is the name of the organisation associated with this project. This field can be left blank. You can change this at a later time; it is for identification purposes only");
	
	$input["Phase"] = new InputRadio("Phase", "Phase", "Project Phase", "[0-9]+", $row["Phase"], 0, 5, 1, 1);
	$input["Phase"]->setHelp("This reflects the current phase of the project. Unless the project has started, leave this as default. This value affects confidence intervals for estimates generated.");
	$input["Phase"]->add(0, "No Work Undertaken");
	$input["Phase"]->add(1, "Requirements Analysis Complete");
	$input["Phase"]->add(2, "Design Complete");
	$input["Phase"]->add(3, "Alpha Release");
	$input["Phase"]->add(4, "Beta Release");
	$input["Phase"]->add(5, "Final Release");
	
	$input["LastIteration"] = new InputText("LastIteration", "LastIteration", "Estimate Version", "[0-9]+", $row["LastIteration"], 0, 32000, -1, -1, true);
	$input["LastIteration"]->setHelp("this is the estimate version - it always begins at zero. This number will be iterated automatically as the estimate is revised. You cannot change this number manually.");
	
	foreach($input as $i) {
	    $form->addInput($i);
	}
	
	// if the user has submitted the form, handle the result
	$form->setButtons(array(array("Update", "Update")));
	$form->setRequest($_REQUEST);
	if ($form->isResult()) {
	    if ($form->isValid()) {        
	        // update table & forward
	        $fieldmapping = array(
	        	"ProjectOwner" => $input["ProjectOwner"]->value,
	            "ProjectName" => $input["ProjectName"]->value,
	            "Organisation" => $input["Organisation"]->value,
	            "Phase" => $input["Phase"]->value
            );
	        if(mysql_query(buildUpdateQuery($fieldmapping, "AccessCode", $estimate_code))) {
	            $template_success = "Estimate updated successfullly";
	        } else {
	            $template_error = "MySQL threw an error: " . mysql_error();
	        }
	    } else {
	        // print error, show form again
	        $template_error = $form->getError();
	    }
	}

	$header_extra = '<link rel="stylesheet" href="static/forms.css" type="text/css" />';

	// print form
	$form->printHeader();
	$form->printBody();
	print("<br />");
	$form->printFooter();
	print("<br />");

} else {
    $header_title = "Estimate Home";
    $template_error = "An error occured (perhaps the Access Code is invalid?) " . mysql_error();
}

$template_body = ob_get_clean();
require 'templates/main.php';
?>