<div class="formitem">
	<table border="0">
	<tr>
		<td width="300px" valign="top">	
			<label for="%fieldname%">%label% <a href="javascript:;" onclick="javascript:toggleVisibility('%fieldname%_help');">[?]</a></label>
		</td>
		<td>	
			%code%
		</td>
	</tr>
	</table>
	<div class="help" style="visibility: hidden; display: none;" id="%fieldname%_help">
		%shorthelp%
	</div>
</div>