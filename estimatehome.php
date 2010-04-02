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
$LastChangedDate: 2010-04-01 16:10:29 +0800 (Thu, 01 Apr 2010) $
$Revision: 14 $
$Author: youknowjack@gmail.com $
*/
ob_start();
require 'components/db.php';

// this file accepts url parameter: "code"
$valid = true;

if (!isset($_GET["code"])) {
    $valid = false;
} else {
    $code = $_GET["code"];
    $sql = sprintf("SELECT * FROM estimates WHERE AccessCode = '%s'", addslashes($code));
    if(!$result = mysql_query($sql)) {
        $valid = false;
    }
}

if ($valid && mysql_num_rows($result)==1) {

    //show links across the top; 'start estimating', 'reports' & 'change history'
    $row = mysql_fetch_assoc($result);
    $header_title = sprintf("Estimate Home (%s)", $row["AccessCode"]);
    $biglinks_items = array(
        array("name" => "Start Estimating", "file" => "estimate.php?s=0&code=".$code, "image" => "copyrightimages/pencil.png", "type" => "button"),
        array("name" => "Reports", "file" => "reportsindex.php", "image" => "copyrightimages/reports.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true)),
        array("name" => "Change History", "file" => "edit.php", "image" => "copyrightimages/cert.png", "type" => "button", "disabled" => ($row["LastIteration"]>0?false:true))
    );
    require 'components/biglinks.php';
    echo "<hr />";
    echo "<h3>View/Edit Estimate Details</h3>";
    

} else {
    $header_title = "Estimate Home";
    $template_error = "An error occured (perhaps the Access Code is invalid?) " . mysql_error();
}

$template_body = ob_get_clean();
require 'templates/main.php';
?>