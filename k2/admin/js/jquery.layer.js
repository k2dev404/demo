(function ($) {
	$.layer = function (par) {
		$('#layer').remove();
		query = par.query || {};
		title = par.title || {};
		iframe = par.iframe || false;
		w = par.w || 0;
		h = par.h || 0;

		if (typeof(arguments[1]) != 'undefined') {
			par.callback = arguments[1];
		}

		if(iframe){
			if(!h){
				h = 300;
			}
			html = '<div id="layer">' +
				'<div class="box">' +
				'<div class="title">' +
				'<div class="close"></div>' + title + '</div>' +
				'<div class="boxInside"><iframe src="' + par.get + '" width="100%" height="100%" frameborder="no"></iframe> </div>' +
				'</div>' +
				'</div>';

			$(html).appendTo('body');

			layer = $('#layer');

			if (par.w) {
				layer.width(par.w);
			}
			if (par.h) {
				layer.height(par.h);
			}

			var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
			var scrolLeft = document.body.scrollLeft || document.documentElement.scrollLeft;

			layer.css({
				'left': Math.ceil(($('body').width() / 2) - (layer.width() / 2) + scrolLeft),
				'top': Math.ceil(($('body').height() / 2) - (layer.height() / 2) + scrollTop)
			});
			layer.show();

			layer.draggable({
				cursor: 'move',
				revert: false,
				addClasses: false,
				handle: $('#layer .title'),
				iframeFix: true,
				scroll: false,
				containment: 'document'
			});

			if (par.resize) {
				if (!par.resizeHeight) {
					par.resizeHeight = 102;
				}
				layer.resizable({
					minWidth: layer.width(),
					minHeight: layer.height(),
					resize: function (event, ui) {
						$('.boxInside').height($('#layer').height() - par.resizeHeight);
					}
				});
			}

			if (par.callback) {
				par.callback();
			}
			$('.close', layer).click(function () {
				$('#layer').remove();
			});

			return;
		}



		$.post(par.get, query, function (data) {
			html = '<div id="layer"><div class="box"><div class="title"><div class="close"></div>' + title + '</div><div class="boxInside">' + data + '</div></div></div>';
			$(html).appendTo("body");

			layer = $('#layer');

			if (par.w) {
				layer.width(par.w);
			}
			if (par.h) {
				layer.height(par.h);
			}

			var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
			var scrolLeft = document.body.scrollLeft || document.documentElement.scrollLeft;

			layer.css({
				'left': Math.ceil(($('body').width() / 2) - (layer.width() / 2) + scrolLeft),
				'top': Math.ceil(($('body').height() / 2) - (layer.height() / 2) + scrollTop)
			});
			layer.show();

			layer.draggable({
				cursor: 'move',
				revert: false,
				addClasses: false,
				handle: $('#layer .title'),
				iframeFix: true,
				scroll: false,
				containment: 'document'
			});

			if (par.resize) {
				if (!par.resizeHeight) {
					par.resizeHeight = 102;
				}
				layer.resizable({
					minWidth: layer.width(),
					minHeight: layer.height(),
					resize: function (event, ui) {
						$('.boxInside').height($('#layer').height() - par.resizeHeight);
					}
				});
			}

			if (par.callback) {
				par.callback();
			}
			$('.close', layer).click(function () {
				$('#layer').remove();
			});
		});
		return false;
	}

	$.prompt = function (_this) {
		par = {
			title: 'Системное сообщение',
			'text': 'Действительно удалить?',
			'href': _this.href,
			'yes': '',
			'no': '$(\'#layer\').remove();'
		};
		if (typeof(arguments[1]) != 'undefined') {
			$.extend(par, arguments[1]);
		}

		html = '<div id="layer" class="prompt"><div class="box"><div class="title"><div class="close"></div>' + par.title + '</div><div class="boxInside"><div class="text">' + par.text + '</div><div class="question"><a href="' + par.href + '" onclick="' + par.yes + '" class="sub yes">Да</a><a class="sub no" onclick="' + par.no + '">Нет</a></div></div></div></div>';
		$(html).appendTo("body");

		layer = $('#layer');
		layer.show();

		$('.close', layer).click(function () {
			$(layer).remove();
		});

		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var scrolLeft = document.body.scrollLeft || document.documentElement.scrollLeft;

		layer.css({
			'left': Math.ceil(($('body').width() / 2) - (layer.width() / 2) + scrolLeft),
			'top': Math.ceil(($('body').height() / 2) - (layer.height() / 2) + scrollTop)
		});

		layer.draggable({
			cursor: 'move',
			revert: false,
			addClasses: false,
			handle: $('#layer .title'),
			iframeFix: true,
			scroll: false,
			containment: 'document'
		});

		return false;
	}

	$.alert = function () {
		par = {title: 'Системное сообщение'};
		if (typeof(arguments[0]) != 'undefined') {
			$.extend(par, arguments[0]);
		}

		html = '<div id="layer" class="alert"><div class="box"><div class="title"><div class="close"></div>' + par.title + '</div><div class="boxInside"><div class="text">' + par.text + '</div><div class="question"><a href="" onclick="$(\'.alert\').remove(); return false;" class="sub">Ок</a></div></div></div></div>';
		$(html).appendTo("body");

		layer = $('#layer');
		layer.show();

		$('.close', layer).click(function () {
			$(layer).remove();
		});

		var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
		var scrolLeft = document.body.scrollLeft || document.documentElement.scrollLeft;

		layer.css({
			'left': Math.ceil(($('body').width() / 2) - (layer.width() / 2) + scrolLeft),
			'top': Math.ceil(($('body').height() / 2) - (layer.height() / 2) + scrollTop)
		});

		layer.draggable({
			cursor: 'move',
			revert: false,
			addClasses: false,
			handle: $('#layer .title'),
			iframeFix: true,
			scroll: false,
			containment: 'document'
		});

		return false;
	}
})(jQuery);
