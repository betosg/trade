<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	//$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	// indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente
	$intCodResposta	= request("var_chavereg");		// cod_agenda para o qual irá ser update da agenda
	$strRedirect	= request("var_redirect");		// pagina que sera feito o redir
	
	if($intCodResposta == ""){
		mensagem("err_sql_desc_card","err_envio_resp",getTText("resposta_cod_null",C_NONE),'','erro','1');
		die();
	}

	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// localiza o número de respostas 
	// referentes a agenda corrente b
	try{
		$strSQL = "
			SELECT
				  cod_agenda
				, cod_resposta 
  				, id_usuario
  				, resposta
 				, dtt_resposta
  				, sys_usr_ins
  				, sys_dtt_ins
			FROM ag_resposta 
			WHERE cod_resposta = ".$intCodResposta;
		$objResult 	= $objConn->query($strSQL);
		$objRS		= $objResult->fetch();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
			
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				border-bottom:1px solid #E9E9E9;
				border-left:  1px solid #E9E9E9;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
		</style>
		<script type="text/javascript">
				
			function cancelar(){
				// OBS: Esta funcao retorna para
				// o historico anterior.
				window.history.back();
			}
			
			function ok(prForm){
				// OBS: Esta funcao submita
				// o form enviado como param
				// em forma de id
				var strForm = prForm;
				document.getElementById(strForm).submit();
			}
		</script>
	</head>
<body bgcolor="#FFFFFF"  style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("520","",getTText("delete_event_resp",C_NONE),CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STdelrespostaexec.php" method="post">
<input type="hidden" name="var_chavereg" value="<?php echo($intCodResposta);?>" />
<input type="hidden" name="var_redirect" value="<?php echo($strRedirect);?>" />
<table cellpadding="0" cellspacing="0" border="0" height="275" width="500" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_antes_del",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 0px 30px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
				
				<!-- DADOS RESPOSTA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_resposta_old",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("cod_resposta",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_resposta"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("quem_registrou",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"sys_usr_ins"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("data_registrado",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("enviado_por",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"id_usuario"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("data_resposta",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_resposta"),true));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("texto",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"resposta"));?></td>
				</tr>
				<tr><td colspan="2" style="border-bottom:1px solid #CCC;">&nbsp;</td></tr>
				<!-- DADOS RESPOSTA -->
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="78%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td align="right" width="23%" style="padding-right:8px;">
									<img src="../img/mensagem_aviso.gif" />
								</td>
								<td align="left"  width="77%"><?php echo(getTText("aviso_del_resp_txt",C_NONE));?></td>
							</tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="10%" align="left">
						<button onClick="ok('formstatic');">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
					<td width="12%" align="left" style="padding-right:25px;">
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_agenda")); ?>',20);
</script>
</html>