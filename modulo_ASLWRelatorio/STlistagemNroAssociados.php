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
table.bordasimples tr td {boder: 1 px solid black;}
.folha { page-break-after: always; }

.tdicon{
		text-align:center;
		font-size:11px;
		font:bold;
		width:25%;		
}


</style>
</head>
<body style="margin:20px 35px 20px 35px;;" >
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
					<font size="2" color="#696969">
						<strong>
						
							<?php echo($strEmpresaRazaoSocial);?><br>
							<?php echo preg_replace("/(\\r)?\\n/i", "<br/>", $strEmprEnderCompleto);?><br>	
							<?php echo($strEmprTelefone);?>
						
						</strong>
					</font>
                </td>
				<td>									
					<font size="2" color="#696969">Emissão:<?php echo date('d/m/y')?></font><br><br>
				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height="25"><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<div align="center"> <font size="4" color="#696969"><strong>Associados em ordem Alfabética</strong></font></div><br/>
					<div align="center"> <font size="3" color="#696969"><strong><i><?php echo($strEmpresaRazaoSocial);?></i></strong></font></div>
				</td>
			</tr>	
</font>			
</table><br><br>

<div>
				
		
<?php 
				try{  		
							$objResult = $objConn->query($strSQL);
						}
						catch(PDOException $e){
							mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
							die();	
						}
						$strUfAnterior = '';
						$strmedica = 0;
						$strodonto = 0;
						$intTotalMedica = 0;
						$intTotalOdonto = 0;
							foreach($objResult as $objRS) {
								

								
								if(getValue($objRS,"uf_extendido") != $strUfAnterior){
									
									if 	(($strmedica != 0) || ($strodonto != 0)){
										?>
											<table width="100%" border="0" bgcolor="#FFFFFF" class="" >		
												</tr>
													<td colspan='3'><font size='2'>Total por estado:</font></td>
												</tr>
													<td colspan='3'><font size='2'>Número beneficiarios:<?php echo str_replace("," , "." ,number_format($strmedica));?></font></td>
												</tr>
													<td colspan='3'> <font size='2'>Número odonto: <?php echo str_replace("," , "." ,number_format($strodonto));?></font></td>
												</tr>			
											</table><br><br><br>

											<?php 
												$strmedica = 0;
												$strodonto = 0;
									} ?>
									
									<br><br><br>
									<hr>

									<table width="100%" border="0"  cellspacing=0 cellpadding=0  bgcolor="#FFFFFF" style="background-color: rgb(204, 204, 204);" >		
										<tr>
											<td width='30%'style="font-size:16px; font-weight:bold;" ><?php echo(getValue($objRS,"uf_extendido"))?></strong></font></td>
											<td width='20%'><center><font size='2'><strong>CÓD ANS</strong></font></center></td>
											<td width='25%'><center><font size='2' ><strong>MÉDICAS</strong></font></center></td>
											<td width='25%'><center><font size='2'><strong>ODONTOLÓGICAS</strong> </font></center></td>
										</tr>
									</table><br>
									
								<?php	
									}
								
										
								?>
									
									<table width="100%" border="0"  cellspacing=0 cellpadding=0 bgcolor="#FFFFFF" >		
										<tr>
											<td width='30%'><font size='2'><strong><?php echo(getValue($objRS,"razao_social"));?></strong></font></td>
										
											<td width='20%'><center><font size='2'><?php if(getValue($objRS,"codigo_ans") == '') {echo '-';} else { echo str_replace("," , "." ,number_format(getValue($objRS,"codigo_ans")));}?></font></center></td>
									
											<td width='25%'><center><font size='2'><?php if(getValue($objRS,"num_beneficiarios_medica") == '') {echo '-';} else { echo str_replace("," , "." ,number_format(getValue($objRS,"num_beneficiarios_medica")));}?></font></center></td>
												
											<td width='25%' ><center><font size='2'><?php if(getValue($objRS,"num_beneficiarios_odonto") == '') {echo '-';} else { echo str_replace("," , "." ,number_format(getValue($objRS,"num_beneficiarios_odonto")));}?></font></center></td>
										
										</tr>
									</table><hr/><br>

								<?Php
										//}
										$strmedica = $strmedica + getValue($objRS,"num_beneficiarios_medica") ;	
										$strodonto = $strodonto + getValue($objRS,"num_beneficiarios_odonto") ;	
											
										$intTotalMedica += getValue($objRS,"num_beneficiarios_medica");
										$intTotalOdonto += getValue($objRS,"num_beneficiarios_odonto");

										$strUfAnterior = getValue($objRS,"uf_extendido");
										
									} //fim do foreach

								?>
								
									<table width="100%" border="0" bgcolor="#FFFFFF" class="" >		
										</tr>
											<td colspan='3'><font size='2'>Total por estado:</font></td>
										</tr>
											<td colspan='3'><font size='2'>Número beneficiarios:<?php echo str_replace("," , "." ,number_format($strmedica));?></font></td>
										</tr>
											<td colspan='3'> <font size='2'>Número odonto: <?php echo str_replace("," , "." ,number_format($strodonto));?></font></td>
										</tr>			
								    </table><br><br><br>


									</table><br><br><br>
									<table width="100%" border="0" bgcolor="#FFFFFF" class="" style="font-size:16px; font-weight:bold;" >		
										</tr>
											<td colspan='3'><font size='2'>Total por geral:</font></td>
										</tr>
											<td colspan='3'><font size='2'>Número beneficiarios:<?php echo str_replace("," , "." ,number_format($intTotalMedica));?></font></td>
										</tr>
											<td colspan='3'> <font size='2'>Número odonto: <?php echo str_replace("," , "." ,number_format($intTotalOdonto));?></font></td>
										</tr>			
									</table><br><br><br>
</div><br><br><br>

<?php

$objResult->closeCursor();
?>
</body>
</html>
<?php
	$objConn = NULL;
?>

