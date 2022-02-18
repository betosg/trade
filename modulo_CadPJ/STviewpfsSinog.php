<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado = request("var_chavereg");	// COD_PJ
	$strTipoCon = (request("var_tipo_consulta") == "") ? "NORM" : request("var_tipo_consulta");
	$strNomeCampoChaveDetail = request("var_field_detail"); // 
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	try{
		// BUSCA RELAÇÕES DA PJ CORRENTE
		$strSQL  = "
			SELECT
				  count(prd_pedido.cod_pedido) AS qtde_ped_homo 
				, cad_pf.cod_pf
				, cad_pj.cod_pj
				, cad_pf.matricula||' - '||cad_pf.nome as nome
				, cad_pj.razao_social
				, cad_pf.cpf
				, cad_pj.cnpj
				, relac_pj_pf.cod_pj_pf
				, relac_pj_pf.funcao
				, relac_pj_pf.categoria
				, relac_pj_pf.departamento
				, relac_pj_pf.dt_admissao
				, relac_pj_pf.dt_demissao
				, relac_pj_pf.obs
			FROM
				relac_pj_pf
			LEFT JOIN cad_pf ON (relac_pj_pf.cod_pf = cad_pf.cod_pf)
			INNER JOIN cad_pj ON (relac_pj_pf.cod_pj = cad_pj.cod_pj)
			LEFT JOIN sd_credencial ON (relac_pj_pf.cod_pj_pf = sd_credencial.cod_pj_pf AND sd_credencial.dtt_inativo IS NULL)
			LEFT OUTER JOIN prd_pedido  
			ON (prd_pedido.situacao <> 'cancelado' AND prd_pedido.it_tipo = 'homo' 
			AND prd_pedido.it_cod_pj_pf = relac_pj_pf.cod_pj_pf AND prd_pedido.dtt_inativo IS NULL) 
			WHERE cad_pj.cod_pj = ".$intCodDado ." AND  relac_pj_pf.dt_inativo IS NULL";
		
		$strSQL .= ($strTipoCon == "NORM") ? " AND relac_pj_pf.dt_demissao IS NULL" : " ";
		$strSQL .= ($strTipoCon == "HOMO") ? " AND relac_pj_pf.dt_demissao IS NOT NULL" : " ";
		$strSQL .= ($strTipoCon == "TODO") ? " " : " ";
		
		$strSQL .= "
			GROUP BY cad_pf.cod_pf
				, cad_pj.cod_pj
				, cad_pf.nome
				, cad_pf.matricula||' - '||cad_pf.nome
				, cad_pj.razao_social
				, cad_pf.cpf
				, cad_pj.cnpj
				, relac_pj_pf.cod_pj_pf
				, relac_pj_pf.funcao
				, relac_pj_pf.categoria
				, relac_pj_pf.departamento
				, relac_pj_pf.dt_admissao
				, relac_pj_pf.dt_demissao
				, relac_pj_pf.obs
			ORDER BY cad_pf.nome";
		// die($strSQL);
		$objResult = $objConn->query($strSQL);		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// TRATAMENTO das cores de linhas do fundo
	// da grade, para alteração de COR caso a
	// linha corrente seja a RELAÇÃO ATIVA
	/* bgcolor = <?php echo(getValue($objRS,"dt_demissao") == "") ? CL_CORLINHA_2 : CL_CORLINHA_1 );?> */
	$boolVerifyChecks = false;
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
		ul{ margin-top: 0px; margin-bottom: 0px; }
		li{ margin-left: 0px; }
		body{ margin:10px;padding-right:10px; }
	</style>
	<style>
		.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
		body{ margin: 10px; background-color:#FFFFFF; } 
		ul{ margin-top: 0px; margin-bottom: 0px; }
		li{ margin-left: 0px; }
	</style>
</head>
<body bgcolor="<?php echo(CL_CORLINHA_1);?>">
<form name='frmSizeBody'>
	<input type='hidden' value='' name='sizeBody'>
	<input type='hidden' value='<?php echo($intCodDado); ?>' name='codAvo'>
</form>
<form name="formpf" action="STetiquetas.php" target="window" method="post" style="margin:0px;">
	<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>" />
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
	<tr>
		<td align="left">
		<?php
			athBeginCssMenu();
				athCssMenuAddItem("","_self",getTText("colaboradores",C_TOUPPER),1);
				athBeginCssSubMenu();
					athCssMenuAddItem("","_self","Novo...",1);
					athBeginCssSubMenu();
					//	athCssMenuAddItem("STverifycpfSinog.php?var_flag_inserir=INS_CARD&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_card",C_NONE));
					//	athCssMenuAddItem("STverifycpfSinog.php?var_flag_inserir=INS_HOMO&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_homo",C_NONE));
					/*	athCssMenuAddItem("STinsFreePF.php?var_flag_inserir=INS_LIVRE&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_livre",C_NONE));*/
						athCssMenuAddItem("STverifyFreeSinog.php?var_flag_inserir=INS_LIVRE&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_livre",C_NONE));
					athEndCssSubMenu();				
					athCssMenuAddItem("","_self","Filtros...",1);
					athBeginCssSubMenu();
						athCssMenuAddItem("STviewpfsSinog.php?var_chavereg=".$intCodDado."&var_tipo_consulta=NORM","_self",getTText("colabs_naohomologados_somente",C_NONE));
						athCssMenuAddItem("STviewpfsSinog.php?var_chavereg=".$intCodDado."&var_tipo_consulta=HOMO","_self",getTText("colabs_homologados_somente",C_NONE));
						athCssMenuAddItem("STviewpfsSinog.php?var_chavereg=".$intCodDado."&var_tipo_consulta=TODO","_self",getTText("colabs_todos",C_NONE));
					athEndCssSubMenu();
				athEndCssSubMenu();
			athEndCssMenu();
			
				
		?>
		</td>
	</tr>
	</table>
	
	<?php
	// TESTA COLABS, CASO CONTRÁRIO, MENSAGEM DE VAZIO
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhuma_resp_pub",C_NONE),"","aviso",1,"","");
	} else{
	?>
	<table bgcolor="<?php echo(CL_CORLINHA_1);?>" style="width:100%;  margin-bottom:0px;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- DEL -->
				<th width="1%"></th><!-- EDIT -->
				<th width="1%"></th><!-- HOMO FAST -->
				<th width="1%"></th><!-- SOLIC CARD -->
				<th width="1%"></th><!-- IMPR CARD -->
				<th width="1%"></th><!-- ETIQUETA -->
				<th width="1%"></th><!-- INSERIR_LCTO -->
				<th width="1%"></th><!-- RECIBO -->
				<th width="1%"></th><!-- Função -->
				<th width="8%" class="sortable-numeric" nowrap>COD</th>
				<th width="25%" class="sortable" nowrap>MATR | NOME</th>
				<th width="12%" class="sortable-numeric" nowrap>CPF</th>
				<th width="17%" class="sortable" nowrap>FUNCAO</th>
				<th width="15%" class="sortable" nowrap>CATEGORIA</th>
				<th width="8%" class="sortable-date-dmy" nowrap>ADMISSÃO</th>
				<th width="8%" class="sortable-date-dmy" nowrap>DEMISSÃO</th>
				<th width="1%"></th><!-- STATUS HOMO -->
				<th width="1%"></th><!-- STATUS PAGAMENTO -->
			</tr>
		</thead>
		<tbody>
	<?php
		// START da listagem de PJs RELACIONADAS
		// com aquele respectivo COD_PF relação
		foreach($objResult as $objRS){
			try{
				// SQL para listagem de CARDS
				// BUSCA a qtde de PEDIDOS DO
				// Tipo CARD e CARDS ativa
				$strSQL = " 
						SELECT DISTINCT
							  count(t5.cod_credencial) AS qtde_credencial
							, count(t6.cod_pedido)     AS qtde_ped_card
							, t2.nome, t5.qtde_impresso, t5.dt_validade
							, t6.it_cod_produto, t5.cod_credencial
						FROM cad_pf t2 
						LEFT OUTER JOIN 
							sd_credencial t5 ON ((t5.cod_pf = t2.cod_pf)
							AND (t5.dtt_inativo IS NULL) 
							AND (CURRENT_DATE <= t5.dt_validade)
							AND (t5.cod_pj_pf = ".getValue($objRS,"cod_pj_pf")."))
						LEFT OUTER JOIN 
							prd_pedido t6 ON ((t6.situacao <> 'cancelado') 
							AND (t6.it_tipo = 'card') 
							AND (CURRENT_DATE <= t6.it_dt_fim_val_produto) 
							AND (t6.it_cod_pf = t2.cod_pf))
						WHERE t2.cod_pf = ".getValue($objRS,"cod_pf")."
						GROUP BY t2.nome, t5.qtde_impresso, t5.dt_validade, t6.it_cod_produto, t5.cod_credencial";
				$objResultPF = $objConn->query($strSQL);
				$objRSPF = $objResultPF->fetch();
				}catch(PDOException $e){
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				}
			$boolAgrupar = true;
			$strIdFrame  = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_conta_pagar_receber");
			
			// Localiza a Credencial VÁLIDA para a RELAÇÃO corrente
			// e com base nisso LOCALIZA o TÍTULO CORRENTE, sua SITUAÇÃO
			try{
				$strSQL = "
					SELECT 
						  fin_conta_pagar_receber.situacao
						, fin_conta_pagar_receber.cod_conta_pagar_receber
						, sd_credencial.cod_credencial
					FROM sd_credencial
					INNER JOIN fin_conta_pagar_receber ON (fin_conta_pagar_receber.cod_pedido = sd_credencial.cod_pedido)
					WHERE sd_credencial.cod_pj_pf = ".getValue($objRS,"cod_pj_pf");
				$objResultTitulos = $objConn->query($strSQL);
				$objRSTitulos     = $objResultTitulos->fetch();
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
			
			$strIdFrame  = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRSTitulos,"cod_conta_pagar_receber");
		?>
			<tr>
				<td align="center">
					<img src="../img/icon_trash.gif" title="<?php echo(getTText("deletar",C_NONE));?>" onclick="document.location.href='STdelcolabSinog.php?var_cod_pj=<?php echo($intCodDado);?>&var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cpf=<?php echo(getValue($objRS,"cpf"));?>';" style="cursor:pointer;" />
				</td>
				<td align="center">
					<img src="../img/icon_write.gif" title="<?php echo(getTText("editar",C_NONE));?>" onclick="document.location.href='STupdcolabSinog.php?var_cod_pj=<?php echo($intCodDado);?>&var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cpf=<?php echo(getValue($objRS,"cpf"));?>';" style="cursor:pointer;" />
				</td>
				<td align="center">
				<?php if((getValue($objRS,"dt_demissao") == "") && (getValue($objRS,"qtde_ped_homo") == 0)){?>	
					<img src="../img/icon_solicitacao_homo_fast.gif" title="<?php echo(getTText("homologar_fast",C_NONE));?>" onClick="document.location.href='../modulo_CadPJ/STGeraHomoFast.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj_pf"));?>&var_cod_pj=<?php echo($intCodDado);?>';" style="cursor:pointer;"/>
				<?php } else{?>
					<img src="../img/icon_solicitacao_homo_fast_off.gif" border="0" title="<?php echo(getTText("homologar_fast",C_NONE));?>" />
				<?php }?>
				</td>
				<td align="center">
				<?php if(((getValue($objRSPF,"qtde_credencial") < 1)&&(getValue($objRSPF,"qtde_ped_card") < 1))){?>
					<img src="../img/icon_renova_card.gif" title="<?php echo(getTText("card_fast",C_NONE));?>" onclick="document.location.href='../modulo_CadPJ/STgeracardfast.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj_pf"));?>&var_cod_pj=<?php echo($intCodDado);?>';" style="cursor:pointer;" />
				<?php } else{?>
					<img src="../img/icon_renova_card_red.gif" title="<?php echo(getTText("card_fast_red",C_NONE));?>" onclick="document.location.href='../modulo_CadPJ/STgeracardfast.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj_pf"));?>&var_cod_pj=<?php echo($intCodDado);?>';" style="cursor:pointer;" />
				<?php }?>
				</td>
				<td align="center">
				<?php if(getValue($objRSPF,"cod_credencial") != ""){?>
					<img src="../img/icon_gera_carteirinha.gif" alt="<?php echo(getTText("impr_card",C_NONE));?>" title="<?php echo(getTText("impr_card",C_NONE));?>" onClick="AbreJanelaPAGE('../modulo_PainelAdmin/STGeraCard.php?var_chavereg=<?php echo(getValue($objRSPF,"cod_credencial"));?>','700','500');" style="cursor:pointer;"/>
				<?php } else{?>
					<img src="../img/icon_impr_carteirinha_off.gif" border="0" title="<?php echo(getTText("impr_card",C_NONE));?>" />
				<?php }?>
				</td>
				<td align="center"><input type="checkbox" name="var_pf_selec[]" id="var_pf_selec" class="inputclean" value="<?php echo(getValue($objRS,"cod_pf"));?>"/></td>
				<td align="center">
				<?php if(getValue($objRSTitulos,"cod_conta_pagar_receber") != ""){?>
					<img src="../img/icon_ver_lancamento.gif" title="Ver Lançamentos" onClick="showDetailGrid('<?php echo(getValue($objRSTitulos,"cod_conta_pagar_receber"));?>','../modulo_CadPJ/STifrlancamento.php?var_cod_resize=<?php echo(request("var_chavereg"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
				<?php } else{?>
					<img src="../img/icon_ver_lancamento_off.gif" title="Ver Lançamentos" border="0" />
				<?php }?>
				</td>
				<td align="center">
				<?php if((getValue($objRSTitulos,"situacao") == "lcto_total") || (getValue($objRSTitulos,"situacao") == "lcto_parcial")){?>
					<img src="../img/icon_recibo.gif" title="Recibo" onClick="showDetailGrid('<?php echo(getValue($objRSTitulos,"cod_conta_pagar_receber"));?>','../modulo_CadPJ/STifrrecibos.php?var_cod_resize=<?php echo(request("var_chavereg"));?>&var_cod_conta_pagar_receber=<?php echo(getValue($objRSTitulos,"cod_conta_pagar_receber"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
				<?php } else{?>
					<img src="../img/icon_recibo_off.gif" title="Recibo" border="0" />
				<?php }?>
				</td>
				<td align="center">
					<img src="../img/icon_prioridade_media.png" title="<?php echo(getTText("tipo",C_NONE));?>" onclick="document.location.href='StPFFuncaoSinog.php?var_cod_pj=<?php echo($intCodDado);?>&var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cpf=<?php echo(getValue($objRS,"cpf"));?>';" style="cursor:pointer;" />
				</td>
				<td style="text-align:center;"><?php echo(getValue($objRS,"cod_pf")); ?></td>
				<td style="text-align:left;"  ><?php echo(getValue($objRS,"nome")); ?></td>
				<td style="text-align:center;"><?php echo(getValue($objRS,"cpf")); ?></td>
				<td style="text-align:left;"  ><?php echo(getValue($objRS,"funcao")); ?></td>
				<td style="text-align:center;"><?php echo(getValue($objRS,"categoria")); ?></td>
				<td style="text-align:center;font-size:10px;color:#CCC;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false)); ?></td>
				<td style="text-align:center;font-size:10px;color:#CCC;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_demissao"),false)); ?></td>
				<td style="text-align:center;">
				<?php if(getValue($objRS,"dt_demissao") != ""){?>
					<img src="../img/icon_sit_invalido.gif" title="<?php echo(getTText("colab_homologado",C_TOUPPER));?>" />
				<?php } else if(getValue($objRS,"qtde_ped_homo") > 0){?>
					<img src="../img/icon_sit_saindo.gif" title="<?php echo(getTText("colab_proc_homo",C_TOUPPER));?>" />
				<?php }?>
				</td>
				<td style="text-align:center;">
				<?php if((getValue($objRSTitulos,"situacao") != "") && (getValue($objRSTitulos,"cod_conta_pagar_receber") != "")){?>
					<img src="../img/icon_obs.gif" title="<?php echo(getTText("card",C_NONE).getValue($objRSTitulos,"cod_credencial")."\n".getTText("titulo_card",C_NONE).getValue($objRSTitulos,"cod_conta_pagar_receber")."\n".getTText("titulo_situacao",C_NONE).strtoupper(getValue($objRSTitulos,"situacao")));?>" />
				<?php }?>
				</td>
			</tr>
			<tr id="detailtr_<?php echo (getValue($objRSTitulos,"cod_conta_pagar_receber"));?>" style="display:none; background:<?php echo(CL_CORLINHA_1);?>" class="iframe_detail">
				<td colspan='18' align="left" valign="middle"><iframe name="<?php echo($strIdFrame);?>" id="<?php echo($strIdFrame);?>" width="100%" src="" frameborder="0" scrolling="no"></iframe></td>	
			</tr>
		<?php }	?>
		</tbody>
		<tfoot>
			<tr bgcolor="#DDDDDD">
				<td colspan="4"></td>
				<td align="center">
					<img src="../img/icon_etiqueta_pf.gif" title="<?php echo(getTText("impr_etiqueta",C_NONE));?>" style="cursor:pointer;" onClick="openTARGET();" /></td>
				<td colspan="11"></td>
			</tr>
		</tfoot>
	</table>
</form>
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