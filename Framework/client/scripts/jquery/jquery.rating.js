//
// rating Plugin
// By Chris Richards
// Last Update: 6/21/2011
// Turns a select box into a star rating control.
//

(function ($) { 
	
	$.fn.rating = function(options)
	{
		var settings =
		{
			showCancel: true,
			cancelValue: null,
			cancelTitle: "Cancel",
			startValue: null,
			disabled: false
		};
		var methods = {
		   hoverOver: function(evt)
			{
				var elm = $(evt.target);
				if( elm.hasClass("ui-rating-cancel") ) {
					elm.addClass("ui-rating-cancel-full");
				} 
				else {
					elm.prevAll().andSelf()
						.not(".ui-rating-cancel")
						.addClass("ui-rating-hover");
				}
			},
			hoverOut: function(evt)
			{
				var elm = $(evt.target);
				//Are we over the Cancel or the star?
				if( elm.hasClass("ui-rating-cancel") ) {
					elm.addClass("ui-rating-cancel-empty")
						.removeClass("ui-rating-cancel-full");
				}
				else {
					elm.prevAll().andSelf()
						.not(".ui-rating-cancel")
						.removeClass("ui-rating-hover");
				}
			},
			click: function(evt)
			{
				var elm = $(evt.target);
				var value = settings.cancelValue;
				//Are we over the Cancel or the star?
				elm.parents(".content-box-content:first").removeClass('formerror');
				if( elm.hasClass("ui-rating-cancel") ) {
					methods.empty(elm, elm.parent());
				}
				else {
					elm.closest(".ui-rating-star").prevAll().andSelf()
						.not(".ui-rating-cancel")
						.prop("className", "ui-rating-star ui-rating-full");
					elm.closest(".ui-rating-star").nextAll()
						.not(".ui-rating-cancel")
						.prop("className", "ui-rating-star ui-rating-empty");
					elm.siblings(".ui-rating-cancel")
						.prop("className", "ui-rating-cancel ui-rating-cancel-empty");
					value = elm.attr("value");
				}
				if( !evt.data.hasChanged ) {
					$(evt.data.selectBox).val( value ).trigger("change");
				}
			},
			change: function(evt)
			{
				var value = $(this).val();
				methods.setValue(value, evt.data.container, evt.data.selectBox);
//				if (isDirty)
//					isDirty = true;
			},
			setValue: function(value, container, selectBox)
			{
				var evt = {"target": null, "data": {}};
				evt.target = $(".ui-rating-star[id="+ selectBox.attr('id') + "_" + value + "]", container);
				evt.data.selectBox = selectBox;
				evt.data.hasChanged = true;
				methods.click(evt);
			},
			empty: function(elm, parent)
			{
				parent.find('.ui-rating-star').removeClass('ui-rating-full');
				parent.find('.ui-rating-star').addClass('ui-rating-empty');				
				elm.prop("className", "ui-rating-cancel ui-rating-cancel-empty")
					.nextAll().prop("className", "ui-rating-star ui-rating-empty");
			}
 
		};

		return this.each(function() {
			var self = $(this), elm, val;
			if ('select-one' !== this.type) { return; }
			if (self.prop('hasProcessed')) { return; }
			if (options) { $.extend( settings, options); }
			self.hide();
			self.prop('hasProcessed', true);
			elm = $("<div/>").prop({
				className: "ui-rating"
			}).insertAfter( self );
			$('option', self).each(function() {
				if(this.value!="") {
					$("<a/>").prop({
						className: "ui-rating-star ui-rating-empty",
						title: $(this).text(),
						id: self.attr('id') + '_' + this.value
					}).appendTo(elm);
				}
			});
			if (true == settings.showCancel) {
				$("<a/>").prop({
					className: "ui-rating-cancel ui-rating-cancel-empty",
					title: settings.cancelTitle
				}).appendTo(elm);
			}
			if ( 0 !==  $('option:selected', self).size() ) {
				methods.setValue( self.val(), elm, self );
			} else {
				val = null !== settings.startValue ? settings.startValue : settings.cancelValue;
				methods.setValue( val, elm, self );
				self.val(val);
			}
			if( true !== settings.disabled && self.prop("disabled") !== true ) {
				$(elm).bind("mouseover", methods.hoverOver)
					.bind("mouseout", methods.hoverOut)
					.bind("click",{"selectBox": self}, methods.click);
			}	
			self.bind("change", {"selectBox": self, "container": elm},  methods.change);
		});
	};
})(jQuery);