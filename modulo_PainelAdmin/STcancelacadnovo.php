<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodPJ  = request("var_chavereg");
	
	// PREFIXO DO MODULO
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	// ABERTURA DE CONEXÃO COM DB	
	$objConn   = abreDBConn(CFG_DB);
	
	// SQL QUE BUSCA A PJ A SER LIBERADA
	try{
		$strSQL = "
			SELECT 
				  cad_pj.nome_fantasia
				, cad_pj.razao_social
				, cad_pj.cod_pj
				, cad_pj.cnpj
				, cad_pj.insc_est
				, cad_pj.email
				, cad_pj.capital
				, cad_pj.num_funcionarios
				, cad_pj.website
				, cad_pj.contato
				, cad_pj.endprin_fone1
				, cad_pj.arquivo_1
				, cad_pj.arquivo_2
				, cad_pj.arquivo_3
				, sys_usuario.cod_usuario
				, sys_usuario.sys_dtt_ins
				, sys_usuario.id_usuario
			FROM
				  sys_usuario
			INNER JOIN cad_pj ON (sys_usuario.codigo = cad_pj.cod_pj)
			WHERE cad_pj.cod_pj = ".$intCodPJ;
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_2;
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			function ok() {
				var strMSG = "";
				
				if(document.getElementById("var_obs_cancelamento").value == ""){ strMSG += "\n\nCADASTRO:"; }
				if(document.getElementById("var_obs_cancelamento").value == ""){ strMSG += "\nObs do Cancelamento:"; }
				if(strMSG != ""){ alert("Informe os Campos Obrigatórios:" + strMSG); return(null); }
				else{
					document.formstatic.submit();
				}
			}

			function cancelar() {
				location.href="../modulo_PainelAdmin/STindex.php";
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx."_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("720","none","CANCELAMENTO DE CADASTRO - (Confirmação)",CL_CORBAR_GLASS_1); ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    <form name="formstatic" action="../modulo_PainelAdmin/STcancelacadnovoexec.php" method="post">
		<input type="hidden" name="var_id_usuario" value="<?php echo(getValue($objRS,"id_usuario"));?>">
		<input type="hidden" name="var_email"      value="<?php echo(getValue($objRS,"email"));?>">
		<input type="hidden" name="var_empresa"    value="<?php echo(getValue($objRS,"nome_fantasia"));?>">
		<input type="hidden" name="var_cod_pj"     value="<?php echo(getValue($objRS,"cod_pj"));?>" />
		<tr><td style="padding:20px 0px 0px 80px;"><strong><?php echo(getTText("aviso_cancela_confirma",C_NONE)); ?></strong></td></tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 80px;" width="100%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr height="10"><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="bottom" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
				  	</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
  					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("cod",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'cod_pj'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("cnpj",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'cnpj'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("insc_est",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'insc_est'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("razao_social",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'razao_social'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("nome_fantasia",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'nome_fantasia'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("email",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,'email'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("website",C_NONE));?></strong></td>
						<td><?php echo(getValue($objRS,'website'));?></td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" width="30%"><strong><?php echo(getTText("contato",C_NONE));?>:</strong></td>
						<td>
							<?php echo(getValue($objRS,"contato"));?>
							&nbsp;&nbsp;<strong><?php echo(getTText("endprin_fone1",C_NONE))?></strong>
							<?php echo(getValue($objRS,'endprin_fone1'));?>
						</td>
					</tr>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td align="right" valign="top" width="30%"><strong><?php echo(getTText("documentos",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="left" valign="top">
									<div style="padding-bottom:6px;">
									<?php
										// Exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_1') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_1'));
											// Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											// que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?>&bull;&nbsp;<a href="#" onClick="AbreJanelaPAGE('../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,"arquivo_1"));?>','500','400');" style="color:#009933;"><?php echo(getvalue($objRS,'arquivo_1')); ?></a>
									<?php } ?>
									</td>
								</tr>
								<tr>
									<td align="left" valign="top">
									<div style="padding-bottom:6px;">
									<?php
										// Exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_2') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_2'));
											// Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											// que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?>&bull;&nbsp;<a href="#" onClick="AbreJanelaPAGE('../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,"arquivo_2"));?>','500','400');" style="color:#009933;"><?php echo(getvalue($objRS,'arquivo_2')); ?></a>
									<?php } ?>
									</div>
									</td>
								</tr>
								<tr>
									<td align="left" valign="top">
									<div style="padding-bottom:6px;">
									<?php
										// Exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_3') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_3'));
											// Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											// que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?>&bull;&nbsp;<a href="#" onClick="AbreJanelaPAGE('../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,"arquivo_3"));?>','500','400');" style="color:#009933;"><?php echo(getvalue($objRS,'arquivo_3')); ?></a>
									<?php } ?>
									</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="right" valign="top" width="30%"><strong>*<?php echo(getTText("obs",C_NONE));?>:</strong></td>
						<td>
							<textarea name="var_obs_cancelamento" id="var_obs_cancelamento" rows="8" cols="70"></textarea>
							<br/><span class="comment_med">Insira aqui as observações e/ou motivos da não-aprovação do cadastro desta Empresa.</span>
						</td>
					</tr>
					<tr height="5"><td colspan="2">&nbsp;</td></tr>
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
				</table>
			</td>	
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:0px 80px 20px 80px;" width="100%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="right" valign="top"><img src="../img/mensagem_aviso.gif"></td>
					<td align="left"  valign="top" style="padding-left:10px;"><?php echo(getTText("aviso_cancela_cadastro",C_NONE)); ?></td>
					<td width="1%"  valign="top" align="right" nowrap>
						<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>
						<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		</form>	 
	</table>
	<?php athEndFloatingBox(); ?>
   	</td>
</tr>
</table>
</body>
</html>
<?php
	// FECHA CURSOR E OBJETO
	$objResult->closeCursor();
	$objConn = NULL; 
?>