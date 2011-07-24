///////////////////////// Variables /////////////////////////
var mq_cookiename = cookieprefix+'_vquote';

///////////////////////// General / Misc. /////////////////////////
function initImg(size) {
	if (LightBoxOnload) {
		LightBoxOnload();
	}
	for(var i =0; i < document.images.length; i++) {
		if (document.images[i].alt == 'switch') {
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
			}
			HandCursor(switchimg);
			Switch(switchimg);
		}
		else if (document.images[i].name == 'resize') {
			ResizeImg(document.images[i],size);
		}
	}
}
function ReloadCountdown(iv) {
	if (iv == -1) {
		window.location.reload();
	}
	else {
		countdown = FetchElement('countdown');
		countdown.firstChild.nodeValue = iv;
		iv = iv - 1;
		setTimeout("ReloadCountdown("+iv+")", 1000);
	}
}
function deletenotice(id) {
	input = confirm(lng['js_confirm_ndelete']);
	if (input == true) {
		notices = document.getElementsByName("notice[]");
		notices[id].value = '';
		noticeArea = FetchElement("notice_"+id);
		noticeArea.style.display = 'none';
		Form = FetchElement('notice');
		Form.submit();
		return;
	}
}
function confirmdelete(box) {
	if (box.checked == true) {
		input = confirm(lng['js_confirm_pdelete']);
		if (input == true) {
			box.checked = true;
		}
		else {
			box.checked = false;
		}
	}
}
function jumptopage(url) {
	var page = prompt(lng['js_page_jumpto'], '');
	if (page !== null && !isNaN(page) && page > 0) {
		document.location.href = url.replace(/&amp;/g, '&') + 'page=' + page + sidx;
	}
}

///////////////////////// AJAX /////////////////////////

// Schliesst oder oeffnet einen Beitrag
function ajax_openclosethread(id, img, isnew) {
	var myConn = new ajax();
	if (!myConn) {alert(lng['ajax0']);}
	var fnWhenDone = function (oXML) {
		if (oXML.responseText == '1' || oXML.responseText == '2') {
			lngval = 'ajax'+oXML.responseText;
			alert(lng[lngval]);
		}
		else if (oXML.responseText == '3' || oXML.responseText == '4') {
			lngval = 'ajax'+oXML.responseText+'_'+isnew;
			img.src = lng[lngval];
		}
	};
	myConn.connect("ajax.php", "GET", "action=openclosethread&id="+id+sidx+ieRand(), fnWhenDone);
}
// Setzt Forum als gelesen
function ajax_markforumread(id, img, small) {
	var myConn = new ajax();
	if (!myConn) {alert(lng['ajax0']);}
	var fnWhenDone = function (oXML) {
		if (oXML.responseText == '1') {
			if (small == 1) {
				img.src = lng['ajax_markforumread_small'];
			}
			else {
				img.src = lng['ajax_markforumread'];
			}
		}
		else {
			// ToDo: Error (0=No Permission)
		}
	};
	myConn.connect("ajax.php", "GET", "action=markforumread&id="+id+sidx+ieRand(), fnWhenDone);
}
// Checkt ob der Nutzername schon existiert
function ajax_doubleudata(name) {
	inline = FetchElement('udata_name');
	if (name.length > 3) {
		var myConn = new ajax();
		if (!myConn) {alert(lng['ajax0']);}
		var fnWhenDone = function (oXML) {
			if (oXML.responseText == '1') {
				lngval = 'ajax'+oXML.responseText;
				alert(lng[lngval]);
			}
			else {
				lngval = 'ajax'+oXML.responseText;
				inline.innerHTML = lng[lngval];
			}
		};
		myConn.connect("ajax.php", "GET", "action=doubleudata&name="+name+sidx+ieRand(), fnWhenDone);
	}
	else {
		inline.innerHTML = '';
	}
}
// Sucht nach Nutzernamen (PN)
function ajax_searchmember(name, key) {
	if (typeof key == 'number') { // undefined on blur
		// Not on special chars
		if (key < 48 || (key > 91 && key < 123)) {
			return;
		}
	}
	inline = FetchElement('membersuggest');
	if (name.length > 2) {
		var myConn = new ajax();
		if (!myConn) {alert(lng['ajax0']);}
		var fnWhenDone = function (oXML) {
			suggest = oXML.responseText;
			if (suggest.length > 3) {
				names = oXML.responseText.split(",");
				for (var i=0;i<names.length;i++) {
					names[i] = '<a tabindex="1'+i+'" href="javascript:ajax_smIns(\''+names[i]+'\');">'+names[i]+'</a>';
				}
				inline.innerHTML = lng['ajax7']+names.join(', ');
			}
			else {
				inline.innerHTML = '';
			}
		};
		myConn.connect("ajax.php", "GET", "action=searchmember&name="+name+sidx+ieRand(), fnWhenDone);
	}
	else {
		inline.innerHTML = '';
	}
}
// Sucht nach Nutzernamen (PN) - Einfügen d. Nutzernamens
function ajax_smIns(name) {
	inline = FetchElement('membersuggest_val');
	inline.value = name;
	inline2 = FetchElement('membersuggest');
	inline2.innerHTML = '';
}
// Sucht nach ignorierten Wörtern
function ajax_search(words, key) {
	if (typeof key == 'number') { // undefined on blur
		// Space (32), DEL (46), Backspace (8), "," (188)
		if (key != 32 && key != 8 && key != 46 && key != 188) {
			return;
		}
	}
	inline = FetchElement('searchsuggest');
	if (words.length > 2) {
		var myConn = new ajax();
		if (!myConn) {alert(lng['ajax0']);}
		var fnWhenDone = function (oXML) {
			x = oXML.responseText;
			if (x == '1') {
				inline.innerHTML = '';
			}
			else {
				ignore = x.split(",");
				if (ignore.length > 0) {
					inline.innerHTML = lng['ajax9']+ignore.join(', ');
				}
				else {
					inline.innerHTML = '';
				}
			}
		};
		myConn.connect("ajax.php", "GET", "action=search&search="+escape(words)+sidx+ieRand(), fnWhenDone);
	}
	else {
		inline.innerHTML = '';
	}
}
// Namen richtig setzen beim PM schreiben
function edit_pmto() {
	FetchElement('membersuggest_val').name = 'name';
	FetchElement('membersuggest_val2').name = 'name2';
	FetchElement('membersuggest_val').disabled = '';
	FetchElement('edit_pmto').style.display = 'none';
}

