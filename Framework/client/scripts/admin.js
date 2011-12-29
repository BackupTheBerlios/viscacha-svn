var lastTypeName = '';

function loadFieldParams(url) {
	var typeName = $('#type').val();
	if (lastTypeName != typeName) {
		$('#params').html('Lade Daten...');
		$('#submit').attr("disabled", "disabled");
		$.ajax({
			url: url,
			data: {type: typeName},
			success: function(data){
				if (data == '') {
					$('#params').html('Bitte Feldtyp wählen...');
				}
				else {
					$('#params').html(data);
					$('#submit').removeAttr("disabled");
				}
			}
		});
	}
	lastTypeName = typeName;
}

function buildUri(baseId, targetId) {
	var base = document.getElementById(baseId);
	var target = document.getElementById(targetId);
	var str = base.value.toLowerCase();
	str = str.replace(/[áàâÁÀÂ]/, 'a');
	str = str.replace(/[çÇ]/, 'c');
	str = str.replace(/[éèëêÉÈËÊ]/, 'e');
	str = str.replace(/[íìîïÍÌÎÏ]/, 'i');
	str = str.replace(/[óòôÓÒÔ]/, 'o');
	str = str.replace(/[úùûÚÙÛ]/, 'u');
	str = str.replace(/[äÄ]/, 'ae');
	str = str.replace(/[öÖ]/, 'oe');
	str = str.replace(/[üÜ]/, 'ue');
	str = str.replace('ß', 'ss');
	str = str.replace(/[^\d\w\-]/i, '-');
	str = str.replace(/-+/, '-');
	target.value = str;
}

// MENU

var DDSPEED = 10;
var DDTIMER = 15;

// main function to handle the mouse events //
function ddMenu(id){
	var h = document.getElementById(id + '-ddheader');
	var c = document.getElementById(id + '-ddcontent');
	var d = c.style.opacity > 0.1 ? -1 : 1;
	clearInterval(c.timer);
	if(d == 1){
		clearTimeout(h.timer);
		if(c.maxh && c.maxh <= c.offsetHeight){
			return;
		} else if(!c.maxh){
			c.style.display = 'block';
			c.style.height = 'auto';
			c.maxh = c.offsetHeight;
			c.style.height = '0px';
		}
		c.timer = setInterval(function(){ddSlide(c,1)},DDTIMER);
	} else{
		h.timer = setTimeout(function(){ddCollapse(c)},50);
	}
}

// collapse the menu //
function ddCollapse(c){
	c.timer = setInterval(function(){ddSlide(c,-1)},DDTIMER);
}

// incrementally expand/contract the dropdown and change the opacity //
function ddSlide(c,d){
	var currh = c.offsetHeight;
	var dist;
	if(d == 1){
		dist = Math.round((c.maxh - currh) / DDSPEED);
		if(dist <= 1){
			dist = 1;
		}
	} else {
		dist = Math.ceil(currh / DDSPEED);
	}
	c.style.height = currh + (dist * d) + 'px';
	c.style.opacity = currh * 1 / c.maxh;
	c.style.filter = 'alpha(opacity=' + (currh * 100 / c.maxh) + ')';
	if((currh < 2 && d != 1) || (currh > (c.maxh - 2) && d == 1)) {
		clearInterval(c.timer);
	}
}