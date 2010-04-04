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

FILE INFO: components/InputText.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $
*/

class InputText extends Input {

    function __construct($name, $column, $label, $validate='', $default='', $min=-1, $max=-1, $minlen=-1, $maxlen=-1, $locked=false) {
        parent::__construct($name, $column, $label, $validate, $default, $min, $max, $minlen, $maxlen, $locked);
    }

    function html() {
        $str = sprintf('<label for="%s"%s>%s%s </label><div class="inputspace"><input name="%s" id="%s" type="text" value="%s"%s%s%s /></div>',
        $this->column, // field name
        (isset($this->labelClass) ? sprintf(' class="%s"',$this->labelClass) : ""), // label class
        $this->label, //label text
        $this->getHelpButton(), // help button
        $this->column, //field name
        $this->column, //field id
        htmlspecialchars($this->value), //field value
        ($this->locked ? ' readonly="readonly" class="lockedinput"' : ""), //islocked
        (isset($this->class) ? sprintf(' class="%s"',$this->class) : ""),
        $this->maxlen!=-1 ? sprintf(' maxlength="%s"',$this->maxlen) : ""); // input class

        // the html function in the root class leaves a %s for fields based on it
        return sprintf(parent::html(), $str);
    }

}

?>