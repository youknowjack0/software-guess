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

FILE INFO: Input/InputRadio.php
$LastChangedDate$
$Revision$
$Author$
*/

class InputRadio extends Input { //TODO: extend validate method to check if response is in the list

    var $items = array();
    
    function __construct($name, $column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
        parent::__construct($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, $locked);
    }

    function html() {
        $str = "";
        foreach($this->items as $k => $v) {
            $str .= sprintf('<label><input name="%s" type="radio" value="%s"%s /> %s</label><br />', $this->name, htmlspecialchars($v), ($v == $this->value ?  ' checked="checked"' : ""), $k);
        }

        return parent::html($str);
    }
    
    // add an item to the list
    function add($val, $string) {
        $this->items[$string] = $val;    
    }

}

?>