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
	
	// VERIFICAÇÃO DE ACESSO
	$strPopulate = "yes";
	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");	
	
	// CODIGO DO LANÇAMENTO
	$intCodRecibo = request("var_chavereg");
	
	// TRATAMENTO, CASO CODIGO LCTO NULO
	if($intCodRecibo == ""){
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
		$strSQL ="SELECT fra.dt_rec
					    ,fra.codigo
                        ,fra.nome
     					,fra.tipo
						,COALESCE(fra.num_documento,'') AS num_documento
						,fra.historico_rec
						,fra.vlr_pago
						,fra.dt_emissao
						,COALESCE(fra.obs,'') AS obs
                   FROM fin_recibo_avulso fra
                  WHERE fra.cod_recibo_avulso = ".$intCodRecibo;
		$objResult = $objConn->query($strSQL);		
	} catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	// FETCH DOS DADOS
	$objRS = $objResult->fetch();
	
	// RECEBE VALORES PARA MONTAR RECIBO
	$strRecebidoDe = getValue($objRS,"nome"         );	
	$floatVlrPago  = getValue($objRS,"vlr_pago"     );	
	$strReferenteA = getValue($objRS,"historico_rec");
	$dtRecebimento = getValue($objRS,"dt_rec"       );
	$strCNPJCPF    = getValue($objRS,"num_documento");
	$strTipoEnt    = getValue($objRS,"tipo"         );
	$dtEmissao     = getValue($objRS,"dt_emissao"   );
	$strObs        = getValue($objRS,"obs"          );
	if ($dtEmissao == ""){
		$dtEmissao = now();
	}
	//Inicialização
	$strDoc = "";
	if ($strCNPJCPF <> ""){
		if ((strcmp(strtolower($strTipoEnt),"cad_pf")== 0)) {
			/*PF*/
			$strDoc = "CPF:";	
		}else{
			/*PJ OU FORNEC*/
			$strDoc = "CNPJ:";			
		}
		$strDoc = " (".$strDoc." ".$strCNPJCPF.")";	
	}
	
	$objResult->closeCursor();	
	
	/*Busca o nome do cliente para o Logo*/
	$strCliName = getsession(CFG_SYSTEM_NAME."_db_name"); 
	$strCliName = str_replace(CFG_SYSTEM_NAME,"",$strCliName);	

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
			height:75%;
		}
		.box_hr_num_recibo{
			width:90%;
			height:10px;
			border:0px solid #C9C9C9;
			padding-left:5px;
			padding-right:5px;
			padding-top:0px;
			padding-bottom:5px;
			vertical-align:top;
			margin-top:0px;
			margin-right:7px;
			text-align:justify;	
			height:25%;		
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
<tr class="tr_header" rowspan="2">        
            <td width="75%" align="left" class="td_header">        
				<img src="../img/LogoMarca<?php echo(strtoupper($strCliName));?>_ReciboAvulso.gif" vspace="5" border="0" style="padding-left:5px;">
            </td>
            <td width="25%" align="center" class="td_header">                                 
				<div class="box_hr_num_recibo">
                	<?php echo getTText("recibo",C_TOUPPER)." N°:".$intCodRecibo;?>                                  
                </div>    
                <div class="box_hr">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
						<tr>
							<td align="left" class="table_box_hr_conteudo">
								<strong><?php echo(getTText("reais_abrev",C_TOUPPER));?></strong>
							</td>
							<td align="right" class="table_box_hr_conteudo">
								<strong><?php echo(number_format((double) getValue($objRS,"vlr_pago"),2,',','.'));?></strong>
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
					  <?php echo "<p>". 
					                 getTText("recibo_cont_lcto_um",C_TOUPPER).                                      /*RECEBEMOS DE                                        */
							 	     "<strong>".strtoupper($strRecebidoDe). $strDoc. "</strong>".                    /*FULANO DE TAL, CPF(00000000000) - CNPJ/CPF opdcional*/
							 	     getTText("recibo_cont_lcto_dois",C_TOUPPER).                                    /*O VALOR DE                                          */
								     "<strong>".number_format((double) $floatVlrPago,2,',','.').                     /*R$ 1.00                                             */
								     " (".strtoupper(valorPorExtenso($floatVlrPago))." )</strong>".                  /*HUM REAL                                            */
								     getTText("recibo_cont_lcto_sete",C_TOUPPER).                                    /*LANÇADO NO DIA                                      */
								     dDate(CFG_LANG,$dtRecebimento,false).", ".                                      /*25/12/2013                                          */
								     getTText("recibo_cont_lcto_tres",C_TOUPPER).                                    /*REFERENTE A                                         */
								     strtoupper($strReferenteA)."." .                                                /*COMPRA DE CALENDÁRIO.                               */
								 "</p>";
							//BLOCO QUE CONTÉM A OBSERVAÇÃO 
							if($strObs <> ""){ 
								echo "<p>" . getTText("obs",C_TOUPPER) . ": <strong>".strtoupper($strObs) . " .</p>"; 								 
							}
					?>
	          </div>
			</td>
		</tr> 
		<tr><td colspan="2" align="right" class="td_cidade"><div class="assinatura"><?php echo getVarEntidade($objConn,"fin_recibo_cidade_assinatura").",&nbsp;".strtoupper(translateDate(dDate(CFG_LANG,$dtEmissao,false)));?></div></td></tr>
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
<?php 	$objConn = NULL; ?>