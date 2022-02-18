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
	
	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	$strModo = request("var_modo");
	
	// SÓ ABRE TELA DOS TÍTULOS | COBRANÇAS SE HOUVER UMA
	// PJ LOGADA NA SESSÃO CORRENTE, ANTI PROBLEMAS ;-)
	
	$intCodDado 	= getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	//$intCodDado 	= getsession(CFG_SYSTEM_NAME . "_entidade_codigo");
	
	$intVlrCampoChaveDetail = "";
	
	$intQtdeTitulos = 0;
	if ($intCodDado != ""){
		//BUSCA TITULOS
		try {
			$strSQL  = "SELECT 
							  t1.cod_conta_pagar_receber
							, t1.situacao
							, t1.vlr_conta
							, t1.vlr_saldo
							, t1.vlr_pago 
							, t1.vlr_mora_multa
							, t1.vlr_outros_acresc
							, t1.dt_emissao
							, t1.dt_vcto
							, t1.num_documento
							, t1.historico
							, t1.tipo_documento
							, t2.cod_pedido 
							, cad_pj.razao_social
							, cad_pj.cnpj
						FROM fin_conta_pagar_receber t1 
						LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) 
						INNER JOIN cad_pj ON (cad_pj.cod_pj = t1.codigo)
						WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado."
						AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial') ";
			if ($strModo == "compacto") $strSQL .= " AND (t1.tipo_documento ILIKE 'BOLETO_SINDICAL') ";
			$strSQL .= "
				ORDER BY t1.dt_emissao
				LIMIT 20 ";
			$objResult = $objConn->query($strSQL);
		}
		catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
?>
<script type="text/javascript" language="javascript">
	var allTrTags = new Array();
	var detailTrFrameAnt = '';
	var moduloDetailAnt = '';
	function showDetailGrid(prChave_reg,prLink,prField){
	
		if(prLink.indexOf("?") == -1){
			strConcactQueryString = "?"
		}else{
			strConcactQueryString = "&"
		}
		var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
		if(detailTr == 'none'){
			 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME; ?>_detailiframe_"+prChave_reg);
	
			var allTrTags  = document.getElementsByTagName("tr");
			for( i=0; i < allTrTags.length; i++){
				if(allTrTags[i].className == 'iframe_detail'){
					allTrTags[i].style.display = 'none';
				}
			}
			document.getElementById("detailtr_"+prChave_reg).style.display = '';
		}else{
			if( moduloDetailAnt == prLink){
					document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
			}else{
				if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
					 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME; ?>_detailiframe_"+prChave_reg);
				}
			}
		}
		moduloDetailAnt = prLink;
	}
	
	function SetIFrameSource(prPage,prId) {
		document.getElementById(prId).src = prPage;
	}
	
	function agruparTitulos(){
		var form = document.formboleto;
		var submeter = false;
		var count = 0;
		for(var i = 0; i < form.elements.length ; i++){
			if(form.elements[i].type == 'checkbox' && form.elements[i].disabled == false){
				if(form.elements[i].checked == true){
					count++;
					submeter = true;
				}
			}
		}
		if(submeter){
			if(count > 1){
				form.submit();
			}
			else if(count <= 1) {
				alert('Não é possível agrupar somente uma cobrança');
				return;
			}
		}
		else{
			alert('Selecione alguma cobrança');
		}
	}
