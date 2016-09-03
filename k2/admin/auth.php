<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/k2/admin/css/auth.css">
	<script src="/k2/admin/js/java.js"></script>
	<title>K2CMS - Авторизация</title>
</head>
<body>
<div class="line">
	<form method="post">
		<table>
			<tr>
				<td>
					<div class="logo"></div>
				</td>
				<td>
					<div class="icon">
						<div class="icon-login"></div>
					</div>
					<input type="text" name="AUTH_LOGIN" class="inp" value="<?=htmlspecialchars($_POST['AUTH_LOGIN'])?>"></td>
				<td>
					<div class="icon">
						<div class="icon-password"></div>
					</div>
					<input type="password" name="AUTH_PASSWORD" class="inp"></td>
				<td><input type="submit" class="sub" value="Войти"></td>
			</tr>
			<tr>
				<td class="copy">&copy; 2016</td>
				<td colspan="3"><label><input type="checkbox" name="AUTH_REMEMBER" value="1">Запомнить меня на этом компьютере</label></td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>