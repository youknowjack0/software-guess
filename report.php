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

FILE INFO: report.php
$LastChangedDate: 2010-05-05 02:30:57 +0800 (Wed, 05 May 2010) $
$Revision: 50 $
$Author: youknowjack@gmail.com $
*/
require 'components/db.php';
require 'Report/Report.php';
require 'Calculation/Calculation.php';
require 'components/utility.php';
require 'question/Question.php';

$type = intval($_GET["type"]);

if (($result = validateEstimateCode($_GET, "estimate")) && $type <= 1 && $type >= 1) { //change this restriction if adding more report types
    
    $row = mysql_fetch_assoc($result);
    $version = $row["LastIteration"]+1;
    $header_title = sprintf("Report (%s version %s)", $_GET["estimate"], $version);
    
    $r = Report::newReport($type); //static report factory; instantiate the report
    
    // add fields
    
    Question::getAllQuestions($_GET["estimate"]);
    
    $allcalcs = Calculation::getAllCalculations();
    $C =& Calculation::getC(Question::$Q, $allcalcs);
    
    $r->addf("PROJECTNAME", $row["ProjectName"]);
    $r->addf("ORGNAME", $row["Organisation"]);
    $r->addf("ESTIMATEVERSION", $version);
    $r->addf("PROJECTPHASE", getPhase(intval($row["Phase"])));
    $r->addf("E", sprintf("%.1f", $C["C_EFF_MEAN"]));
    $r->addf("D80", sprintf("%.1f", $C["C_EFF_STD"] * 1.28155));
    $r->addf("D90", sprintf("%.1f", $C["C_EFF_STD"] * 1.64485));
    $r->addf("D95", sprintf("%.1f", $C["C_EFF_STD"] * 1.95996));
    $r->addf("D99", sprintf("%.1f", $C["C_EFF_STD"] * 2.57583));
    $r->addf("D99.9", sprintf("%.1f", $C["C_EFF_STD"] * 3.29053));
    $r->addf("cocomoyn", isset($C['C_EFF_COCOMO']) && $C['C_EFF_COCOMO'] != null ? "Yes" : "No");
    $r->addf("delphiyn", isset($C['C_EFF_DELPHI']) && $C['C_EFF_DELPHI'] != null ? "Yes" : "No");
    $r->addf("pertyn", isset($C['C_EFF_PERT']) && $C['C_EFF_PERT'] != null ? "Yes" : "No");
    $r->addf("cocomo", sprintf("%.1f", $C["COCOMO_E"]));
    $r->addf("delphi", sprintf("%.1f", $C["DELPHI_EFFORT"]));
    $r->addf("pert", sprintf("%.1f", $C["PERT_EFFORT"]));
    $r->addf("cocomo-calib", sprintf("&micro;=%.1f, &#963;=%.1f", $C["C_EFF_COCOMO"],$C["C_EFF_COCOMO_STD"] ));
    $r->addf("delphi-calib", sprintf("&micro;=%.1f, &#963;=%.1f", $C["C_EFF_DELPHI"],$C["C_EFF_DELPHI_STD"]));
    $r->addf("pert-calib", sprintf("&micro;=%.1f, &#963;=%.1f", $C["C_EFF_PERT"],$C["C_EFF_PERT_STD"]));
    $r->addf("numcalibration", $C["C_MIN_N"]);
    
    // print report
    
    $r->printReport();

} else {

    $header_title = "Report";
    $template_error = "An error occured (perhaps the estimate code is invalid?) " . mysql_error();
}
?>