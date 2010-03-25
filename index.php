<!-- 
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
 -->
<!--  
FILE INFO:
$LastChangedDate$
$Revision$
$Author$
$HeadURL$
 -->
<?php

    require 'static/header.php';

    // This file just creates a launcher page with some buttons to do various 
    // stuff. It achieves this by setting some variables which are passed to the
    // template file: "biglinks.php"
    
    // The $buttons var is passed to the biglinks file. Each nested array rep-
    // resents 1 button/link. The "type" field determines how the button/link
    // is displayed. The "file" field determines which page is called to handle
    // the user when the button/input is pressed/submitted.
    
    $buttons = array(
        array("name" => "New Estimate", "file" => "new.php", "image" => "", "type" => "button"), //todo: create images
        array("name" => "Existing Estimate", "file" => "edit.php", "image" => "", "type" => "input"),
        array("name" => "Calibrate", "file" => "calibrate.php", "image" => "", "type" => "input")
    );
    require 'templates/biglinks.php';
    
    require 'static/footer.php';
?>