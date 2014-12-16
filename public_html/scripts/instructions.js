$(document).ready(function () {

	$("div").click(function () {
		close_help();
	});

	$(".filter_header").click(
		function () {
			$(".filter_conteiner").slideToggle();
			$(this).find("span").toggleClass("activebg");
		});

	// blocking lists

	var cat = $("select[name='category']");
	var prod = $("select[name='product']");
	var price = $("input[name='price']");


	var cat_items = $("select[name='category']").find("option").length;

	/*
	 if (cat_items > 1 && cat.val() == '')
	 {
	 $(".filter_conteiner select[name!='category']").attr("disabled","disabled");
	 $("input[name='price']").attr("disabled","disabled");
	 }
	 else
	 if (prod.val() == '')
	 {
	 $(".filter_conteiner select").not(cat).not(prod).attr("disabled","disabled");
	 $("input[name='price']").attr("disabled","disabled");
	 }
	 */

	var $orderForm = $('.js-place-order');
	$orderForm.on('submit', validate);

});

function validate() {
	var customer = $("select[name='customer']");
	var order_form = $("form[name='order_form']");
	var error = '';
	var inn = $("input[name='inn']").val();
	var nonum = false;


	for (var i = 0; i < inn.length; i++) {
		var s = parseInt(inn.substr(i, 1));

		if (isNaN(s)) {
			nonum = true;
		}
	}


	var exclusion = ['patronymic', 'fax', 'okpo', 'contactperson'];

	$.each(order_form.find("input"),
		function (k, v) {
			if ( $.inArray( $(v).attr("name"), exclusion ) == -1 && $(v).val() == '' )
				error += $(v).parent("td").prev("td").text() + '\n';
		});

	if (error) alert('Заполните поля:\n' + error);
	else {
		if ((inn.length !== 12 && customer.val() == 1) || (nonum && customer.val() == 1))

			alert('ИНН должно содержать 12 цифр.');
		else {
			if ((inn.length !== 10 && customer.val() == 2) || (nonum && customer.val() == 2))

				alert('ИНН должно содержать 10 цифр.');
			else
			//order_form.submit();
				return true
		}
	}
	return true
}

function list_processing(list) {
	var curent = $(list);
	var request = '';
	request = 'price=' + $("input[name='price']").val();

	var data = $("form[name='filter'] select");

	$.each(data, function (k, v) {

		request += '&' + $(v).attr('name') + '=' + urlencode($(v).val());
	});

	//alert(request);

	ajax_request(request, $("form[name='filter']"));
}

function resset() {
	//if (confirm('Очистить фильтр?'))
	//{

	var r = '';
	var electro_filter = $("form[name='filter'] select");

	$.each(electro_filter,
		function (k, v) {
			r += '&' + $(v).attr('name') + '=';
		});

	request = 'price=' + $("input[name='price']").val() + r;
	ajax_request(request, $("form[name='filter']"));

	//}
}

function ajax_request(request, update) {
	$(".loading").css("opacity", "0.9").show();
	$(".load").css("opacity", "0.9").show();

	$.ajax({
		// url: '/modules/catalog/ajax/Ajax_filter_processorJson.php?'+request,
		url: '/modules/catalog/ajax/Ajax_filter_processorJson.php?' + request,
		type: "GET",
		dataType: 'json',
		success: function (data) {
			update.html(data.response);
			$(".loading").fadeOut("slow");
			$(".load").fadeOut("slow");
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$(".loading").fadeOut("slow");
			$(".load").fadeOut("slow");
			alert("Error... " + textStatus + "        " + errorThrown);
		}
	});
}


function define_customer(value) {
	document.location.href = '/cart.php&exec_order=form&customer=' + value;
}

function range_definer(elem) {
	var max = '';
	var max_list = '<option value="">---</option>';

	switch ($(elem).attr("name")) {
		case 'I_max[]':
		case 'Q_max[]':
		case 'R[]':
			max = [5, 10, 20];
			break

		case 'U[]':
			max = [1, 5, 10];
			break
	}

	$.each(max,
		function (k, v) {
			if (1 * v > 1 * $(elem).val())
				max_list += '<option value="' + v + '">' + v + '</option>';
		});

	$(elem).parent("td").next("td").next("td").find("select").html(max_list);
}

function change_listing_action(item) {
	$(item).parent("p").prev("form").attr("action", $(item).attr("alt"));
	// $(item).click(function(){ $(this).parent("p").prev("form").submit(); });
}

function submit_listing(item) {
	$(item).attr("href", "javascript: void[0];");
	$("form[name='listing']").submit();

	return false;
}

function urlencode(text) {
	var trans = [];
	for (var i = 0x410; i <= 0x44F; i++) trans[i] = i - 0x350;
	trans[0x401] = 0xA8;
	trans[0x451] = 0xB8;
	var ret = [];
	for (var i = 0; i < text.length; i++) {
		var n = text.charCodeAt(i);
		if (typeof trans[n] != 'undefined') n = trans[n];
		if (n <= 0xFF) ret.push(n);
	}
	return escape(String.fromCharCode.apply(null, ret));
}

function show_help(img, help_id, shift) {
	close_help();

	$.post('/modules/catalog/ajax/Ajax_help.php?page_id=' + help_id,
		function (data) {
			if (data) {
				var uaVers = '';
				var ua = '';
				var pos_cont = $(".filter_conteiner").position();
				var p_cont = pos_cont.top;
				var pos = $(img).position();
				var p_img = pos.top;

				if (!shift) shift = 0;

				if (window.navigator.userAgent.indexOf("MSIE") >= 0) {
					ua = 'Explorer';
					uaVers = window.navigator.userAgent.substr(window.navigator.userAgent.indexOf("MSIE") + 5, 3);
				}

				if (ua == 'Explorer' && uaVers == '6.0') {
					p_cont = 1 * p_cont - 12;
				}

				var wrapper_div = '<div id="help" style="top: ' + (p_cont + p_img + shift + 14) + 'px"><!--[if lte IE 6.5]><iframe></iframe><![endif]--><img src="/img/close.png" class="close png24" onClick="close_help()" >' + data + '</div>';

				$("div.menu").prepend(wrapper_div);

				$("div#help iframe").css("height", ($("div#help").height() + 45) + 'px');
			}
		});
}

function close_help() {
	$("#help").remove();
	$("#help2").remove();
}

function show_help_2(img, help_id, shift) {
	close_help();

	$.post('/modules/catalog/ajax/Ajax_help.php?page_id=' + help_id,
		function (data) {
			if (data) {/*
			 var uaVers = '';
			 var ua = '';
			 var pos_cont = $(".filter_conteiner").position();
			 var p_cont = pos_cont.top;
			 var pos = $(img).position();
			 var p_img = pos.top;

			 if (!shift) shift = 0;

			 if (window.navigator.userAgent.indexOf ("MSIE") >= 0)
			 {
			 ua = 'Explorer';
			 uaVers=window.navigator.userAgent.substr(window.navigator.userAgent.indexOf("MSIE")+5,3);
			 }

			 if (ua == 'Explorer' && uaVers == '6.0')
			 {
			 p_cont = 1*p_cont - 12;
			 }
			 */
				var wrapper_div = '<div id="help2"><!--[if lte IE 6.5]><iframe></iframe><![endif]--><img src="/img/close.png" class="close png24" onClick="close_help()" >' + data + '</div>';

				$("div#help_holder").append(wrapper_div);

				//$("div#help iframe").css("height",($("div#help").height()+45)+'px');
			}
		});
}
 