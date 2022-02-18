<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// INI: INCLUDE requests ORDIN�RIOS -------------------------------------------------------------------------------------
/*
 Por defini��o esses s�o os par�metros que a p�gina anterior de prepara��o (execaslw.php) manda para os executores.
 Cada executor pode utilizar os par�metros que achar necess�rio, mas por defini��o queremos que todos fa�am os
 requests de todos os par�metros enviados, como no caso abaixo:
 Vari�veis e Carga:
	 -----------------------------------------------------------------------------
	 vari�vel          | "alimenta��o"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora in�cio do relat�rio
	 $intRelCod		   | C�digo do relat�rioRodap� do relat�rio
	 $strRelASL		   | ASL - Conulta com par�metros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com par�metros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/T�tulo do relat�rio
	 $strRelDesc	   | Descri��oo do relat�rio	
	 $strRelHead	   | Cabe�alho do relat�rio
	 $strRelFoot	   | Rodap� do relat�rio		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralat�rios - sem o nome da tabela do campo que ser� retornado
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunRequest.php");
// FIM: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------


// INI: INCLUDE funcionalideds B�SICAS ---------------------------------------------------------------------------------
/* Fun��es
	 filtraAlias($prValue)
	 ShowDebugConsuta($prA,$prB)
	 ShowCR("CABECALHO/RODAPE",str)
  A��es:
  	 SEGURAN�A: Faz verifica��o se existe usu�rio logado no sistema
  Vari�veis e Carga:
	 -----------------------------------------------------------------------------
	 vari�vel          | "alimenta��o"
	 -----------------------------------------------------------------------------
	 $strDIR           | Pega o diretporio corrente (usado na exporta��o) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds B�SICAS ---------------------------------------------------------------------------------

function convertem($term, $tp) { 
	if ($tp == "1") $palavra = strtr(strtoupper($term),"������������������������������","������������������������������"); 
	elseif ($tp == "0") $palavra = strtr(strtolower($term),"������������������������������","������������������������������"); 
	return $palavra; 
}

$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
$intCodNFDeb = request("var_chavereg");

//Recebe o c�digo do produto. Deixei apenas o par�metro no ASLW para evitar que o SQL deste relat�rio seja alterado indevidamente.
$intCodProd = str_replace('\'','',$strRelSQL);
if ($intCodProd == "") $intCodProd = request("var_cod_prod");
//die($intCodProd);

$intPagina   = request("var_pagina");

$intTamanho = 50;
if ($intPagina == "") $intPagina = 1;

/*Se n�o veio nada no c�digo da NF Deb ent�o � para gerar para v�rios porque 
  vai estar sendo executado via relat�rios ASL. Logo, deve paginar.*/
$bolPaginar = ($intCodNFDeb == "");

$objConn = abreDBConn(CFG_DB);

