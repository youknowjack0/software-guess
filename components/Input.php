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
*/
?>
<!--
FILE INFO: Input.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
-->
<?php

    // class to represent a generic input
    
    class Input {

         private $validate; // regex to validate user input         
         private $value; // the default/input value
         private $min; // minimum value (should only be used for number restricted fields)
         private $max; // maximum value (should only be used for number restricted fields)
         private $minlen; //min string length
         private $maxlen; //max string length
         private $locked; //can the user edit this?
         private $label; //label shown when printed on screen
         private $column; //associated database column
         
         function __construct($column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {             
             $this->column = $column;
             $this->label = $label;
             $this->validate = $validate;
             $this->value = $default;
             $this->min = $min;
             $this->max = $max;
             $this->minlen = $minlen;
             $this->maxlen = $maxlen;
             $this->locked = $false;
         }
         
         function html() {            
         }
    }
    
    class InputText extends Input {
        
        function __construct($column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
             parent::__construct($column, $label, $validate, $default, $min, $max, $minlen, $maxlen, $locked);       
        }
        
        function html() {
            return sprintf('<label for="%s">%s</label><input name="%s" id="%s" type="text" value="%s" />', $this->column, $this->label, $this->column, $this->column, $this->value);
        }
        
    }
?>