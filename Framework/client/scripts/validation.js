$(document).ready(function() {
	for (var key in clientValidation) {
		if (clientValidation.hasOwnProperty(key)) {
			$('*[name=' + clientValidation[key] + ']').blur(function(){
				checkField($(this));
			});
		}
	}
});

function checkField(field) {
	field.removeClass('input_ok');
	field.removeClass('input_error');
	var url = field.closest('form').attr('action');
	var name = encodeURIComponent(field.attr('name'));
	var val = encodeURIComponent(field.val());
	$.ajax({
		url: url,
		data: "ajax="+name+"&"+name+"="+val,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if (data.hasOwnProperty('valid')) {
				if (data['valid']) {
					field.addClass('input_ok');
				}
				else {
					field.addClass('input_error');
				}
			}
			if (data.hasOwnProperty('messages')) {
/*				if ($.isArray(data['messages'])) {
					for (var msg in data['messages']) {
						if (msg.length > 0) {
							alert(msg);
						}
					}
				}
				else */if (data['messages'].length > 0) {
					alert(data['messages']);
				}
			}
		}
	});
}