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
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
*/

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
    var $errormessage;
     
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
     
    //in this parent function, use %s as placeholder for child input fields
    function html() {
        $return = '<div class="formitem">%s';
        // prints the documentation & necessary newlines
        if(isset($this->shortHelp)) {
            $return .= sprintf(' <a href="#" onclick="javascript:toggleVisibility(\'%s\');">[?]</a>', $this->name . "_help");
        }
        if(isset($this->helpLink)) {
            $return .= sprintf(' [<a href="%s">documentation</a>]', $this->helpLink);
        }
        
        //print("<br />");
        if(isset($this->shortHelp)) {
            $return .= sprintf('<div class="help" style="visibility:hidden;display:none;" id="%s_help">%s</div>', $this->name, $this->shortHelp);
        }
        $return .= '</div>';
        return $return;
    }
    
    function isValid() {
        $this->errormessage = "";
        // test against regex
        if($this->validate != "") {
	        $matches = array();
	        preg_match('/'.$this->validate.'/', $this->value, $matches);
	        if(!isset($matches[0]) || !($matches[0] == $this->value)) {
	            $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it does not match regex "%s"', $this->value, $this->label, $this->validate);
	            return false;
	        }
        }
        //test for length
        if($this->min != -1) { // todo: check for float/int binary representation issues
            if(floatval($this->value) < $this->min) {
                $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it is less than the minimum of %s"', $this->value, $this->label, $this->min);
                return false;
            }
        }
        if($this->max != -1) { // todo: check for float/int binary representation issues
            if(floatval($this->value) > $this->max) {
                $this->errormessage = sprintf('Input "%s" in field "%s" is not valid: it is greater than the maximum of %s"', $this->value, $this->label, $this->max);
                return false;
            }
        }
        if($this->minlen != -1) {
            if(strlen($this->value) < $this->minlen) {
                $this->errormessage = sprintf('Input "%s" (%s chars) in field "%s" is not valid: it is shorter than the minimum length of %s"', $this->value, strlen($this->value), $this->label, $this->minlen);
                return false;
            }
        }
        if($this->maxlen != -1) {
            if(strlen($this->value) > $this->maxlen) {
                $this->errormessage = sprintf('Input "%s" (%s chars) in field "%s" is not valid: it is longer than the maximum length of %s"', $this->value, strlen($this->value), $this->label, $this->maxlen);
                return false;
            }
        }
        return true;                   
    }
    
    function getErrorMessage() {
        return $this->errormessage;
    }
    
}

class InputText extends Input {

    function __construct($name, $column, $label, $validate='', $default='', $minlen=-1, $maxlen=-1, $locked=false) {
        parent::__construct($name, $column, $label, $validate, $default, -1, -1, $minlen, $maxlen, $locked);
    }

    function html() {
        $str = sprintf('<label for="%s"%s>%s </label><div class="inputspace"><input name="%s" id="%s" type="text" value="%s"%s%s /></div>',
        $this->column, // field name
        (isset($this->labelClass) ? sprintf(' class="%s"',$this->labelClass) : ""), // label class
        $this->label, //label text
        $this->column, //field name
        $this->column, //field id
        $this->value, //field value
        ($this->locked ? ' readonly="readonly" class="lockedinput"' : ""), //islocked
        (isset($this->class) ? sprintf(' class="%s"',$this->class) : "")); // input class

        // the html function in the root class leaves a %s for fields based on it
        return sprintf(parent::html(), $str);
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

?>