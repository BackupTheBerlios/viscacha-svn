/**
 * jQuery Rating Plugin
 *
 * Turns a select box into a star rating control.
 *
 * Author:       Chris Richards
 * Contributors: Dovy Paukstys
 *               Adrian Macneil
 */

(function ($) {

	$.fn.rating = function(options) {
		var settings =
		{
			showCancel: true,
			cancelValue: null,
			cancelTitle: "Cancel",
			startValue: null,
			disabled: false
		};
		if (options) { $.extend( settings, options); }

		var methods = {
		   hoverOver: function(evt) {
				var elm = $(evt.target);
				if (elm.hasClass("ui-rating-cancel")) {
					elm.attr("class", "ui-rating-cancel ui-rating-cancel-full");
				} else {
					elm.prevAll().andSelf()
						.not(".ui-rating-cancel")
						.addClass("ui-rating-hover");
				}
			},
			hoverOut: function(evt) {
				var elm = $(evt.target);
				if (elm.hasClass("ui-rating-cancel")) {
					elm.attr("class", "ui-rating-cancel ui-rating-cancel-empty");
				} else {
					elm.prevAll().andSelf()
						.not(".ui-rating-cancel")
						.removeClass("ui-rating-hover");
				}
			},
			click: function(evt) {
				var elm = $(evt.target);
				var value = settings.cancelValue;
				if (elm.hasClass("ui-rating-cancel")) {
					elm.siblings(".ui-rating-star")
						.attr("class", "ui-rating-star ui-rating-empty");
					elm.attr("class", "ui-rating-cancel ui-rating-cancel-empty");
				} else {
					elm.prevAll().andSelf().not(".ui-rating-cancel")
						.attr("class", "ui-rating-star ui-rating-full");
					elm.nextAll().not(".ui-rating-cancel")
						.attr("class", "ui-rating-star ui-rating-empty");
					elm.siblings(".ui-rating-cancel")
						.attr("class", "ui-rating-cancel ui-rating-cancel-empty");
					value = elm.attr("data-value");
				}

				if (!evt.data.hasChanged) {
					$(evt.data.selectBox).val(value).trigger("change");
				}
			},
			change: function(evt) {
				methods.setValue($(this).val(), evt.data.container, evt.data.selectBox);
			},
			setValue: function(value, container, selectBox) {
				var evt = {"target": null, "data": {}};

				evt.target = $(".ui-rating-star[data-value="+ value +"]", container);
				evt.data.selectBox = selectBox;
				evt.data.hasChanged = true;
				methods.click(evt);
			}
		};

		return this.each(function() {
			var self = $(this);

			if ('select-one' !== this.type) { return; }
			if (self.data('rating-loaded')) { return; }

			self.hide();
			self.data('rating-loaded', true);

			var elm = $(document.createElement("div")).attr({
				"class": "ui-rating"
			}).insertAfter(self);
			$('option', self).each(function() {
				if (this.value !== "") {
					$(document.createElement("a")).attr({
						"class": "ui-rating-star ui-rating-empty",
						"title": $(this).text(),
						"data-value": this.value
					}).appendTo(elm);
				}
			});
			if (settings.showCancel) {
				$(document.createElement("a")).attr({
					"class": "ui-rating-cancel ui-rating-cancel-empty",
					"title": settings.cancelTitle
				}).appendTo(elm);
			}
			if (self.val() !== "") {
				methods.setValue(self.val(), elm, self);
			} else {
				var value = settings.startValue !== null ? settings.startValue : settings.cancelValue;
				methods.setValue(value, elm, self);
				self.val(value);
			}

			if (settings.disabled === false && self.is(":disabled") === false) {
				$(elm).bind("mouseover", methods.hoverOver)
					.bind("mouseout", methods.hoverOut)
					.bind("click", {"selectBox": self}, methods.click);
			}

			self.bind("change", {"selectBox": self, "container": elm}, methods.change);
		});
	};
})(jQuery);