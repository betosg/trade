<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// HEADERS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intVlrCampoChaveDetail  = request("var_chavereg"); // cod_pj
	$strNomeCampoChaveDetail = request("var_field_detail");
	$strSituacao = request("var_situacao");
	
	$intQtdeTitulos = 0;
	if ($strSituacao == "") $strSituacao = "aberto";
	
	// ABERTURA DE CONN COM DB
	$objConn = abreDBConn(CFG_DB);
		
	// SQL
	try{
		$strSQL  = "
			SELECT
				  fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.situacao
				, fin_conta_pagar_receber.tipo_documento
				, fin_conta_pagar_receber.nosso_numero
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.dt_emissao
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.dt_vcto
				, fin_conta_pagar_receber.obs
				, fin_conta_pagar_receber.cod_agrupador
				, ( SELECT COUNT(fin.cod_agrupador) FROM fin_conta_pagar_receber as fin 
					WHERE fin.cod_agrupador = fin_conta_pagar_receber.cod_conta_pagar_receber) as qtde_agrup
			FROM     
				fin_conta_pagar_receber
			WHERE ";
			if ($strSituacao == 'a_vencer')
				{
					$strSQL .= "fin_conta_pagar_receber.tipo LIKE 'cad_pj' 
							AND fin_conta_pagar_receber.codigo = ".$intVlrCampoChaveDetail." 
							AND fin_conta_pagar_receber.pagar_receber = false 
							AND (fin_conta_pagar_receber.situacao = 'aberto' OR fin_conta_pagar_receber.situacao = 'lcto_parcial')
							AND (fin_conta_pagar_receber.dt_vcto >= CURRENT_DATE)
							ORDER BY situacao, fin_conta_pagar_receber.cod_conta_pagar_receber";
				}
			elseif ($strSituacao == 'vencidos')
				{
					$strSQL .= "fin_conta_pagar_receber.tipo LIKE 'cad_pj' 
							AND fin_conta_pagar_receber.codigo = ".$intVlrCampoChaveDetail." 
							AND fin_conta_pagar_receber.pagar_receber = false 
							AND (fin_conta_pagar_receber.situacao = 'aberto' OR fin_conta_pagar_receber.situacao = 'lcto_parcial') 
							AND (fin_conta_pagar_receber.dt_vcto < CURRENT_DATE)
							ORDER BY situacao, fin_conta_pagar_receber.cod_conta_pagar_receber";
				}
			else
				{
					$strSQL .= "fin_conta_pagar_receber.tipo LIKE 'cad_pj' 
							AND fin_conta_pagar_receber.codigo = ".$intVlrCampoChaveDetail." 
							AND fin_conta_pagar_receber.pagar_receber = false 
							AND fin_conta_pagar_receber.situacao LIKE '" . $strSituacao . "' 
							ORDER BY situacao, fin_conta_pagar_receber.cod_conta_pagar_receber";	
				}
			//echo($strSQL);
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}

