<?
$html .= '<script type="text/javascript">
	window.kryotherm || (window.kryotherm = {});
	window.kryotherm.cart =' . json_encode($array) . '
	window.kryotherm.customer =' . json_encode($customer) . '
</script>';

$html .= '<br /><h2>Ваши данные:</h2>

<!--<form class="js-place-order-form" name="order_form" action="/cart.php&exec_order=send" method="post"> -->

<form class="form order-form js-place-order-form '. (($customer == '1') ?'individual-person':'legal-person' ) .'" name="place-order" method="post" action="http://193.200.10.117:8080/cgi-bin/cgi_link">
	<table width="100%" class="form-table">
		<tbody>

	<!-- ------------------------------------ DATA ABOUT CUSTOMER ------------------------------------- -->

			<tr class="common-input">
				<td class="label" width="30%">Заказчик:</td>
				<td>
					<select class="customer-data" size="1" name="customer">
						<option value="1" ' . (($customer == 1) ? 'selected' : '') . '>Физическое лицо</option>
						<option value="2" ' . (($customer == 2) ? 'selected' : '') . '>Юридическое лицо</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label">Способ доставки:</td>
				<td>
					<select class="customer-data" size="1" name="shipping">
						<option value=""> --- </option>
						<option value="СПСР-Экспресс">СПСР-Экспресс</option>
						<option value="Грузовозов">Грузовозов</option>
						<option value="Автотрейдинг">Автотрейдинг</option>
						<option value="Почта России">Почта России</option>
						<option value="Самовывоз">Самовывоз</option>
						<option value="Другое">Другое</option>
					</select>
				</td>
			</tr>

			<!-- ФИЗИЧЕСКОЕ ЛИЦО -->
			<tr class="individual-person-input">
				<td class="label">Имя: <span class="active">*</span></td>
				<td><input class="customer-data" name="name" type="text" value=""></td>
			</tr>
			<tr class="individual-person-input">
				<td class="label">Отчество:</td>
				<td><input class="customer-data" name="patronymic" type="text" value=""></td>
			</tr>
			<tr class="individual-person-input">
				<td class="label">Фамилия: <span class="active">*</span></td>
				<td><input class="customer-data" name="surname" type="text" value=""></td>
			</tr>
			<tr class="individual-person-input">
				<td class="label">Адрес грузополучателя: <span class="active">*</span></td>
				<td><input class="customer-data" name="adress" type="text" value=""></td>
			</tr>
			<tr class="individual-person-input">
				<td class="label">Факс:</td>
				<td><input class="customer-data" name="fax" type="text" value=""></td>
			</tr>

			<!-- ОБЩИЕ -->
			<tr class="common-input">
				<td class="label">E-mail: <span class="active">*</span></td>
				<td><input class="payment-data customer-data" name="EMAIL" id="EMAIL" type="text" value="developer085@gmail.com"/></td>
			</tr>
			<tr class="common-input">
				<td class="label">Телефон: <span class="active">*</span></td>
				<td><input class="customer-data" name="phone" type="text" value=""></td>
			</tr>
			<tr class="common-input">
				<td class="label">ИНН: <span class="active">*</span></td>
				<td><input class="customer-data " name="inn" type="text" value=""></td>
			</tr>

			<!-- ЮРИДИЧЕСКОЕ ЛИЦО -->
			<tr class="legal-person-input">
				<td class="label">Полное название организации: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="organisation"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">КПП: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="kpp"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">ОКПО:</td>
				<td><input class="customer-data" type="text" value="" name="okpo"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">Юридический адрес: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="jaddress"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">Фактический адрес: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="postaladdress"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">Банковские реквизиты: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="bank"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">ФИО Ген. директора: <span class="active">*</span></td>
				<td><input class="customer-data" type="text" value="" name="gendir"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">Контактное лицо:</td>
				<td><input class="customer-data" type="text" value="" name="contactperson"></td>
			</tr>
			<tr class="legal-person-input">
				<td class="label">Факс:</td>
				<td><input class="customer-data" type="text" value="" name="fax"></td>
			</tr>

	<!-- ------------------------------------- DATA ABOUT PAYMENT ------------------------------------- -->

			<tr class="legal-person-input individual-person-input">
				<td class="label">ORDER:</td>
				<td><input class="payment-data customer-data" name="ORDER" id="ORDER" type="text" value=""/></td>
			</tr>
			<tr>
				<td class="label">AMOUNT:</td>
				<td><input class="payment-data" name="AMOUNT" id="AMOUNT" type="text" value="20"/></td>
			</tr>
			<tr>
				<td class="label">CURRENCY:</td>
				<td><input class="payment-data" name="CURRENCY" id="CURRENCY" type="text" value="RUB"/></td>
			</tr>
			<tr>
				<td class="label">DESC:</td>
				<td><input class="payment-data" name="DESC" id="DESC" type="text" value="Test product"/></td>
			</tr>
			<tr>
				<td class="label">TERMINAL:</td>
				<td><input class="payment-data" name="TERMINAL" id="TERMINAL" type="text" value="79036829"/></td>
			</tr>
			<tr>
				<td class="label">TRTYPE:</td>
				<td><input class="payment-data" name="TRTYPE" id="TRTYPE" type="text" value="1"/></td>
			</tr>
			<tr>
				<td class="label">MERCH_NAME:</td>
				<td><input class="payment-data" name="MERCH_NAME" id="MERCH_NAME" type="text" value="ECOGEN_TECHNOLOGY_TEST"/></td>
			</tr>
			<tr>
				<td class="label">MERCHANT:</td>
				<td><input class="payment-data" name="MERCHANT" id="MERCHANT" type="text" value="790367686219999"/></td>
			</tr>
			<tr>
				<td class="label">NONCE:</td>
				<td><input class="payment-data" name="NONCE" id="NONCE" type="text" value="F2B2DD7E603A7ADA"/></td>
			</tr>
			<tr>
				<td class="label">BACKREF:</td>
				<td><input class="payment-data" name="BACKREF" id="BACKREF" type="text" value="http://kryotherm.hol.es/cart.php&exec_order=send"/></td>
			</tr>
			<tr>
				<td class="label">KEY:</td>
				<td><input class="payment-data" name="KEY" id="KEY" type="text" value="C50E41160302E0F5D6D59F1AA3925C45"/></td>
			</tr>
			<tr>
				<td class="label">TIMESTAMP:</td>
				<td><input class="payment-data" name="TIMESTAMP" id="TIMESTAMP" type="text" value="20141216090758"/></td>
			</tr>
			<tr>
				<td class="label">MAC_DATA:</td>
				<td><input class="payment-data" name="MAC_DATA" id="MAC_DATA" type="text" value=""</td>
			</tr>
			<tr>
				<td class="label">P_SIGN:</td>
				<td><input class="payment-data" name="P_SIGN" id="P_SIGN" type="text" value="" /</td>
			</tr>
			<tr>
				<td class="label">LANG:</td>
				<td><input class="payment-data" name="LANG" id="LANG" type="text" value=""/></td>
			</tr>
			<tr>
				<td class="label">SERVICE:</td>
				<td><input class="payment-data" name="SERVICE" id="SERVICE" type="text" value=""/></td>
			</tr>
			<tr>
				<td class="label">Комментарии пользователя:</td>
				<td><textarea name="descript"></textarea></td>
			</tr>
			<tr>
				<td colspan="2" class="vam">
					<span class="active">*</span> &mdash; поля обязательные для заполнения.
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input class="js-place-order-form-submit" type="submit" value="Подтвердить заказ"></td>
			</tr>
		</tbody>
	</table>
</form>
<br />
'

?>