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
    require 'components/Input.php';

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
        
        public function __construct($type, $method, $currentfile) {
            $this->type = $type;
            $this->method = $method;
            $this->currentfile = $currentfile;
        }
        
        // has the user filled out the form
        public function isResult() {
        
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
        
        public function setRequest($req) {
            $this->req = $req;
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