<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente
	$intCodDado   = request("var_chavereg"); // CODIGO DO CHEQUE
	$boolNominal  = request("var_nominal");  // NOMINAL - MARCAÇÃO
	$boolCruzado  = request("var_cruzado");  // CRUZADO - MARCAÇÃO
	$intQtdeChar  = 0;
	$intRangeChar = 0;
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_cod_miss","err_impr_cheque",getTText("cheque_cod_null",C_NONE),'','erro','1');
		die();
	}

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// Busca os dados do CHEQUE para exibição em tela
	try{
		$strSQL = "
			SELECT 
				  fin_cheques.cod_cheques 
				, fin_cheques.idcheque
				, fin_cheques.nrocheque
				, fin_cheques.valorcheque
				, fin_cheques.datacheque
				, fin_cheques.referencia
				, fin_cheques.cedente
				, fin_cheques.qtde_impresso
				, fin_cheques.cidade_assinatura
				, fin_banco.nome
				, fin_banco.modelo_cheque_img
			FROM  fin_cheques
			INNER JOIN fin_banco ON (fin_banco.num_banco = fin_cheques.idbanco)
			WHERE fin_cheques.cod_cheques = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// Fetch dos dados localizados
	$objRS = $objResult->fetch();
	
	// Update do CHEQUE MARCANDO O NÚMERO de Impressões em UM
	try{
		$strSQL = "UPDATE fin_cheques SET qtde_impresso = qtde_impresso + 1 WHERE cod_cheques = ".$intCodDado;
		$objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - ".getTText("cheque",C_NONE)." ".getTText("hsbc",C_NONE));?></title>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
	<style type="text/css">
		.serif{
			font-family:"Courier New",Verdana,Arial,Helvetica,sans-serif;
			font-size:12px;
			font-weight:bold; 
		}
		
		.sans{
			font-family:Tahoma,Verdana,Arial,Helvetica,sans-serif;
			font-size:12px;
			font-weight:bold; 
		}
		
		.valor{ 
			padding-top:20px;
			padding-right:30px;
			vertical-align:top;
		}
		
		.extenso{
			line-height:20px;
			text-align:left;
			padding-top:13px;
			padding-left:20px;
			padding-right:20px;
		}
		
		.cedente{
			padding-left:30px;
			text-align:left;
			vertical-align:bottom;
		}
		
		.cidade{
			padding-left:15px;
			text-align:left;
			vertical-align:bottom;
		}
		
		.mesano{
			text-align:left;
			vertical-align:bottom;
		}
	</style>
</head>
<body style="width:100%;height:100%;background:#FFF;margin-top:0px;margin-left:0px;" onLoad="window.resizeTo(700,380);window.print();">
	<!-- TABELA COM DIMENSÕES DE UM CHEQUE DO BANRISUL - LARGURA:159(4cm[CANHOTO])+661(17.5cm) ALTURA:287(7.6cm) -->
	<table cellpadding="0" cellspacing="0" border="0" width="661" height="285">
	<tr>
		<td width="0">
			
		</td>
		<td width="633" style="background-image:url(../img/<?php echo(getValue($objRS,"modelo_cheque_img"));?>);">
			<?php if($boolCruzado == "1"){?>
			<div style="position:absolute;z-index:9999;width:300px;height:150px">
				<img src="../img/cheque_marcacruzado.gif" border="0" />
			</div>
			<?php }?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" style="vertical-align:top;text-align:left;">
				<tr height="40"><td width="100%" class="serif valor" align="right"><?php echo("#".number_format((double) getValue($objRS,"valorcheque"),2,',','.')."#");?></td></tr>
				
				<!-- VALOR POR EXTENSO -->
				<tr height="27">
					<td class="serif extenso">
						<span style="width:65px;"></span>
						<?php 
							//echo(strlen("( ".strtoupper(valorPorExtenso(getValue($objRS,"valorcheque")))." )  "));
							echo("( ".strtoupper(valorPorExtenso(getValue($objRS,"valorcheque")))." )  ");
							$intQtdeChar  = strlen("( ".strtoupper(valorPorExtenso(getValue($objRS,"valorcheque")))." )  ");
							$intRangeChar = (($intQtdeChar >  0) && ($intQtdeChar <=  30)) ? ( 82 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 30) && ($intQtdeChar <=  40)) ? ( 90 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 40) && ($intQtdeChar <=  50)) ? ( 95 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 50) && ($intQtdeChar <=  60)) ? (102 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 60) && ($intQtdeChar <=  70)) ? (107 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 70) && ($intQtdeChar <=  80)) ? (112 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 80) && ($intQtdeChar <=  90)) ? (117 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = (($intQtdeChar > 90) && ($intQtdeChar <= 105)) ? (120 - $intQtdeChar) : $intRangeChar;
							$intRangeChar = ($intQtdeChar  > 105) ? (132 - $intQtdeChar) : $intRangeChar;
							for($i = 0; $i < $intRangeChar; $i++){ echo("# "); }
						?>
					</td>
				</tr>
				<!-- VALOR POR EXTENSO -->
				
				<!-- LINHA CEDENTE -->
				<tr height="22"><td class="serif cedente"><?php echo(($boolNominal == "1") ? getValue($objRS,"cedente") : "");?></td></tr>
				<!-- LINHA CEDENTE -->
				
				<!-- LINHA CIDADE -->
				<tr height="20" >
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
					<tr>
						<td width="336">&nbsp;</td>
						<td width="90" class="sans cidade"><?php echo(getValue($objRS,"cidade_assinatura"));?></td>
						<td width="40"  class="sans" valign="bottom">
							<?php 
								$dtAssinatura = dDate(CFG_LANG,getValue($objRS,"datacheque"),false);
								$dtAssinatura = explode("/",$dtAssinatura);
								echo("&nbsp;&nbsp;&nbsp;".$dtAssinatura[0]);
							?>						</td>
						<td width="131" class="sans mesano">
							<?php
								echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
								echo(ucwords(getMesExtensoFromMes($dtAssinatura[1])));
							?>						</td>
						<td width="63" class="sans mesano">
							<?php //echo(substr($dtAssinatura[2],2,4));?>
							<?php echo($dtAssinatura[2]);?>						</td>
					</tr>
					</table>
					</td>
				</tr>
				<!-- LINHA CIDADE -->
				
				<!-- RODAPÉ CHEQUE [ASSINATURA/DADOS BANCO] -->
				<tr height="39" ><td>&nbsp;</td></tr>
				<tr height="94%"><td>&nbsp;</td></tr>
				<!-- RODAPÉ CHEQUE [ASSINATURA/DADOS BANCO] -->
			</table>
		</td>
	</tr>
	</table>
</body>
</html>
