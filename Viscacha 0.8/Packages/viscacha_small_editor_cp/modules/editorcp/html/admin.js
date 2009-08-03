///////////////////////// Variables /////////////////////////
var box_img_plus = 'admin/html/images/plus.gif';
var box_img_minus = 'admin/html/images/minus.gif';

///////////////////////// General / Misc. /////////////////////////
function disable (txt) {
	if (txt.id == 'dis1') {
		input = FetchElement("dis2");
	}
	else {
		input = FetchElement("dis1");
	}

	if (txt.value != '') {
		input.disabled="disabled";
	}
	else {
		input.disabled="";
	}

	return;

}
function locate(url) {
	if (url != '') {
		location.href = url;
	}
}
function hideLanguageBoxes() {
	for(var i=1;i<256;i++) {
		box = FetchElement('language_'+i);
		check = FetchElement('use_'+i);
		if (box && check) {
			if (check.checked != true && check.checked != 'checked') {
				box.style.display = 'none';
			}
		}
	}
}
function useit(rq){
	var revisedMessage;
	var currentMessage = document.getElementsByName("temp2")[0].value;
	revisedMessage = currentMessage+rq;
	document.getElementsByName("temp2")[0].value=revisedMessage;
	document.getElementsByName("temp2")[0].focus();
	return;
}
function insert_doc(url,title) {
	opener.document.getElementsByName("url")[0].value = url;
	if (opener.document.getElementsByName("title")[0].value.length < 2) {
		opener.document.getElementsByName("title")[0].value = title;
	}
    top.close();
}

///////////////////////// PopUps / Confirm /////////////////////////
function openHookPosition(hook) {
	var url = 'editorcp.php?action=packages&job=plugins_hook_pos&hook=';
	if (hook == null) {
		var hook = FetchElement('hook').value;
	}
	window.open(url+hook+'#key', "sourcecode", "width=640,height=480,resizable=yes,scrollbars=yes,location=yes");
	return false;
}
function docs() {
    window.open("editorcp.php?action=cms&job=nav_docslist","","width=480,height=480,resizable=yes,scrollbars=yes");
}
function coms() {
    window.open("editorcp.php?action=cms&job=nav_comslist","","width=480,height=480,resizable=yes,scrollbars=yes");
}
function changeLanguageUsage(lid) {
	box = FetchElement('language_'+lid);
	if (box.style.display == 'none') {
		box.style.display = '';
		return true;
	}
	else {
		var test = confirm(lng['confirmNotUsed']);
		if (test) {
			box.style.display = 'none';
			return true;
		}
		else {
			return false;
		}
	}
}
function init() {
	for(var i=0; i < document.images.length; i++) {
	    name = document.images[i].alt;
		if (name == 'collapse') {
			switchimg = document.images[i];
			id = switchimg.id.replace("img_","");
			boxes[i] = id;
			part = FetchElement("part_"+id);
			if(document.cookie && part.style.display != 'none') {
				hide = GetCookie(id);
				if(hide != '') {
					switchimg.src = box_img_plus;
					part.style.display = 'none';
				}
				else {
					switchimg.src = box_img_minus;
				}
			}
			HandCursor(switchimg);
			Switch(switchimg);
		}
	}
}
function initTranslateDetails() {
	for(var i=0; i < document.images.length; i++) {
	    name = document.images[i].name;
		if (name == 'c') {
			switchimg = document.images[i];
			id = switchimg.id.replace("img_","");
			boxes[i] = id;
			part = FetchElement("part_"+id);
			part.style.display = 'none';
			HandCursor(switchimg);
			Switch(switchimg, true);
		}
	}
}

///////////////////////// AdminCP /////////////////////////
function All(job) {
	for(var i =0; i < document.images.length; i++) {
	    name = document.images[i].alt;
		if (name == 'collapse') {
			switchimg = document.images[i];
			id = switchimg.id.replace("img_","");
			part = FetchElement("part_"+id);
			if(job == 1) {
				switchimg.src = box_img_plus;
				part.style.display = 'none';
				SetCookie(id);
			}
			else {
				switchimg.src = box_img_minus;
				part.style.display = 'block';
				KillCookie(id);
			}
			Switch(switchimg);
		}
	}
}

///////////////////////// AJAX /////////////////////////
function ajax_noki(img, params) {
	var myConn = new ajax();
	if (!myConn) {alert(lng['ajax0']);}
	var fnWhenDone = function (oXML) {
	    if (oXML.responseText == '1' || oXML.responseText == '0') {
	    	img.src = noki(oXML.responseText);
	    }
	    else {
	    	alert(oXML.responseText);
	    }
	};
	myConn.connect("editorcp.php", "GET", params+ieRand(), fnWhenDone);
}
function noki(int) {
	if (int == '1') {
		return 'admin/html/images/yes.gif';
	}
	else {
		return 'admin/html/images/no.gif';
	}
}