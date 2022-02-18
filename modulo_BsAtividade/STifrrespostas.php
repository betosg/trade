<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
	
	// REQUESTS
	$intCodDado  	= request("var_chavereg");
	$strNameDetail  = request("var_field_detail");
	$strRedirect 	= request("var_redirect");

	// ABRE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);

	// FAZ BUSCA DE RESPOSTAS PARA DETERMINADA TAREFA
	try{
		$strSQL = "
			SELECT 
  				  cod_resposta
				, id_from
				, id_to
				, resposta
				, horas
				, dtt_resposta
				, arquivo_anexo
				, sigiloso
			FROM tl_resposta
			WHERE cod_todolist = ".$intCodDado."
			ORDER BY dtt_resposta DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	$boolVerifyChecks = false;
	
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
		<title><?php echo(strtoupper(CFG_SYSTEM_NAME));?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="_css/default.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style>
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin: 10px; background-color:#FFFFFF; } 
			ul{ margin-top: 0px; margin-bottom: 0px; }
			li{ margin-left: 0px; }
		</style>
	</head>
<body bgcolor="#FFFFFF">
	
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
				<?php
					// concatenamos o link corretamente para os casos
					// onde o redirect tenha sido informado ou não
					athBeginCssMenu();
						athCssMenuAddItem("","_self",getTText("respostas",C_TOUPPER),1);
						athBeginCssSubMenu();
							athCssMenuAddItem("STinsresposta.php?var_chavereg=".$intCodDado,"_self",getTText("resposta_inserir",C_NONE));
						athEndCssSubMenu();
					athEndCssMenu();		
				?>
			</td>
		</tr>
	</table>
	
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhuma_resp_pub",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_resposta",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("id_from",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("id_to",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("dtt_resposta",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("horas",C_TOUPPER));?></th>
				<th width="50%" class="sortable" nowrap><?php echo(getTText("resposta",C_TOUPPER));?></th>
				<th width="01%"></th><!-- ANEXO -->
				<th width="01%"></th><!-- SIGILOSO -->
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td style="vertical-align:top;text-align:center;"><?php echo(getValue($objRS,"cod_resposta"));?></td>
				<td style="vertical-align:top;text-align:center;">
				<?php if(getValue($objRS,"id_from") == "sistema"){?>
					<span style="color:#AAA;font-size:9px;font-style:italic;"><?php echo(getTText("sistema",C_NONE));?></span>
				<?php } else{?>
					<?php echo(getValue($objRS,"id_from"));?>
				<?php }?>
				</td>
				<td style="vertical-align:top;text-align:center;">
				<?php if(getValue($objRS,"id_to") == "sistema"){?>
					<span style="color:#AAA;font-size:9px;font-style:italic;"><?php echo(getTText("sistema",C_NONE));?></span>
				<?php } else{?>
					<?php echo(getValue($objRS,"id_to"));?>
				<?php }?>
				</td>
				<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_resposta"),true));?></td>
				<td style="vertical-align:top;text-align:center;"><?php echo(getValue($objRS,"horas"));?></td>
				<td style="vertical-align:top;text-align:left;"><?php echo(getValue($objRS,"resposta"));?></td>
				<td style="vertical-align:top;text-align:center;">
				<?php if(getValue($objRS,"arquivo_anexo") != ""){?>
					<img src="../img/icon_anexo.gif" border="0" style="cursor:pointer;" onclick="AbreJanelaPAGE('../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/<?php echo(getValue($objRS,"arquivo_anexo"));?>','500','500');" />
				<?php }?>				
				</td>
				<td style="vertical-align:top;text-align:center;">
				<?php if((getValue($objRS,"sigiloso") != "") && (getValue($objRS,"id_to") == getsession(CFG_SYSTEM_NAME."_id_usuario"))){?>
					<img src="../img/icon_sigiloso.gif" border="0" title="<?php echo(getTText("sigiloso_tooltip",C_NONE).": ".getValue($objRS,"sigiloso"))?>" />
				<?php }?>
				</td>
			</tr>
		<?php }?>
		</tbody>
	</table>
	<?php }?>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php
	$objConn = NULL;
	$objResult->closeCursor();
?>