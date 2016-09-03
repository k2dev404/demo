<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Квитанция</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
H1 {font-size: 12pt;}
p, ul, ol, h1 {margin-top:6px; margin-bottom:6px}
td {font-size: 9pt;}
small {font-size: 7pt;}
body {font-size: 10pt;}
</style>
</head>
<body bgColor="#ffffff">

<table border="0" cellspacing="0" cellpadding="0" style="width:180mm; height:145mm;">
<tr valign="top">
	<td style="width:50mm; height:70mm; border:1pt solid #000000; border-bottom:none; border-right:none;" align="center">
	<b>Извещение</b><br>
	<font style="font-size:53mm">&nbsp;<br></font>
	<b>Кассир</b>
	</td>
	<td style="border:1pt solid #000000; border-bottom:none;" align="center">
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><small><i>Форма № ПД-4</i></small></td>
			</tr>
			<tr>
				<td style="border-bottom:1pt solid #000000;">%DATA_POL_NAME%</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование получателя платежа)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:37mm; border-bottom:1pt solid #000000;">%DATA_INN%/%DATA_KPP%</td>
				<td style="width:9mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">%DATA_N_SCHET%</td>
			</tr>
			<tr>
				<td align="center"><small>(ИНН получателя платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер счета получателя платежа)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>в&nbsp;</td>
				<td style="width:73mm; border-bottom:1pt solid #000000;">%DATA_BANK%</td>
				<td align="right">БИК&nbsp;&nbsp;</td>
				<td style="width:33mm; border-bottom:1pt solid #000000;">%DATA_BIK%</td>
			</tr>
			<tr>
				<td></td>
				<td align="center"><small>(наименование банка получателя платежа)</small></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%DATA_KOR_SCHET%</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:60mm; border-bottom:1pt solid #000000;">Оплата заказа № %ORDER_ID% от %DATE%</td>
				<td style="width:2mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер лицевого счета (код) плательщика)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Ф.И.О. плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%PAYER_NAME%&nbsp;</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Адрес плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%PAYER_ADDRESS%&nbsp;</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Сумма платежа&nbsp;<font style="text-decoration:underline;">%ORDER_SUM_RUB%&nbsp;</font>&nbsp;руб.&nbsp;<font style="text-decoration:underline;">&nbsp;%ORDER_SUM_KOP%&nbsp;</font>&nbsp;коп.</td>
				<td align="right">&nbsp;<!-- &nbsp;&nbsp;Сумма платы за услуги&nbsp;&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп. --></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
				<td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 20____ г.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td><small>С условиями приема указанной в платежном документе суммы,
				в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
			</tr>

		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><b>Подпись плательщика _____________________</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr valign="top">
	<td style="width:50mm; height:70mm; border:1pt solid #000000; border-right:none;" align="center">
	<b>Извещение</b><br>
	<font style="font-size:53mm">&nbsp;<br></font>
	<b>Кассир</b>
	</td>
	<td style="border:1pt solid #000000;" align="center">
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><small><i>Форма № ПД-4</i></small></td>
			</tr>
			<tr>
				<td style="border-bottom:1pt solid #000000;">%DATA_POL_NAME%</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование получателя платежа)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:37mm; border-bottom:1pt solid #000000;">%DATA_INN%/%DATA_KPP%</td>
				<td style="width:9mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">%DATA_N_SCHET%</td>
			</tr>
			<tr>
				<td align="center"><small>(ИНН получателя платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер счета получателя платежа)</small></td>

			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>в&nbsp;</td>
				<td style="width:73mm; border-bottom:1pt solid #000000;">%DATA_BANK%</td>
				<td align="right">БИК&nbsp;&nbsp;</td>
				<td style="width:33mm; border-bottom:1pt solid #000000;">%DATA_BIK%</td>
			</tr>
			<tr>
				<td></td>
				<td align="center"><small>(наименование банка получателя платежа)</small></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%DATA_KOR_SCHET%</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:60mm; border-bottom:1pt solid #000000;">Оплата заказа № %ORDER_ID% от %DATE%</td>
				<td style="width:2mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер лицевого счета (код) плательщика)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Ф.И.О. плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%PAYER_NAME%</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Адрес плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;">%PAYER_ADDRESS%&nbsp;</td>
			</tr>
		</table>

		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Сумма платежа&nbsp;<font style="text-decoration:underline;">%ORDER_SUM_RUB%&nbsp;</font>&nbsp;руб.&nbsp;<font style="text-decoration:underline;">&nbsp;%ORDER_SUM_KOP%&nbsp;</font>&nbsp;коп.</td>
				<td align="right">&nbsp;<!-- &nbsp;&nbsp;Сумма платы за услуги&nbsp;&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп. --></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
				<td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 20____ г.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td><small>С условиями приема указанной в платежном документе суммы,
				в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><b>Подпись плательщика _____________________</b></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<script type="text/javascript">window.print();</script>