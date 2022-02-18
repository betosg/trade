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
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
	
	// REQUESTS
	$intCodDado     = request("var_chavereg"); 		// cod_pf
	$strNameDetail  = request("var_field_detail");	

	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// faz busca de respostas com 
	// base no cod agenda enviado
	try{
		$strSQL = "
			SELECT
				  cad_pf.nome
				/*, cad_pj.razao_social*/
				, fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.dt_emissao
				, fin_conta_pagar_receber.dt_vcto
				, fin_conta_pagar_receber.situacao
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.vlr_pago
				, '' as cod_credencial
			FROM fin_conta_pagar_receber 
			/*LEFT JOIN prd_pedido ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido) */
			INNER JOIN cad_pf ON (fin_conta_pagar_receber.codigo = cad_pf.cod_pf AND tipo = 'cad_pf') 
			/*INNER JOIN cad_pj ON (fin_conta_pagar_receber.codigo = cad_pj.cod_pj) 
			LEFT JOIN relac_pj_pf ON (relac_pj_pf.cod_pf = cad_pf.cod_pf AND relac_pj_pf.cod_pj = cad_pj.cod_pj) 
			LEFT JOIN sd_credencial ON (prd_pedido.cod_pedido = sd_credencial.cod_pedido) */
			WHERE cad_pf.cod_pf = ".$intCodDado;
		// echo($strSQL);
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
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
		<script>
			var allTrTags = new Array();
			var detailTrFrameAnt = '';
			var moduloDetailAnt = '';
			function showDetailGridT(prChave_reg,prLink, prField){
				if(prLink.indexOf("?") == -1){ strConcactQueryString = "?"; } 
				else{ strConcactQueryString = "&"; }
				var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
				if(detailTr == 'none'){
					SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
					var allTrTags  = document.getElementsByTagName("tr");
					for(i=0; i < allTrTags.length; i++){
						if(allTrTags[i].className == 'iframe_detail'){ allTrTags[i].style.display = 'none';	}
					}
					document.getElementById("detailtr_"+prChave_reg).style.display = '';
				}else{
					if(moduloDetailAnt == prLink){ document.getElementById("detailtr_"+prChave_reg).style.display = 'none'; }
					else{
						if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
						 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
						}
					}

				}
				moduloDetailAnt = prLink;
			}

			function SetIFrameSource(prPage,prId){ document.getElementById(prId).src = prPage; }

		</script>
	</head>
<body bgcolor="#FFFFFF">
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
	<tr>
		<td align="left">
		<?php
			athBeginCssMenu();
				athCssMenuAddItem("","_self",getTText("titulos_relacionados",C_TOUPPER),0);
			athEndCssMenu();		
		?>
		</td>
	</tr>
	</table>
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhum_titulo",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- VIEW LCTOS -->
				<th width="01%" class="sortable" nowrap><?php echo(getTText("cod_conta_pagar_receber",C_TOUPPER));?></th>
				<!--th width="20%" class="sortable" nowrap><?php //echo(getTText("razao_social",C_TOUPPER));?></th-->
				<th width="10%" class="sortable" nowrap><?php echo(getTText("situacao",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("emissao",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("vcto",C_TOUPPER));?></th>
				<th width="10%" class="sortable-numeric" nowrap><?php echo(getTText("vlr_conta",C_TOUPPER));?></th>
				<th width="10%" class="sortable-numeric" nowrap><?php echo(getTText("vlr_pago",C_TOUPPER));?></th>
				<th width="30%" class="sortable" nowrap><?php echo(getTText("historico",C_TOUPPER));?></th>
				<th width="1%" nowrap></th><!-- FUNÇÃO DO COLABORADOR -->
				<!--th width="1%" nowrap></th--><!-- CODIGO CREDENCIAL -->
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){
			$strIdFrame = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_conta_pagar_receber");
		?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center" style="vertical-align:top;">
					<img src="../img/icon_ver_lancamento.gif" title="<?php echo(getTText("lancamentos",C_NONE));?>" onClick="showDetailGridT('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_CadPF/STifrlancamentoABFM.php?var_cod_resize=<?php echo(request("var_chavereg"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;" />
				</td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_conta_pagar_receber"));?></td>
				<!--td align="left" style="vertical-align:middle;"><div style="height:12px;width:200px;overflow:hidden;"><?php echo(getValue($objRS,"razao_social"));?></div></td-->
				<td align="center" style="vertical-align:middle;"><span style="color:#AAA;font-size:09px;"><?php echo(strtoupper(getValue($objRS,"situacao")));?></span></td>
				<td align="center" style="vertical-align:middle;"><span style="color:#AAA;font-size:09px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false));?></span></td>
				<td align="center" style="vertical-align:middle;"><span style="color:#AAA;font-size:09px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false));?></span></td>
				<td align="right" style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,',','.'));?></td>
				<td align="right" style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_pago"),2,',','.'));?></td>
				<td align="left" style="vertical-align:middle;"><?php  echo(getValue($objRS,"historico"));?></td>
				<!--td style="vertical-align:top;text-align:center;">
				<?php //if(getValue($objRS,"cod_credencial") != ""){?>
					<img src="../img/icon_obs.gif" title="<?php //echo("CREDENCIAL RELACIONADA: ".getValue($objRS,"cod_credencial"));?>" />
				<?php //}?>
				</td-->
				<!--td style="vertical-align:top;text-align:center;">
				<?php if(getValue($objRS,"funcao") != ""){?>
					<img src="../img/icon_funcao.gif" title="<?php //echo(getValue($objRS,"funcao"));?>" />
				<?php }?>
				</td-->
			</tr>
			<tr id="detailtr_<?php echo (getValue($objRS,"cod_conta_pagar_receber"));?>" style="display:none; background:<?php echo(CL_CORLINHA_1);?>" class="iframe_detail">
			<td colspan='17' align="left" valign="middle">
				<iframe name="<?php echo($strIdFrame);?>" id="<?php echo($strIdFrame);?>" width="100%" src="" frameborder="0" scrolling="no"></iframe>
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