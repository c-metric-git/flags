//rc_admin_js ver. 1.3 by redcocker 2012/2/13

function showhide(id){
	if(document.getElementById){
		if(document.getElementById(id).style.display == "block"){
			document.getElementById(id).style.display = "none";
		}else{
			document.getElementById(id).style.display = "block";
		}
	}
}

function confirmreset(){
	var flag = window.confirm ( 'Click "OK" to restore all settings?');
	return flag;
}

function confirmcache(){
	var flag = window.confirm ( 'Click "OK" to clear cache?');
	return flag;
}

function confirmdelete(){
	var flag = window.confirm ( 'Click "OK" to delete PDFs?');
	return flag;
}
