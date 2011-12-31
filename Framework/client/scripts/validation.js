$(document).ready(function() {
	for (var key in clientValidation) {
		if (clientValidation.hasOwnProperty(key)) {
			$('*[name=' + clientValidation[key] + ']')
			.blur(function(){
				checkField($(this));
			})
			.focus(function(){
				$(this).removeClass('input_ok');
				$(this).removeClass('input_error');
			});
		}
	}
});

function checkField(field) {
	$('#input_msg').fadeOut("slow").remove();
	// Start new request
	var url = field.closest('form').attr('action');
	var name = encodeURIComponent(field.attr('name'));
	var val = encodeURIComponent(field.val());
	$.ajax({
		url: url,
		data: "ajax="+name+"&"+name+"="+val,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			// Highlight field with appropriate styles
			if (data.hasOwnProperty('valid')) {
				if (data['valid']) {
					field.addClass('input_ok');
				}
				else {
					field.addClass('input_error');
				}
			}
			// Show error message
			if (data.hasOwnProperty('messages')) {
				if (data['messages'].length > 0) {
					var top = field.position().top + field.outerHeight();
					$('body').append( '<div id="input_msg"><div id="input_msg_img"></div><div id="input_msg_txt">' + data['messages'].join("<br />") + '</div></div>' );
					$('#input_msg').css("top", top+"px").css("left", field.position().left+"px").fadeIn("slow");
				}
			}
		}
	});
}