jQuery.fn.extend({
	disableSelect: function () {
		this.each(function () {
			this.onselectstart = function () {
				return false;
			};
			this.unselectable = "on";
			jQuery(this).css('-moz-user-select', 'none');
		});
	}, enableSelect: function () {
		this.each(function () {
			this.onselectstart = function () {
			};
			this.unselectable = "off";
			jQuery(this).css('-moz-user-select', 'auto');
		});
	}
});

$.fn.emptySelect = function () {
	return this.each(function () {
		if (this.tagName == 'SELECT') this.options.length = 0;
	});
}

$.fn.loadSelect = function (optionsDataArray, emptySelect) {
	if (emptySelect) {
		this.emptySelect();
	}
	return this.each(function () {
		if (this.tagName == 'SELECT') {
			var selectElement = this;
			$.each(optionsDataArray, function (index, optionData) {
				var option = new Option(optionData.caption, optionData.value);
				if ($.browser.msie) {
					selectElement.add(option);
				} else {
					selectElement.add(option, null);
				}
			});
			return selectChange(this);
		}
	});
}

function treeStart(e) {
	w = e.clientX;
	if (w < 250) {
		w = 250;
	}
	$('#tree').css('width', w);
	$('#tree-box').css('width', w);
}

function selectChange(_this) {
	var value = $(_this).val();
	var child = $(_this).attr("child");
	var select_id = $(_this).attr("select_id");
	var sel = $("select[name=" + child + "]");
	$.getJSON("/k2/select.php", {"SELECT": select_id, "PARENT": value}, function (data) {
		if (data.length) {
			sel.loadSelect([{value: 0, caption: "Выбрать"}], 1);
			sel.loadSelect(data, 0);
			sel[0].disabled = false;
		} else {
			sel.loadSelect([{value: 0, caption: "Недоступно"}], 1);
			sel[0].disabled = true;
		}
	});
	return false;
}

function checkBox(_this, type, i) {
	p = $(_this).parent();
	$('a', p).removeClass('check');
	$(_this).addClass('check');
	$('#align' + i).val(type);
	return false;
}

var key = 0;

$(document).keydown(function (e) {
	var event = e || window.event;
	key = event.keyCode;
	if (key == 27) {
		$('#layer, #context-menu').remove();
		return false;
	}
	if (event.ctrlKey && key == 39) {
		elm = $('.navPage .next');
		if (elm.length && (href = elm.attr('href'))) {
			location.href = elm.attr('href');
		}
	}
	if (event.ctrlKey && key == 37) {
		elm = $('.navPage .prev');
		if (elm.length && (href = elm.attr('href'))) {
			location.href = elm.attr('href');
		}
	}
});

function setComponent(field) {
	var component = $('#setComponent');
	$.layer({
		'get': '/k2/admin/system/component/component.php?field=' + field + '&component=' + $('#setComponent').val(),
		'title': 'Компонент &laquo;' + $('#setComponent option:selected').text() + '&raquo;',
		w: 800,
		'resize': 1
	});
}

function actionDelete(action) {
	if (action) {
		$('#form').attr('action', $('#action').val() + '.php').submit();
	} else {
		$('#layer').remove();
		$('#action').val('0');
	}
	return false;
}

var tree_move_elm = false;

