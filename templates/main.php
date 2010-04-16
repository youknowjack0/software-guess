<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- COPYRIGHT NOTICE: Please see copyright.txt  -->
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

FILE INFO: templates/main.php
$LastChangedDate$
$Revision$
$Author$
$HeadURL$
*/ ?>
<html>
<head>
<title>GUESS: <?php echo $header_title ?></title>
<?php if(isset($header_extra)) {echo $header_extra;} ?>
<link rel="stylesheet" href="static/shared.css" type="text/css" />
<script language="javascript" src="static/shared.js"></script>
</head>
<body <?php if (isset($header_bodytag_extra)) {echo $header_bodytag_extra;} ?>>

	<div id="bigboy">
		<div id="topdiv">
			<a href="index.php"><img id="logo" style="border:none;" src="static/GUESStitle2.png" alt="G.U.E.S.S." /></a>
		</div>
		<div id="topright">
		</div>
		<?php if(isset($template_breadcrumbs)) {
		    echo '<div class="breadcrumbs">';
		    echo $template_breadcrumbs;
		    echo '</div>';
		}?>
		<?php if(isset($template_headerspace)) { 
		    echo $template_headerspace;
		} else {
			printf("<h2>%s</h2>", $header_title);
		} 
		?>	
		<hr />
		<?php if(isset($template_error) && $template_error != "") { ?><div class="error"><?php echo $template_error; ?></div><?php } ?>
		<?php if(isset($_REQUEST['error']) && $_REQUEST['error'] != "") { ?><div class="error"><?php echo htmlspecialchars($_REQUEST['error']); ?></div><?php } ?>
		<?php if(isset($template_success) && $template_success != "") { ?><div class="success"><?php echo $template_success; ?></div><?php } ?>
		<?php if(isset($_REQUEST['success']) && $_REQUEST['success'] != "") { ?><div class="success"><?php echo htmlspecialchars($_REQUEST['success']); ?></div><?php } ?>
		<noscript><div class="error">Javascript is required to use most of this software.</div></noscript>
		<?php echo $template_body; ?>
		<hr />
		<div style="text-align:center;font-size:small;">
			&copy; 2010 Jack Langman, Daniel Fozdar, Nelson Yiap, Zhihua Guo, Vivek Koul & Aaron Taylor. All rights reserved. <br />
			<a href="copyright.txt">Read here</a> for the full copyright terms (BSD License). <a href="thirdparty.txt">Read here</a> for third party attributions.
		</div>
	</div>
</body>
</html>