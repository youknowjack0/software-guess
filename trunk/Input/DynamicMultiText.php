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

FILE INFO: Input/DynamicMultiText.php
$LastChangedDate: 2010-04-16 17:53:23 +0800 (Fri, 16 Apr 2010) $
$Revision: 31 $
$Author: youknowjack@gmail.com $
*/

class DynamicMultiText extends Input {

    var $fieldcount;
    
    function __construct($name, $column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
        parent::__construct($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, $locked);

    }
    
    function html() {
        $str = "";
        for($i=0;$i<$this->fieldcount;$i++) {
            $str .= sprintf('%d. <input name="%s" id="%s" type="text" value="%s"%s%s%s /><br />',
            $i+1,                    
	        $this->column."_".$i, //field name
	        $this->column."_".$i, //field id
	        htmlspecialchars($this->value[$i]), //field value
	        ($this->locked ? ' readonly="readonly" class="lockedinput"' : ""), //islocked
	        (isset($this->class) ? sprintf(' class="%s"',$this->class) : ""),
	        $this->maxlen!=-1 ? sprintf(' maxlength="%s"',$this->maxlen) : ""); // input class
        }
        
        // the html function in the root class leaves a %s for fields based on it
        return parent::html($str);
    }
    
    function setCount($num) { //todo: this should really be in the constructor.. never gonna happen :)
        $this->fieldcount = $num;
        $default = $this->value;
        if(!is_array($this->value)) { //if it's a non array default from the database, then dupe it into an array
	        $this->value = array();
	        for($i=0;$i<$this->fieldcount;$i++) {
	            $this->value[] = $default;
	        }
        } else { //number of fields may have increased 
            for($i=0;$i<$this->fieldcount;$i++) {
                if(!isset($this->value[$i])) {
                    $this->value[$i]=null;
                }                
            }
        }
    }
    
    function setRequest($req) {
        if(isset($req[$this->column."_1"])) {
            $this->value = array();
            for($i=0;$i<$this->fieldcount;$i++) {
                $this->value[$i] = $req[$this->column."_".$i];
            }                                    
        }
    }

}

?>