//...	
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
<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	body{ padding:10px; }
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
</style>
<script language="javascript">
function removePedido(prCodPedido){
    if(confirm("Tem certeza que deseja remover este pedido?")){
        window.location = 'STremovepedido.php?var_chavereg=<?php echo($intVlrCampoChaveDetail); ?>&var_cod_pedido=' + prCodPedido + '&var_url_retorno=STifrfinanceiro.php';
    }
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
            alert('Não é possível agrupar somente uma cobrança.');
            return;
        }
    }
    else{
        alert('Selecione alguma cobrança.');
    }
}

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
<body style="margin:0px 0px 10px 0px;background-color:#FFFFFF;">
	<form name='frmSizeBody'>
		<input type='hidden' value='' name='sizeBody'>
		<input type='hidden' value='<?php echo($intVlrCampoChaveDetail); ?>' name='codAvo'>
	</form>
	<form name="formboleto" action="STAgrupaTitulo.php" method="post" style="margin:0px;">
	<input type="hidden" name="var_chavereg" value="<?php echo($intVlrCampoChaveDetail); ?>">
	<input type="hidden" name="var_url_retorno" value="STifrfinanceiro.php">
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
	<tr>
		<td align="left">
		<?php
			athBeginCssMenu();
				athCssMenuAddItem("","_self",getTText("contas_a_pagar_receber",C_TOUPPER),1);
				athBeginCssSubMenu();	
					athCssMenuAddItem("","_self",getTText("abertos",C_UCWORDS),1);
						athBeginCssSubMenu();
							athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=aberto"      ,"_self",getTText("abertos",C_UCWORDS));
							athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=vencidos"      ,"_self",getTText("vencidos",C_UCWORDS));
							athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=a_vencer"      ,"_self",getTText("a_vencer",C_UCWORDS));
						athEndCssSubMenu();
					athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=lcto_parcial","_self",getTText("parciais",C_UCWORDS));
					athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=agrupado"    ,"_self",getTText("agrupados",C_UCWORDS));
					athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=lcto_total"  ,"_self",getTText("pagos",C_UCWORDS));
					athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=cancelado"   ,"_self",getTText("cancelados",C_UCWORDS));
					athCssMenuAddItem("STifrfinanceiro.php?var_chavereg=".$intVlrCampoChaveDetail."&var_field_detail=".$strNomeCampoChaveDetail."&var_situacao=%"           ,"_self",getTText("todos",C_UCWORDS));
				athEndCssSubMenu();
			athEndCssMenu();		
		?>
		</td>
	</tr>
	</table>
	<?php
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1,"","");
		die();
	}
	?>
	<table bgcolor="#CFCFCF" style="width:auto;border-right:1px solid #E9E9E9;" class="tablesort">
		<thead>
			<tr>
				<th width="01%"></th>
				<th width="01%"></th>
				<th width="01%"></th>
				<!--th width="01%">
				<div id="detail_desagrupa" style="display:none;">
					<img src='../img/icon_boleto_off.gif' onClick="agruparTitulos()" style="cursor:pointer;" alt='<?php echo(getTText("agrupar_titulo",C_NONE))?>' title='<?php echo(getTText("agrupar_titulo",C_NONE))?>' border="0">
				</div>
				</th-->
				<th width="01%"></th>
				<!--th width="01%"></th-->
				<th width="05%" class="sortable" nowrap>COD</th>
				<th width="12%" class="sortable" nowrap>NOSSO NUM</th>
				<th width="10%" class="sortable" nowrap>SITUAÇÃO</th>
				<th width="08%" class="sortable-numeric" nowrap>ORIG</th>
				<th width="08%" class="sortable-numeric" nowrap>PAGO</th>
				<th width="10%" class="sortable-date-dmy" nowrap>EMIS</th>
				<th width="10%" class="sortable-date-dmy" nowrap>VCTO</th>
				<th width="30%" class="sortable" nowrap>HISTÓRICO</th>
				<th width="01%" nowrap></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$strCOLOR 				 = CL_CORLINHA_2;
			$boolAgrupar 	         = true;
			$Ct                      = 1;
			$dblValotTotal           = 0;
			$intCodContaPagarReceber = "";
			$strDescricao 			 = "";
			$strDescricaoAux 		 = "";

			foreach($objResult as $objRS){
				$boolAgrupar = true;
				$strIdFrame  = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_conta_pagar_receber");
			?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center">
					<?php if(getValue($objRS,"situacao") == "aberto"){ ?>
					<a href="STupdpagarreceber.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>" target="tradeunion_main" style="border:none;"><img src="../img/icon_write.gif" title="Editar" border="0" style="cursor:pointer;"></a>
					<?php }	else{?>
					<img src="../img/icon_write_off.gif" title="Editar" border="0">
					<?php }?>
				</td>
				<!--td align="center">
					<?php 
					// Se qtde_agrup > 0 é um titulo resultante de um agrupamento
					//if ((getValue($objRS,"qtde_agrup") > 0)&&(getValue($objRS,"situacao") == "aberto")&&((getValue($objRS,"vlr_pago") == "")||(getValue($objRS,"vlr_pago") == 0))){?>
						<img src="../img/icon_desagrupar.gif" title="Desagrupar" border="0" style="cursor:pointer;" onClick="location.href='STDesagrupaTitulo.php?var_chavereg=<?php //echo(getValue($objRS,"cod_conta_pagar_receber"));?>&var_cod_pj=<?php //echo($intVlrCampoChaveDetail);?>';">
					<?php //}	else{?>
						<img src="../img/icon_desagrupar_off.gif" title="Desagrupar" border="0">
					<?php //}?>
				</td-->
				<td align="center">
					<img src="../img/icon_ver_lancamento.gif" title="Ver Lançamentos" onClick="showDetailGridT('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_CadPJ/STifrlancamento.php?var_cod_resize=<?php echo(request("var_chavereg"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
				</td>
				<!--td align="center">
					<?php// if((getValue($objRS,"situacao") == 'aberto') && (getValue($objRS,"qtde_agrup") <= 0) && (getValue($objRS,"vlr_conta") > 0)){ $intQtdeTitulos++; ?>
					<input type="checkbox" name="var_cod_conta_pagar_receber[]" value="<?php //echo(getValue($objRS,"cod_conta_pagar_receber"))?>" class="inputclean" style="margin:0px;">
					<?php// } else{?>
					<input type="checkbox" disabled="disabled" class="inputclean" style="margin:0;">
					<?php// }?>
				</td-->
    	        <td align="center">
					<?php if((getValue($objRS,"situacao") == "lcto_total") || (getValue($objRS,"situacao") == "lcto_parcial")){?>
					<img src="../img/icon_recibo.gif" title="Recibo" onClick="showDetailGridT('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_CadPJ/STifrrecibos.php?var_cod_resize=<?php echo(request("var_chavereg"));?>&var_cod_conta_pagar_receber=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
					<?php } else{?>
					<img src="../img/icon_recibo_off.gif" title="Recibo" border="0" />
					<?php }?>
    	        </td>
				<td align="center">
					<?php
						$strSIT = strtolower(getValue($objRS,"situacao"));
						if(($strSIT=="aberto") || ($strSIT=="lcto_parcial")){
							echo("<a href='javascript:;' style='border:none;' onclick=\"AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=" . getValue($objRS,"cod_conta_pagar_receber") . "','750','580');\" title='Imprimir Boleto'> ");
							echo(" <img src='../img/icon_boleto.gif' border='0' target='_blank' alt='Imprimir Boleto' title='Imprimir Boleto'>");
							echo("</a>");
						} else{
					?>
					<img src="../img/icon_boleto_off.gif" border="0" title="Imprimir Boleto" />
					<?php }?>
	            </td>
				<td align="center"><?php echo(getValue($objRS,"cod_conta_pagar_receber"));?></td>
				<td align="center"><?php echo(getValue($objRS,"nosso_numero")); ?></td>
				<td align="center"><?php echo(strtoupper($strSIT));?></td>
				<td align="right" ><?php echo(FloatToMoeda(getValue($objRS,"vlr_conta")));?></td>
				<td align="right" ><?php echo(FloatToMoeda(getValue($objRS,"vlr_pago")));?></td>
				<td align="center"><span style="color:#AAA;font-size:09px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false));?></span></td>
				<td align="center"><span style="color:#AAA;font-size:09px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false));?></span></td>
				<td align="left"  ><?php echo(getValue($objRS,"historico"));?></td>
				<td style="vertical-align:middle;" align="center">
					<?php if(getValue($objRS,"obs") != ''){ ?>
					<img src="../img/icon_obs.gif" title="<?php echo(getValue($objRS,"obs")); ?>" border="0">
					<?php } ?>
				</td>
			</tr>
			<tr id="detailtr_<?php echo (getValue($objRS,"cod_conta_pagar_receber"));?>" style="display:none; background:<?php echo(CL_CORLINHA_1);?>" class="iframe_detail">
				<td colspan='17' align="left" valign="middle"><iframe name="<?php echo($strIdFrame);?>" id="<?php echo($strIdFrame);?>" width="100%" src="" frameborder="0" scrolling="no"></iframe></td>	
			</tr>
		<?php }	?>
	</tbody>
	<?php //if($intQtdeTitulos > 1) {?>
		<script type="text/javascript" language="javascript">
			//document.getElementById("detail_desagrupa").style.display = "block";
		</script>
		<!--tfoot style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;" >
  		 	<tr>
				<td colspan="3" style="background-color:<?php //echo(CL_CORBAR_GLASS_2);?>;"></td>
				<td align="center" valign="middle" style="background-color:<?php //echo(CL_CORBAR_GLASS_2);?>;">
					<img src='../img/icon_agrupa_titulos.gif' onClick="agruparTitulos()" style="cursor:pointer;" alt='<?php //echo(getTText("agrupar_titulo",C_NONE))?>' title='<?php //echo(getTText("agrupar_titulo",C_NONE))?>' border="0">
				</td>
				<td colspan="12" style="background-color:<?php //echo(CL_CORBAR_GLASS_2);?>;"></td>
			</tr>
		</tfoot-->
	<?php //} else {?>
	<tfoot>
  		<tr>
			<td colspan="17" style="background-color:<?php echo(CL_CORLINHA_1);?>;"></td>
		</tr>
	</tfoot>
	<?php //} ?>
</table>
</form>
<br/>
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