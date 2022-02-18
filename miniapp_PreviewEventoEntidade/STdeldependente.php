<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	/***           DEFINI��O DE PAR�METROS            ***/
	/****************************************************/
	$var_cod_cad     = request("var_cod_cad");  // CHAVE PAI P/ RESIZE	
	$intCodDado   = request("var_chavereg");  
	$strRedirect 	 = request("var_redirect"); // redirect para qual p�gina deve ir

	
	
	// ABERTURA DE CONEX�O COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	
	
	// SQL PADR�O DA LISTAGEM
	try{

		$strSQL = "           SELECT nome ";
		$strSQL = $strSQL . "      , TO_CHAR(cad_funcionario_dependente.data_nasc, 'DD/MM/YYYY') AS dt_nasc ";
		$strSQL = $strSQL . "      , parentesco ";
		$strSQL = $strSQL . "      , cpf ";
		$strSQL = $strSQL . " FROM cad_funcionario_dependente ";
		$strSQL = $strSQL . "		LEFT JOIN cad_parentesco ON cad_funcionario_dependente.cod_parentesco = cad_parentesco.cod_parentesco ";
		$strSQL = $strSQL . " WHERE cad_funcionario_dependente.cod_dependente = " . $intCodDado;
//die($strSQL);
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		
		// localiza o cod_cadastro pai para que seja
		// feito o resize do iframe corretamente	
	
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	
	/***    A��O DE PREPARA��O DA GRADE - OPCIONAL    ***/
	/****************************************************/
	// Controle de acesso diferenciado por estar em n�vel IFRAME.
	// caso sua p�gina n�o esteja em um IFRAME DETAIL, utilize a-
	// penas a linha abaixo:
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");
	// no caso abaixo, fazemos a verficarAcesso() retornar um valor
	// false caso o usu�rio nao tenha direito sobre a app, e com base
	// no true ou false manipulamos a mensagem para que funcione no
	// IFRAME DETAIL. Ainda assim, posicionamos esse trecho de codigo
	// aqui pq anteriormente n�o t�nhamos o cod_fornec para que seja
	// feito resize do iframe.

	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"DEL","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","A��o a ser realizada:&nbsp;DEL","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME */";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
		$strScript .=" </script>";
		echo($strScript);die();
	}
	
	/***         FUN��ES AUXILIARES - OPCIONAL        ***/
	/****************************************************/
	$strColor = CL_CORLINHA_2; 				// inicializa variavel para pintar linha
	function getLineColor(&$prColor){ 	// fun��o para cores de linhas
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			/* suas adapta��es css aqui */
		</style>
		<script language="javascript" type="text/javascript">
			/* seu c�digo javascript aqui */
			/****** Fun��es de a��o dos bot�es - In�cio ******/
			var strLocation = null;
			
			function ok() {
				strLocation = "../miniapp_cadfuncionario_dependente/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
				submeterForm();
			}

			function cancelar() {
				document.location.href = "../miniapp_cadfuncionario_dependente/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
			/****** Fun��es de a��o dos bot�es - Fim ******/
		</script>
	</head>
	
	<!-- UTILIZAMOS O BODY ABAIXO QUANDO ESTA P�GINA N�O � CHAMADA EM UMA IFRAME DETAIL -->
	<!--<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" 
	     background="../img/bgFrame_|?php echo(CFG_SYSTEM_THEME);?|_main.jpg">-->
	
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none",getTText("dependente_inserir_title",C_NONE)." (". getTText("deletar",C_NONE).")",CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="../_database/athdeletetodb.php" method="POST">
	<input type="hidden" name="DEFAULT_TABLE" 			value="cad_funcionario_dependente" />
	<input type="hidden" name="DEFAULT_DB" 				value="<?php echo(CFG_DB);?>" />
	<input type="hidden" name="FIELD_PREFIX" 			value="DBVAR_" />
	<input type="hidden" name="RECORD_KEY_NAME" 		value="COD_DEPENDENTE" />
	<input type="hidden" name="RECORD_KEY_VALUE"		value="<?php echo($intCodDado);?>" />
	<input type="hidden" name="DEFAULT_LOCATION" 		value=""/>
	
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="500" bgcolor="#FFFFFF" style="background-color:#FFFFFF;border:1px solid #CCCCCC;">
		<!-- ESTA LINHA ABRIGA A FRASE 'Preencha campos corretamente:', USADA NAS OPERA��ES INS -->
		<tr><td align="left" valign="top" style="padding:5px 0px 0px 15px;">&nbsp;</td></tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 50px 0px 50px;">
				<table cellspacing="2" cellpadding="3" border="0" width="100%">					
					<!-- MIOLO DA TABELA - CORE DA DIALOG ONDE FICAM  OS CAMPOS -->
					<!-- LINHA DO GRUPO E NOME GRUPO -->
					<tr bgcolor="#FFFFFF">
						<td width="27%" align="right">&nbsp;</td>
						<td width="73%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_contato_ins",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<!-- FIM LINE GRUPO -->
					<!-- CAMPOS: OS CAMPOS S�O DIVIDOS BASICAMENTE EM UM ROTULO + CAMPO / VALOR POR LINHA -->
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right">
							<strong><?php echo(getTText("codigo",C_UCWORDS));?>:</strong>
						</td>
						<td width="73%" align="left"><?php echo($intCodDado);?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><?php echo(getValue($objRS,"nome"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("data_nasc",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><?php echo(getValue($objRS,"data_nasc"));?></td>
					</tr>

					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("parentesco",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><?php echo(getValue($objRS,"parentesco"));?></td>
					</tr>
										
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><?php echo(getValue($objRS,"cpf"));?></td>
					</tr>
																									
					<!-- FIM CAMPOS -->
					<!-- FIM DO CORE DE CAMPOS DA TABELA -->					
					<tr><td colspan="2" class="destaque_med"><?php echo(getTText("campos_obrig",C_NONE));?></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
				</table>			
			</td>
		</tr>
		<!-- LINHA DOS BUTTONS E AVISO -->
		<tr>
			<td colspan="3" style="padding:10px 50px 0px 50px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="70%">
							
							<!-- MENSAGEM DE AVISO VAI AQUI, PARA DIALOG DE DELE��O -->
							<!-- CASO VOC� QUEIRA INFORMAR UMA MENSAGEM, ALTERE O ICONE
							 	 E O LANG UTILIZADO PARA A MENSAGEM --->
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
								<tr>
									<td align="right" width="23%">
										<img src="../img/mensagem_aviso.gif" />
									</td>
									<td align="left"  width="77%" style="padding-left:5px;">
										<?php echo(getTText("aviso_del_txt",C_NONE));?>
									</td>
								</tr>
							</table>
							<!-- BLOCO PARA MENSAGEM . FIM -->
							
						</td>
						<!-- goNext() -->
						<td width="20%" align="left">
							<button onClick="ok();">
								<?php echo(getTText("ok",C_UCWORDS));?>
							</button>
						</td>
						<td width="10%" align="left">
							<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>	
		<!-- LINHA ACIMA DOS BOT�ES -->
	</table>
	</form>
	<?php athEndFloatingBox();?>
	</td>
	</tr>
</table>
</body>
	<script type="text/javascript">
	  // Quando esta p�gina for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?PHP echo $var_cod_cad; ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>