</script>
<form name="formboleto" action="../modulo_PainelPJ/STagrupatitulo.php" method="post" style="margin:0px;">
<input type="hidden" name="var_chavereg" value="<?php echo($intVlrCampoChaveDetail); ?>">
<input type="hidden" name="var_url_retorno" value="../modulo_PainelPJ/STincludetitulos.php">	
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
			body{ margin: 0px; background-color:#FFFFFF; } 
			ul{ margin-top: 0px; margin-bottom: 0px; }
			li{ margin-left: 0px; }
			.fontgrid { font: normal 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}
		</style>
	</head>
<body bgcolor="#FFFFFF">
<table bgcolor="#FFFFFF" style="width:99%;margin-bottom:0px;border-bottom:1px solid #CCC;" class="tablesort">
<?php if($objResult->rowCount() > 0){?>
	<thead>
	<tr>
		<?php if ($strModo != "compacto") { ?>
			<th width="01%"></th> <!-- BOLETO //-->
			<th width="01%"></th> <!-- DESAGRUPAR //-->
			<!--th width="01%">
				<div id="detail_desagrupa" style="display:none;">
					<img src='../img/icon_agrupa_titulos.gif' onClick="agruparTitulos()" style="cursor:pointer;" alt='<?php echo(getTText("agrupar_titulo",C_NONE));?>' title='<?php echo(getTText("agrupar_titulo",C_NONE));?>' border="0">			
				</div>			
		     </th--> <!-- AGRUPAR -->
			<th width="01%"></th> <!-- RECIBO //-->
			<!--th width="01%"></th--> <!-- LANÇAMENTOS //-->
		<?php } ?>
		<th width="01%"></th> <!-- CALC JUROS //-->
		<th width="04%"><?php echo(getTText("cod",C_NONE));?></th>
		<th width="05%" class="sortable"><?php echo(getTText("documento",C_NONE));?></th>
		<th width="08%" class="sortable-currency"><?php echo(getTText("valor",C_NONE));?></th>
		<?php if ($strModo != "compacto") { ?>
			<th width="08%" class="sortable-currency"><?php echo(getTText("saldo",C_NONE));?></th>
		<?php } ?>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("emissao",C_NONE));?></th>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("vcto",C_NONE));?></th>
		<th width="37%" class="sortable"><?php echo(getTText("documento",C_NONE));?></th>
		<th width="20%" class="sortable"><?php echo(getTText("juros",C_NONE));?></th>
	</tr>
	</thead>
   	<tbody>
	<?php foreach($objResult as $objRS){?>
	<?php 
		// PARA CADA LINHA, MONTA O NOME DO SACADO
		$intQtdeTitulos++;
		$strSACADO  = "";
		$strSACADO .= getValue($objRS,"razao_social");
		$strSACADO .= (getValue($objRS,"cnpj") != "") ? " (CNPJ: ".getValue($objRS,"cnpj").")" : "";
		
		// Busca ao menos uma ocorrencia de que o titulo corrente 
		// é tido como referencia como agrupador para outro titulo 
		// qualquer. Caso ele seja agrupador, significa que é um 
		// titulo novo de uma agrupamento.
		try{
			$strSQL = "	SELECT fin.cod_agrupador FROM fin_conta_pagar_receber as fin WHERE fin.cod_agrupador = ".getValue($objRS,"cod_conta_pagar_receber");
			$objResult2 = $objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage,"erro",1,"");
			die();
		}
		$boolAgrupador = (($objResult2->rowCount()) > 0)&&(getValue($objRS,"situacao") == "aberto")&&((getValue($objRS,"vlr_pago") == "")||(getValue($objRS,"vlr_pago") == 0));
		// Conta o número de linhas obtidas > 0 - é um titulo resultante de um agrupamento
	?>
		<tr>
		<?php if ($strModo != "compacto") { ?>
			<td align="center"><a href="javascript:void(0);" onClick="AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','750','580');"><img src='../img/icon_boleto.gif' alt='ver boleto' border='0'></a></td>
			<!--td align="center">
				<?php 
				if($boolAgrupador){?>
					<img src="../img/icon_desagrupar.gif" title="Desagrupar" border="0" style="cursor:pointer;" onClick="location.href='../modulo_PainelPJ/STdesagrupatitulo.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>&var_cod_pj=<?php echo($intCodDado);?>&var_location=../modulo_PainelPJ/STincludetitulos.php';">
				<?php }	else{?>
					<img src="../img/icon_desagrupar_off.gif" title="Desagrupar" border="0">
				<?php }
			?></td-->
			<!--td align="center">
			<?php if((getValue($objRS,"situacao") == "aberto") && (getValue($objRS,"vlr_conta") > 0)){ $intQtdeTitulos++;?>
				<input type="checkbox" name="var_cod_conta_pagar_receber[]" value="<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>" class="inputclean" style="margin:0px;">
			<?php } else{?>
				<input type="checkbox" class="inputclean" style="margin:0px;" disabled="disabled" />
			<?php }?></td-->
			<td align="center">
			<?php if(((getValue($objRS,"situacao")) == "lcto_total")||((getValue($objRS,"situacao")) == "lcto_parcial")){ ?>
				<img src="../img/icon_recibo.gif" title="Recibo" onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_FinContaPagarReceber/STifrrecibos.php?var_cod_resize=<?php echo(request("var_chavereg"));?>&var_cod_conta_pagar_receber=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
			<?php } else{ ?>
				<img src="../img/icon_recibo_off.gif" border="0" />
			<?php } ?>			</td>
			<td align="center"><a onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_PainelPJ/STifrlancamento.php','cod_conta_pagar_receber')" style="cursor:pointer"><img src="../img/icon_ver_lancamento.gif" alt="ver lançamentos" border="0"></a></td>
		<?php } ?>
			<td align="center">
				<?php 
				if (!$boolAgrupador) {
					//Só EXIBIA se era realmente uma SINDICAL... mas...
					//CHAMADO 32314 o Alexandre pede pra que o calculo de juros seja chamado tbm para guia ASSISTENCIAIS.
					//desta forma a rodirna STCAlcJurosTitulosGuiaExec e STCalcJurosGuia foram igualadas as rotinas que temso dentro do moudlo_FinContasPagarReceber
					//que já previam calculo de ASSISTENCIAL
					/*
					$strIcone = "../img/icon_calc_juros_outro.gif";
					if ((stripos(getValue($objRS,"historico"), "sindica") !== false) || (stripos(getValue($objRS,"historico"), "guias sindicais") !== false)) {
						$strIcone = "../img/icon_calc_juros.gif";
					}

					if ($strIcone != "../img/icon_calc_juros_outro.gif") {
						?>
						<a target="_parent" href="../modulo_PainelPJ/STCalcJurosTituloGuia.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>">
						<img src="<?php echo($strIcone);?>" title="<?php echo(getTText("calc_juros_guia",C_NONE));?>" border="0" style="cursor:pointer;"></a>
						<?php
					}
					---------------------------------------------- */

					$strIcone = "../img/icon_calc_juros_outro.gif";
					if ( (strtoupper(getValue($objRS,"tipo_documento"))=="BOLETO_SINDICAL") 
					  || (strtoupper(getValue($objRS,"tipo_documento"))=="BOLETO_ASSISTENCIAL") ) 
					{
						$strIcone = "../img/icon_calc_juros.gif";
				?>
						  <a target="_parent" href="../modulo_PainelPJ/STCalcJurosTituloGuia.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>">
						  <img src="<?php echo($strIcone);?>" title="<?php echo(getTText("calc_juros_guia",C_NONE));?>" border="0" style="cursor:pointer;"></a>
				<?php
					}
				}
				?>
			</td>
			<td style="text-align:center;"><?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?></td>
			<td style="text-align:center;"><?php echo(getValue($objRS,"num_documento")); ?></td>
			<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",","")); ?></td>
			<?php if ($strModo != "compacto") { ?>
				<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_saldo"),2,",","")); ?></td>
			<?php } ?>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false)); ?></td>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false)); ?></td>
			<td style="text-align:left;"><?php echo(getValue($objRS,"tipo_documento"));?></td>
			<td style="text-align:left;" nowrap="nowrap"><?php echo(number_format((double) getValue($objRS,"vlr_mora_multa"),2,",","")." / ".number_format((double) getValue($objRS,"vlr_outros_acresc"),2,",","")); ?></td>
		</tr>
		<tr id="detailtr_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" bgColor="#FFFFFF" style="display:none;" class="iframe_detail">
			<td colspan="14"><iframe name="tradeunion_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" id="<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" width="99%" src="" frameborder="0" scrolling="no"></iframe></td>
		</tr>
	<?php }?>
    </tbody>
	<?php if(($intQtdeTitulos > 1)&&($strModo != "compacto")) {?>
		<script type="text/javascript" language="javascript">
			document.getElementById("detail_desagrupa").style.display = "block";
		</script>
		<tfoot style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;" >
  		<tr>
			<td colspan="2" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;"></td>
			<td align="center" valign="middle" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;">
				<!--img src='../img/icon_agrupa_titulos.gif' onClick="agruparTitulos()" style="cursor:pointer;" title='<?php echo(getTText("agrupar_titulo",C_NONE));?>' border="0"-->
			</td>
			<td colspan="11" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;"></td>
		</tr>
		</tfoot>
	<?php }?>
<?php } else {?>
    <tbody>
		<tr><td colspan="14" style="text-align:center;"><div style="padding-top:2px;padding-bottom:2px;font-style:italic;color:#CCC;"><?php echo("Nenhuma Conta a Pagar / Receber"); ?></div></td></tr>
   	</tbody>
<?php }?>
</table>
<?php
	//athEndFloatingBox();
	$objResult->closeCursor();
}

$objConn = NULL;
?>
</body>
</html>
