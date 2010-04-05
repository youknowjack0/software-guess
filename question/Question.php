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
    var $groupid;
    var $dbkey; //for the LATEST RESPONSE
    var $dbversion=0; //version of latest value from the db //TODO: This is not nice n clear
    
    public static $Q = array();
    
    /*      
     * guaranteed to be ordered by 'order' ascending
     * up to max version of $version
     * 
     */
    static function getAllQuestions($estimatecode, $version = -1) {
        $Q =& Question::$Q;
        $questions = array();
        $sql = "SELECT * FROM Questions ORDER BY `Order` ASC";
        $result = mysql_query($sql);
        $value;
        $vString = "";
        if($version != -1) {
            $vString = sprintf(" AND Version <= %d", $version); 
        }
        while($question = mysql_fetch_assoc($result)) {
            //grab the responses, least recent responses first
            $sql = sprintf("SELECT `ID`, `Value`, `IsArray`, `Version` FROM Responses WHERE QuestionCode = '%s' AND EstimateCode = '%s'%s ORDER BY Version ASC", $question["Code"], $estimatecode, $vString);
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
            $qObj->groupid = $question["Group"];
            /*$qObj->dbkey = $question["ID"];*/
            
            if(mysql_num_rows($result2)>0) {                
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
	                $qObj->value[$response["Version"]] = $value;
	                $qObj->dbversion = $response["Version"];
	                $qObj->dbkey = $response["ID"];
                }                      
            } else {
                if(isset($question["Default"]) && $question["Default"] != "") {
                    //suppress errors
                    ob_start();
                        eval("\$ALKJALKJSD=".$question['Default'].";"); //TODO: verify & shift to a private variable space
                        $val = $ALKJALKJSD;                        
                        //$Q[$question['Code']] = $val; //don't set Q w/ default values
                        $qObj->value[0] = $val; // records the default value as belonging to version 0
                    ob_end_clean();
                }                             
            }
            
            //TODO: is there a better way to do this line?
            
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
        $Q =& Question::$Q;
        eval("\$ISNGOISUGNOI=".$this->conditions.";");
        return $ISNGOISUGNOI;
    }
    
    /* true on success, false on fail. use mysql_error for error text */
    function committResponse($latestversion, $estimatecode) {
                    
        $val = $this->getLatestValue();
        $isArr = 0;

        if(is_array($val)) {
            $isArr = 1;
            foreach($val as $v) {
                $v = str_replace(",", "\,", $v); //escape commas            
            }
            $val = implode(",", $val);
        }
        
        $val = addslashes($val); //safe for sql execution
        
        $fieldmapping = array(
            "QuestionCode" => strtoupper($this->code),
            "Value" => $val,
            "EstimateCode" => strtoupper($estimatecode),
            "IsArray" => $isArr,
            "Version" => $latestversion        
        );      
        
        if(intval($this->dbversion) < intval($latestversion) || intval($this->dbversion) == 0) { //insert new response
            $sqltemplate = "INSERT INTO `responses` (`%s`) VALUES('%s')";
            $fields = array();
            foreach($fieldmapping as $k => $v) {
                $fields[] = $k;
            }
            $sql = sprintf($sqltemplate, implode("`,`", $fields), implode("','", $fieldmapping));                      
        } elseif (intval($this->dbversion) == intval($latestversion)) { //update existing response
            if (!isset($this->dbkey)) die(); //this should never happen; it's here to protect the db          
            $sqltemplate = "UPDATE `responses` SET %s WHERE `ID`=%s";
            $mixfield = array();
            foreach($fieldmapping as $f => $v) {
                $mixfield[] = sprintf("`%s`='%s'", $f, $v);
            }
                        
            $sql = sprintf($sqltemplate, implode(",", $mixfield), $this->dbkey);              
            
        } else {            
            die(); //this shouldn't happen
        }
        
        mysql_query($sql);
        
    }
    
    //TODO: cleanup
    function isUpToDate($latest) {
        if($latest==1) {
            return true;
        }
        if(!isset(Question::$Q[$this->code])) {
            return true;
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
    
    //todo: neaten this up
    function getLatestValueVersion() {
        $key;
        foreach($this->value as $k => $v) {
            $key = $k;
        }
        if(!isset($key)) {
            return;
        } else {
            return $key;
        }        
        
    }
    
    function hasValue() {
        if(isset($this->value) && $this->getLatestValueVersion() > 0) {
            return true;
        }
        return false;
    }
    
    function setResponse($version, $value) {
        $this->value[$version] = $value;
    }

}

?>