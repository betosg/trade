<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	$intcodigo       = request("var_cod_dado"); 
	$strRedirect 	 = request("var_redirect"); // redirect para qual p�gina deve ir
	
	
	// ABERTURA DE CONEX�O COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	// SQL PADR�O DA LISTAGEM - BREVE DESCRI��O
	try{
		// seleciona todos os contatos do Industrial
		// com cod_cadastro enviado para este script
		$strSQL = "	select cad_pf_fornec.nome
					 , relac_pj_pf_fornec.funcao
					 , cad_pf_fornec.email
					 , cad_pf_fornec.cod_pf_fornec
					 , cad_pf_fornec.endprin_fone1
					 , cad_pf_fornec.cpf
				FROM cad_pj_fornec 
						INNER JOIN relac_pj_pf_fornec ON cad_pj_fornec.cod_pj_fornec =  relac_pj_pf_fornec.cod_pj_fornec
						INNER JOIN cad_pf_fornec ON cad_pf_fornec.cod_pf_fornec =  relac_pj_pf_fornec.cod_pf_fornec
				WHERE cad_pf_fornec.cod_pf_fornec = " . $intcodigo;
				
					
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		
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

//	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"UPD","not die")){
//		mensagem("err_acesso_titulo","err_acesso_desc","A��o a ser realizada:&nbsp;UPD","","erro",1,"not html");
//		$strScript  = "";
//		$strScript .= "<script type=\"text/javascript\">";
//		$strScript .= "/* usado para redimensionar o IFRAME ";
//		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
//		$strScript .=" <script>";
//		echo($strScript);die();
//	}
	
		

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
				//strLocation = "../miniapp_cadpfFornec/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
				submeterForm();
			}

			function cancelar() {
				window.location = "../miniapp_cadpfFornec/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
			}
			
			
			function submeterForm() {
				document.formstatic.submit();
			}
			
			function selcargo() {
				var strnome = document.formstatic.DBVAR_STR_IDCARGO.options[document.formstatic.DBVAR_STR_IDCARGO.selectedIndex].text
				document.formstatic.DBVAR_STR_CARGO.value = strnome
			}		
			
			function sobrenome(){
				var var_NomeContato
				var var_QtdaSobrenome							
				var_NomeContato = document.getElementById("DBVAR_STR_CONTATO").value;
				var_NomeContato = var_NomeContato.split(" ");
				if (var_NomeContato.length < 2) {alert("Voc� precisa inserir NOME E SOBRENOME para o contato.");document.getElementById("DBVAR_STR_CONTATO").focus();}
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
	<?php athBeginFloatingBox("520","none",getTText("contatos_inserir_title",C_NONE),CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="STupdcontatoexec.php" method="POST" onSubmit="checkCPF(document.formstatic.DBVAR_STR_CPF.value,true);" />
	
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="500" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
		<tr>
			<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
				<strong><?php echo(getTText("preencha_campos",C_NONE));?></strong>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 50px 0px 50px;">
				<table cellspacing="2" cellpadding="3" border="0" width="100%">
						
					
					<!-- MIOLO DA TABELA - CORE DA DIALOG ONDE FICAM  OS CAMPOS -->
					<!-- LINHA DO GRUPO E NOME GRUPO -->
					<tr bgcolor="#FFFFFF">
						<td width="27%" align="right">&nbsp;</td>
						<td width="73%" align="left" class="destaque_gde">
							<strong><?php echo(getTText("dados_contato",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<!-- FIM LINE GRUPO -->
					<!-- CAMPOS: OS CAMPOS S�O DIVIDOS BASICAMENTE EM UM ROTULO + CAMPO / VALOR POR LINHA -->
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("cod_ind_pai",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left">
							<?php echo($intcodigo);?>
                            <input type="hidden" name="DBVAR_INT_COD_PF" id="DBVAR_INT_COD_PF" style="width:200px;" maxlength="99" value="<?php echo($intcodigo);?>"/>							
                            <input type="hidden" name="DBVAR_INT_COD_PJ" id="DBVAR_INT_COD_PJ" style="width:200px;" maxlength="99" value="<?php echo($var_cod_cad);?>"/>							
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("contato",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><input type="text" name="DBVAR_STR_CONTATO" id="DBVAR_STR_CONTATO" style="width:200px;" maxlength="99" value="<?php echo(getValue($objRS,"nome"));?>"/></td>
					</tr>					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("cargo",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"> <input type="text" name="DBVAR_STR_CARGO" style="width:150px;" maxlength="30"  value="<?php echo(getValue($objRS,"funcao"));?>"/>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("cpf",C_TOUPPER));?>:</strong></td>
						<td width="73%" align="left"> 
							<input type="text" name="DBVAR_STR_CPF" maxlength="11" style="width:100px;" onBlur="checkCPF(this.value,true);" value="<?php echo(getValue($objRS,"cpf"));?>"/>
							<span class="comment_peq"><?php echo(getTText("obs_somente_numeros",C_NONE));?></span>							</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("email",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><input type="text" name="DBVAR_STR_EMAIL" size="50" style="width:220px;"  value="<?php echo(getValue($objRS,"endprin_fone1"));?>"/></td>
					</tr>	
                    <tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong><?php echo(getTText("telefone1",C_UCWORDS));?>:</strong></td>
						<td width="73%" align="left"><input type="text" name="DBVAR_STR_FONE" size="50" style="width:220px;"  value="<?php echo(getValue($objRS,"email"));?>"/></td>
					</tr>					
				</table>
			</td>					
		</tr>																		
        <!-- FIM CAMPOS -->
        <!-- FIM DO CORE DE CAMPOS DA TABELA -->
        <tr><td colspan="2" class="destaque_med"></td></tr>
        <tr><td colspan="2" class="linedialog"></td></tr>										
		<!-- LINHA DOS BUTTONS E AVISO -->
		<tr>
			<td colspan="3" style="padding:10px 50px 0px 50px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="70%">
							<!-- MENSAGEM DE AVISO VAI AQUI, PARA DIALOG DE DELE��O -->
							<!-- CASO VOC� QUEIRA INFORMAR UMA MENSAGEM, ALTERE O ICONE
							 	 E O LANG UTILIZADO PARA A MENSAGEM
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
								<tr>
									<td align="right" width="23%">
										<img src="../img/mensagem_aviso.gif" />
									</td>
									<td align="left"  width="77%">|?php echo(getTText("aviso_del_resp_txt",C_NONE));?|</td>
								</tr>
							</table>
							<!-- BLOCO PARA MENSAGEM . FIM -->
						</td>
						<!-- goNext() -->
						<td width="10%" align="left">
							<button onClick="ok();" id="botaoOK" name="botaoOK">
								<?php echo(getTText("ok",C_UCWORDS));?>
							</button>
						</td>
						<td width="10%" align="left">
							<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						</td>
						<td width="10%" align="left">
							<?php /*?><button onClick="aplicar();">
								<?php echo(getTText("aplicar",C_UCWORDS));?>
							</button><?php */?>
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
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo $var_cod_cad; ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>