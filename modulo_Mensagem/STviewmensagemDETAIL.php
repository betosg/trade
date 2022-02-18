<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodMensagem	= request("var_chavereg");		// cod_mensagem
	$intWidth       = request("var_width");
	$strPopulate 	= "yes";
	$strLocation    = request("var_location");
	
	// if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// Busca dados da Mensagem
	try{
		$strSQL = "
			SELECT
				  msg_mensagem.cod_mensagem
				, msg_mensagem.assunto
				, msg_mensagem.mensagem
				, msg_mensagem.dtt_envio
				, msg_mensagem.remetente
				, msg_destino.dtt_lido
				, msg_destino.id_usuario
				, msg_pasta.nome_pasta
			FROM  msg_mensagem
			INNER JOIN msg_destino ON (msg_destino.cod_mensagem   = msg_mensagem.cod_mensagem)
			INNER JOIN msg_pasta   ON (msg_pasta.cod_pasta = msg_destino.cod_pasta)
			WHERE
				msg_mensagem.cod_mensagem = ".$intCodMensagem;
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
		
		// Faz update na tabela de mensagem
		// setando que ja foi lida a MSG
		if(getValue($objRS,"dtt_lido") == ""){
			$strSQL = "UPDATE msg_destino SET dtt_lido = CURRENT_TIMESTAMP WHERE cod_mensagem = ".$intCodMensagem;
			$objConn->query($strSQL);
		}
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
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
			var strLocation = null;
			function ok() {
				<?php if($intWidth == ""){ ?>
					<?php if($strLocation == ""){ ?>
						var strLocation = "../modulo_Mensagem/";
						document.location.href = strLocation;
					<?php } else{?>
						var strLocation = "<?php echo($strLocation);?>";
						document.location.href = strLocation;
					<?php }?>
				<?php } else{?>
					window.close();
				<?php }?>
			}

			function cancelar() {
				<?php if($intWidth == ""){ ?>
					<?php if($strLocation == ""){ ?>
						var strLocation = "../modulo_Mensagem/";
						document.location.href = strLocation;
					<?php } else{?>
						var strLocation = "<?php echo($strLocation);?>";
						document.location.href = strLocation;
					<?php }?>
				<?php } else{?>
					window.close();
				<?php }?>
			}
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<!-- body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;" -->

<!-- USO -->
<center>
<?php athBeginFloatingBox(($intWidth == "") ? "725" : $intWidth,"",getTText("view_mensagem",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" width="<?php echo(($intWidth == "") ? "705" : $intWidth - 20 );?>" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<!-- DADOS NOVA MENSAGEM -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_resposta",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("cod",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_mensagem"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("na_pasta",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"nome_pasta"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("dtt_envio",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_envio"),false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("dtt_lido",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_lido"),true));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong><?php echo(getTText("remetente",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"remetente"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("destinatario",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"id_usuario"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("assunto",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"assunto"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong><?php echo(getTText("mensagem",C_UCWORDS));?>:</strong>
					</td><td width="77%" align="left"><?php echo(getValue($objRS,"mensagem"));?></td>
				</tr>
				<!-- DADOS NOVA RESPOSTA -->
				
				
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
								
				
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3" valign="top" align="right" style="padding-right:30px;">
			<button onClick="ok();"><?php echo(getTText("ok",C_NONE));?></button>
			<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>

</html>