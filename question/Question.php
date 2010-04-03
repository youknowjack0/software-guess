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

FILE INFO: question/Question.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
*/

/* represents a Question from the database */
class Question {
    
    var $questiontemplate;
    var $questiontemplateparameters;
    var $name;
    var $shorthelp;
    var $longhelp;
    var $conditions;
    var $value; //indexed by version
    var $regex;
    var $code;
    var $min;
    var $max;
    var $minlen;
    var $maxlen;
    var $order;
    var $Q;
    
    /*      
     * guaranteed to be ordered by 'order' ascending
     * up to max version of $version
     * 
     */
    static function getAllQuestions($estimatecode, $version) {
        $Q = array();
        $questions = array();
        $sql = "SELECT * FROM Questions ORDER BY `Order` ASC";
        $result = mysql_query($sql);
        $value;
        while($question = mysql_fetch_assoc($result)) {
            //grab the responses, least recent responses first
            $sql = sprintf("SELECT `Value`, `IsArray`, `Version` FROM Responses WHERE Version <= %s AND QuestionCode = '%s' AND EstimateCode = '%s' ORDER BY Version ASC", $version, $question["Code"], $estimatecode);
            $result2 = mysql_query($sql);
                        
            $qObj = new Question($question["Code"]);
            
            //set question fields
            $qObj->questiontemplate = $question["QuestionTemplate"];
            $qObj->questiontemplateparameters = $question["QuestionTemplateParameters"];
            $qObj->name = $question["Name"] . " (" . $qObj->code . ")";
            $qObj->shorthelp = $question["ShortHelp"];
            $qObj->longhelp = $question["LongHelp"];
            $qObj->conditions = $question["Conditions"];
            $qObj->regex = $question["Regex"];
            $qObj->min = $question["Min"];
            $qObj->max = $question["Max"];
            $qObj->minlen = $question["MinLen"];
            $qObj->maxlen = $question["MaxLen"];
            $qObj->order = $question["Order"];
            
            if($result2) {
                while($response = mysql_fetch_assoc($result2)) {
	                if($response["IsArray"] == true) { //TODO: make this code work with escaped backslashes
	                    $value = preg_split("/(?<!\\\\),/", $response["Value"]); //comma not preceded by a backslash
	                    for($i=0;$i<count($value);$i++) {
	                        $value[$i] = str_replace("\,", ",", $value[$i]);
	                    }              
	                } else {
	                    $value = $response["Value"];
	                }
	                $Q[$question['Code']] = $value;
                }                      
            } else {
                $val = eval($question['Default'].";");
                $Q[$question['Code']] = $val;
                $qObj->value[$response["Version"]] = $val;
            }
            
            //TODO: is there a better way to do this line?
            $qObj->Q &= $Q;
            
            $questions[$qObj->code]=$qObj;
                    
        }
        return $questions;        
    }

    /* $questioncode: the code identifying the question to be instantiated
     */
    function __construct($code) {
        $this->code = $code;
    }
    
    /* true if Conditional is true so that the question can be answered
     * false otherwise
     */
    function canAnswer() {
        $Q = $this->Q;
        eval("\$ISNGOISUGNOI=".$this->conditions.";");
        return $ISNGOISUGNOI;
    }
    
    //TODO: cleanup
    function isUpToDate($latest) {
        if($latest==1) {
            return true;
        }
        if(!isset($this->value)) {
            return false;
        }
        $ver;
        foreach($this->value as $k => $v) {
            $ver = $k;
        }
        if(!isset($ver)) {
            if ($latest == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($ver == $latest) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    /* gets the most recent value
     * 
     */
    function getLatestValue() {
        $val;
        foreach($this->value as $v) {
            $val = $v;
        }
        if(!isset($val)) {
            return;
        } else {
            return $val;
        }
    }
    
    function hasValue() {
        if(isset($this->value)) {
            return true;
        }
        return false;
    }

}

?>