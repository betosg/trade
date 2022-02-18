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
	/*$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");*/
	
	// REQUESTS
	$intCodDado     = request("var_chavereg"); 		// COD_ARQUIVO
	$strNameDetail  = request("var_field_detail");	
	$strRedirect 	= request("var_redirect");		// redirect da pagina [provavel que venha null nesta pag]

	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// faz busca de respostas com 
	// base no cod agenda enviado
	try{
		$strSQL = "
			SELECT 
				  arq_retorno_cobr_item.cod_arq_cobr_item
				, cad_pj.cnpj
				, cad_pj.razao_social
				, arq_retorno_cobr_item.num_registro
				, arq_retorno_cobr_item.processado
				, arq_retorno_cobr_item.vlr_tarifas
				, arq_retorno_cobr_item.vlr_pago
				, arq_retorno_cobr_item.vlr_titulo
				, arq_retorno_cobr_item.vlr_desconto
				, arq_retorno_cobr_item.vlr_acrescimo
				, arq_retorno_cobr_item.vlr_liquido
				, arq_retorno_cobr_item.dt_ocorrencia
				, arq_retorno_cobr_item.cod_banco_cobr
				, arq_retorno_cobr_item.cod_agencia_cobr 
				, arq_retorno_cobr_item.sys_usr_ins
			FROM arq_retorno_cobr_item
			INNER JOIN fin_lcto_ordinario ON (fin_lcto_ordinario.cod_arq_cobr_item = arq_retorno_cobr_item.cod_arq_cobr_item)
			INNER JOIN cad_pj ON (fin_lcto_ordinario.codigo = cad_pj.cod_pj)
			WHERE arq_retorno_cobr_item.cod_arq_retorno = ".$intCodDado."
			ORDER BY arq_retorno_cobr_item.sys_dtt_ins DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	$boolVerifyChecks = false;
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_2;
	
	// função para cores de linhas
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
					// concatenamos o link corretamente para os casos
					// onde o redirect tenha sido informado ou não
					athBeginCssMenu();
						athCssMenuAddItem("","_self",getTText("linhas",C_TOUPPER),1);
					athEndCssMenu();		
				?>
			</td>
		</tr>
	</table>
	
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhuma_linha",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th> 		<!-- VIEW -->
				<!--<th width="1%"></th>--> <!-- EDIT -->
				<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_linha",C_TOUPPER));?></th>
				<th width="20%" class="sortable" nowrap><?php echo(getTText("cnpj",C_TOUPPER));?></th>
				<th width="40%" class="sortable" nowrap><?php echo(getTText("empresa",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("vlr_tit",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("vlr_pago",C_TOUPPER));?></th>
				<!--<th width="10%" class="sortable" nowrap><?php //echo(getTText("vlr_desconto",C_TOUPPER));?></th>-->
				<th width="10%" class="sortable" nowrap><?php echo(getTText("vlr_acrescimo",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("vlr_liquido",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("tarif",C_TOUPPER));?></th>
				<th width="20%" class="sortable-date-dmy" nowrap><?php echo(getTText("dt_ocorrencia",C_TOUPPER));?></th>
				<th width="20%" class="sortable" nowrap><?php echo(getTText("usuario",C_TOUPPER));?></th>
				<th></th><!-- NUM REG. E PROCESSADO -->
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td width="1%" align="center" style="vertical-align:middle;">
					<a href="../_fontes/insupddelmastereditor.php?var_basename=modulo_ArqRetCobrItem&var_oper=VIE&var_chavereg=<?php echo(getValue($objRS,"cod_arq_cobr_item"))?>&var_populate=yes" target="<?php echo(CFG_SYSTEM_NAME."_main");?>">
					<img src="../img/icon_zoom.gif" alt="<?php echo(getTText("visualizar",C_NONE));?>" 
				    title="<?php echo(getTText("visualizar",C_NONE));?>" border="0" style="cursor:pointer;" />
					</a>
				</td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_arq_cobr_item"));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cnpj"));?></td>
				<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"razao_social"));?></td>
				<td align="right" style="vertical-align:middle; "><?php echo(number_format((double) getValue($objRS,"vlr_titulo"),2,',','.'));?></td>
				<td align="right" style="vertical-align:middle; "><?php echo(number_format((double) getValue($objRS,"vlr_pago"),2,',','.'));?></td>
				<!--<td align="right" style="vertical-align:middle; "><?php //echo(number_format((double) getValue($objRS,"vlr_desconto"),2,',','.'));?></td>-->
				<td align="right" style="vertical-align:middle; "><?php echo(number_format((double) getValue($objRS,"vlr_acrescimo"),2,',','.'));?></td>
				<td align="right" style="vertical-align:middle; "><?php echo(number_format((double) getValue($objRS,"vlr_liquido"),2,',','.'));?></td>
				<td align="right" style="vertical-align:middle; "><?php echo(number_format((double) getValue($objRS,"vlr_tarifas"),2,',','.'));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_ocorrencia"),false));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"sys_usr_ins"));?></td>
				<td align="center" style="vertical-align:middle;">
					<?php if((getValue($objRS,"processado") != "") || (getValue($objRS,"num_registro") != "")){?>
					<img src="../img/icon_obs.gif" alt=" PROCESSADO: <?php echo(getValue($objRS,"processado")."\n");?> NUM. REGISTRO: <?php echo(getValue($objRS,"num_registro"));?>" title=" PROCESSADO: <?php echo(getValue($objRS,"processado")."\n");?> NUM. REGISTRO: <?php echo(getValue($objRS,"num_registro"));?>" />
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