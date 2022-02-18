<?php
	// CABEÇALHOS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// CODIGO DO LANÇAMENTO
	$intCodLcto = request("var_chavereg");
	
	// TRATAMENTO, CASO CODIGO LCTO NULO
	if($intCodLcto == ""){
		echo(
			"<center>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
					<tr>
					<td align=\"center\" valign=\"middle\" width=\"100%\">");
				mensagem("err_dados_titulo","err_sql_desc_card",getTText("cpf_matricula_null",C_NONE),"","aviso",1)	;
				echo 
				   ("</td>
					</tr>
				</table>
			</center>");
		die();
	}
	
	// AVBERTURA DE CONEXÃO COM DB
	$objConn 	= abreDBConn(CFG_DB);
	
	// BUSCA DADOS REFERENTES AO LANÇAMENTO
	try{
		$strSQL ="
			SELECT 
				  lct.cod_lcto_ordinario
				, lct.vlr_lcto
				, lct.vlr_multa
				, lct.vlr_juros
				, lct.vlr_desc
				, lct.dt_lcto
				, lct.historico as lcto_historico
				, lct.obs
				, lct.sys_dtt_ins
				, lct.sys_usr_ins
				, lct.tipo_documento
				, lct.extra_documento
				, pcont.cod_reduzido
				, pcont.nome as plano_conta
				, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
					   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT razao_social FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
					   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
				  END AS razao_social
				, fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.codigo
				, fin_conta_pagar_receber.vlr_desc AS desc
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.cod_pedido
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.obs
			FROM 
				fin_lcto_ordinario lct
			INNER JOIN fin_conta_pagar_receber ON (lct.cod_conta_pagar_receber = fin_conta_pagar_receber.cod_conta_pagar_receber)
			LEFT JOIN fin_plano_conta pcont ON lct.cod_plano_conta = pcont.cod_plano_conta
			WHERE lct.cod_lcto_ordinario = ".$intCodLcto."
			ORDER BY cod_conta_pagar_receber DESC, dt_lcto DESC";
		$objResult = $objConn->query($strSQL);		
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// SQL PARA SOMATÓRIO DOS LCTOS
	try{
		$strSQL = "SELECT SUM(fin_lcto_ordinario.vlr_lcto + fin_lcto_ordinario.vlr_juros - fin_lcto_ordinario.vlr_desc) AS total FROM fin_lcto_ordinario WHERE fin_lcto_ordinario.cod_lcto_ordinario = ".$intCodLcto;
		//die($strSQL);
		$objResultLctosTotal  = $objConn->query($strSQL);
		$objRSLctoTotal = $objResultLctosTotal->fetch();
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// TRATAMENTO PARA NÃO EXIBIÇÃO CASO NÃO EXISTA LANÇAMENTOS
	if($objResult->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("lcto_nao_existe",C_NONE),"","aviso",1);
		die();
	}
	
	// FETCH DOS DADOS
	$objRS = $objResult->fetch();
	
	// RECEBE VALORES PARA MONTAR CABEÇALHO
	$strNomePF     = getValue($objRS,"nome");
	$intCodContaPR = getValue($objRS,"cod_conta_pagar_receber");
	$intVlrDesc    = getValue($objRS,"desc");
	$intVlrSaldo   = getValue($objRS,"vlr_saldo");
	$intVlrPago    = getValue($objRS,"vlr_pago");
	$intVlrPago    = getValue($objRS,"vlr_lcto");
	$intVlrConta   = getValue($objRS,"vlr_conta");
	$strHistorico  = getValue($objRS,"historico");
	$strObs        = getValue($objRS,"obs");
?>
<html>
<head>
	<title><?php echo(strtoupper(CFG_SYSTEM_NAME));?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		<!-- RESIZE DA PÁGINA no onload para se --> 
		<!-- enquadrar ao layout do RECIBO  :D  -->
		window.onload = function (){
			self.resizeTo(820,500);
			//window.print(); 
		}
	</script>
	<style type="text/css">
		.table_operador			{ padding:2px; }
		.table_footer			{ margin-top:5px;border:1px solid #C9C9C9;padding:2px; }
		.table_footer_conteudo	{ padding-left:25px;color:#666666;font-size:12px; }
		.td_
		 body					{ padding:5px 5px 5px 5px;font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif; }
		.table_master			{ border:1px solid #C9C9C9; }
		.tr_header				{ background-color:#E9E9E9;height:40px;border:1px solid #C9C9C9; }
		.assinatura				{ font-size:16px; }
		.td_header				{ vertical-align:middle;border-bottom:1px solid #C9C9C9;}
		.tr_table				{ margin-top:20px; }
		.td_cidade				{ padding:0px 25px 0px 0px; }
		.td_data_atual			{ padding:35px 24px 0px 0px; }
		.td_baixo_assinatura	{ padding-right:25px;color:#666666;font-size:14px; }
		
		.td_lcto{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
			font-weight:bold;
		}
		
		.td_lcto_conteudo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:11px;
		}
		
		.font_title{ 
			font-family:"Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
			font-size:30px;
			font-weight:bolder;
			padding-left:25px; 
		}
		
		.box_header{ 
			font-family:"Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
			text-align:justify;
			font-size:14px;
			width:95%;
			border:1px solid #C9C9C9;
			padding-top:20px;
			padding-bottom:20px;
			padding-right:5px;
			padding-left:5px;
			margin-top:15px;
			margin-bottom:15px;
		}
		
		.box_hr{
			width:90%;
			height:30px;
			border:1px solid #C9C9C9;
			padding-left:5px;
			padding-right:5px;
			padding-top:px;
			margin-top:0px;
			margin-right:7px;
			background-color:#FFF;
		}
		
		.table_box_hr_conteudo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:16px;
			font-weight:bolder;
		}
		
		.input_name_recibo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:15px;
			height:20px;
			border-top:0px transparent solid;
			border-right:0px transparent solid;
			border-bottom:1px dashed #E9E9E9;
			border-left:0px transparent solid;
			text-align:center;
			background-image:url(); 
			padding:0px;
		}
		
		.input_cpf_recibo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
			height:20px;
			border-top:0px transparent solid;
			border-right:0px transparent solid;
			border-bottom:1px dashed #E9E9E9;
			border-left:0px transparent solid;
			text-align:center;
			background-image:url(); 
			padding:0px;
		}
	</style>
</head>
<body bgcolor="#FFFFFF">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_master">
		<tr class="tr_header">
			<td width="75%" align="left" class="td_header">
				<span class="font_title"><?php echo getTText("recibo",C_TOUPPER);?></span>
			</td>
			<td width="25%" align="center" class="td_header">
				<div class="box_hr">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
						<tr>
							<td align="left" class="table_box_hr_conteudo">
								<strong><?php echo(getTText("reais_abrev",C_TOUPPER));?></strong>
							</td>
							<td align="right" class="table_box_hr_conteudo">
								<strong><?php echo(number_format((double) getValue($objRSLctoTotal,"total"),2,',','.'));?></strong>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr class="tr_table">
			<td colspan="2" align="center">
				<div class="box_header">
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
					<?php echo getTText("recibo_cont_lcto_um",C_TOUPPER).
								"<strong>".getValue($objRS,"razao_social")."</strong>".
								getTText("recibo_cont_lcto_dois",C_TOUPPER).
								"<strong>".number_format((double) getValue($objRSLctoTotal,"total"),2,',','.').
								" (".strtoupper(valorPorExtenso(getValue($objRSLctoTotal,"total")))." )</strong>".
								getTText("recibo_cont_lcto_sete",C_TOUPPER).
								dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false).", ".
								getTText("recibo_cont_lcto_tres",C_TOUPPER).
								getValue($objRS,"cod_conta_pagar_receber")." ( ".$strHistorico." ) ".
								getTText("recibo_cont_lcto_quatro",C_TOUPPER).
								number_format((double) getValue($objRS,"vlr_conta"),2,',','.').", ".
								getTText("recibo_cont_lcto_cinco",C_TOUPPER).
								number_format((double) getValue($objRS,"vlr_saldo"),2,',','.').
								getTText("recibo_cont_lcto_seis",C_TOUPPER);
					?>
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
				</div>
			</td>
		</tr>
		<tr class="tr_table">
			<td colspan="2" align="center">
				<div class="box_header" style="margin-top:3px;padding-top:5px;padding-bottom:5px;">
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td width="10%" align="center" class="td_lcto"><?php echo(getTText("cod_lcto_ordinario",C_TOUPPER));?></td>
							<td width="18%" align="center" class="td_lcto"><?php echo(getTText("dt_lcto",C_TOUPPER));?></td>
							<td width="18%" align="right"  class="td_lcto" style="padding-right:5px;"><?php echo(getTText("vlr_do_lcto",C_TOUPPER));?></td>
							<td width="18%" align="right"  class="td_lcto" style="padding-right:5px;"><?php echo(getTText("vlr_juros",C_TOUPPER));?></td>
							<td width="18%" align="right"  class="td_lcto" style="padding-right:5px;"><?php echo(getTText("vlr_desc",C_TOUPPER));?></td>
							<td width="18%" align="right"  class="td_lcto" style="padding-right:5px;"><?php echo(getTText("total_pg",C_TOUPPER));?></td>
						</tr>
						<tr>
							<td width="10%" align="center" class="td_lcto_conteudo">&bull;&nbsp;<?php echo(getValue($objRS,"cod_lcto_ordinario"));?></td>
							<td width="18%" align="center" class="td_lcto_conteudo"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false));?></td>
							<td width="18%" align="right"  class="td_lcto_conteudo" style="padding-right:5px;">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td width="40%" align="right"><?php echo(getTText("reais_abrev",C_NONE));?></td>
										<td width="60%" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_lcto"),2,',','.'));?></td>
									</tr>
								</table>
							</td>
							<td width="18%" align="right"  class="td_lcto_conteudo" style="padding-right:5px;">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td width="40%" align="right"><?php echo(getTText("reais_abrev",C_NONE));?></td>
										<td width="60%" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_juros"),2,',','.'));?></td>
									</tr>
								</table>
							</td>
							<td width="18%" align="right"  class="td_lcto_conteudo" style="padding-right:5px;">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td width="40%" align="right"><?php echo(getTText("reais_abrev",C_NONE));?></td>
										<td width="60%" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_desc"),2,',','.'));?></td>
									</tr>
								</table>
							</td>
							<td width="18%" align="right"  class="td_lcto_conteudo" style="padding-right:5px;">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td width="40%" align="right"><?php echo(getTText("reais_abrev",C_NONE));?></td>
										<td width="60%" align="right">
											<?php
												$dblSubTotal = 0; 
												$dblSubTotal = getValue($objRS,"vlr_lcto") + getValue($objRS,"vlr_juros") - getValue($objRS,"vlr_desc");
												echo(number_format((double) $dblSubTotal,2,',','.'));
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
				</div>
			</td>
		</tr>
		<tr><td colspan="2" align="right" class="td_cidade"><div class="assinatura"><?php echo getVarEntidade($objConn,"fin_recibo_cidade_assinatura").",&nbsp;".strtoupper(translateDate(dDate(CFG_LANG,now(),false)));?></div></td></tr>
		<tr>
        	<td colspan="2" align="right" class="td_data_atual">
        		<div class="assinatura">
					<?php 
						//echo getTText("linha_ass",C_TOUPPER);
					    if (getVarEntidade($objConn,"fin_recibo_img_assinatura")!="") {
							echo "<img src='" . getVarEntidade($objConn,"fin_recibo_img_assinatura") . "' border='0' height='50'>";
						} else {
							echo getTText("linha_ass",C_TOUPPER);
						}
					?>
                </div>
            </td>
        </tr>
		<tr><td colspan="2" align="right" class="td_baixo_assinatura"><?php echo getVarEntidade($objConn,"razao_social");?></td></tr>
		<tr><td colspan="2" align="right" valign="top" class="td_baixo_assinatura"><?php echo(getTText("cnpj",C_TOUPPER).": ");?><?php echo getVarEntidade($objConn,"cnpj");?></td></tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="0" style="" width="100%" class="table_footer">
		<tr>
			<td colspan="2" align="left" class="table_footer_conteudo">
				<?php echo getVarEntidade($objConn,"nome_fan")." - ".getVarEntidade($objConn,"lc_logradouro").", ".getVarEntidade($objConn,"lc_num")." - ".getVarEntidade($objConn,"lc_bairro")." - ".getVarEntidade($objConn,"lc_cidade")." - ".getTText("fone",C_TOUPPER).getVarEntidade($objConn,"fone");?>
			</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table_operador">
		<tr>
			<td align="left"><span class="comment_peq"><?php echo(getTText("operador",C_NONE).getsession(CFG_SYSTEM_NAME."_id_usuario"));?></span></td>
			<td align="right"><span class="comment_peq"><?php echo(dDate(CFG_LANG,now(),true));?></span></td>
		</tr>
	</table>
</body>
</html>