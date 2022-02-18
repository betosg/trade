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
	
	// POPULATE
	// $strPopulate = (request("var_populate") == "") ? "yes" : request("var_populate");//Flag de verificação se necessita popular o session ou não
 	// if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	
	// VERIFICAÇÃO DE ACESSO
	// CARREGA PREFIX DOS SESSIONS
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");
	
	// REQUESTS
	$intCodDado     = request("var_chavereg"); 							// COD_HOMOLOGACAO
	$intTITULO   	= request("var_cod_conta_pagar_receber"); 			// COD_CONTA_PAGAR_RECEBER
	$strNameDetail  = request("var_field_detail");	
	$strRedirect 	= request("var_redirect");							// REDIRECT PARA PÁGINA IDEAL

	// ABRE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// FAZ BUSCA DOS RECIBOS CORRETOS
	// PARA O TITULO CORRENTE
	try{
		$strSQL = "
			SELECT 
				  fin_recibo.cod_recibo
				, fin_recibo.cod_conta_pagar_receber
				, fin_recibo.cod_lcto_ordinario
				, fin_recibo.cod_lcto_em_conta
				, fin_recibo.arquivo
				, fin_recibo.sacado
				, fin_recibo.vlr_total
				, fin_recibo.vlr_total_juros
				, fin_recibo.vlr_total_desc
				, fin_recibo.vlr_saldo
				, fin_recibo.num_impressoes
				, fin_recibo.sys_dtt_ult_print
				, fin_recibo.sys_usr_ins
				, fin_recibo.sys_dtt_ins
				, fin_recibo.sys_usr_upd
				, fin_recibo.sys_dtt_upd
			FROM 
				fin_recibo
			WHERE fin_recibo.cod_conta_pagar_receber = ".$intTITULO."
			ORDER BY fin_recibo.sys_dtt_ins DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// INICIALIZA VARIAVEL PARA PINTAR LINHA
	$strColor = CL_CORLINHA_2;
	
	// FUNÇÃO QUE PINTA LINHAS NO ESTILO SIM-NÃO
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
<body bgcolor="#FFFFFF" onLoad="">
	
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
				<?php
					// concatenamos o link corretamente para os casos
					// onde o redirect tenha sido informado ou não
					athBeginCssMenu();
						athCssMenuAddItem("","_self",getTText("recibos",C_TOUPPER),1);
						athBeginCssSubMenu();
							athCssMenuAddItem("../modulo_CadPJ/STshowrecibo.php?var_chavereg=".request("var_cod_resize")."&var_cod_conta_pagar_receber=".$intTITULO,"_self",getTText("recibo_inserir",C_NONE));
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
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhum_recibo_ins",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="01%"></th> 	<!-- IMPRRECIBO -->
				<th width="03%" class="sortable" nowrap><?php echo(getTText("cod_recibo",C_TOUPPER));?></th>
				<th width="28%" class="sortable" nowrap><?php echo(getTText("sacado",C_TOUPPER));?></th>
				<th width="22%" class="sortable-date-dmy" nowrap><?php echo(getTText("dtt_ins",C_TOUPPER));?></th>
				<th width="08%" class="sortable-numeric"  nowrap><?php echo(getTText("vlr_total",C_TOUPPER));?></th>
				<th width="08%" class="sortable-numeric"  nowrap><?php echo(getTText("juros",C_TOUPPER));?></th>
				<th width="08%" class="sortable-numeric"  nowrap><?php echo(getTText("desc",C_TOUPPER));?></th>
				<th width="07%" class="sortable-numeric"  nowrap><?php echo(getTText("vlr_saldo",C_TOUPPER));?></th>
				<th width="01%"></th>	<!-- NUM_IMPRESSOES -->
				<th width="01%"></th>	<!-- NOME_ARQUIVO -->
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_imprimir.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" title="<?php echo(getTText("imprimir",C_NONE));?>" border="0" style="cursor:pointer;" onclick="AbreJanelaPAGE('../_database/athupdatetodb.php?DEFAULT_TABLE=fin_recibo&DEFAULT_DB=<?php echo(CFG_DB);?>&FIELD_PREFIX=DBVAR_&RECORD_KEY_NAME=cod_recibo&RECORD_KEY_VALUE=<?php echo(getValue($objRS,"cod_recibo"));?>&DBVAR_AUTODATE_SYS_DTT_ULT_PRINT=&DBVAR_NUM_NUM_IMPRESSOES=<?php echo(getValue($objRS,"num_impressoes")+1);?>&DBVAR_AUTODATE_SYS_DTT_UPD=&DBVAR_STR_SYS_USR_UPD=<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>&DEFAULT_LOCATION=../modulo_FinContaPagarReceber/<?php echo(getValue($objRS,"arquivo"));?>','600','400');"/>
					<!--input type="hidden" name="DEFAULT_TABLE" value="RV_REVISTA">
					<input type="hidden" name="DEFAULT_DB" value="[database.mdb]">
					<input type="hidden" name="FIELD_PREFIX" value="DBVAR_">
					<input type="hidden" name="RECORD_KEY_NAME" value="COD_REVISTA">
					<input type="hidden" name="DEFAULT_LOCATION" value="../modulo_revista/update.asp">
					<input type="hidden" name="DBVAR_AUTODATE_DT_CRIACAO" value=""-->
					<!-- UPDATETODB COM REDIRECT PARA ARQUIVO DO RECIBO -->
				</td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_recibo"));?></td>
				<td align="left"   style="vertical-align:middle;"><?php echo(getValue($objRS,"sacado"));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true));?></td>
				<td align="right"  style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_total"),2,',','.'));?></td>
				<td align="right"  style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_total_juros"),2,',','.'));?></td>
				<td align="right"  style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_total_desc") ,2,',','.'));?></td>
				<td align="right"  style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_saldo"),2,',','.'));?></td>
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_obs.gif" title="<?php echo("NÚMERO DE IMPRESSÕES: ".getValue($objRS,"num_impressoes"));?>" border="0" />
				</td>
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_anexo.gif" title="<?php echo(getValue($objRS,"arquivo"));?>" border="0" />
				</td>
			</tr>
		<?php }?>
		</tbody>
	</table>
	<?php }?>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  //resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  window.parent.resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_resize")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php
	$objConn = NULL;
	$objResult->closeCursor();
?>