<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$intCodUsuario = request("var_chavereg");

$strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "UPD_DIR");

$strBGColor = CL_CORLINHA_1;

$objConn    = abreDBConn(CFG_DB);
if($intCodUsuario != ""){
	try{
		$strSQL = " SELECT cod_app, dir_app
					 FROM sys_app 
					ORDER BY dir_app ";
		$objResult = $objConn->query($strSQL); //Seleciona todas as aplicações cadastradas no sistema
		
		$strSQL = " SELECT nome FROM sys_usuario WHERE cod_usuario = " . $intCodUsuario;
		$objRS = $objConn->query($strSQL)->fetch(); // Seleciona o nome do usuário
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc",$e->getMessage(),"","erro",1);
	die();
}

$strNomeUsuario = $objRS["nome"]; //nome do usuário 
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script>
var intI = 0;
function submeter(){
	if(intI < document.forms.length){
		document.forms[intI].var_cod_direitos.value = processarDireitos(intI);
		document.forms[intI].submit();
		intI++;
	}
	else{
		intI = 0;
		location.href="setdireitos.php?var_chavereg=<?php echo($intCodUsuario); ?>";
	}
}

function processarDireitos(prCodForm){
	var codigos = "";
	var intAux = 0;
	
	while (eval("document.forms[" + prCodForm + "].msguid_" + intAux) != null){
	    if (eval("document.forms[" + prCodForm + "].msguid_" + intAux) != null){
			if (eval("document.forms[" + prCodForm + "].msguid_" + intAux).checked){
			    if (codigos != ""){
			      codigos = codigos + ", " + eval("document.forms[" + prCodForm + "].msguid_" + intAux).value;
			    }
			    else{
			      codigos = eval("document.forms[" + prCodForm + "].msguid_" + intAux).value;
			    }
			}
		}
	  intAux++;
	}
	return(codigos);
}
</script>
</head>
<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" text="#000000" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("650","none",getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER) . " - " . getTText("tabela_de_direitos",C_UCWORDS) . " <b>(" . $strNomeUsuario . ")</b>",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			<tr>
				<td align="center" valign="top">
					<table width="600" border="0" cellspacing="0" cellpadding="4">
			<?php
				foreach($objResult as $objRS){ // Foreach das aplicações
					echo("<tr bgcolor=\"" . $strBGColor . "\">
							<form name=\"formdir_" . $objRS["cod_app"] . "\" id=\"formdir_" . $objRS["cod_app"] . "\" action=\"setdireitosexec.php\" method=\"post\" target=\"VBossIframeSave_" . $objRS["cod_app"] . "\">
							<input type=\"hidden\" name=\"var_coduser\" value=\"" . $intCodUsuario . "\">
							<input type=\"hidden\" name=\"var_codapp\"  value=\"" . $objRS["cod_app"] . "\">
							<input type=\"hidden\" name=\"var_cod_direitos\"  value=\"\">
							<td align=\"right\" nowrap><b>" . $objRS["dir_app"] . ":</b></td>
							<td style=\"padding-left:10px;\">
								<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
									<tr>");
								
					
					$strSQL = " SELECT sys_app_direito.id_direito 
								 FROM sys_app_direito_usuario, sys_app_direito  
								 WHERE sys_app_direito_usuario.cod_usuario = " . $intCodUsuario . "
								  AND sys_app_direito.cod_app = " . $objRS["cod_app"] . " 
								  AND sys_app_direito_usuario.cod_app_direito = sys_app_direito.cod_app_direito";
					$objResultLocal = $objConn->query($strSQL); // Seleciona as operações existentes na aplicação
					
					$arrPerm = array();
					foreach($objResultLocal as $objRSLocal){ //Preenche um array com as operações
						array_push($arrPerm,$objRSLocal["id_direito"]);
					}
					
					try{	
						$strSQL = " SELECT * FROM sys_app_direito 
									WHERE cod_app = " . $objRS["cod_app"] . "
									ORDER BY cod_app_direito";
						$objResultLocal = $objConn->query($strSQL); //Seleciona as operações do usuário para esta aplicação
					}
					catch(PDOException $e){
						mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
						die();
					}
					
					$intI = 0; // Contador auxiliar para os checkboxes
					foreach($objResultLocal as $objRSLocal){ //Foreach das operações nessa aplicação
						echo("			<td>" . $objRSLocal["id_direito"] . "</td>
										<td width=\"1\"><input type=\"checkbox\" name=\"msguid_" . $intI . "\" id=\"msguid_" . $intI . "\" value=\"" . $objRSLocal["cod_app_direito"] . "\"");
						(in_array($objRSLocal["id_direito"],$arrPerm)) ? print(" checked ") : NULL; //Verifica se o usuário tem a premissão para essa operação
						echo(" class=\"inputclean\">&nbsp;&nbsp;</td>");
						$intI++;
					}
					
					$objResultLocal->closeCursor();
					
					echo("          </form>
									</tr>
								</table>
							</td>
							<td width=\"99%\" align=\"right\" valign=\"middle\" nowrap>
								<iframe id=\"VBossIframeSave_" . $objRS["cod_app"] . "\" allowtransparency=\"true\" frameborder=\"0\" width=\"16\" height=\"16\" name=\"VBossIframeSave_" . $objRS["cod_app"] . "\" scrolling=\"no\"></iframe>
							&nbsp;&nbsp;</td>
						</tr>");
						
					$strBGColor = ($strBGColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
				}
				$objResult->closeCursor();
			?>
						<tr><td height="5" colspan="3"></td></tr>
						<tr><td height="1" colspan="3" bgcolor="#CCCCCC"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeter();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php athEndFloatingBox(); ?>
   </td>
 </tr>
</table>
</body>
</html>