///////////////////////// MultiQuote /////////////////////////
function mq_init() {
	var cookie = mqgetCookie();
	if(cookie) {
		var values = cookie.split(',');
		for(var i = 0; i < values.length; i++) {
			var itm = FetchElement('mq_'+values[i]);
			var itml = FetchElement('mq_'+values[i]+'_link');
			if(itm) {
				itm.src = mq_img_on;
			}
			if(itml) {
				itml.innerHTML = lng['js_quote_multi_2'];
			}
		}
	}
}
function mqmakeCookie(value) {
	var cookie = mq_cookiename + '=' + escape(value) + '; ';
	document.cookie = cookie;
}
function mqgetCookie() {
	if(document.cookie == '') {
		return false;
	}

	var name = mq_cookiename;
	var firstPos;
	var lastPos;
	var cookie = document.cookie;
	firstPos = cookie.indexOf(name);
	if(firstPos != -1) {
		firstPos += name.length + 1;
		lastPos = cookie.indexOf(';', firstPos);
		if(lastPos == -1) {
			lastPos = cookie.length;
		}
		return unescape(cookie.substring(firstPos, lastPos));
	}
	else {
		return false;
	}
}
function multiquote(id) {
	img = FetchElement('mq_'+id);
	link = FetchElement('mq_'+id+'_link');
	cookie = mqgetCookie();
	values = new Array();
	newval = new Array();
	add	   = 1;

	if(cookie) {
		values = cookie.split(',');
		for(var i = 0; i < values.length; i++) {
			if(values[i] == id) {
				 add = 0;
			}
			else {
				newval[newval.length] = values[i];
			}
		}
	}
	if(add) {
		newval[newval.length] = id;
		img.src = mq_img_on;
		link.innerHTML = lng['js_quote_multi_2'];
	}
	else {
		img.src = mq_img_off;
		link.innerHTML = lng['js_quote_multi'];
	}

	mqmakeCookie(newval.join(','));
}

