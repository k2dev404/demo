$(function () {
	var body = $('body');

	if ($('.catalog').size()) {
		var obj = $('.catalog .name');
		obj.height(
			Math.max.apply(null, obj.map(function () {
				return $(this).height();
			}).get())
		);

		obj = $('.catalog .prop');
		obj.height(
			Math.max.apply(null, obj.map(function () {
				return $(this).height();
			}).get())
		);
	}

	if ($('.filterPrice').size()) {
		var priceMin = $('.filter input[name=priceMin]');
		var priceMax = $('.filter input[name=priceMax]');

		$('.filterPrice .lineBox').slider({
			range: true,
			min: parseInt(priceMin.attr('min')),
			max: parseInt(priceMax.attr('max')),
			values: [priceMin.val(), priceMax.val()],
			slide: function (event, ui) {
				$('.filter input[name=priceMin]').val(ui.values[0]);
				$('.filter input[name=priceMax]').val(ui.values[1]);
			}
		});
	}

	if($('.filterProp').size()){
		body.on('click', '.filterProp .title', function(){
			$(this).parent().toggleClass('active');

			var id = $(this).closest('.item').attr('id');

			var list = [];
			$('.filter .filterProp .item').each(function(){
				if(!$(this).hasClass('active')){
					list.push($(this).attr('id'));
				}
			});

			$.cookie('FILTER_HIDE', list, { expires: 7, path: '/' });
		});
	}

	$('body').on('click', '.quantity .n, .fullQuantity .n', function(){
		var p = $(this).parent();
		var plus = $(this).hasClass('up');

		$('.n', p).removeClass('active');

		var v = parseInt($('input', p).val());
		if(v < 1 || isNaN(v)){
			v = 1;
		}
		if(plus){
			v++;
		}else{
			v--;
		}
		if(v < 1){
			v = 1;
		}
		if(v > 1){
			$('.down', p).addClass('active');
		}
		$('.up', p).addClass('active');
		$('input', p).val(v);
	});

	if($('.contactList').size()){

		$('#map .mapItem').height($('.contactListBox .left').height() - 25);

		$('body').on('click', '.contactList .more a', function(){

			$('.contactList .item, .contactList .mapItem').removeClass('active');
			$(this).closest('.item').addClass('active');

			$('.mapItem' + $(this).attr('data-id')).addClass('active');

			return false;
		});
	}

	$('body').on('click', '.filter .sub', function(){
		$(this).closest('form').submit();
		return false;
	});

	$('body').on('click', '.catalogFull .preview a', function(){

		$('#photo').css('background-image', 'url(' + $(this).attr('href') +')');
		$('.catalogFull .preview a').removeClass('active');
		$(this).addClass('active');

		return false;
	});


	if($('.slider').size()){
		$('.slider .sliderBox').cycle({
			fx: 'fade',
			delay: 1000,
			pager: '.slider .nav',
			timeout: 6000
		});
	}

	body.on('click', '.inCart', function () {
		var _this = $(this);

		$.post('/cart/?ajax=1&action=add', $(this).closest('form').serialize(), function(data){
			$('.cart .count').text(data);

			_this.val('в корзине').text('в корзине').addClass('green');
		});

		return false;
	});

	var setInterval = null;
	$('body').on('click', '.basket .quantity .n', function(){
		var form = $(this).closest('form');

		clearTimeout(setInterval);
		setInterval = setTimeout(function(){
			$.post('/cart/?ajax=1&action=calc', form.serialize(), function(data){
				$('.basket').html($(data).find('.basket').html());
			});
		}, 1000);

		return false;
	});

	body.on('submit', '.order form', function () {

		var form = $(this).closest('form');

		$.post(form.attr('action') + '&ajax=1', form.serialize(), function(data){
			var obj = eval(data);
			var error = obj[0].error;

			if(error.length){
				alert(error);
			}else{
				if(obj[0].redirect){
					location.href = obj[0].redirect;
				}
			}
		});

		return false;
	});

	body.on('click', '.order .form2 input', function () {
		$('.deliveryLocation').css('display', 'none');
		if($(this).val() == 2){
			$('.deliveryLocation').css('display', 'flex');
		}
	});




	$('.page').css('min-height', parseInt($(document).height()) - 340);
});