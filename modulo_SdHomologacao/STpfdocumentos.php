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
	
	// CARREGA PREFIX PARA SESSIONS
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
	
	// REQUESTS
	$intCodDado    = request("var_chavereg"); 		// COD_HOMOLOGACAO
	$strNameDetail = request("var_field_detail");	
	$strRedirect   = request("var_redirect");		// redirect da pagina [provavel que venha null nesta pag]

	// ABRE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// LOCALIZA TODOS OS DOCUMENTOS EMITIDOS
	// PARA A HOMOLOGAÇÃO ATUAL, NEW NEW NEW
	try{
		$strSQL = "
			SELECT 
  				  sd_homologacao_documento.cod_homologacao_documento
				, UPPER(sd_homologacao_documento.tipo) AS tipo
				, sd_homologacao_documento.titulo
  				, sd_homologacao_documento.html_texto
				, sd_homologacao_documento.qtde_impresso
 				, sd_homologacao_documento.dtt_inativo
  				, sd_homologacao_documento.sys_usr_ins
  				, sd_homologacao_documento.sys_dtt_ins
				, sd_homologacao.situacao
			FROM 
				  sd_homologacao_documento
			INNER JOIN
				  sd_homologacao ON (sd_homologacao_documento.cod_homologacao = sd_homologacao.cod_homologacao AND sd_homologacao.cod_homologacao = ".$intCodDado.")
			ORDER BY sd_homologacao_documento.sys_dtt_ins DESC";
		$objResult  = $objConn->query($strSQL);
		$objResultV = $objConn->query($strSQL);
		$objRSV	    = $objResultV->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// INICIALIZA VAR PARA TROCAR DE COR LINHA
	$strColor = CL_CORLINHA_2;
	
	// FUNÇÃO PARA TROCA DE COR DE LINHAS
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title></title>
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
			// MENU DE DOCUMENTOS
			athBeginCssMenu();
				athCssMenuAddItem("","_self",getTText("documentos",C_TOUPPER),0);
			athEndCssMenu();		
		?>
		</td>
	</tr>
	</table>
	<?php
	// TESTE PARA MENSAGEM DE VAZIO
	if($objResult->rowCount() == 0){
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhum_documento_para_homologacao",C_NONE),"","aviso",1,"","");
	} else{
	?>
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="01%"></th><!-- EDIT -->
				<th width="01%"></th><!-- REIMPRIMIR -->
				<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_homologacao_documento",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("tipo",C_TOUPPER));?></th>
				<th width="30%" class="sortable" nowrap><?php echo(getTText("titulo",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("usr_ins",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("dtt_ins",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("inativo_em",C_TOUPPER));?></th>
				<th width="01%"></th>
				<th width="01%"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center">
					<img src="../img/icon_write.gif" title="Editar" border="0" style="cursor:pointer" onclick="location.href='../modulo_SdHomologacao/STupddocumento.php?var_chavereg=<?php echo(getValue($objRS,"cod_homologacao_documento"));?>'" />
				</td>
				<td align="center">
					<img src="../img/icon_imprimir.gif" title="Imprimir / Reimprimir" border="0" style="cursor:pointer" onclick="AbreJanelaPAGE('../modulo_SdHomologacao/STimprdocumento.php?var_chavereg=<?php echo(getValue($objRS,"cod_homologacao_documento"));?>','700','600');" />
				</td>
				<td align="center"><?php echo(getValue($objRS,"cod_homologacao_documento"));?></td>
				<td align="center"><?php echo(getValue($objRS,"tipo"));?></td>
				<td align="left"  ><?php echo(getValue($objRS,"titulo"));?></td>
				<td align="center"><span style="font-size:09px;color:#AAA;"><?php echo(getValue($objRS,"sys_usr_ins"));?></span></td>
				<td align="center"><span style="font-size:09px;color:#AAA;"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?></span></td>
				<td align="center">
					<span style="font-size:09px;color:#AAA;">
					<?php echo((getValue($objRS,"dtt_inativo")!="") ? dDate(CFG_LANG,getValue($objRS,"dtt_inativo"),false) : "-");?>
					</span>
				</td>
				<td align="center">
					<?php if(getValue($objRS,"html_texto")!=""){?>
					<img src="../img/icon_obs.gif" border="0" title="<?php echo("TEXTO: ".getValue($objRS,"html_texto"));?>" />
					<?php }?>
				</td>
				<td align="center">
					<img src="../img/icon_detalhes.gif" border="0" title="<?php echo("QUANTIDADE DE IMPRESSÕES: ".getValue($objRS,"qtde_impresso"));?>" />
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