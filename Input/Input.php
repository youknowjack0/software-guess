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

FILE INFO: Input.php
$LastChangedDate$
$Revision$
$Author$
*/

//TODO: star required items
//TODO: merge name with column, they're serving the same purpose

// class to represent a generic input

class Input {

    var $validate; // regex to validate user input
    var $value; // the default/input value
    var $min; // minimum value (should only be used for number restricted fields)
    var $max; // maximum value (should only be used for number restricted fields)
    var $minlen; //min string length
    var $maxlen; //max string length
    var $locked; //can the user edit this?
    var $label; //label shown when printed on screen
    var $column; //associated database column
    var $name; //name used in form operations
    var $class; // css class
    var $labelClass; //css label class
    var $displayOnly;
    var $helpLink;
    var $shortHelp;
    var $longHelp;
    var $errormessage;
    var $template;
     
    function __construct($name, $column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
        $this->column = $column;
        $this->label = $label;
        $this->validate = $validate;
        $this->value = $default;
        $this->min = $min;
        $this->max = $max;
        $this->minlen = $minlen;
        $this->maxlen = $maxlen;
        $this->locked = $locked;
        $this->name = $name;
        $this->displayOnly = false;
        $this->template = 'compact.php';
        $this->longHelp = "";
    }
    
    // once again, OO this!
    static function getInput($type, $templateparameters, $name, $column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
        $input;
	    if($type=="SimpleText") { 
	        $input = new InputText($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, false);
	    } elseif($type=="Radio") {
	        $input = new InputRadio($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, false);
	        //params	        
	        $QOZHAAKHIGHA;
	        eval("\$QOZHAAKHIGHA=".$templateparameters.";");        
	        $input->items = $QOZHAAKHIGHA; 
	    } elseif($type=="DynamicMultiText") {
	        $input = new DynamicMultiText($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, false);
	        $Q =& Question::$Q; //so template parameters can be processed if it references a question output
	        $QOZHAAKHIGHA;
	        eval("\$QOZHAAKHIGHA=".$templateparameters.";");
	        $input->setCount($QOZHAAKHIGHA);
	    }
	    return $input;        
    }
    
    function setTemplate($file) {
        $this->template = $file;
    }

    function setInputClass($class) {
        $this->class  = $class;
    }

    function setLabelClass($class) {
        $this->labelClass = $class;
    }
    
    function setHelpID($helpitem) {
        $this->helpLink = "help.php?item=" . $helpitem;
    }
    
    function setHelp($helptext) {
        $this->shortHelp = $helptext;
    }
    
    function setLongHelp($helptext) {
        $this->longHelp = $helptext;
    }
     
    function html($code) {
        
        //load template
        ob_start();
        require 'Input/templates/' . $this->template;
        $tstr = ob_get_clean();
        
        //define keyword replacement array
        $keywords = array(
           	"%fieldname%" => $this->name,
            "%label%" => $this->label,
            "%code%" => $code,
            "%shorthelp%" =>  $this->shortHelp, 
            "%longhelp%" => $this->longHelp        
        );
        
        //perform replacement
        foreach($keywords as $k => $v) {
            $tstr = str_replace($k, $v, $tstr);
        }
        
        //return it
        return $tstr;
    }
    
    /*function getHelpButton() {
        if(isset($this->shortHelp)) {
            return sprintf(' <a href="#" onclick="javascript:toggleVisibility(\'%s\');">[?]</a>', $this->name . "_help");
        }
        return "";
    }*/
    
    function isValid() {
        $this->errormessage = "";
        if(is_array($this->value)) {
            $val = $this->value;
            $specialerror = true;
        } else {
            $specialerror = false;
            $val = array($this->label => $this->value);
        }
        foreach($val as $k => $v) {
            if($specialerror) {
                $k = $this->label . " (" . ($k+1) . ")";
            }            
                    // test against regex
	        if($this->validate != "") {
		        $matches = array();
		        //print($this->validate);
		        preg_match('/'.$this->validate.'/', $v, $matches);
		        if(!isset($matches[0]) || !($matches[0] == $v)) {
		            $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it does not match regex "%s"', htmlspecialchars($v), $k, $this->validate);
		            return false;
		        }
	        }
	        //test for length
	        if($this->min != -1) { // todo: check for float/int binary representation issues
	            if(floatval($v) < $this->min) {
	                $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it is less than the minimum of %s"', htmlspecialchars($v), $k, $this->min);
	                return false;
	            }
	        }
	        if($this->max != -1) { // todo: check for float/int binary representation issues
	            if(floatval($v) > $this->max) {
	                $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it is greater than the maximum of %s"', htmlspecialchars($v), $k, $this->max);
	                return false;
	            }
	        }
	        if($this->minlen != -1) {
	            if(strlen($v) < $this->minlen) {
	                $this->errormessage = sprintf('Input "%s" (%s chars) in field "%s" is not valid: it is shorter than the minimum length of %s"', htmlspecialchars($v), strlen($v), $k, $this->minlen);
	                return false;
	            }
	        }
	        if($this->maxlen != -1) {
	            if(strlen($v) > $this->maxlen) {
	                $this->errormessage = sprintf('Input "%s" (%s chars) in field "%s" is not valid: it is longer than the maximum length of %s"', htmlspecialchars($v), strlen($v), $k, $this->maxlen);
	                return false;
	            }
	        }
        }
        return true;                   
    }
    
    function getErrorMessage() {
        return $this->errormessage;
    }
    
    function getValue() {
        return $this->value;
    }
    
    function setRequest($req) {
        if(isset($req[$this->name])) {                        
            $this->value = $req[$this->name];
        }
    }
    
}

// use this to print some plain code as part of a form
// does not behave like a normal input/output
class HTML extends Input {
    var $code;
    function __construct($code) {
        $this->locked = true;
        $this->displayOnly = true;
        $this->code = $code;
    }
    
    function html() {
        return $this->code;
    }
    
    function isValid() {
        return true;
    }

}

require 'Input/InputRadio.php';
require 'Input/InputText.php';
require 'Input/DynamicMultiText.php';

?>