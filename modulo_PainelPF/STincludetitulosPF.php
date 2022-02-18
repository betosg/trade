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
							t1.ano_vcto
							 , t1.cod_conta_pagar_receber
							, t1.situacao
							,	case when t1.situacao = 'aberto' then 'ABERTO' 
										else 
											case when t1.situacao = 'lcto_parcial' then 'ABERTO'
												else 'PAGO'
											end
								end as situacao_txt
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
							, cad_pf.nome
							, cad_pf.cpf
							, (select max(fin_lcto_ordinario.dt_lcto)  FROM fin_lcto_ordinario where fin_lcto_ordinario.cod_conta_pagar_receber = t1.cod_conta_pagar_receber limit 1) as dt_pgto
							, (select * from sp_libera_pagamento(t1.codigo,t1.tipo,t1.dt_vcto)) as libera_pgto
						FROM fin_conta_pagar_receber t1 
						LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) 
						INNER JOIN cad_pf ON (cad_pf.cod_pf = t1.codigo)
						WHERE t1.tipo = 'cad_pf' AND t1.codigo = ".$intCodDado." 
						AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial' or t1.situacao = 'lcto_total') ";
			//if ($strModo == "compacto") $strSQL .= " AND (t1.tipo_documento ILIKE 'BOLETO_SINDICAL') ";
		 	 $strSQL .= "
				ORDER BY 4 ASC, t1.ano_vcto desc , t1.dt_vcto DESC, t1.cod_conta_pagar_receber desc
                /*LIMIT 20 */";
            
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
		<th width="01%"></th> <!-- BOLETO //-->
		<th width="01%"></th> <!-- RECIBO //-->			
		<!--th width="01%"></th--> <!-- LCTO //-->		
		<th width="04%"><?php echo(getTText("cod",C_NONE));?></th>		
		<th width="05%" class="sortable"><?php echo(getTText("status",C_NONE));?></th>
		<th width="37%" class="sortable"><?php echo(getTText("referencia",C_NONE));?></th>
		<th width="08%" class="sortable-currency"><?php echo(getTText("valor",C_NONE));?></th>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("vcto",C_NONE));?></th>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("pgto",C_NONE));?></th>				
	</tr>
	</thead>
   	<tbody>
	<?php foreach($objResult as $objRS){?>
	<?php 
		

	
		
		// PARA CADA LINHA, MONTA O NOME DO SACADO
		$intQtdeTitulos++;
		$strSACADO  = "";
		$strSACADO .= getValue($objRS,"nome");
		$strSACADO .= (getValue($objRS,"cpf") != "") ? " (CPF: ".getValue($objRS,"cpf").")" : "";
		
		//define bg-color linha pago ou aberto
		if (getValue($objRS,"situacao_txt")=='ABERTO'){
			$strColor = "#F69680";
		}else{
			$strColor = "#BBF4B0";
		}
		
		
	?>
		<tr style="background-color: <?php echo($strColor);?>;">
		<?php //if ($strModo != "compacto") { ?>
			
			<td align="center" style="background-color: <?php echo($strColor);?>;">
			<?php if(((getValue($objRS,"situacao")) != "lcto_total")){ ?>
				<!--a href="javascript:void(0);" onClick="AbreJanelaPAGE('../_boletos/STshowboletoBepay.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','750','580');"><img src='../img/icon_pagamento.gif' alt='Pagar Agora' title='Pagar Agora' border='0'></a-->
					<?php if (getValue($objRS,"libera_pgto")=="sim"){?>
							<a href='STshowpagamento.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>' target='_self'><img src='../img/icon_pagamento.gif' alt='Pagar Agora' title='Pagar Agora' border='0'></a-->
					<?php }else{?>
							<img src='../img/icon_pagamento_pb.gif' alt='Efetue primeiro o pagamento da anuidade anterior' title='Efetue primeiro o pagamento da anuidade anterior' border='0'>
					<?php }?>
		<?php    }?>
			</td>
			
			<td align="center">
			<?php if(((getValue($objRS,"situacao")) == "lcto_total")){
				//if (1==2){
				?>
				<img src="../img/icon_recibo.gif" title="Recibo" onClick="AbreJanelaPAGE('../modulo_FinContaPagarReceberPF/STshowrecibonormal.php?var_duas_vias=sim&var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>',750,580)" border="0" style="cursor:pointer;">
			<?php } else{ ?>
				<!--img src="../img/icon_recibo_off.gif" border="0" /-->
			<?php } ?>			</td>
			<!--td align="center"><a onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_PainelPJ/STifrlancamento.php','cod_conta_pagar_receber')" style="cursor:pointer"><img src="../img/icon_ver_lancamento.gif" alt="ver lançamentos" border="0"></a></td-->
		<?php //} ?>
			
			<td style="text-align:center;"><?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?></td>
			<td style="text-align:left;"><?php echo(getValue($objRS,"situacao_txt"));?></td>
			<td style="text-align:left;"><?php echo(getTText(getValue($objRS,"historico"),C_NONE));?></td>			
			<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",","")); ?></td>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false)); ?></td>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_pgto"), false)); ?></td>
		</tr>
		<tr id="detailtr_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" bgColor="#FFFFFF" style="display:none;" class="iframe_detail">
			<td colspan="14"><iframe name="tradeunion_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" id="<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" width="99%" src="" frameborder="0" scrolling="no"></iframe></td>
		</tr>
	<?php }?>
    </tbody>
	
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
