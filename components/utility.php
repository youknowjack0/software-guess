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

FILE INFO: components/utility.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
$HeadURL: https://software-guess.googlecode.com/svn/trunk/index.php $
*/

function buildInsertQuery($inputs, $table) {
    $inputsArr = array();
    $inputstring = "";
    $valuesArr;
    $valuestring = "";
    foreach($inputs as $i) {
        $inputsArr[] = $i->column;
        $valuesArr[] = addslashes($i->value);
    }
    $inputstring = implode("`,`", $inputsArr);
    $valuestring = implode("','", $valuesArr);
    $sql = sprintf("INSERT INTO %s (`%s`) VALUES ('%s')", $table, $inputstring, $valuestring);
    return $sql;	
}

/* if valid returns the mysql result, else false */
function validateEstimateCode($req, $field = 'code') {

	if (!isset($req[$field])) {
	    return false;
	} else {
	    $code = $_GET[$field];
	    $sql = sprintf("SELECT * FROM Estimates WHERE AccessCode = '%s'", addslashes($code));
	    if(!$result = mysql_query($sql)) {
	        return false;
	    }
	}
	
	if (mysql_num_rows($result) == 1) {
	    return $result;
	} else {
	    return false;
	}
}

/* if valid returns the mysql result, else false */
function validateQuestionCode($req) {

	if (!isset($req['question'])) {
	    return false;
	} else {
	    $allquestions = Question::getAllQuestions(addslashes($req["estimate"]));
	    if(isset($allquestions[$req['question']]) && $allquestions[$req['question']]->canAnswer()) {
	        return $allquestions;
	    } else {
	        return false;
	    }
	}

}

function getBreadcrumbs($file, $parameters) {
    $sitemap = array (
        "Home" => array("file" => "index.php"),
        "Estimate Home" => array("file" => "estimatehome.php", "params" => array("estimate"), "parent" => "Home"),
        "Question List" => array("file" => "estimate.php", "params" => array("estimate"), "parent" => "Estimate Home"),
        "Question" => array("file" => "estimate-question.php", "params" => array("estimate", "question"), "parent" => "Question List"),
        "Change History" => array("file" => "changes.php", "params" => array("estimate"), "parent" => "Estimate Home")    
    );
    
    $linktemplate = '<a href="%s">%s</a> &gt; ';
    
    //find file
    $thispage;
    $thispagename;
    foreach($sitemap as $p => $v) {
        if($v["file"] == $file) {
            $thispage = $v;
            $thispagename = $p; 
            break;
        }
    }
    
    $linkstr = $thispagename;
    
    $page =& $thispage;
    while(isset($page["parent"])) {
        $pagename = $page["parent"];
        $page =& $sitemap[$pagename];
        $str = "";
        $paramarray;
        if(isset($page["params"])) {
            foreach($page["params"] as $p) {
                $paramarray[$p] = $parameters[$p]; //relevant parameters only
            }
            foreach($paramarray as $k => $p) {
                $paramarray[$k] = $k . "=" . $p;
            }
            $str = "?" . implode("&",$paramarray);            
        }
        
        $linkstr = sprintf($linktemplate, $page["file"] . $str, $pagename) . $linkstr;        
    }
    
    return $linkstr;
    
}


?>