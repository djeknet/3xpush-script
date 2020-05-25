//$.noConflict();

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( "select.cs-select" ) ).forEach( function(el) {
		new SelectFx(el);
	} );

	jQuery(".selectpicker").selectpicker;

	$("#menuToggle, .navbar-toggler").on("click", function(event) {
		$("body").toggleClass("open");

		var openClass = $("body").hasClass("open");
		console.log(openClass);

		if(openClass) {
			// записываем в куки
			var date = new Date(new Date().getTime() +1000*60*60*24*30);
			$.cookie("body_open_class", 1, {
				expires: date,
				domain: location.host,
				path: '/'
			});
		}
		else {
			// удаляем
			$.cookie("body_open_class", null, {
				expires: -1,
				domain: location.host,
				path: '/'
			});
		}

	});

	$(".search-trigger").on("click", function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(".search-trigger").parent(".header-left").addClass("open");
	});

	$(".search-close").on("click", function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(".search-trigger").parent(".header-left").removeClass("open");
	});

	// $('.user-area> a').on('click', function(event) {
	// 	event.preventDefault();
	// 	event.stopPropagation();
	// 	$('.user-menu').parent().removeClass('open');
	// 	$('.user-menu').parent().toggleClass('open');
	// });

	$.validator.addMethod("rangeOrZero", function (value, element, options) {
		if (value >= options[0] && value <= options[1]) {
			return true;
		}

		if(value < options[0]) {
			$(element).val(options[0]);
		}

		if(value > options[1]) {
			$(element).val(options[1]);
		}

		return false;
	});

	$.validator.addMethod("checkSumma", function (value, element, options) {
		if(value > userBalance) {
			return false;
		}
		return  true;
	});

	$('#exchange_buyform').validate({
		rules: {
			price: {
				required: true,
				rangeOrZero: [
					$('input[name=price]').data('minpice'), $('input[name=price]').data('maxprice')
				]
			},
		},
		messages: {
			price: "Значение должно быть в интервале от " + $('input[name=price]').data('minpice') + " до " + $('input[name=price]').data('maxprice'),
		}
	});

	$('#exchange_buyform').submit(function (e) {
		var $form = $(this);
		if(! $form.valid()) return false;
		formLoadingAjax(this);
		return  true;
	});


});

function formLoadingAjax(form) {

	if($(form).find(".error:visible").length) {
		return false;
	}

	var buttonContainer = $(form).find('.button-container');

	$(buttonContainer).addClass('button-container-load');

	if(!$(form).find('.ajax-load').length) {
		$(buttonContainer).append('<div class="ajax-load"></div>');
	}
}

function validationForm() {

	if(typeof exchange_minprice === 'undefined') {
		return false;
	}


	$('#buyform').validate({
		rules: {
			price: {
				required: true,
				rangeOrZero: [
					exchange_minprice, exchange_maxprice
				]
			},
		},
		messages: {
			price: "Значение должно быть в интервале от " + exchange_minprice + " до " + exchange_maxprice,
		},
	});

	$('#buyform').submit(function (e) {
		var $form = $(this);
		if(! $form.valid()) return false;
		formLoadingAjax(this);
		return  true;
	});
}

