<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
	
	//Inicia conexão com banco
	$objConn = abreDBConn(CFG_DB);
	
	//Recebe cod_pf
	$intCodPF = request("var_chavereg");
	
	//Consulta dados da PF conforme o cod_pf enviado
	try {
		$strSQL = "SELECT 
							cad_pf.nome, 
							cad_pf.rg, 
							cad_pf.cpf, 
							cad_pf.foto,
							cad_pj.cnpj, 
							relac_pj_pf.funcao,
							sd_credencial.pf_matricula,
							sd_credencial.cod_credencial,
							sd_credencial.dt_validade
						FROM 
							relac_pj_pf,
							cad_pj,
							cad_pf
						LEFT JOIN sd_credencial ON cad_pf.cod_pf = sd_credencial.cod_pf
						WHERE 
							cad_pf.cod_pf = ". $intCodPF ."
						AND 
							relac_pj_pf.cod_pf = cad_pf.cod_pf
						AND
							relac_pj_pf.cod_pj = cad_pj.cod_pj";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//Cria array associativo para o sql gerado acima
	$objRS = $objResult->fetch();
	
	//Associa valores da consulta com variáveis a serem usadas no modelo html
	$TAG_FOTO = getValue($objRS,"foto");
	//Sem tempo agora, inserir tratamento para não mostrar o último sobrenome caso passe de 26 caracteres e
	//caracter anterior for igual a espaço. também nao mostrar caso antes desse ultimo sobrenome for um de ou dos
	/* FORMATAÇÃO EM DATAS PARA */
	$dtValTemp 			= explode("/",dDate(CFG_LANG,getValue($objRS,"dt_validade"),false));
	$TAG_VAL_MES_CARD   = $dtValTemp[1];
	$TAG_VAL_DIA_CARD   = $dtValTemp[0];
	$TAG_VAL_ANO_CARD   = $dtValTemp[2];
	/* FIM FORMATAÇÃO EM DATAS PARA EXIBIÇÃO NA CREDENCIAL */ 
	$TAG_NOME 			= substr(getValue($objRS,"nome"),0,26);
	$TAG_RG 			= getValue($objRS,"rg");
	$TAG_CPF 			= getValue($objRS,"cpf");
	$TAG_CNPJ 			= getValue($objRS,"cnpj");
	$TAG_FUNCAO			= getValue($objRS,"funcao");
	$TAG_NUM_MATRICULA 	= getValue($objRS,"pf_matricula");
	$TAG_COD_CREDENCIAL = getValue($objRS,"cod_credencial");
	
	//Consulta código da empresa através do cod_pf e relação
	try {
		$strSQL = "SELECT 
							cad_pj.razao_social
						FROM
							cad_pj,
							relac_pj_pf
						WHERE
							relac_pj_pf.cod_pf = " . $intCodPF . "
						AND
							cad_pj.cod_pj = relac_pj_pf.cod_pj";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$TAG_RAZAO_SOCIAL = substr(getValue($objRS,"razao_social"),0,25).".";
	
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php echo (CFG_SYSTEM_TITLE) ?></title>
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<style media="print">.no_imp {display: none;}</style>
	</head>
<body style="padding: 0px 0px 0px 0px; background-color:#FFFFFF;" marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">
	<table align="left" valign="top" width="321" height="205" cellpadding="0" cellspacing="0" border="0" background="../img/layout_credencial_sfoto.jpg">
		<tr>
			<td width="218" align="center" valign="bottom">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="210" height="27" align="left" valign="bottom">&nbsp;</td>
					</tr>
					<tr>
						<td align="left" valign="bottom">
							<table cellpadding="2" cellspacing="0" border="0" style="display: inline;">
								<tr><td>&nbsp;</td></tr>
							</table>	
							<table cellpadding="4" cellspacing="" border="0" bgcolor="" style="display: inline;">
								<tr>
									<td width="213" height="115" align="left" valign="bottom" style="padding-left:10px;padding-bottom:17px;">
										<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td style="font-size:8px; padding-bottom:5px; padding-left:137px;" align="right"><?php echo($TAG_VAL_DIA_CARD."/".$TAG_VAL_MES_CARD."/<div style='font-size:16px;font-weight:bold;display:inline;'>".$TAG_VAL_ANO_CARD."</div>"); ?></td>
											</tr>
											<tr>
												<td style="font-size:9px;"><strong><?php echo ($TAG_NOME) ?></strong></td>
											</tr>
											<tr>
												<td style="font-size:8px;letter-spacing:1px;"><?php echo ($TAG_RAZAO_SOCIAL) ?></td>
											</tr>
											<!--<tr>
												<td>
													<table cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>MATRÍCULA nº <?php echo ($TAG_NUM_MATRICULA)?>
															</td>
															<td align="right"><?php echo("<div class=\"comment_peq\">&nbsp; - (".$TAG_COD_CREDENCIAL.")</div>") ?>
															</td>
														</tr>
													</table>
												<td>
											</tr>-->
											<tr>
												<td style="font-size:8px; letter-spacing:1px;">CNPJ: <?php echo ($TAG_CNPJ) ?></td>
											</tr>
											<tr>
												<td style="font-size:8px; letter-spacing:1px;">RG: <?php echo ($TAG_RG) ?></td>
											</tr>
											<tr>
												<td style="font-size:8px; letter-spacing:1px;">FUNÇÃO: <?php echo ($TAG_FUNCAO) ?></td>
											</tr>
										</table>
								  </td>			
								</tr>
							</table>
						</td>
					</tr>
				</table>
		  </td>
		    <td width="103" align="center" valign="top">
				<table border="0" cellpadding="3" cellspacing="0">
					<tr><td height="7"></td>
					</tr>
					<tr>
						<td width="80" height="103" align="left" valign="top" style="padding: 6px 3px 0px 0px;">
					<img src="../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/<?php echo ($TAG_FOTO)?>" width="84" height="110"/>
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle">
						<?php echo("<div style='font-size:8px;'>".$TAG_NUM_MATRICULA." <div class='comment_peq' style='display:inline;'> / ".$TAG_COD_CREDENCIAL."</div></div>");?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top"><?php echo("<div style='font-size:8px;'>Matrícula</div>");?></td>
					</tr>
				</table>
	 	  </td>
		</tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="221" align="center" style="padding-bottom:10px; padding-left:2px;">
							<?php echo("<div style='font-size:8px;'>".barCode39($TAG_CPF,true,"CPF")."</div>");?>
						</td>
				</table>
			</td>
		</tr> 
	</table>
</body>
</html>
<?php
	$objConn = NULL;
?>