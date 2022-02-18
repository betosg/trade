<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// INI: INCLUDE requests ORDINÁRIOS -------------------------------------------------------------------------------------
/*
 Por definição esses são os parâmetros que a página anterior de preparação (execaslw.php) manda para os executores.
 Cada executor pode utilizar os parâmetros que achar necessário, mas por definição queremos que todos façam os
 requests de todos os parâmetros enviados, como no caso abaixo:
 Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora início do relatório
	 $intRelCod		   | Código do relatórioRodapé do relatório
	 $strRelASL		   | ASL - Conulta com parâmetros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com parâmetros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/Título do relatório
	 $strRelDesc	   | Descriçãoo do relatório	
	 $strRelHead	   | Cabeçalho do relatório
	 $strRelFoot	   | Rodapé do relatório		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralatórios - sem o nome da tabela do campo que será retornado
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunRequest.php");
// FIM: INCLUDE requests ORDIÃ€RIOS -------------------------------------------------------------------------------------


// INI: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------
/* Funções
	 filtraAlias($prValue)
	 ShowDebugConsuta($prA,$prB)
	 ShowCR("CABECALHO/RODAPE",str)
  Ações:
  	 SEGURANÇA: Faz verificação se existe usuário logado no sistema
  Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $strDIR           | Pega o diretporio corrente (usado na exportação) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------
//echo(request("conta_combo(29)"));
function convertem($term, $tp) { 
	if ($tp == "1") $palavra = strtr(strtoupper($term),"àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß"); 
	elseif ($tp == "0") $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ"); 
	return $palavra; 
}

$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
$objConn = abreDBConn(CFG_DB);

//echo $strRelInpts;
 $arrInputs = explode(";",$strRelInpts);
//echo "<br>".$arrInputs[2];
$idContaBanco = explode(":",$arrInputs[2]);

$dblTotal = 0;

$strSQLSaldo = "SELECT cod_conta, nome, vlr_saldo, dtt_inativo  FROM fin_conta WHERE cod_conta = ". trim($idContaBanco[1]) ." Limit 1";
try{
	$objResult = $objConn->query($strSQLSaldo);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}
foreach ($objResult as $objRS){
	$dblSaldo = getValue($objRS,"vlr_saldo");
}


try{  		
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}


  foreach($objResult as $objRS) {
		$strDados .= "	<tr> ";
		$strDados .= "		<td><font size='2'>".(getValue($objRS,"beneficiario"))."</font></td> ";
		$strDados .= "		<td align='right'><font size='2'>".FloatToMoeda(getValue($objRS,"vlr_pagar"))."</font></td> ";
		$strDados .= "		<td style='padding-left:8px' ><font size='2'>".getValue($objRS,"solicitante")."</font></td> ";
		$strDados .= "	</tr> ";
		$dblTotal += getValue($objRS,"vlr_pagar");
  }//fim do foreach




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


  $strEmpresaRazaoSocial = getVarEntidade($objConn,"razao_social");
  $strEmprEnderCompleto = getVarEntidade($objConn,"endereco_completo");  
  $strEmprTelefone = getVarEntidade($objConn,"telefone_sindicato");
  $strLogotipo     = getVarEntidade($objConn,"logotipo_empresa");  

?>
<table width="100%" border="0" bgcolor="#FFFFFF">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>								
								<td width="99%" style="padding-left:20px;padding-bottom:10px;text-align:left;vertical-align:top;">
									<font size="2"><?php echo($strEmpresaRazaoSocial);?><br>
									<?php echo preg_replace("/(\\r)?\\n/i", "<br/>", $strEmprEnderCompleto);?><br>	
                                    <?php echo($strEmprTelefone);?></font>
                                </td>
                                <td>									
									<img align="center" src="<?php echo $strLogotipo; ?>"><br><br>
								</td>
							</tr>
			</table>
		</td>
	</tr>
	<tr height="25"><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2"><div align="LEFT"> <font size="3"><strong>AUTORIZAÇÃO DE PAGAMENTOS</strong></font></div></td>
			</tr>				
			
			<table width="100%" border="0" class="bordasimples" cellspacing="0">
				<thead>
					<tr style="">
						<th width="33%"><font size="2">Data</font></th>
						<th width="34%"><font size="2">Setor solicitante</font></th>
						<th width="33%"><font size="2">Responsável</font></th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td style="border:1px solid black;"><font size="2"><b><?php echo dDate("PTB", now(), false) ?></b></font></td>
						<td style="border:1px solid black;"><b></b></td>
						<td style="border:1px solid black;"><font size="2"><b></b></font></td>
					</tr>
				</tbody>
			</table>
			<br>
			<br>
			<table width="100%" border="0">
				<tr>
					<td>
						<table width="100%" border="0" class="bordasimples"  cellspacing="0">							
                            <tr align="left"><td colspan="3"><strong>VALORES PARA PAGAMENTOS</strong></td></tr>
                            <tr align="left">
                                <td align="left" width="40%" ><strong><font size="2">Saldo em C/C</font></strong></td>
                                <td align="right" width="20%"><strong><font size="2"><?php echo(FloatToMoeda($dblSaldo));?></font></strong></td>
                                <td align="right" width="40%"><strong><font size="2"><span id="saldoFinal"><?php echo(FloatToMoeda($dblSaldo-$dblTotal));?></span></font></strong></td>
                            </tr>                            
                         </table>
                        <table width="100%" border="0" class="bordasimples"  cellspacing="0">							 
                            <tr>
								<td width="40%" ><font size="2"><strong>BENEFICIÁRIO</strong></font></td>
								<td width="20%" align="center"><font size="2"><strong>VALOR</strong></font></td>
								<td width="40%" style="padding-left:8px"><font size="2"><strong>SOLICITANTE</strong></font></td>
							</tr>
								<?php echo($strDados);?>
							<tr>
							  <td align="right"><font size="2"><strong>Total:</strong></font></td>
							  <td align="right"><font size="2"><strong><?php echo(FloatToMoeda($dblTotal)); ?></strong></font></td>
                              <td>&nbsp;</td>
							</tr>							
						</table>
					</td>
				</tr>
			</table>						
		</td>
	</tr>
    <tr>
    <td>
   <br><br><br>
    <div style="border-bottom:solid 1px #000000; width:300px">&nbsp;</div>
    <font size="2">Autorização Dir. Executiva</font></td>
    </tr>
</table>
<?php

$objResult->closeCursor();
?>
</body>
</html>
<?php
	$objConn = NULL;
?>
