//TODO enable proper client-side form validation

$(function () {

	// HMAC GENERATOR DEFINITION
	var hexcase = 0;
	var b64pad = "";
	var chrsz = 8;

	var hex_sha1 = function(s) {
		return binb2hex(core_sha1(str2binb(s), s.length * chrsz));
	}

	var b64_sha1 = function(s) {
		return binb2b64(core_sha1(str2binb(s), s.length * chrsz));
	}

	var str_sha1 = function(s) {
		return binb2str(core_sha1(str2binb(s), s.length * chrsz));
	}

	var hex_hmac_sha1 = function(key, data) {
		return binb2hex(core_hmac_sha1(key, data));
	}

	var b64_hmac_sha1 = function(key, data) {
		return binb2b64(core_hmac_sha1(key, data));
	}

	var str_hmac_sha1 = function(key, data) {
		return binb2str(core_hmac_sha1(key, data));
	}

	var core_sha1 = function(x, len) {
		x[len >> 5] |= 0x80 << (24 - len % 32);
		x[((len + 64 >> 9) << 4) + 15] = len;
		var w = Array(80);
		var a = 1732584193;
		var b = -271733879;
		var c = -1732584194;
		var d = 271733878;
		var e = -1009589776;
		for (var i = 0; i < x.length; i += 16) {
			var olda = a;
			var oldb = b;
			var oldc = c;
			var oldd = d;
			var olde = e;
			for (var j = 0; j < 80; j++) {
				if (j < 16) w[j] = x[i + j];
				else w[j] = rol(w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16], 1);
				var t = safe_add(safe_add(rol(a, 5), sha1_ft(j, b, c, d)),
					safe_add(safe_add(e, w[j]), sha1_kt(j)));
				e = d;
				d = c;
				c = rol(b, 30);
				b = a;
				a = t;
			}
			a = safe_add(a, olda);
			b = safe_add(b, oldb);
			c = safe_add(c, oldc);
			d = safe_add(d, oldd);
			e = safe_add(e, olde);
		}
		return Array(a, b, c, d, e);
	}

	var sha1_ft = function(t, b, c, d) {
		if (t < 20) return (b & c) | ((~b) & d);
		if (t < 40) return b ^ c ^ d;
		if (t < 60) return (b & c) | (b & d) | (c & d);
		return b ^ c ^ d;
	}

	var sha1_kt = function(t) {
		return (t < 20) ? 1518500249 : (t < 40) ? 1859775393 :
			(t < 60) ? -1894007588 : -899497514;
	}

	var core_hmac_sha1 = function(key, data) {
		var bkey = str2binb(key);
		if (bkey.length > 16) bkey = core_sha1(bkey, key.length * chrsz);
		var ipad = Array(16), opad = Array(16);
		for (var i = 0; i < 16; i++) {
			ipad[i] = bkey[i] ^ 0x36363636;
			opad[i] = bkey[i] ^ 0x5C5C5C5C;
		}
		var hash = core_sha1(ipad.concat(str2binb(data)), 512 + data.length * chrsz);
		return core_sha1(opad.concat(hash), 512 + 160);
	}

	var safe_add = function(x, y) {
		var lsw = (x & 0xFFFF) + (y & 0xFFFF);
		var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
		return (msw << 16) | (lsw & 0xFFFF);
	}

	var rol = function(num, cnt) {
		return (num << cnt) | (num >>> (32 - cnt));
	}

	var str2binb = function(str) {
		var bin = Array();
		var mask = (1 << chrsz) - 1;
		for (var i = 0; i < str.length * chrsz; i += chrsz)
			bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (24 - i % 32);
		return bin;
	}

	var binb2str = function(bin) {
		var str = "";
		var mask = (1 << chrsz) - 1;
		for (var i = 0; i < bin.length * 32; i += chrsz)
			str += String.fromCharCode((bin[i >> 5] >>> (24 - i % 32)) & mask);
		return str;
	}

	var binb2hex = function(binarray) {
		var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		var str = "";
		for (var i = 0; i < binarray.length * 4; i++) {
			str += hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8 + 4)) & 0xF) +
			hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8  )) & 0xF);
		}
		return str;
	}

	var binb2b64 = function(binarray) {
		var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		var str = "";
		for (var i = 0; i < binarray.length * 4; i += 3) {
			var triplet = (((binarray[i >> 2] >> 8 * (3 - i % 4)) & 0xFF) << 16)
				| (((binarray[i + 1 >> 2] >> 8 * (3 - (i + 1) % 4)) & 0xFF) << 8 )
				| ((binarray[i + 2 >> 2] >> 8 * (3 - (i + 2) % 4)) & 0xFF);
			for (var j = 0; j < 4; j++) {
				if (i * 8 + j * 6 > binarray.length * 32) str += b64pad;
				else str += tab.charAt((triplet >> 6 * (3 - j)) & 0x3F);
			}
		}
		return str;
	}

	var _hex2bin = [
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 0, 0, 0, 0, 0, // 0-9
		0, 10, 11, 12, 13, 14, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, // A-F
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 10, 11, 12, 13, 14, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, // a-f
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
	];

	var hex2bin = function(str) {
		var len = str.length;
		var rv = '';
		var i = 0;

		var c1;
		var c2;

		while (len > 1) {
			h1 = str.charAt(i++);
			c1 = h1.charCodeAt(0);
			h2 = str.charAt(i++);
			c2 = h2.charCodeAt(0);

			rv += String.fromCharCode((_hex2bin[c1] << 4) + _hex2bin[c2]);
			len -= 2;
		}

		return rv;
	}

	var tomacdata = function(fldId) {
		var str = "";
		oFld = document.getElementById(fldId)
		if (oFld.value == "") {
			str = "-";
		} else {
			str = oFld.value.length + oFld.value;
		}
		return str;
	}

	var calc_timestamp = function() {
		var date = new Date();
		var year = date.getUTCFullYear().toString();
		var month = ((date.getUTCMonth() + 1).toString().length == 1) ? '0' + (date.getUTCMonth() + 1).toString() : (date.getUTCMonth() + 1).toString();
		var day = (date.getUTCDate().toString().length == 1) ? '0' + date.getUTCDate() : date.getUTCDate().toString();
		var hour = (date.getUTCHours().toString().length == 1) ? '0' + date.getUTCHours() : date.getUTCHours().toString();
		var min = (date.getUTCMinutes().toString().length == 1) ? '0' + date.getUTCMinutes() : date.getUTCMinutes().toString();
		var sec = (date.getUTCSeconds().toString().length == 1) ? '0' + date.getUTCSeconds() : date.getUTCSeconds().toString();
		return year + month + day + hour + min + sec;
	}

	// END HMAC GENERATOR DEFINITION

	// INITIALIZE
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

	// -------------- HMAC GENERATOR USAGE --------------
	var generateHMAC = function() {
		ORDER.value = +(new Date());
		TIMESTAMP.value = calc_timestamp();
		MAC_DATA.value = tomacdata('AMOUNT') +
		tomacdata('CURRENCY') +
		tomacdata('ORDER') +
		tomacdata('MERCH_NAME') +
		tomacdata('MERCHANT') +
		tomacdata('TERMINAL') +
		tomacdata('EMAIL') +
		tomacdata('TRTYPE') +
		tomacdata('TIMESTAMP') +
		tomacdata('NONCE') +
		tomacdata('BACKREF');
		P_SIGN.value = hex_hmac_sha1(hex2bin(KEY.value), MAC_DATA.value);

	}
	// ------------ END HMAC GENERATOR USAGE --------------

	if (window.kryotherm && window.kryotherm.total_cost){
		window.kryotherm.total_cost = window.kryotherm.total_cost || calculateTotalCost();
		window.kryotherm.total_amount = window.kryotherm.total_cost;
	}

	window.$orderForm = $('.js-place-order-form[name=place-order]');
	var $totalPrice =  $('.total-cost');
	var $deliveryPrice =  $('.delivery-price');
	var $customerSelect = $orderForm.$customerSelect = $orderForm.find('[name=customer]');
	var $shippingSelect = $orderForm.$shippingSelect = $orderForm.find('[name=shipping]');
	var $districtSelect = $orderForm.$districtSelect = $orderForm.find('[name=district]');

	var checkCustomerValue = function(custormerValue) {
		if ( custormerValue+'' !== '1' && custormerValue+'' !== '2' ) { custormerValue = 1 }
		if (custormerValue+'' ===  '1') {
			$orderForm.addClass('individual-person');
			$orderForm.removeClass('legal-person');
		} else if (custormerValue+'' ===  '2') {
			$orderForm.removeClass('individual-person');
			$orderForm.addClass('legal-person');
		}
	}
	var checkShippingValue = function(shippingValue) {
		var deliveryPrice = $(this.selectedOptions[0]).data('price');
		if ( shippingValue === "Major express") {
			$districtSelect.removeClass('hidden');
		} else {
			$districtSelect.addClass('hidden');
		}
		if (deliveryPrice) {
			$deliveryPrice.removeClass('hidden').find('.value').text(deliveryPrice);
			$totalPrice.find('.value').text(kryotherm.total_cost+deliveryPrice);
			window.kryotherm.total_amount = kryotherm.total_cost+deliveryPrice;
			delivery_price.value = deliveryPrice;
		} else {
			$deliveryPrice.addClass('hidden');
			$totalPrice.find('.value').text(kryotherm.total_cost);
			window.kryotherm.total_amount = kryotherm.total_cost;
			delivery_price.value = '';
		}
	}
	var checkDistrictValue = function(districtValue) {
		var deliveryPrice = $(this.selectedOptions[0]).data('price');

		if (deliveryPrice) {
			$deliveryPrice.removeClass('hidden').find('.value').text(deliveryPrice);
			$totalPrice.find('.value').text(kryotherm.total_cost+deliveryPrice);
			window.kryotherm.total_amount = kryotherm.total_cost+deliveryPrice;
			delivery_price.value = deliveryPrice;
		} else {
			$deliveryPrice.addClass('hidden');
			$totalPrice.find('.value').text(kryotherm.total_cost);
			window.kryotherm.total_amount = kryotherm.total_cost;
			delivery_price.value = '';
		}
	}


	$customerSelect.on('change', function() {
		$orderForm.valid();
		$orderFormValidator.resetForm();
		checkCustomerValue.call(this,this.value);
	})
	checkCustomerValue.call($customerSelect[0],$customerSelect[0].value);

	$shippingSelect.on('change', function() {
		checkShippingValue.call(this, this.value);
	})
	checkShippingValue.call($shippingSelect[0], $shippingSelect[0].value);

	$districtSelect.on('change', function() {
		checkDistrictValue.call(this, this.value);
	})
	checkDistrictValue.call($districtSelect[0], $districtSelect[0].value);

	var getDateString = function () {
		var date = new Date();
		var dateString = date.getDate()+'.'+date.getMonth()+'.'+date.getFullYear()+' '+date.getHours()+':'+date.getMinutes()
		return dateString;
	}
	var calculateTotalCost = function() {
		return 1
	}

	var onOrderFormSubmit = function (form, e) {
		//prepare values
		//checkShippingValue.call($shippingSelect[0], $shippingSelect[0].value);
		//checkDistrictValue.call($districtSelect[0], $districtSelect[0].value);
		AMOUNT.value = window.kryotherm.total_amount;
		order_date.value = getDateString();

		generateHMAC();

		$form = $(form);
		if ($customerSelect.val()+'' ===  '1') {
			var selector = '.individual-person-input';
		} else if ($customerSelect.val()+'' ===  '2') {
			var selector = '.legal-person-input';
		}
		//use values
		$dataAboutCustomerInputs = $form.find(selector+' .customer-data, .common-input .customer-data, [name=customer]');
		$dataAboutPaymentInputs = $form.find('.payment-data');

		dataAboutCustomer = {}
		$dataAboutCustomerInputs.each(function (i, input) {
			dataAboutCustomer[input.name] = input.value
		});

		dataAboutPayment = {}
		$dataAboutPaymentInputs.each(function (i, input) {
			dataAboutPayment[input.name] = input.value
		});

		api.session.setUser(dataAboutCustomer, function (response) {
			if (response.status === 0) {
				//console.log(response);
				form.submit();
			} else {
				alert('Что-то пошло не так, попробуйте еще раз.')
			}
		});

	}

	window.$orderFormValidator = $orderForm.validate({
		submitHandler: onOrderFormSubmit,
		//invalidHandler: function(event, validator) {},
		//ignore: ":hidden",
		rules: validationRules,
		messages: validationMessages,
		//groups: {},
		//onsubmit: true,
		//onfocusout: function(element,event) {}, // or Boolean
		//onkeyup: function(element,event) {}, // or Boolean
		//onclick: function(element,event) {}, // or Boolean
		//focusInvalid: true,
		errorClass: "invalid",
		validClass: "valid",
		//errorElement: "label",
		//wrapper: window, // String
		//errorLabelContainer: '', // Selector
		//errorContainer: '', // Selector
		//showErrors: function(errorMap, errorList) {},
		//errorPlacement: function(error, element) {},
		//success: '', // String(class) or Function($label)
		//highlight: function(element,errorClass,validClass) {},
		//unhighlight: function(element,errorClass,validClass) {},
		//ignoreTitle: false

	})
	// END  INITIALIZE

});

