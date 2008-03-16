/********************************************************************
 * openWYSIWYG popup functions Copyright (c) 2006 openWebWare.com
 * Contact us at devs@openwebware.com
 * This copyright notice MUST stay intact for use.
 *
 * $Id: wysiwyg-popup.js,v 1.2 2007/01/22 23:45:30 xhaggi Exp $
 ********************************************************************/
var WYSIWYG_Popup = {

	/**
	 * Return the value of an given URL parameter.
	 *
	 * @param param Parameter
	 * @return Value of the given parameter
	 */
	getParam: function(param) {
	  var query = window.location.search.substring(1);
	  var parms = query.split('&');
	  for (var i=0; i<parms.length; i++) {
	    var pos = parms[i].indexOf('=');
	    if (pos > 0) {
	       var key = parms[i].substring(0,pos).toLowerCase();
	       var val = parms[i].substring(pos+1);
	       if(key == param.toLowerCase())
	       	return val;
	    }
	  }
	  return null;
	}
}

// close the popup if the opener does not hold the WYSIWYG object
if(!window.opener) window.close();

// bind objects on local vars
var WYSIWYG = window.opener.WYSIWYG;
var WYSIWYG_Core = window.opener.WYSIWYG_Core;
var WYSIWYG_Table = window.opener.WYSIWYG_Table;


/* ---------------------------------------------------------------------- *\
  Function    : insertImage()
  Description : Inserts image into the WYSIWYG.
\* ---------------------------------------------------------------------- */
function insertImage() {
	var n = WYSIWYG_Popup.getParam('wysiwyg');

	// get values from form fields
	var src = document.getElementById('src').value;
	var alt = document.getElementById('alt').value;
	var width = document.getElementById('width').value
	var height = document.getElementById('height').value
	var border = document.getElementById('border').value
	var align = document.getElementById('align').value
	var vspace = document.getElementById('vspace').value
	var hspace = document.getElementById('hspace').value

	// insert image
	WYSIWYG.insertImage(src, width, height, align, border, alt, hspace, vspace, n);
  	window.close();
}

/* ---------------------------------------------------------------------- *\
  Function    : loadImage()
  Description : load the settings of a selected image into the form fields
\* ---------------------------------------------------------------------- */
function loadImage() {
	var n = WYSIWYG_Popup.getParam('wysiwyg');

	// get selection and range
	var sel = WYSIWYG.getSelection(n);
	var range = WYSIWYG.getRange(sel);

	// the current tag of range
	var img = WYSIWYG.findParent("img", range);

	// if no image is defined then return
	if(img == null) return;

	// assign the values to the form elements
	for(var i = 0;i < img.attributes.length;i++) {
		var attr = img.attributes[i].name.toLowerCase();
		var value = img.attributes[i].value;
		//alert(attr + " = " + value);
		if(attr && value && value != "null") {
			switch(attr) {
				case "src":
					// strip off urls on IE
					if(WYSIWYG_Core.isMSIE) value = WYSIWYG.stripURLPath(n, value, false);
					document.getElementById('src').value = value;
				break;
				case "alt":
					document.getElementById('alt').value = value;
				break;
				case "align":
					selectItemByValue(document.getElementById('align'), value);
				break;
				case "border":
					document.getElementById('border').value = value;
				break;
				case "hspace":
					document.getElementById('hspace').value = value;
				break;
				case "vspace":
					document.getElementById('vspace').value = value;
				break;
				case "width":
					document.getElementById('width').value = value;
				break;
				case "height":
					document.getElementById('height').value = value;
				break;
			}
		}
	}

	// get width and height from style attribute in none IE browsers
	if(!WYSIWYG_Core.isMSIE && document.getElementById('width').value == "" && document.getElementById('width').value == "") {
		document.getElementById('width').value = img.style.width.replace(/px/, "");
		document.getElementById('height').value = img.style.height.replace(/px/, "");
	}
}

/* ---------------------------------------------------------------------- *\
  Function    : selectItem()
  Description : Select an item of an select box element by value.
\* ---------------------------------------------------------------------- */
function selectItemByValue(element, value) {
	if(element.options.length) {
		for(var i=0;i<element.options.length;i++) {
			if(element.options[i].value == value) {
				element.options[i].selected = true;
			}
		}
	}
}