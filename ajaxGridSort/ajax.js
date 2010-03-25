/*
 * Neeraj Thakur
 * Sr.. Developer
 *
 * Modified by Jack Langman
 */

var numFields;
var arrValidate;
var arrRange;
var arrFields;
var filename;
var url;

function ags_init(ags_numFields, ags_arrValidate, ags_arrRange, ags_arrFields, ags_filename) {
	numFields = ags_numFields;
	arrValidate = ags_arrValidate;
	arrRange = ags_arrRange;
	arrFields = ags_arrFields;
	filename = ags_filename;
	url = filename + "?param="; // The server-side scripts	
}

function GetXmlHttpObject(handler)
{ 
	var objXmlHttp=null

	if (navigator.userAgent.indexOf("Opera")>=0)
	{
		alert("This software may not work correctly in Opera"); 
		objXmlHttp=new XMLHttpRequest()
		objXmlHttp.onload=handler
		objXmlHttp.onerror=handler 
		return objXmlHttp
	}
	if (navigator.userAgent.indexOf("MSIE")>=0)
	{ 
		var strName="Msxml2.XMLHTTP"
			if (navigator.appVersion.indexOf("MSIE 5.5")>=0)
			{
				strName="Microsoft.XMLHTTP"
			} 
		try
		{ 
			objXmlHttp=new ActiveXObject(strName)
			objXmlHttp.onreadystatechange=handler 
			return objXmlHttp
		} 	
		catch(e)
		{ 
			alert("Error. Scripting for ActiveX might be disabled"); 
			return 
		} 
	} 
	if (navigator.userAgent.indexOf("Mozilla")>=0)
	{
		objXmlHttp=new XMLHttpRequest()
		objXmlHttp.onload=handler
		objXmlHttp.onerror=handler 
		return objXmlHttp
	}
}



function getagents(column,direc) {		
	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("GET",url + escape(column) + "&mode=list&dir=" + direc + "&rand=" + myRandom, true);
	xmlHttp.send(null);
}

function saveRecord(mode,id,param,dir)
{		

	var str = "";
	if(!validate()) {
		return;
	}
	for (var i=0;i<numFields;i++) {
		str = str + "f" + i + "=" + document.getElementById("f"+i).value;
		str = str + "&";
	}			

	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("GET",filename+"?" + str + "mode="+mode+"&param=" + escape(param) + "&dir=" + dir + "&rand=" + myRandom, true);
	xmlHttp.send(null);
}

function saveNewRecord(mode,param,dir)
{
	var str = "";
	if(!validate()) {
		return;
	}
	for (var i=1;i<numFields;i++) { // skip id
		str = str + "f" + i + "=" + document.getElementById("f"+i).value;
		str = str + "&";
	}

	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("GET",filename+"?"+str+"mode="+mode+"&param=" + escape(param) + "&dir=" + dir + "&rand=" + myRandom, true);
	xmlHttp.send(null);	

}

function newRecord(mode,param,dir)
{
	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("GET",filename+"?mode="+mode+"&param=" + escape(param) + "&dir=" + dir + "&rand=" + myRandom, true);
	xmlHttp.send(null);
}

function manipulateRecord(mode,id,param,dir)
{
	if(mode == "delete") {
		if ( confirm("Are you sure you want to "+mode+" record ?") != 1 )
		{
			return false;	
		}
	} 

	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("GET",filename+"?ID="+id+"&mode="+mode+"&param=" + escape(param) + "&dir=" + dir + "&rand=" + myRandom, true);
	xmlHttp.send(null);
}	

function validate() {
	var re;
	for(var i=1;i<numFields;i++) {
		if (arrValidate[i] == 0) {
			continue;
		}
		re = new RegExp(arrValidate[i]);
		element = document.getElementById("f"+i);
		var result = re.exec(trim(element.value));
		var fail = false;
		if (!result) {
			fail=true;
		} else if (result[0] != trim(element.value)) {
			fail=true;
		}
		if(fail) {
			alert("Input '"+element.value+"' is invalid for '" + arrFields[i] + "'. Should match regex: /"+arrValidate[i]+"/");
			return false;
		}
	}
	for(var i=1;i<numFields;i++) {
		if (arrRange[i] == 0) {
			continue;
		}
		var lower = arrRange[i][0];
		var upper = arrRange[i][1];
		var testval = parseFloat(document.getElementById("f"+i).value);
		if (testval < lower || testval > upper) {
			alert("Invalid input for '" + arrFields[i] + "'. Should fall within range: " + lower + "-" + upper);
			return false;
		}
	}
	return true;
}

trim = function(str) {
	return str.replace(/^\s+|\s+$/g,"");
}

function handleHttpResponse() {
	if (xmlHttp.readyState == 4) {
		document.getElementById("hiddenDIV").style.visibility="visible"; 		
		document.getElementById("hiddenDIV").innerHTML='';
		document.getElementById("hiddenDIV").innerHTML=xmlHttp.responseText;
	}
}