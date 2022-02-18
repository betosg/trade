<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// TRATAMENTO PARA CÓDIGO DA PJ
	$intCodPJ   = request("var_chavereg"); 		// getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	$flagInsert = request("var_flag_inserir");	// FLAG PARA TIPO DE INSERÇÃO DE PF
	
	
	$strSesPfx 	= strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
	
	// ABERTURA DE CONEXÃO NO BANCO
	$objConn   	= abreDBConn(CFG_DB);
	
	// TRATAMENTO PARA FLAG DE INSERÇÃO
	$strTitle  	= ($flagInsert == "INS_HOMO") ? getTText("insercao_de_colab_homologando",C_NONE) : getTText("insercao_de_colab_credencial",C_NONE);
?> 
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE);?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript">
		function ok() {
			if (document.formverifica.var_cod_pf.value == "") {
			   //alert('cheguei');
				document.formverifica.action = "../modulo_CadPJ/STinsFreePFSinog.php";
				document.formverifica.submit();
			} else {
				//alert('cheguei relac');
				document.formverifica.action = "../modulo_CadPJ/STinsFreePFRelacSinog.php";
				document.formverifica.submit();
			}
		}
	
		function cancelar() {
			location.href = "../modulo_CadPJ/STviewpfsSinog.php?var_chavereg=<?php echo($intCodPJ);?>";
		}
	</script>
</head>
<body bgcolor="#FFFFFF">
<!-- background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;"-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
	<tr>
	<form name="formverifica" id="formverifica" action="" method="post">
		<input type="hidden" name="var_chavereg"      id="var_chavereg"     value="<?php echo($intCodPJ); ?>" />
		<input type="hidden" name="var_flag_inserir"  id="var_flag_inserir" value="<?php echo($flagInsert);?>" />
   		<td align="center" valign="top">
		<?php athBeginFloatingBox("630","none",getTText("novo_colaborador",C_NONE)." - (".$strTitle.")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<tr><td height="22" style="padding:10px">&nbsp;</td></tr>
			<tr> 
		  		<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><strong><?php echo(getTText("verificacao_de_cpf",C_TOUPPER));?></strong></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td width="25%" align="right" valign="top" nowrap style="padding-left:35px;"><strong>*Cod. PF:</strong></td>
							<td width="75%" nowrap align="left">
								<input name="var_cod_pf" id="var_cod_pf" value="" type="text" size="30" maxlength="11" title="CPF" tabindex="1" onKeyPress="javascript:return validateNumKey(event);">
                                <a href="javascript:abreJanelaPageLocal('../modulo_CadPF/?var_acao=single&var_fieldname=var_cod_pf&var_formname=formverifica','');"><img src="../img/icon_search.gif" border="0" hspace="5" align="absmiddle"></a>
								<!--
                                <br /><span class="comment_med"> Insira o código de PF(pessoa fisíca) ou clique na lupa para ver a grade PF,
                                <br>onde você poderá clicar no código desejado para enviar para o campo,
                                <br>aperte ok e assim irá para formulario de PF relacionado.
                                <br>Ou mande o campo vazio e preencha o cadastro de COLABORADOR.</span>
                                //-->
                                <br /><span class="comment_med">Utilize a LUPA para pesquisar uma PF (Pessoa física) sem CPF.
                                <br>se desejar inserir uma nova PF deixe o campo em branco aperte [ok].</span>
							</td>
						</tr>
						<tr><td colspan="2" height="10">&nbsp;</td></tr>
						<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>		
						<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding:10px 30px 20px 30px;">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td width="5%" align="right" valign="top" nowrap style="padding-left:15px;padding-right:10px;"><img src="../img/mensagem_info.gif" alt="INFORMAÇÃO" title="INFORMAÇÃO" /></td>
							<td align="left" width="40%"><?php echo(getTText("aviso_verificacao_cpf_txt",C_NONE));?></td>
							<td align="right" width="10%"  style="padding:10px 0px 10px 10px;">
								<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							</td>
							<td align="right" width="10%" style="padding:10px 0px 10px 10px;">
								<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
					</table>
				</td>								
			</tr>					
		</table>
		<?php athEndFloatingBox();?>
		</td>
	</form>
	</tr>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php $objConn = NULL; ?>