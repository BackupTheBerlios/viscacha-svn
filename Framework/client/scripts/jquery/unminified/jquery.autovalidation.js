(function($) {

	var methods = {
		reset: function(field) {
			field.removeClass('input_ok');
			field.removeClass('input_error');
		},
		hide: function() {
			$('#input_msg').fadeOut("slow");
		},
		check: function(field) {
			methods.hide(field);
			methods.reset(field);
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
							$('#input_msg').remove();
							$('body').append( '<div id="input_msg"><div id="input_msg_txt">' + data['messages'].join("<br />") + '</div><div id="input_msg_img"></div></div>' );
							$('#input_msg').mouseover(function(){ hideTip(); });
							$('#input_msg').css("top", (field.position().top - $('#input_msg').height())+"px").css("left", field.position().left+"px").fadeIn("slow");
						}
					}
				}
			});
		}
	};

	$.fn.AutoValidator = function(fields) {
		for (var key in fields) {
			if (fields.hasOwnProperty(key)) {
				$('*[name=' + fields[key] + ']')
				.blur(function(){
					methods.check($(this));
				})
				.focus(function(){
					methods.hide();
					methods.reset($(this));
				});
			}
		}
	};

})(jQuery);