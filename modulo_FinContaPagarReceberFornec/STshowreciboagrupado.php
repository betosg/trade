<?php
	// CABEÇALHOS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	//include_once("../_scripts/scripts.js");
	//include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodTitulo  = request("var_chavereg");
	$strNomeSacado = request("var_nome_sacado");
	$bool2Paginas  = (request("var_duas_vias") != "") ? TRUE : FALSE;
	$arrPFS 	   = Array(); 					// INICIALIZA O ARRAY DE PFS
		
	// TRATAMENTO CONTRA COD_TITULO VAZIO
	if($intCodTitulo == ""){
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
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// LOCALIZA DADOS DO TITULO INFORMADO
	// PARA CABEÇALHO SUPERIOR DO RECIBO
	try{
		$strSQL = "
			SELECT  
				  cad_pj.razao_social
				, fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.vlr_desc AS desc
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.cod_pedido
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.obs
			FROM
				fin_conta_pagar_receber
			INNER JOIN 
				cad_pj ON cad_pj.cod_pj = fin_conta_pagar_receber.codigo
			WHERE
				fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodTitulo;
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// FETCH DOS DADOS DO TITULO
	$objRS = $objResult->fetch();
	
	// LOCALIZA OS DADOS DOS TÍTULOS AGRUPADOS
	try{
		$strSQL = "
			SELECT
				  fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.vlr_desc AS desc
				, fin_conta_pagar_receber.codigo
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.cod_pedido
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.obs
				, prd_pedido.cod_pedido
				, prd_pedido.valor as valor_ped
				, prd_pedido.it_valor
				, prd_produto.rotulo
				, prd_produto.descricao as prod_descricao
				, prd_produto.valor as valor_prod
				, cad_pf.nome
				, cad_pf.cpf
			FROM
				fin_conta_pagar_receber
			LEFT JOIN 
				prd_pedido ON (prd_pedido.cod_pedido = fin_conta_pagar_receber.cod_pedido)
			LEFT JOIN 
				prd_produto ON (prd_pedido.it_cod_produto = prd_produto.cod_produto)
			LEFT JOIN
				cad_pf ON (cad_pf.cod_pf = prd_pedido.it_cod_pf)
			WHERE
				fin_conta_pagar_receber.cod_agrupador = ".$intCodTitulo;
		$objResultTits = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// LOCALIZA OS LANÇAMENTOS COM BASE NO CODIGO DO TITULO INFROMADO
	try{
		$strSQL = "
			SELECT  
				  fin_lcto_ordinario.cod_lcto_ordinario
				, fin_lcto_ordinario.vlr_lcto
				, fin_lcto_ordinario.vlr_desc
				, fin_lcto_ordinario.vlr_juros
				, fin_lcto_ordinario.vlr_multa
				, fin_lcto_ordinario.dt_lcto
				, fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_lcto_ordinario.tipo_documento
				, fin_conta_pagar_receber.vlr_desc AS desc
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.cod_pedido
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.obs
			FROM
				fin_lcto_ordinario
			INNER JOIN
				fin_conta_pagar_receber ON (fin_conta_pagar_receber.cod_conta_pagar_receber = fin_lcto_ordinario.cod_conta_pagar_receber)
			WHERE
				fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodTitulo." ORDER BY dt_lcto";
		//die($strSQL);
		$objResultLctosCount  = $objConn->query($strSQL);
		$objResultLctosLista  = $objConn->query($strSQL);
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
		
	// SQL PARA BUSCAR OS LANÇAMENTOS
	try{
		$strSQL = "
			SELECT  
 				   SUM(fin_lcto_ordinario.vlr_lcto + fin_lcto_ordinario.vlr_juros - fin_lcto_ordinario.vlr_desc) AS total
				 , SUM(fin_lcto_ordinario.vlr_juros) AS total_juros
				 , SUM(fin_lcto_ordinario.vlr_desc) AS total_desc

			FROM
				fin_lcto_ordinario,
				fin_conta_pagar_receber
			WHERE	
				fin_conta_pagar_receber.cod_conta_pagar_receber = fin_lcto_ordinario.cod_conta_pagar_receber
			AND
				fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodTitulo;
		//die($strSQL);
		$objResultLctosTotal  = $objConn->query($strSQL);
		$objRSLctoTotal = $objResultLctosTotal->fetch();
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}	
	
	
	// TRATAMENTO PARA EXIBIÇÃO DE MENSAGEM CASO TITULO SEM LCTO
	if($objResultLctosCount->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("desc_sem_lctos",C_NONE),"","aviso",1);
		die();
	}
	
	// INICIALIZA STREAM DE ARQUIVO
	$strStreamFile  = "";
	$strStreamFile .= "
<html>
<head>
	<title>".strtoupper(CFG_SYSTEM_NAME)."</title>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	<link href='../../../_cemisys/_css/".CFG_SYSTEM_NAME.".css' rel='stylesheet' type='text/css'>
	<script type='text/javascript'>
		<!-- RESIZE DA PÁGINA no onload para se --> 
		<!-- enquadrar ao layout do RECIBO  :D  -->
		window.onload = function (){
			self.resizeTo(800,600);
			//window.print(); 
		}
	</script>
	<style type='text/css'>
		.table_operador			{ padding:2px; }
		.table_footer			{ margin-top:5px;border:1px solid #C9C9C9;padding:2px; }
		.table_footer_conteudo	{ padding-left:25px;color:#666666;font-size:12px; }
		 body					{ padding:5px 5px 5px 5px;font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif; }
		.table_master			{ border:1px solid #C9C9C9; }
		.tr_header				{ background-color:#E9E9E9;height:40px;border:1px solid #C9C9C9; }
		.assinatura				{ font-size:13px;margin-top:5px;vertical-align:top;}
		.td_header				{ vertical-align:middle;border-bottom:1px solid #C9C9C9;}
		.tr_table				{ margin-top:20px !important; }
		.td_cidade				{ padding:0px 25px 0px 0px; }
		.td_data_atual			{ padding:35px 24px 0px 0px; }
		.td_align				{ padding-left:15px;padding-right:15px; }
		.td_baixo_assinatura	{ padding-right:25px;color:#666666;font-size:10px; }
		.td_lctos_min			{ font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif; font-size:10px; }
		/*.td_master				{ padding-left:15px; }*/
		
		.td_lctos{ 
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif; 
			font-size:09px;
			font-weight:bold; 
		}
		
		.font_title{ 
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:18px;
			font-weight:bolder;
			padding-left:25px; 
		}
		
		.box_header{ 
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			text-align:justify;
			font-size:11px;
			border:1px solid #C9C9C9;
			padding-top:15px;
			padding-bottom:15px;
			padding-right:8px;
			padding-left:8px;
			margin:10px 0px 10px 0px;
		}
		
		.box_linha_sup{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif; 
			text-align:justify;
			font-size:10px;
			border:1px solid #C9C9C9;
			padding:5px 8px 5px 8px;
			margin-top:20px;
		}
		
		.box_hr{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			height:18px;
			border:1px solid #C9C9C9;
			padding-left:5px;
			padding-right:5px;
			padding-top:px;
			margin-top:0px;
			margin-right:13px;
			background-color:#FFF;
		}
		
		.table_box_hr_conteudo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
			font-weight:bolder;
		}
		
		.input_name_recibo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
			height:20px;
			border-top:0px transparent solid;
			border-right:0px transparent solid;
			border-bottom:1px dashed #CCC;
			border-left:0px transparent solid;
			text-align:left;
			background-image:url(); 
			padding:0px;
		}
		.input_cpf_recibo{
			font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
			height:20px;
			border-top:0px transparent solid;
			border-right:0px transparent solid;
			border-bottom:1px dashed #CCC;
			border-left:0px transparent solid;
			text-align:left;
			background-image:url(); 
			padding:0px;
		}
		
		.box_fichas{
			margin-left:15px;
			width:150px;
			margin-bottom: 3px;
		}
		
		.box_info_fichas{
			margin-left:15px;
			margin-bottom:10px;
			text-align:justify;
			font-size:9px;
		}
		
		.box_fichas_inside{
			margin-bottom:8px;
			padding: 3px;
			margin-left:15px;
			width:170px;
			font-family: 'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;
			font-size:09px;
			border:1px dashed #CCC;
		}
	</style>
</head>
<body bgcolor='#FFFFFF'>";

$strStreamBody = "
	<table cellpadding='0' cellspacing='1' border='0' width='100%' style='margin-bottom:20px;'>
		<tr>
			<td width='20%' align='left' class='td_header' style='border:none;'><img src='../../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/imgdin/logotipo.jpg' border='0' height='50'/></td>
			<td width='80%' align='justify' class='td_header' style='padding:05px;margin-left:15px;border:1px dashed #CCC;font-size:09px;font-weight:bold;'>".getVarEntidade($objConn,"razao_social")."</td>
		</tr>
	</table>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class='table_master'>
		<tr class='tr_header'>
			<td width='75%' align='left' class='td_header'>
				<span class='font_title'>".getTText("recibo",C_TOUPPER)."</span>
			</td>
			<td width='25%' align='center' class='td_header' style='padding-left:15px;'>
				<div class='box_hr'>
					<table cellspacing='0' cellpadding='0' border='0' width='100%' height='100%'>
						<tr>
							<td align='left' class='table_box_hr_conteudo'>
								<strong>".getTText("reais_abrev",C_TOUPPER)."</strong>
							</td>
							<td align='right' class='table_box_hr_conteudo'>
								<strong>".number_format((double) getValue($objRSLctoTotal,"total"),2,',','.')."</strong>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr class='tr_table'>
			<td width='75%' class='td_align'>
				<div class='box_linha_sup'>
					<!-- BLOCO QUE CONTÉM A DESCRICAO DO NUMERO DO TITULO -->
					".
						getTText("recibo_linha_sup",C_TOUPPER).
						"<strong>".getValue($objRS,"cod_conta_pagar_receber")."</strong>
					<!-- BLOCO QUE CONTÉM A DESCRICAO DO NUMERO DO TITULO -->
				</div>
			</td>
			<td width='25%' class='td_align'>
				<div class='box_linha_sup'>
					<!-- BLOCO QUE CONTÉM A DESCRICAO DO NUMERO DO TITULO -->
					<strong>".substr(dDate(CFG_LANG,now(),true),0,16)."</strong>
					<!-- BLOCO QUE CONTÉM A DESCRICAO DO NUMERO DO TITULO -->
				</div>
			</td>				
		</tr>
		<tr class='tr_table'>
			<td colspan='2' class='td_align'>
				<div class='box_header'>
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
					".
						getTText("recibo_cont_tit_um",C_TOUPPER).
						" ".$strNomeSacado." "
						.getTText("recibo_cont_tit_um_dois",C_TOUPPER).
						"<strong>".getTText("reais_abrev",C_TOUPPER).
						number_format((double) getValue($objRSLctoTotal,"total"),2,',','.').
						" (".trim(strtoupper(valorPorExtenso(getValue($objRSLctoTotal,"total")))).")</strong>".
						getTText("recibo_cont_tit_dois",C_TOUPPER).
						getValue($objRS,"cod_conta_pagar_receber")."
					<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO PAGAMENTO DO LANÇAMENTO -->
				</div>
			</td>
		</tr>
		
		<tr class='tr_table'>
			<td colspan='2' class='td_align'>
				<div class='box_header' style='border:0px;padding:0px;margin-bottom:4px;'>
				<strong>".getTText("titulos_agrup",C_TOUPPER)."</strong>
				</div>
			</td>
		</tr>
		<tr class='tr_table'>
			<td colspan='2' class='td_align'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%' >
					<tr>
						<td width='10%' align='center' class='td_lctos' style='border:1px solid #C9C9C9;'>
							".getTText("titulo_no",C_TOUPPER)."
						</td>
						<td width='10%' align='center' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							".getTText("pedido_no",C_TOUPPER)."
						</td>
						<td width='13%' align='right' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;padding-right:05px;'>
							".getTText("pedido_vlr",C_TOUPPER)."
						</td>
						<td width='25%' align='center' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							".getTText("produto_rot",C_TOUPPER)."
						</td>
						<td width='14%' align='center' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							".getTText("produto_vlr",C_TOUPPER)."
						</td>
						<td width='18%' align='right' class='td_lctos'  style='border:1px solid #C9C9C9;border-left:0px;padding-right:10px;'>
							".getTText("titulo_vlr",C_TOUPPER)."
						</td>
					</tr> 
					<!-- LISTA TODOS TITULOS CORRESPONDENTES -->";
					// Inicializa Header para ser usado na listagem
					$auxCounter 		= 0;
					$strObsFichas       = ""; 
					$strHeaderObsFichas = "";
					foreach($objResultTits as $objRSTit){
					$arrPFS[$auxCounter] = ((getValue($objRSTit,"nome") == "") || (getValue($objRSTit,"cpf") == "")) ? "" : getValue($objRSTit,"nome")." (CPF: ".getValue($objRSTit,"cpf").")";
					$strStreamBody .= "
					<tr>
						<td width='10%' align='left' class='td_lctos_min' style='padding-left:8px;border-bottom:1px solid #C9C9C9;border-left:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							&bull;&nbsp;".getValue($objRSTit,"cod_conta_pagar_receber")."
						</td>
						<td width='10%' align='center' class='td_lctos_min' style='border-bottom:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							".((getValue($objRSTit,"cod_pedido") == "") ? " - " : getValue($objRSTit,"cod_pedido"))."
						</td>
						<td width='13%' align='right' class='td_lctos_min' style='border-bottom:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='left' class='td_lctos_min' width='30%' style='padding-left:10px;'>
										".((getValue($objRSTit,"valor_ped") == "") ? "" : getTText("reais_abrev",C_TOUPPER))."
									</td>
									<td align='right' class='td_lctos_min' width='70%' style='padding-right:5px;'>
										".((getValue($objRSTit,"valor_ped") == "") ? " - " :number_format((double) getValue($objRSTit,"valor_ped"),2,',','.'))."
									</td>
								</tr>
							</table>
						</td>
						<td width='25%' align='left' class='td_lctos_min' style='padding-left:10px;border-bottom:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							".((getValue($objRSTit,"prod_descricao") == "") ? "<span class='comment_peq'>&bull;&nbsp;".getTText("cobranca_s_produto",C_TOUPPER)."</span>" : substr(getValue($objRSTit,"prod_descricao"),0,32))."
						</td>
						<td width='14%' align='right' class='td_lctos_min' style='padding-right:5px;border-bottom:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' class='td_lctos_min' width='40%' style='padding-left:10px;'>
										".((getValue($objRSTit,"rotulo") == "") ? " " : getTText("reais_abrev",C_TOUPPER))."
									</td>
									<td align='right' class='td_lctos_min' width='60%' style='padding-right:5px;'>
										".((getValue($objRSTit,"rotulo") == "") ? " - " : number_format((double) getValue($objRSTit,"valor_prod"),2,',','.'))."
									</td>
								</tr>
							</table>
						</td>
						<td width='18%' align='right' class='td_lctos_min' style='padding-right:5px;border-bottom:1px solid #C9C9C9;border-right:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' width='60%' class='td_lctos_min' style='padding-left:10px;'>
										".getTText("reais_abrev",C_TOUPPER)."
									</td>
									<td align='right' width='40%' class='td_lctos_min' style='padding-right:5px;'>
										".number_format((double) getValue($objRSTit,"vlr_conta"),2,',','.')."
									</td>
								</tr>
							</table>
						</td>
					</tr>"; 
					$auxCounter++;
					} // foreach($objResultTits...
					$strStreamBody .= "
					<tr>
						<td colspan='5' class='td_lctos_min' align='right' style='border:0px;padding-top:07px;font-size:12px;'>
							<strong>".getTText("vlr_total_deste_tit",C_TOUPPER).": </strong>
						</td>
						<td class='td_lctos_min' align='right' style='padding-top:07px;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td width='40%' align='right'  class='td_lctos_min' style='border:0px;padding-right:10px;font-size:12px;'>
										<strong>".getTText("reais_abrev",C_TOUPPER)." </strong>
									</td>
									<td width='60%' align='right'  class='td_lctos_min' style='border:0px;padding-right:05px;font-size:12px;'>
										<strong>".number_format((double) getValue($objRS,"vlr_conta"),2,',','.')."</strong>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<!-- LANÇAMENTOS -->
		<tr class='tr_table'>
			<td colspan='2' class='td_align'>
				<div class='box_header' style='border:0px;padding:0px;margin-bottom:4px;'>
				<strong>".getTText("lancamentos",C_TOUPPER)."</strong>
				</div>
			</td>
		</tr>
		<tr class='tr_table'>
			<td colspan='2' class='td_align'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<tr>
						<td width='10%' align='center' class='td_lctos' style='border:1px solid #C9C9C9;'>
							".getTText("cod_lcto_ordinario",C_TOUPPER)."
						</td>
						<td width='14%' align='center' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							".getTText("dt_lcto",C_TOUPPER)."
						</td>
						<td width='18%' align='right' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;padding-right:10px;'>
							".getTText("vlr_do_lcto",C_TOUPPER)."
						</td>
						<td width='18%' align='right' class='td_lctos' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;padding-right:10px;'>
							".getTText("vlr_juros",C_TOUPPER)."
						</td>
						<td width='18%' align='right' class='td_lctos' colspan='2' style='border-right:1px solid #C9C9C9;border-top:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;padding-right:10px;'>
							".getTText("vlr_desc",C_TOUPPER)."
						</td>
						<td width='18%' align='right' class='td_lctos' style='border:1px solid #C9C9C9;border-left:0px;padding-right:05px;'>
							".getTText("subtotal",C_TOUPPER)."
						</td>
					</tr>
					<!-- LISTA TODOS TITULOS CORRESPONDENTES -->";
					
					// Inicializa Variáveis utilizadas
					// para mostragem de TOTAL PAGO e SUBTOTAL
					$dblSubTotal  = 0;
					$dblTotalPago = 0;
						
					foreach($objResultLctosLista as $objRSLcto){
					
					$strStreamBody .= "
					<tr>
						<td width='10%' align='left' class='td_lctos_min' style='padding-left:8px;border:1px solid #C9C9C9;border-top:0px;'>
							&bull;&nbsp;".getValue($objRSLcto,"cod_lcto_ordinario")."
						</td>
						<td width='14%' align='center' class='td_lctos_min' style='border-right:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							".((getValue($objRSLcto,"dt_lcto") == "") ? " - " : dDate(CFG_LANG,getValue($objRSLcto,"dt_lcto"),false))."
						</td>
						<td width='18%' align='right' class='td_lctos_min' style='border-right:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' width='40%' class='td_lctos_min' style='padding-left:10px;'>
										".((getValue($objRSLcto,"vlr_lcto") == "") ? "" : getTText("reais_abrev",C_TOUPPER))."
									</td>
									<td align='right' width='60%' class='td_lctos_min' style='padding-right:10px;'>
										".((getValue($objRSLcto,"vlr_lcto") == "") ? " - " : number_format((double) getValue($objRSLcto,"vlr_lcto"),2,',','.'))."
									</td>
								</tr>
							</table>
						</td>
						<td width='18%' align='right' class='td_lctos_min' style='border-right:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' width='40%' class='td_lctos_min' style='padding-left:10px;'>
										".((getValue($objRSLcto,"vlr_juros") == "") ? "" : getTText("reais_abrev",C_TOUPPER))."
									</td>
									<td align='right' width='60%' class='td_lctos_min' style='padding-right:10px;'>
										".((getValue($objRSLcto,"vlr_juros") == "") ? " - " : number_format((double) getValue($objRSLcto,"vlr_juros"),2,',','.'))."
									</td>
								</tr>
							</table>
						</td>
						<td width='18%' align='right' class='td_lctos_min' colspan='2' style='border-right:1px solid #C9C9C9;border-bottom:1px solid #C9C9C9;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' width='40%' class='td_lctos_min' style='padding-left:10px;'>
										".((getValue($objRSLcto,"vlr_desc") == "") ? "" : getTText("reais_abrev",C_TOUPPER))."
									</td>
									<td align='right' width='60%' class='td_lctos_min' style='padding-right:10px;'>
										".((getValue($objRSLcto,"vlr_desc") == "") ? " - " : number_format((double) getValue($objRSLcto,"vlr_desc"),2,',','.'))."
									</td>
								</tr>
							</table>
						</td>
						<td width='18%' align='right' class='td_lctos_min' style='border:1px solid #C9C9C9;border-left:0px;border-top:0px;'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='right' width='40%' class='td_lctos_min' style='padding-left:10px;'>
										".getTText("reais_abrev",C_TOUPPER)."
									</td>
									<td align='right' width='60%' class='td_lctos_min' style='padding-right:5px;'>
										".number_format((double) getValue($objRSLcto,"vlr_lcto") + getValue($objRSLcto,"vlr_juros") - getValue($objRSLcto,"vlr_desc"),2,',','.')."
									</td>
								</tr>
							</table>
						</td>
					</tr>";
					// Realiza somatória de TOTAL PAGO
					// TOTAL = SUB_TOTAL + TOTAL_ACUMULADO
					$dblTotalPago = $dblTotalPago + $dblSubTotal;
					} // foreach($objResultLctosLista ...
					
					$strStreamBody .= "
					<tr>
						<td colspan='6' class='td_lctos_min' align='right' style='border:0px;padding-top:5px;font-size:12px;'>
							<strong>".getTText("total_recebido",C_TOUPPER).": </strong>
						</td>
						<td class='td_lctos_min' align='right' >
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td width='40%' align='right'  class='td_lctos_min' style='border:0px;padding-top:5px;padding-right:10px;font-size:12px;'>
										<strong>".getTText("reais_abrev",C_TOUPPER)." </strong>
									</td>
									<td width='60%' align='right'  class='td_lctos_min' style='border:0px;padding-top:5px;padding-right:05px;font-size:12px;'>
										<strong>".number_format((double) $dblTotalPago,2,',','.')."</strong>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan='6' class='td_lctos_min' align='right' style='border:0px;font-size:12px;'>
							<strong>".getTText("total_restante",C_TOUPPER).": </strong>
						</td>
						<td class='td_lctos_min' align='right' >
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td width='40%' align='right'  class='td_lctos_min' style='border:0px;padding-right:10px;font-size:12px;'>
										<strong>".getTText("reais_abrev",C_TOUPPER)." </strong>
									</td>
									<td width='60%' align='right'  class='td_lctos_min' style='border:0px;padding-right:05px;font-size:12px;'>
										<strong>".number_format((double) getValue($objRS,"vlr_saldo"),2,',','.')."</strong>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
				</table>
			</td>
		</tr>
		<!-- LANÇAMENTOS -->
		
		<tr>
			<td colspan='2' align='left' valign='top'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width='40%' align='left' valign='top' class='td_cidade'>
							<div class='box_header' style='Padding:5px;margin-left:15px;border:1px dashed #ccc;font-size:10px;color:#555;'>
								<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO ARRENDAMENTO -->
								<strong>COLABORADORES:</strong><br />";
								for($auxCounter = 0; $auxCounter < count($arrPFS); $auxCounter++){
									if($arrPFS[$auxCounter] != ""){
										$strStreamBody .= "&bull;&nbsp;".$arrPFS[$auxCounter]."<br />";
									}
								}
								$strStreamBody .= "
								<!-- BLOCO QUE CONTÉM A DESCRIÇÃO DO ARRENDAMENTO -->
							</div>
						</td>
						<td width='60%'align='right' valign='top'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'> 
								<tr>
									<td width='100%'align='right' valign='top' class='td_cidade'>
										<div class='assinatura'>Para maior clareza, firmamos o presente recibo.</div>
									</td>
								</tr>
								<tr>
									<td width='100%'align='right' valign='top' class='td_cidade'>
										<div class='assinatura'>
											".getVarEntidade($objConn,"fin_recibo_cidade_assinatura").", "
											.strtoupper(translateDate(dDate(CFG_LANG,now(),false)).".
										</div>
									</td>
								</tr>
								<tr>
									<td colspan='2' align='right' class='td_data_atual'>
										<div class='assinatura'>
											".getTText("linha_ass",C_TOUPPER)."
										</div>
									</td>
								</tr>
								<tr>
									<td colspan='2' align='right' class='td_baixo_assinatura'>
										".getVarEntidade($objConn,"nome_fan")." ".getTText("cnpj",C_TOUPPER).": ".getVarEntidade($objConn,"cnpj")."
									</td>
								</tr>
								<tr>
									<td colspan='2' align='right' valign='top' class='td_baixo_assinatura'>
										".getVarEntidade($objConn,"lc_logradouro").", ".getVarEntidade($objConn,"lc_num")." - ".getVarEntidade($objConn,"lc_bairro")." - ".getVarEntidade($objConn,"lc_cidade")." - ".getTText("fone",C_TOUPPER).getVarEntidade($objConn,"fone"))."
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<!--table cellspacing='0' cellpadding='0' border='0' style='' width='100%' class='table_footer'>	
		<tr>
			<td colspan='2' align='left' class='table_footer_conteudo'>
				".getVarEntidade($objConn,"razao_social")." - ".getVarEntidade($objConn,"lc_logradouro").", ".getVarEntidade($objConn,"lc_num")." - ".getVarEntidade($objConn,"lc_bairro")." - ".getVarEntidade($objConn,"lc_cidade")." - ".getTText("fone",C_TOUPPER).getVarEntidade($objConn,"fone")."
			</td>
		</tr>
	</table-->
	<table cellspacing='0' cellpadding='0' border='0' width='100%' class='table_operador'>
		<tr>
			<td align='left'><span class='comment_peq'  style='color:#CCC;font-size:09px;'>".getTText("operador",C_NONE).getsession(CFG_SYSTEM_NAME."_id_usuario")."</span></td>
			<td align='right'><span class='comment_peq' style='color:#CCC;font-size:09px;'>".dDate(CFG_LANG,now(),true)."</span></td>
		</tr>
	</table>";
	
if($bool2Paginas){ $strStreamBody = $strStreamBody."<div style='margin-top:15px;border-top:1px dashed #444;'>&nbsp;</div>".$strStreamBody; }	

$strStreamFile .= $strStreamBody."
</body>
</html>";
	
	// Prefixo FILE
	$strPrefixFile = date("Y").date("m").date("d").date("H").date("i").date("s");
	
	// Feito o Stream do Arquivo, guarda-o em um html
	$strFileNew = "../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/recibo/recibo_".$strPrefixFile."_".$intCodTitulo.".html";
	file_put_contents($strFileNew,$strStreamFile);
	
	// Caso o arquivo tenha sido gravado corretamente, 
	// então grava no banco de dados os Dados referentes 
	// a este Recibo
	if(file_exists($strFileNew)){
		try{
			$strSQL = "
				INSERT INTO fin_recibo(
					  cod_conta_pagar_receber
					, arquivo
					, sacado
					, vlr_total
					, vlr_total_juros
					, vlr_total_desc
					, vlr_saldo
					, num_impressoes
					, sys_usr_ins
					, sys_dtt_ins
					, sys_dtt_ult_print)
				VALUES(
					   ".$intCodTitulo."
					, '".prepStr($strFileNew)."'
					, '".prepStr($strNomeSacado)."'
					,  ".getValue($objRSLctoTotal,"total")."
					,  ".getValue($objRSLctoTotal,"total_juros")."
					,  ".getValue($objRSLctoTotal,"total_desc")."
					,  ".getValue($objRS,"vlr_saldo")."
					,   1
					, '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
					, CURRENT_TIMESTAMP
					, CURRENT_TIMESTAMP
					)
					";
			//die($strSQL);
					
			$objResult = $objConn->query($strSQL);
		} catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	
	// Faz redirect para o Recibo recém inserido
	redirect($strFileNew);
?>