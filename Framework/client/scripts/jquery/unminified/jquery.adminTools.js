(function($){
	$.fn.FieldParamLoader = function(url) {
		var last = '',
		load = function(url) {
			var typeName = $('#type').val();
			if (last != typeName) {
				$('#params').html('Lade Daten...');
				$('#submit').attr("disabled", "disabled");
				$.ajax({
					url: url,
					data: {type: typeName},
					success: function(data){
						if (data == '') {
							$('#params').html('Bitte Feldtyp w�hlen...');
						}
						else {
							$('#params').html(data);
							$('#submit').removeAttr("disabled");
						}
					}
				});
			}
			last = typeName;
		};
		return this.each(function() {
			$(this).change( function() { load(url); } );
			load(url);
		});
	};
	$.fn.AdminMenu = function() {
		return this.each(function() {
			$(this).children('dt').click(
				function() {
					$(this).next('dd').animate({
						opacity: 'toggle',
						height: 'toggle'
						}, 500
					);
				}
			);
		});
	};
	$.fn.UriCreator = function(targetId) {
		return this.each(function() {
			$(this).keyup(function() {
				var str = $(this).val().toLowerCase();
				str = str.replace(/[������]/, 'a');
				str = str.replace(/[��]/, 'c');
				str = str.replace(/[��������]/, 'e');
				str = str.replace(/[��������]/, 'i');
				str = str.replace(/[������]/, 'o');
				str = str.replace(/[������]/, 'u');
				str = str.replace(/[��]/, 'ae');
				str = str.replace(/[��]/, 'oe');
				str = str.replace(/[��]/, 'ue');
				str = str.replace('�', 'ss');
				str = str.replace(/[^\d\w\-]/i, '-');
				str = str.replace(/-+/, '-');
				$(targetId).val(str);
			});
		});
	};
})(jQuery);