var validationRules = {
	name: {
		required: true
	},
	shipping: {
		required: true,
		minlength: 1
	},
	district: {
		//required: function() {
		//	if ($orderForm.$shippingSelect.val() === "Major express") {
		//		return true
		//	} else {
		//		return false
		//	}
		//}
		required: true,
		minlength: 1
	},
	surname: {
		required: true
	},
	EMAIL: {
		required: true,
		email: true
	},
	inn: {
		required: true,
		minlength: function () {
			$customerInputValue = $orderForm.$customerSelect.val()+'';
			if ($customerInputValue === '1') {
				return 12
			} else if ($customerInputValue === '2') {
				return 10
			}
		},
		maxlength: function () {
			$customerInputValue = $orderForm.$customerSelect.val()+'';
			if ($customerInputValue === '1') {
				return 12
			} else if ($customerInputValue === '2') {
				return 10
			}
		}
	},
	adress: {
		required: true
	},
	phone: {
		required: true
	},
	organisation: {
		required: true
	},
	kpp: {
		required: true
	},
	jaddress: {
		required: true
	},
	postaladdress: {
		required: true
	},
	bank: {
		required: true
	},
	gendir: {
		required: true
	}
}

var validationCommonMessages = {
	required: "Это поле обязательно для заполнения."
}
var validationMessages = {
	name: {
		required: validationCommonMessages.required
	},
	shipping: {
		required: 'Выберите способ доставки.'
	},
	district: {
		required: 'Выберите округ для доставки.'
	},
	surname: {
		required: validationCommonMessages.required
	},
	EMAIL: {
		required: validationCommonMessages.required,
		email: 'Пожалуйста, введите валидный e-mail адрес.'
	},
	inn: {
		required: validationCommonMessages.required,
		minlength: $.validator.format('Длинна ИНН должна быть {0} сомволов.'),
		maxlength: $.validator.format('Длинна ИНН должна быть {0} сомволов.')
	},
	adress: {
		required: validationCommonMessages.required
	},
	phone: {
		required: validationCommonMessages.required
	},
	organisation: {
		required: validationCommonMessages.required
	},
	kpp: {
		required: validationCommonMessages.required
	},
	jaddress: {
		required: validationCommonMessages.required
	},
	postaladdress: {
		required: validationCommonMessages.required
	},
	bank: {
		required: validationCommonMessages.required
	},
	gendir: {
		required: validationCommonMessages.required
	}
}


//required – Makes the element required.
//remote – Requests a resource to check the element for validity.
//minlength – Makes the element require a given minimum length.
//maxlength – Makes the element require a given maxmimum length.
//rangelength – Makes the element require a given value range.
//min – Makes the element require a given minimum.
//max – Makes the element require a given maximum.
//range – Makes the element require a given value range.
//email – Makes the element require a valid email
//url – Makes the element require a valid url
//date – Makes the element require a date.
//dateISO – Makes the element require an ISO date.
//number – Makes the element require a decimal number.
//digits – Makes the element require digits only.
//creditcard – Makes the element require a credit card number.
//equalTo – Requires the element to be the same as another one


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
			if ($.inArray($(v).attr("name"), exclusion) == -1 && $(v).val() == '')
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