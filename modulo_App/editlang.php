<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strIdioma    = request("var_lang�");
$strOper      = request("var_oper");
$intCodModulo = request("var_chavereg");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), $strOper);

$objConn = abreDBConn(CFG_DB); //Abertura do banco de dados

//Array com os �ndices das l�nguas. Para adicionar mais uma l�ngua, deve-se adicionar aqui!
$arrLangs = array("ptb","en","es");

if($strOper == "INS_LANG"){ //Verifica a opera��o e coloca o nome do arquivo padr�o
	$strIdioma = "ptb";
}
else{
	(empty($strIdioma)) ? $strIdioma = CFG_LANG : NULL;
}

try{
	$strSQL = " SELECT dir_app FROM sys_app WHERE cod_app = " . $intCodModulo; // Pesquisa pelo nome do diret�rio dos arquivos a serem editados
	$objRS  = $objConn->query($strSQL)->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
	
if(isset($objRS)){ 
	$strDir = $objRS["dir_app"];
} 
else{
	mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
	die();
}

$strTable = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
				<tr>
					<td align=\"left\" class=\"padrao_gde\">" . getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER) . " (" . getTText(strtolower($strOper),C_UCWORDS) . ")</td>
					<td align=\"right\">";
			
			$arrFile = array(); //Declara��o do array dos nomes dos arquivos
			
			//Coloca os nomes dos arquivos num array
			if($resHandle = opendir("../" . $strDir . "/lang/")){
				while(false !== ($strFile = readdir($resHandle))) {
					array_push($arrFile,strtolower($strFile));
				}
			}
			closedir($resHandle);
			
			switch($strOper){
				case "UPD_LANG":  /***** Caso a opera��o for de atualiza��o da l�ngua, entra aqui *****/
					$strTable .= "<select name=\"var_lang�\" onChange=\"trocarLang(this.value);\">
									<option value=\"\">Selecione a l�ngua</option>";
					foreach($arrFile as $strFile){	
						// Ele verifica se o arquivo n�o � igual a ".", "..", que n�o comece com "_" e que tenha a extens�o .lang 
						if($strFile != "." && $strFile != ".." && !preg_match("/^_/",$strFile) && preg_match("/.lang$/",$strFile)){
							// Tira a extens�o para passar apenas o nome do arquivo para a proxima p�gina
							$strFile = substr($strFile,0,strpos($strFile,"."));
							$strTable .= "<option value=\"" . $strFile . "\"";
							if($strIdioma == $strFile){
								$strTable .= " selected ";
							}
							$strTable .= ">" . $strFile . "</option>";
						}
					}
					$strTable .= "</select>";
				break;
				
				case "INS_LANG":  /***** Caso a opera��o for de inser��o de uma nova l�ngua, entra aqui *****/
					$strTable .= "<select name=\"var_lang�\">
									<option value=\"\">Selecione a l�ngua</option>";
					foreach($arrLangs as $strLang){ 
						$strTable .= "<option value=\"" . $strLang . "\"";
						$strTable .= (in_array(strtolower($strLang) . ".lang", $arrFile)) ? "style=\"color:#00AA00;\">" : ">" ;
						$strTable .= $strLang . "</option>";
					}
					$strTable .= "</select>";
				break;
				
				default:    /***** Caso n�o for selecionado uma opera��o v�lida, entra aqui *****/
					mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
					die();
				break;
			}
			
$strTable .= "
						</select>
					</td>
				</tr>
			</table>";
			
try{ //Tenta abrir o arquivo especificado para editar, caso contr�rio � exibida uma mensagem na tela
	if(!$arrFile = @file("../" . $strDir . "/lang/" . $strIdioma . ".lang")){
		throw new Exception("O arquivo n�o pode ser encontrado, favor entrar em contato com o suporte");
	}
	$strFileTip = "";//file_get_contents("../" . $strDir . "/lang/_model.lang"); // arquivo com as descri��es dos campos
}
catch(Exception $e){
	mensagem("err_stream_titulo","err_stream_desc",$e->getMessage(),"","erro",1);
	die();
}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
		<script>
			function submeter(prAction){
				if(document.formeditlang.var_lang�.selectedIndex != 0){
					strAction = prAction.toLowerCase();
					if(strAction == "aplicar"){
						document.formeditlang.var_location�.value = "editlang.php?var_oper=<?php echo($strOper); ?>&var_chavereg=<?php echo($intCodModulo); ?>";
					}
					else if(strAction == "ok"){
						document.formeditlang.var_location�.value = "data.php";
					}
					document.formeditlang.submit();
				}
			}
			
			function trocarLang(prLang){
				location.href="editlang.php?var_oper=<?php echo($strOper); ?>&var_chavereg=<?php echo($intCodModulo); ?>&var_lang�=" + prLang;
			}
		</script>
	</head>
<body style="margin:10px 0px 10px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<form name="formeditlang" action="editlangexec.php" method="post">
	<input type="hidden" name="var_dir�" value="<?php echo($strDir); ?>">
	<input type="hidden" name="var_location�" value="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none",$strTable,CL_CORBAR_GLASS_1); ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
					<tr> 
						<td align="center" valign="top">
							<table width="550" height="100%" border="0" cellpadding="0" cellspacing="4">
								<tr><td colspan="2" height="10"></td></tr>
								<?php
									$strColor = CL_CORLINHA_2;
									foreach($arrFile as $intLineNum => $strLine){
										$strColor = ($strColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
										
										(!empty($strBuffer)) ? $strLine = trim($strBuffer) . " " . trim($strLine) : $strLine = trim($strLine); 
										
										if(preg_match("/'$/",$strLine) || $strIdioma == "_model"){ //verifica se tem o caracter "'" no final da linha, caso n�o tiver ele vai juntar com a pr�xima linha.
											
											$strIndex = preg_replace("/(>?)|[ ]*('(.*)')/is","",$strLine); //extrai o �ndice da linha do arquivo
											($strOper != "INS_LANG") ? $strValue = preg_replace("/>\w+|[ ]+'|\\\\*|'$/","",$strLine) : $strValue = ""; //Se n�o for o arquivo padr�o, � extra�do o valor do �ndice da linha do arquivo
											
											$strTip = langIndexComment($strIndex, $strFileTip);	//Acha o coment�rio dentro da string do arquivo passado no par�metro
											
											echo("
								<tr bgcolor=\"" . $strColor . "\">
									<td width=\"100\" nowrap align=\"right\" valign=\"top\" title=\"" . $strTip . "\">
										<b>" . $strIndex . ":</b>
									</td>
									<td style=\"padding-left:10px\">");

											if(strlen($strValue) <= 50){
												echo("	<input type=\"text\" name=\"" . $strIndex . "\" value=\"" . $strValue . "\" size=\"40\">");
											}
											else{
												echo("	<textarea name=\"" . $strIndex . "\" rows=\"3\" cols=\"40\">" . $strValue . "</textarea>");
											}
											
											if($strTip != ""){
												echo("&nbsp;&nbsp;<span class=\"comment_med\">(" . $strTip . ")</span>");
											}
											
											echo("
									</td>
								</tr>");
											
											$strBuffer = "";
										}
										else{
											$strBuffer = $strLine;
										}
									}
								?>
								<tr><td colspan="2" height="5"></td></tr>
								<tr><td height="1" align="center" valign="middle" colspan="2" bgcolor="#CCCCCC"></td></tr>
								<tr>
									<td align="right" style="padding:10px 0px 10px 10px;" colspan="2">
										<button onClick="submeter('Ok')"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
										<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
										<button onClick="submeter('Aplicar')"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
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
	</form>
</body>
</html>