try{
    /*Na busca para gera��o em lote, filtra pelos t�tulos com a situa��o <> 'cancelado'. */
	$offset = "";
	$strSQL = "";
	$strSQL = "SELECT 
				  fin_nota_debito.cod_nota_debito,
				  fin_nota_debito.codigo,
				  fin_nota_debito.tipo,
				  fin_nota_debito.razao_social,
				  fin_nota_debito.cnpj_cpf,
				  fin_nota_debito.cod_conta_pagar_receber,
				  fin_nota_debito.dt_vcto,
				  fin_nota_debito.dt_emissao,
				  fin_nota_debito.descricao_despesas,
				  fin_nota_debito.end_cep,
				  fin_nota_debito.end_logradouro,
				  fin_nota_debito.end_numero,
				  fin_nota_debito.end_complemento,
				  fin_nota_debito.end_bairro,
				  fin_nota_debito.end_cidade,
				  fin_nota_debito.end_estado,
				  fin_nota_debito.end_pais,
				  fin_nota_debito.vlr_total,
				  fin_nota_debito.obs
			  FROM fin_nota_debito ";
	if ($intCodNFDeb!= "") {
		$strSQL .= "WHERE fin_nota_debito.cod_nota_debito = ".$intCodNFDeb ;
	}elseif ($intCodProd != "") {
		$strSQL .= " LEFT JOIN fin_conta_pagar_receber ON (fin_nota_debito.cod_conta_pagar_receber = fin_conta_pagar_receber.cod_conta_pagar_receber)
                     LEFT JOIN prd_pedido ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido)
					 WHERE prd_pedido.it_cod_produto = ". (string) $intCodProd . " AND fin_conta_pagar_receber.situacao <> 'cancelado' " ;
        $offset = " OFFSET ((" .$intPagina. " - 1) * ". $intTamanho .") LIMIT " . $intTamanho;					 
	}else $strSQL .= "WHERE 0 = 1 ";//Para n�o trazer nada na consulta se n�o for passado par�metro.
	
	$strSQL .= " ORDER BY fin_nota_debito.razao_social, fin_nota_debito.cod_nota_debito " . $offset  . ";"; 
	
	//die($strSQL);	
		
	$objResult = $objConn->query($strSQL);

}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 
	echo(" <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" type=\"text/css\">
	<link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
	<script type='text/javascript' src='../_scripts/tablesort.js'></script>");
?>
<style type="text/css">

table.bordasimples {border-collapse: collapse;}
table.bordasimples tr td {border:1px solid #000000;}
.folha { page-break-after: always; }

.tdicon{
		text-align:center;
		font-size:11px;
		font:bold;
		width:25%;		
}

img{
	border:none;
}
</style>
</head>
<body style="margin:10px 0px 10px 0px;">
<?php
$intCont = 0;
$intCodNFDeb_Old = "";
foreach($objResult as $objRS) {
  $strEmpresaRazaoSocial = getVarEntidade($objConn,"razao_social");
  $strEmprEnderCompleto = getVarEntidade($objConn,"endereco_completo");
  $strEmprCNPJ = getVarEntidade($objConn,"cnpj");
  $strEmprInscEst = getVarEntidade($objConn,"insc_est");  
  $strClienteInscEst = "";

	if(getValue($objRS,"tipo") == "cad_pj"){
		// SQL para buscar dados da pj
		try{
			$strSQL = "SELECT insc_est						  
					  FROM cad_pj
					  WHERE cod_pj = '".getValue($objRS,"codigo")."';";
			//echo($strSQL);
			$objResult2 = $objConn->query($strSQL); // execu��o da query	
		}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
		}  
	}
	foreach($objResult2 as $objRS2){
		$strClienteInscEst = getValue($objRS2,"insc_est");
	}
		
	//Incrementa aqui porque � usada como controle para colocar o script de auto-print 
	//na p�gina ou para exibir a mensagem de que n�o tem NF a ser feita 
	$intCont++;
		
	//Para quebra de p�gina se s�o duas ou mais folhas
	if ($intCont > 1) echo "<div class='folha'>&nbsp;</div>";

?>
<table width="100%" border="0" bgcolor="#FFFFFF">
	<tr>
		<td>
			<table width="100%" border="0">
				<tr>
					<td colspan="2">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td width="300">
									<?php $logo = getVarEntidade($objConn,"logotipo_empresa"); ?>
									<img width="300" align="center" src="<?php echo $logo; ?>"><br><br>
								</td>
								<td width="99%" style="padding-left:20px;padding-bottom:10px;text-align:left;font-size:10px;vertical-align:bottom;"><?php echo preg_replace("/(\\r)?\\n/i", "<br/>", $strEmprEnderCompleto);?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height="25"><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td colspan="2"><div align="center"> <font size="3"> <b>NOTA DE D�BITO <b></font></div></td>
					</tr>
				<tr>
					<td width="62%">&nbsp;</td>
					<td width="38%"><div align="left"> <font size="2">CNPJ..................: <?php echo($strEmprCNPJ); ?><br>
						Insc. Estadual.....: <?php echo($strEmprInscEst); ?> <br>
						Data da Emiss�o.: <?php echo(dDate("PTB", getValue($objRS,"dt_emissao"), false)); ?></font><br>
					  </div></td>
				</tr>
			</table>
			<table width="100%" border="0" class="bordasimples">
				<thead>
					<tr style="">
						<th width="33%" style="border:1px solid black;border-right:none;"><font size="2">Nota de D�bito N�</font></th>
						<th width="34%" style="border:1px solid black;border-right:none;border-left:none"><font size="2">Vencimento</font></th>
						<th width="33%" style="border:1px solid black;border-left:none"><font size="2">Valor em R$</font></th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td style="border-right:none"><font size="3"><b><?php echo getValue($objRS,"cod_nota_debito") ?></b></font></td>
						<td style="border-right:none; border-left:none"><b><?php echo dDate("PTB", getValue($objRS,"dt_vcto"), false) ?></b></td>
						<td style="border-left:none"><font size="3"><b><?php echo number_format(getValue($objRS,"vlr_total"), 2, ',', '.'); ?></b></font></td>
					</tr>
				</tbody>
			</table>
			<br>
			<br>
			<table width="100%" border="1" style="border-collapse:collapse">
				<tr>
					<td>
						<table width="100%" border="0">
							<tr>
								<td width="11%"><font size="2">Cliente:</font></td>
								<td width="89%"><font size="2"><b><?php echo getValue($objRS,"razao_social") ?></b></font></td>
							</tr>
							<tr>
								<td><font size="2">Endere�o:</font></td>
								<td><font size="2"><b><?php echo(getValue($objRS,"end_logradouro").", ".getValue($objRS,"end_numero")." - ".getValue($objRS,"end_complemento"));?></b></font></td>
							</tr>
							<tr>
							  <td><font size="2">Cidade:</font></td>
							  <td><font size="2"><b><?php echo(strtoupper(getValue($objRS,"end_cidade"))); ?></b></font></td>
							</tr>
							<tr>
							  <td><font size="2">Estado:</font></td>
							  <td><font size="2"><b><?php echo(strtoupper(getValue($objRS,"end_estado")));?></b></font></td>
							</tr>
							<tr>
							  <td><font size="2">CNPJ/CPF:</font></td>
							  <td><font size="2"><b><?php echo getValue($objRS,"cnpj_cpf") ?></b></font></td>
							</tr>
							<tr>
							  <td><font size="2">I.E:</font></td>
							  <td><font size="2"><b><?php echo($strClienteInscEst); ?></b></font></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br>
			<table width="100%" border="0" class="bordasimples">
				<tr>
					<td width="21%" height="65" valign="top" style="border-right:none"><font size="2">Valor por Extenso. :</font></td>
	
<?php	
	//recebe o valor
	$valor = getValue($objRS,"vlr_total") ;
	//recebe o valor escrito
	$var_valor_extenso = valorporextenso($valor);
	//imprime o valor em Maisculas
?>
	
					<td width="79%" align="justify" style="border-left:none"><font face="Lucida Console" size="2"><b> <?php echo "( ".convertem($var_valor_extenso, 1)." )"; ?>
<?php 
		$palavra = strlen($var_valor_extenso);
		
		while ($palavra < 184) {
			echo " ";
			$palavra++;
			if ($palavra < 184){
				echo "#";
				$palavra++;
			}	
		}
 ?>
					</b></font></td>
				</tr>
			</table>
			<font size="2">Devem � <?php  echo $strEmpresaRazaoSocial ?>, a import�ncia correspondente �s despesas abaixo: </font><br>
			<br>
			<table width="100%"  border="1" style="border-collapse:collapse">
				<tr>
					<td>
						<table width="100%" border="0">
							<tr><td colspan="5" ><font size="2">Descri��o das Despesas</font></td></tr>        
							<tr><td colspan="5"><font size="2"><b>&nbsp;</b></font></td></tr>        
							<tr><td colspan="5"><hr></td></tr>
							<tr>
								<td width="100%" height="150" valign="top"><?php echo getValue($objRS,"descricao_despesas") ?> </font></td>
							</tr>
							<tr><td colspan="5"><hr></td></tr>
							<tr>
							  <td width="100%" valign="top" colspan="1" align="right"><font size="1"><b>R$ <?php echo number_format(getValue($objRS,"vlr_total"), 2, ',', '.'); ?> </b></font></td>
							</tr>
						  </table>
					</td>
				</tr>
			</table>
			<br>
			<br>
			<br>
			<font size="2"><? echo(getValue($objRS,"obs")); ?></font>
		</td>
	</tr>
</table>
<?php
}
$objResult->closeCursor();

if ($intCont > 0) {
?>
	<script>
		/*Como est� abrindo em v�rias janelas para paginar, algumas vezes d� erro de acesso negado nessa parte
		  Como as chamadas de windo.open j� tem dimensões n�o estou redimensionando mais*/
		window.onload = function (){
			//self.resizeTo(800,700);
			window.print(); 
		}
	</script>
<?php
	if ($bolPaginar) {
		$intPagina++;
?>
<form name="formeditor" method="post" action="STnotadebitolote.php" target="_blank">
	<input type="hidden" name="var_cod_prod" value="<?php echo $intCodProd; ?>" />
	<input type="hidden" name="var_pagina" value="<?php echo $intPagina; ?>" />
	
	<!--<input type="hidden" name="var_sql" value="<?php //echo $strRelSQL; ?>" />//-->
</form>
<script type="text/javascript" language="javascript">
	document.formeditor.submit();
</script>
<?php
	}
}else mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("sem_notas_debito_para_o_produto",C_NONE),"","info",1);

?>
</body>
</html>
<?php
	$objConn = NULL;
?>
