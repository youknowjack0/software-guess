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

FILE INFO: inputform.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $

*/
    require 'Input/Input.php';

    // this file exposes the InputForm class
    
    class InputForm {
        
        public static $NEW_RECORD = 1;
        public static $UPDATE_RECORD = 2;
        var $type;
        var $method;
        var $target;
        var $handler;
        var $currentfile;
        var $inputs = array();
        var $req;
        var $errormessage;
        var $name;
        var $isResult;
        var $buttons;
        
        public function __construct($type, $method, $currentfile, $name="f1") {
            $this->type = $type;
            $this->method = $method;
            $this->currentfile = $currentfile;
            $this->name = $name;
            $this->isResult = false;
        }
        
        // has the user filled out the form
        public function isResult() {
            return $this->isResult;
        }
        
        // name indexed associative array?
        public function getResults() {
            
        }
        
        public function headers() {

        }
        
        // add a new Input to this form
        public function addInput($input) {
            $this->inputs[] = $input;
        }
        
        public function updateTable($table) {
                       
        }
        
        public function printBody() {
            foreach($this->inputs as $i) {
                print($i->html());
            }
        }
        
        public function printHeader() {
            printf('<form method="%s" action="%s">', $this->method, $this->currentfile);
        }
        
        public function printFooter() {
            foreach($this->buttons as $b) {
                printf('<input type="submit" value="%s" name="%s" />', $b[1], $b[0]);
            }
            print('</form>');
        }
        
       public function setButtons($buttons = null) {
           if (!isset($buttons)) {
               $this->buttons = array(array($this->name . "_submit", "Submit", "submit"));
           } else {
	           foreach($buttons as $b) {
	               $x = array();
	               $x[0] = $this->name . "_" . $b[0];
	               $x[1] = $b[1];
	               $x[2] = $b[0];
	               $this->buttons[] = $x;
	           }
           }
       }
        
        /* returns button name if pressed, false otherwise */
        public function setRequest($req) {
            $this->req = $req;
            $buttonPressed = false;
            foreach($this->buttons as $b) {
                if(isset($req[$b[0]])) {
                    $buttonPressed = $b[2];
                    break;
                }
            }
            if($buttonPressed) {
                $this->isResult = true;                
                foreach ($this->inputs as $i) {
                    if(isset($req[$i->name])) {                        
                        $i->value = $req[$i->name];
                    }
                }
                return $buttonPressed;
            }
        }
        
        public function isValid() {
            $this->errormessage = "";
            $isValid=true;
            foreach($this->inputs as $i) {
                if(!$i->isValid()) {
                    $this->errormessage .= $i->getErrorMessage() . "<br />";
                    $isValid = false;
                }
            }
            return $isValid;
        }
        
        public function getError() {
            return $this->errormessage;
        }
        
    }

?>