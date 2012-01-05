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
							$('#params').html('Bitte Feldtyp wählen...');
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
				$(targetId).val(str);
			});
		});
	};
})(jQuery);