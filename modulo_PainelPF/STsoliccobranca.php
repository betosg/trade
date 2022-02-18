<?php 
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	$strAmbiente = request("var_ambiente");
	
	// ABERTURA DE CONEXÃO COM BANCO
	if ($strAmbiente !=""){
		setsession(CFG_SYSTEM_NAME . "_db_name","tradeunion_".$strAmbiente);
		setsession(CFG_SYSTEM_NAME . "_emissao_avulsa","emissao_avulsa");
	}		
		$objConn   = abreDBConn(CFG_DB);
	
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	$intCodDado = getSession(CFG_SYSTEM_NAME."_pj_selec_codigo");
	
	$strCNPJ    = request("var_cnpj");
	
	if ($intCodDado != "") {
		$strSQL = " SELECT cod_cnae_n2, cnpj FROM cad_pj WHERE cod_pj = ".$intCodDado;
	}else{
		$strSQL = " SELECT cod_cnae_n2, cnpj, cod_pj FROM cad_pj WHERE cnpj = '" . $strCNPJ . "'";			
	}

	try{
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
		
	if($objResult->rowCount()>0){
		$objRS = $objResult->fetch();
		
		if ($intCodDado == ""){
			$intCodDado = getValue($objRS, "cod_pj");
			setSession(CFG_SYSTEM_NAME."_pj_selec_codigo",getValue($objRS, "cod_pj"));
		}
		
		$strCodAtividade = str_pad(getValue($objRS,"cod_cnae_n2"), 3, "0", STR_PAD_LEFT);
		
		$strCNPJ    = getValue($objRS, "cnpj");
		if ($strCodAtividade == "000") $strCodAtividade = "";
	}
	$objResult->closeCursor();
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	$strRepProduto = getVarEntidade($objConn, "guia_atraso_repetir_produto");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - Solicitação Guia Avulsa");?></title>
<style type="text/css">
	.span_manual{
		float:right;
		background-image:url(../img/icon_document_pdf.png);
		background-repeat:no-repeat;
		background-position:right;
		height:15px;
		padding-top: 2px;
		padding-right:20px;
		margin-right:5px;
		cursor:pointer;
		font-size:10px;
		color:#009900;
		font-weight:600;
	}
</style>
</head>
<body bgcolor="#F5F5F5"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
    	  <?php athBeginFloatingBox("720","none","<strong>Geração de Guia</strong>",CL_CORBAR_GLASS_1); ?>
		     <table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
                   <tr><td colspan="2" height="10">&nbsp;</td></tr>
                   <tr>
					<td align="center" width="100%" valign="top" style="padding:0px 80px 0px 80px;"> 
                          	<table cellpadding="4" cellspacing="0" border="0" width="100%">
                           		<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                                <tr>
		                            <td colspan="2" width="87%" align="left" valign="top" class="destaque_gde"><img src="../img/opcao_1.gif">&nbsp;<strong>GUIAS EXISTENTES</strong></td>
        	                    </tr>
                    	        <tr>
                                    <td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td>
                          		</tr>
                                <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
									<td colspan="2"><?php echo getTText("mensagem1",C_NONE); ?></td>
								</tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
									<td colspan="2">
										<table width="100%" height="150" border="0">
										<tr>
											<td>
											<iframe src="STincludetitulos.php?var_modo=compacto" width="100%" height="150" frameborder="0" style="border:1px solid #999999"></iframe>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_1)?>"><td colspan="2" height="5"></td></tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
									<td colspan="2"><img src="../img/icon_calc_juros.gif">&nbsp;<?php echo getTText("mensagem2",C_NONE); ?></td>
								</tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>"><td colspan="2" height="20"></td></tr>
                                <tr>
		                            <td colspan="2" align="left" valign="top" class="destaque_gde"><img src="../img/opcao_2.gif">&nbsp;<strong>OU GERE UMA GUIA NOVA</strong></td>
        	                    </tr>
                    	        <tr>
                                    <td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td>
                          		</tr>
                                <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
									<td colspan="2">
									<form id="formproduto" name="formproduto" method="post" action="STsolicguias.php">
									<strong><?php echo(getTText("produto",C_NONE)); ?>:</strong>&nbsp;
									<select name="var_chavereg" id="var_chavereg" style="width:180px;">
										<option value=""></option>
										<?php
										if ($strRepProduto == "N") {
											$strSQL = " SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor, t2.it_cod_produto
														FROM prd_produto t1
														LEFT JOIN prd_pedido t2 ON (t2.cod_pj = ".$intCodDado." AND t1.cod_produto = t2.it_cod_produto)
														WHERE t1.dtt_inativo IS NULL 
														AND t2.it_cod_produto IS NULL
														AND CURRENT_DATE BETWEEN t1.dt_ini_val_produto AND t1.dt_fim_val_produto 
														AND t1.visualizacao = 'publico' 
														AND ((t1.tipo <> 'homo' AND t1.tipo <> 'card') OR t1.tipo IS NULL) 
														ORDER BY 2 ";
										}
										else {
											$strSQL = " SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor
														FROM prd_produto t1
														WHERE t1.dtt_inativo IS NULL 
														AND CURRENT_DATE BETWEEN t1.dt_ini_val_produto AND t1.dt_fim_val_produto 
														AND t1.visualizacao = 'publico' 
														AND ((t1.tipo <> 'homo' AND t1.tipo <> 'card') OR t1.tipo IS NULL) 
														ORDER BY 2 ";
										}
										echo(montaCombo($objConn,$strSQL,"cod_produto","rotulo_valor",""));
										?>
									</select>&nbsp;<button onClick="javascript:document.formproduto.submit(); return false;">Gerar</button>
									</form>
									</td>
								</tr>
                          		<tr>
                               		<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php //echo getTText("campos_obrig",C_NONE); ?></td>
                          		</tr>
                          		<tr><td height="1" colspan="2" bgcolor="#FFFFFF"></td></tr>
                          		<tr>
                            		<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
                                        <table width="100%" height="20" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">                                
                                            <tr>
                                                <td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><!-- img src="../img/mensagem_info.gif" //--></td>
                                                <td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php //echo getTText("aviso_geracao_guia_recolhimento",C_NONE); ?></td>
                                                <td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
                                                    <!-- button onClick="cancelar(); return false;">Cancelar</button //-->
												</td>
                                            </tr>
                                        </table>
									</td>
                                </tr>
                            </table>
                  </td>
                  </tr>
              </table>
          <?php athEndFloatingBox(); ?>
		</td>
      </tr>	   
    </table>     	
</body>
</html>
<?php
	//}
	$objConn = NULL;
?>