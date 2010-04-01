function toggleVisibility(id) {
	e = document.getElementById(id).style;
	if (e.visibility=='hidden') {
		e.visibility='visible';
		e.display='block';
	} else {
		e.visibility='hidden';
		e.display='none';
	}
	
}