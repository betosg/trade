<html>
	<head>
		<title>Console r�pido - PROEVENTO STUDIO</title>
		<style>
			td, th   { font-family:Arial; font-size:12px; }
			textarea { font-family:Courier New; }
		</style>
	</head>
	<body>
		<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center">
		  <form name="formconsole" action="consoleexec.asp" method="post">
			<tr>
				<th colspan="2">Console r�pido</th>
			</tr>
			<tr><td colspan="2" height="15"></td></tr>
			<tr>
				<td align="right" nowrap>Banco de Dados (.mdb):&nbsp;</td>
				<td><input type="text" name="var_db_path" size="93" value="D:\wwwroot\proeventovista\_database\"></td>
			</tr>
			<tr>
				<td align="right" nowrap>Select Origem (.mdb):&nbsp;</td>
				<td>
					<textarea name="var_select_dados" rows="10" cols="70"></textarea><br>
					<small><i>Instru��o de sele��o de dados no Access</i></small>
				</td>
			</tr>
			<tr><td colspan="2" height="10"></td></tr>
			<tr>
				<td align="right" nowrap>Insert Dados (PostgreSQL):&nbsp;</td>
				<td>
					<textarea name="var_insert_dados" rows="10" cols="70"></textarea><br>
					<small><i>Instru��o para inser��o no PostgreSQL</i></small>
				</td>
			</tr>
			<tr><td colspan="2" height="30"></td></tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="Enviar">
					&nbsp;&nbsp;
					<input type="button" value="Cancelar" onClick="history.back();">
				</td>
			</tr>
		  </form>
		</table>
	</body>
</html>