var k2 = {
	transcription: {
		ru_str: "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя",
		en_str: ['A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'CH', 'SH', 'SHH', String.fromCharCode(35), 'I', String.fromCharCode(39), 'JE', 'JU', 'JA', 'a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shh', String.fromCharCode(35), 'i', String.fromCharCode(39), 'je', 'ju', 'ja'],
		translit: function (org_str) {
			var tmp_str = "";
			for (var i = 0, l = org_str.length; i < l; i++) {
				var s = org_str.charAt(i),
					n = k2.transcription.ru_str.indexOf(s);
				if (n >= 0) {
					tmp_str += k2.transcription.en_str[n];
				}
				else {
					tmp_str += s;
				}
			}
			return tmp_str;
		},
		par: {},
		timer: null,
		init: function () {
			if (typeof(arguments[0]) != 'undefined') {
				k2.transcription.par = arguments[0];
			}
			$('#transcription-from').keyup(function () {
				k2.transcription.begin();
				/*k2.transcription.timer = setTimeout(function(){
				 k2.transcription.begin();
				 }, 2000);*/
			});
			$('#transcription-from').keypress(function () {
				/*clearTimeout(k2.transcription.timer);*/
			});
		},
		begin: function () {
			if (!k2.transcription.par.ignore_cookie) {
				if ($.cookie('K2_TRANSCRIPTION') == 1) {
					return false;
				}
			}
			text = $('#transcription-from').val();
			text = k2.transcription.translit(text);
			text = text.replace(/(^\s+)|(\s+$)/g, '');
			text = text.replace(/(\&#\d+;)+/g, '');
			text = text.replace(/[^a-zA-Z0-9\-\_ ]+/g, '');
			text = text.replace(/[ ]+/g, '-');
			text = text.replace(/[-]+/g, '-');
			text = text.toLowerCase();

			if (text && k2.transcription.par.preffix) {
				text = k2.transcription.par.preffix + text;
			}
			if (k2.transcription.par.suffix) {
				text = text + k2.transcription.par.suffix;
			}
			$('#transcription-to').val(text);
		},
		lock: function (_this) {
			cookie = $.cookie('K2_TRANSCRIPTION');
			if (cookie == null || cookie == 0) {
				$(_this).removeClass('unlock').addClass('lock');
				$.cookie('K2_TRANSCRIPTION', '1');
			} else {
				$(_this).removeClass('lock').addClass('unlock');
				$.cookie('K2_TRANSCRIPTION', '0');
			}
			return false;
		}
	},
	template: {
		add: function () {
			k2.transcription.init({'igonere_cookie': true, 'preffix': 'template-', 'suffix': '.php'});
			k2.form.submit('#template-form');
		},
		copy: function () {
			k2.transcription.init({'igonere_cookie': true});
			k2.form.submit('#template-form');
		}
	},
	bookmark: function () {
		k2.form.submit('#bookmark-form');
	},
	group: {
		add: function () {
			k2.form.submit('#group-form');
		},
		edit: function () {
			k2.form.submit('#group-form');
		}
	},
	form: {
		submit: function (id) {
			$(id).submit(function () {
				k2.form.transmit = true;
				$('input[required]', this).each(function () {
					val = $(this).val();
					if (!val.length) {
						k2.form.transmit = false;
						return false;
					}
				});
				return k2.form.transmit;
			});
			k2.form.autofocus();
		},
		autofocus: function (id) {
			$('input[autofocus]', id).each(function () {
				$(this)[0].focus();
			});
		}
	},
	tab: function () {

	},
	exclamation: function (obj) {
		$(function () {
			$('body').prepend('<div id="exclamation">' + obj.text + '</div>');
		});
	},
	block: {
		sort: function (section) {
			$('.sf-body').tableDnD({
				dragHandle: 'sf-td',
				onDrop: function (tbl, row) {
					var sortId = '';
					$('tr', tbl).each(function () {
						sortId += $(this).attr('sort_id') + ',';
					});
					$.post('/k2/admin/section/block/move.php', {'ROW': sortId});
				}
			});
			$('.sf-body tr').hover(function () {
				$(this.cells[0]).addClass('sf-active');
			}, function () {
				$(this.cells[0]).removeClass('sf-active');
			});
		}
	},
	component: {
		icon: function () {
			$('#componentTable td').hover(function () {
				$(this).addClass('hover');
			}, function () {
				$(this).removeClass('hover');
			});
			$('#componentTable img').click(function () {
				name = $(this).attr('name');
				$('#componentIconValue').val(name);
				$('#componentIcon').attr('src', '/k2/admin/i/component/' + name + '.png');
				$('#layer').remove();
				return false;
			});
		},
		set: function (field) {
			var component = $('#setComponent');
			$.layer({
				'get': '/k2/admin/system/component/component.php?field=' + field + '&component=' + $('#setComponent').val(),
				'title': 'Компонент &laquo;' + $('#setComponent option:selected').text() + '&raquo;',
				w: 800,
				'resize': 1
			});
		},
		past: function (par) {
			var mceId = 'f' + par.field;
			html = '<img src="/k2/dev/component/' + par.component + '/static/icon.gif?' + (Math.random() * 1) + '" class="mceItemNoResize" component="' + par.component + ':' + par.collection + '" />';
			if (tinyMCE.get(mceId).selection.getContent()) {
				tinyMCE.get(mceId).execCommand('mceReplaceContent', false, html);
			} else {
				tinyMCE.get(mceId).execCommand('mceInsertContent', false, html);
			}
			$.post('/k2/admin/system/component/field.php', {
				'component': par.component,
				'field': par.field,
				'collection': par.collection
			});
			$('#layer').remove();
		}
	},
	tool: {
		layer: function (id) {
			if ($('#layer-' + id).css('display') == 'none') {
				$('#layer-' + id).show();
			} else {
				$('#layer-' + id).hide();
			}
			return false;
		}
	}
}

var fm = {
	layer: function (id, _this) {
		$('.fm-upload-layer').hide();
		$('.fm-upload-panel a').removeClass('fm-upload-panel-active');
		$(_this).attr({'class': 'fm-upload-panel-active'});
		k2.tool.layer(id);
	},
	click: function (path) {
		setTimeout(function () {
			location.href = path;
		}, 400);
		return false;
	},
	dblclick: function (field, obj) {
		tinyMCE = window.parent.tinyMCE;
		if (tinyMCE.get(field).selection.getContent()) {
			tinyMCE.get(field).execCommand('mceReplaceContent', false, '<a href="' + obj.path + '">{$selection}</a>');
			$('#layer', window.parent.document).remove();
		} else if (obj.width > 0) {
			tinyMCE.get(field).execCommand('mceInsertContent', false, '<img src="' + obj.path + '" width="' + obj.width + '" height="' + obj.height + '">');
			$('#layer', window.parent.document).remove();
		}
		return false;
	}
}

var table = {
	sort: function (type) {
		var c = 'table';
		if (typeof(arguments[1]) != 'undefined') {
			c = arguments[1];
		}
		$('.' + c + ' tr').hover(function () {
			if (!$(this).hasClass('noblick')) {
				$('td.sf-td', this).addClass('sf-active');
			}
		}, function () {
			$(this.cells[0]).removeClass('sf-active');
		});
		$('.sf-body').tableDnD({
			dragHandle: 'sf-td',
			onDrop: function (tbl, row) {
				if (type != -1) {
					var group = '';
					var field = '';
					$('tr', tbl).each(function () {
						group += ($(this).hasClass('group') == true) + ',';
						field += $(this).attr('field') + ',';
					});
					if (type) {

					} else {
						$.post('/k2/admin/dev/field/sort.php', {'GROUP': group, 'FIELD': field});
					}
				}
			}
		});
	},
	check: {
		all: function (_this, tbl) {
			$('input[type=checkbox]', tbl)[$(_this)[0].checked ? 'prop' : 'removeProp']('checked', 'checked');
		}
	}
}

var tree = {
	elm_to: false,
	code: 0,
	elm: false,
	parent: false,
	first: false,
	start: function (e) {
		if (e.button != 0 && e.button != 1) {
			return false;
		}
		$(this.elm).disableSelect();
		$('body').disableSelect();
		$('body').bind('mousemove', tree.move);
		return false;
	},
	move: function (e) {
		if (!tree.first) {
			tree.first = true;
			$(tree.parent).addClass('tree-move');
			$('.tree-section').append('<i class="tree-move-border"></i>');
			$('.tree-move-border').live('mouseover mouseout', function (event) {
				if (event.type == 'mouseover') {
					$(this).addClass('tree-move-border-hover');
					tree.elm_to = $(this);
					tree.code = 2;
				} else {
					$(this).removeClass('tree-move-border-hover');
					tree.code = 0;
				}
			});
			$('#tree a').live('mouseover mouseout', function (event) {
				if (event.type == 'mouseover') {
					if ($(this).parents('.tree-move').size() == 0) {
						$(this).addClass('tree-move-a-hover');
						tree.elm_to = $(this);
						tree.code = 1;
					}
				} else {
					$(this).removeClass('tree-move-a-hover');
					tree.code = 0;
				}
			});
			$('<div id="tree-move-icon"></div>').appendTo('body');
		}
		$('#tree-move-icon').css({'left': e.pageX + 15, 'top': e.pageY + 15});
		return true;
	},
	stop: function () {
		$('body').enableSelect();
		$('body').unbind('mousemove', tree.move);
		$('#focus-field').focus();
		$('body').unbind('mouseup');
		$('#tree-move-icon').remove();
		$(tree.parent).removeClass('tree-move');
		$('#tree a').removeClass('tree-move-a-hover').unbind('mouseover mouseout');
		$('.tree-move-border').removeClass('tree-move-border-hover').unbind('mouseover mouseout');
		tree.first = false;
		tree.complite();
		tree.elm_to = false;
		tree.code = 0;
	},
	complite: function () {
		if (!tree.code) {
			return false;
		}
		if (tree.code == 1) {
			fromTo = $(tree.elm_to).attr('section');
		}
		if (tree.code == 2) {
			p = $(tree.elm_to).parent();
			fromTo = $('a', p).attr('section');
		}
		$.prompt(this, {
			'href': '/k2/admin/section/move.php?section_from=' + $(tree.elm).attr('section') + '&section_to=' + fromTo + '&code=' + tree.code,
			'text': 'Перенести выбранный раздел?'
		});
	}
}

$(function () {
	if($('input[name=URL_ALTERNATIVE]').size()){
		//var urlAlt = $('input[name=URL_ALTERNATIVE]').val();

		//if(!urlAlt.length){
		$('input[name=NAME]').keyup(function () {
			var text = $('input[name=NAME]').val();

			text = k2.transcription.translit(text);
			text = text.replace(/(^\s+)|(\s+$)/g, '');
			text = text.replace(/(\&#\d+;)+/g, '');
			text = text.replace(/[^a-zA-Z0-9\-\_ ]+/g, '');
			text = text.replace(/[ ]+/g, '-');
			text = text.replace(/[-]+/g, '-');
			text = text.toLowerCase();

			$('input[name=URL_ALTERNATIVE]').val(text);
		});
		//}
	}

	if (typeof(section) != 'undefined') {
		$('#tree a[section=' + section + ']').parent().addClass('current');
	}

	k2.form.autofocus();
	k2.tab();

	$('.table tr').each(function () {
		var goto = $(this).attr('goto');
		if (goto) {
			$(this).dblclick(function () {
				location.href = goto;
			});
		}
	});
	$('body').click(function () {
		$('#context-menu').remove();
		$('.filetree a').removeClass('active');
	});
	$('.filetree a').mousedown(function (e) {
		if (e.button == 2) {
			$(this)[0].oncontextmenu = function () {
				return false;
			}
			$('#context-menu').remove();
			$('.filetree a').removeClass('active');

			if (e.pageX || e.pageY) {
				x = e.pageX;
				y = e.pageY;
			} else if (e.clientX || e.clientY) {
				x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
				y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
			}
			x += 2;
			y += 2;

			section = $(this).attr('section');
			url = $(this).attr('url');
			menu = '<div id="context-menu">' +
			'<a href="/k2/admin/section/add.php?section=' + section + '">Добавить подраздел</a>' +
			'<a href="/k2/admin/section/edit.php?section=' + section + '">Редактировать</a>' +
			'<a href="/k2/admin/section/delete.php?id=' + section + '" onclick="return $.prompt(this)">Удалить</a>' +
			'<div class="line"></div>' +
			'<a href="' + url + '" blank="true" target="_blank">Посмотреть на сайте</a>' +
			'</div>';

			$(menu).appendTo('body');

			h = parseInt($('#context-menu').height());

			hw = document.compatMode == 'CSS1Compat' && !window.opera ? document.documentElement.clientHeight : document.body.clientHeight;
			hw += document.body.scrollTop - h;

			if (hw < y) {
				y = y - h - 10;
			}

			$('#context-menu').css({'left': x + 'px', 'top': y + 'px'}).show();

			$(this).addClass('active');

			return false;
		}
		return true;
	});

	$('#slider').mousedown(function (e) {
		$('body').disableSelect();
		$('body').bind('mousemove', treeStart);
		$('body').mouseup(function (e) {
			$('body').enableSelect();
			$('body').unbind('mousemove', treeStart);
			$('#focus-field').focus();
			$.cookie('K2_TREE_WIDTH', parseInt($('#tree').css('width')));
			$('body').unbind('mouseup');
		});
	});

	$('#tree-content').each(function () {
		$(this).show().treeview({
			collapsed: true,
			animated: 'fast',
			control: '.controlMap',
			persist: 'cookie',
			cookieId: 'K2_TREE'
		});
		$('.filetree a').mousedown(function (e) {
			tree.elm = $(this);
			tree.parent = $(tree.elm).parent().parent();
			tree.start(e);
			$('body').mouseup(function (e) {
				tree.stop();
			});
			return false;
		});
		return false;
	});

	$('#sizePage').change(function () {
		location.href = $(this).attr('url') + 'page_size=' + $(this).val();
	});

	$('.form').submit(function () {
		$('#BAPPLY_x').remove();
		if (key == 16) {
			$(this).append('<input type="hidden" name="BAPPLY_x" value="1" id="BAPPLY_x">');
		}
	});

	$('.moreField .link').click(function () {
		if ($('.moreFieldBox').css('display') == 'none') {
			$.cookie('K2_MORE_FIELD', '1');
			$(this).text('Скрыть дополнительные поля');
		} else {
			$.cookie('K2_MORE_FIELD', '0');
			$(this).text('Показать дополнительные поля');
		}
		$('.moreFieldBox').toggle();
		return false;
	});
});