///////////////////////// Lightbox /////////////////////////
//By: Richard Lee - Transcendent Design - tdesignonline.com - RichardAndrewLee@yahoo.com
//Optimization: Frédéric MADSEN - MadsenFr@laposte.net
var llocation;
var lbox = document.createElement('div');
var lighterboxwrapper=document.createElement('div');
var lc=document.createElement('div');
var labox=document.createElement('div');
var portimage=document.createElement('img');
var lboxlink=document.createElement('div');
var pi=document.createElement('span');
var ni=document.createElement('span');
var ci=document.createElement('span');
var picarray=new Array();
function lb_onload() {
	lbox.id='lighterbox2';
	lbox.style.height=document.body.offsetHeight+'px';
	document.body.appendChild(lbox);
	lighterboxwrapper.id='lighterboxwrapper2';
	lbox.appendChild(lighterboxwrapper);
	lc.id='lighterboxclose2';
	lbox.appendChild(lc);
	labox.id='lighterboxcontent2';
	lighterboxwrapper.appendChild(labox);
	portimage.id='lighterboxportimage2';
	labox.appendChild(portimage);
	lboxlink.id='lighterboxclosebutton2';
	labox.appendChild(lboxlink);
	pi.id='lighterboxprevimage2';
	pi.appendChild(document.createTextNode('<'));
	lboxlink.appendChild(pi);
	ni.id='lighterboxnextimgage2';
	ni.appendChild(document.createTextNode('>'));
	lboxlink.appendChild(ni);
	ci.id='lighterboxcloseimage2';
	ci.appendChild(document.createTextNode('X'));
	ci.onclick = close;
	lboxlink.appendChild(ci);
	piclinks=document.getElementsByTagName('a');
	for(var z=0;z<piclinks.length;z++) {
		if(piclinks[z].rel.toLowerCase().match('lightbox')) {
			picarray.push(piclinks[z]);
			piclinks[z].onclick = function(){
				for(var i=0;i<picarray.length;i++) {
					if(picarray[i] == this) {
						llocation=i;
						break;
					}
				}
				setpic(this);
				return false;
			}
		}
	}
}
function close() {
	portimage.src='none'
	lbox.style.display='none'
}
function setpic(thispic) {
	var isSingle = (typeof(thispic.src) != 'undefined');
	lc.onclick = close;
	function nif(){
		if(llocation==picarray.length-1) {
			llocation=-1;
		}
		setpic(picarray[llocation+=1]);
	}
	ni.onclick=nif;
	portimage.onclick=nif;
	pi.onclick = function(){
		if(llocation==0) {
			llocation=picarray.length;
		}
		setpic(picarray[llocation-=1]);
	}
	function checkKeycode(e){
		if(lbox.style.display=='block') {
			if(e) {
				keycode = e.which;
			}
			else {
				keycode = window.event.keyCode;
			}
			if(!isSingle && keycode==37) {
				if(llocation==0) {
					llocation=picarray.length
				}
				setpic(picarray[llocation-=1]);
			}
			else if(keycode==27) {
				close();
			}
			else if(!isSingle && (keycode==39||keycode==13)) {
				nif();
			}
		}
	}
	document.onkeydown=checkKeycode;
	portimage.style.opacity='0';
	portimage.style.filter='alpha(opacity=0)';
	portimage.src = (isSingle ? thispic.src : thispic.href);
	ni.style.display = isSingle ? 'none' : 'inline';
	pi.style.display = isSingle ? 'none' : 'inline';
	portimage.onload = function() {
		lighterboxwrapper.style.width=portimage.offsetWidth+'px';
		lighterboxwrapper.style.marginLeft='-'+labox.offsetWidth/2+'px';
		lighterboxwrapper.style.marginTop='-'+labox.offsetHeight/2+'px';
		for(var fd=0;fd<11;fd++) {
			setTimeout('portimage.style.opacity="'+fd/10+'";portimage.style.filter="alpha(opacity='+(fd*10)+')";',fd*50);
		}
	}
	lbox.style.display='block';
}
window.onscroll=function(){
	if(lbox.style.display=='block') {
		lbox.style.left=(document.documentElement.scrollLeft||document.body.scrollLeft)+'px';
	}
}

LightBoxCallback = setpic;
LightBoxOnload = lb_onload;