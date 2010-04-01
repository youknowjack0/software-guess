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

FILE INFO: biglinks.php
$LastChangedDate: 2010-03-25 17:48:06 +0800 (Thu, 25 Mar 2010) $
$Revision: 3 $
$Author: youknowjack@gmail.com $

*/
    // this file expects $biglinks_items passed to it as an nested array of
    //  items
    // structure:
    // "name", "file", "type", "image"
    //  name: button/link name shown on the page
    //  file: file to handle the button/input
    //  type: "button" or "input" -- input can be used for search or w/e
    //  image: image to use for the button
    // example:
    // $biglinks_items = array(
    //    array("name" => "New Estimate", "file" => "new.php", "image" => "", "type" => "button"), //todo: create images
    //    array("name" => "Existing Estimate", "file" => "edit.php", "image" => "", "type" => "input"),
    //    array("name" => "Calibrate", "file" => "calibrate.php", "image" => "", "type" => "input")
    // );
    //
    // For input types, the user input will be passed as an url parameter named
    //  'input'
    //
    // Styles:
    // todo: define styles
    // #biglinks_buttons -- wraps around all items
    // .biglinks_button -- each item
    // .biglinks_image -- the images
    
    echo "<div id=\"biglinks_buttons\">";
    foreach ($biglinks_items as $item) {
        echo "<div class=\"biglinks_button\">";
        if ($item["type"] == "button") {
            printf('<a href="%s"><img src="%s" class="biglinks_image" alt="%s" /><br />%s</a>', $item["file"], $item["image"], $item["name"], $item["name"]);
        } elseif ($item["type"] == "input") {
            printf('<form method="GET" action="%s" >', $item["file"]);
            printf('<img src="%s" class="biglinks_image" alt="%s" /><br />%s<br />', $item["image"], $item["name"], $item["name"]);
            echo '<input type="text" name="input" />';
            echo '<input type="submit" name="submit" value="Go" />';
            echo '</form>';
        }        
        echo "</div>";
    }
    echo "</div>";

?>