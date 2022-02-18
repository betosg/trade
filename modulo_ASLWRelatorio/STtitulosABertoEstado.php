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
//echo(request("conta_combo(29)"));
function convertem($term, $tp) { 
	if ($tp == "1") $palavra = strtr(strtoupper($term),"������������������������������","������������������������������"); 
	elseif ($tp == "0") $palavra = strtr(strtolower($term),"������������������������������","������������������������������"); 
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


  $strEmpresaRazaoSocial = getVarEntidade($objConn,"nome_fan");
 // $strEmprEnderCompleto = getVarEntidade($objConn,"endereco_completo");  
 // $strEmprTelefone = getVarEntidade($objConn,"telefone_sindicato");
 // $strLogotipo     = getVarEntidade($objConn,"logotipo_empresa");  

?>
<table width="100%" border="0" bgcolor="#FFFFFF">

	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			
		        <tr>								
				<td width="99%" style="padding-left:20px;padding-bottom:10px;text-align:left;vertical-align:top;">
					<font size="4">
						<strong>
							<?php echo($strEmpresaRazaoSocial);?><br>
						</strong>
					</font>
                </td>
				<td>									
					<font size="2" >Emiss�o:<?php echo date('d/m/Y')?></font><br><br>
				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height="5"><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<div align="center"> <font size="4" ><strong>Relat�rios de Contas a Receber - EM ABERTO - por ESTADO/periodo(Tipo de Documento)</strong></font></div><br/>
					<div align="center"></div>
				</td>
			</tr>	
</font>			
</table><br>

<div>

<table width="100%" border="0"  cellspacing=0 cellpadding=0  bgcolor="#FFFFFF"  >		
    <tr>
        <td width='10%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>Vencimento</strong></font></td>
        <td width='20%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>Nome Chave Organiza��o</strong></font></center></td>
        <td width='10%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>C�d. ANS</strong></font></center></td>
		<td width='10%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>N�mero</strong></font></center></td>
		<td width='10%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>Valor</strong> </font></center></td>
		<td width='25%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>Tipo Docto</strong> </font></center></td>
		<td width='25%' style="background-color: rgb(204, 204, 204);"><center><font size='2'><strong>Obs</strong> </font></center></td>
    </tr>
		
<?php 
				try{  		
							$objResult = $objConn->query($strSQL);
						}
						catch(PDOException $e){
							mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
							die();	
						}
						$strUfAnterior = '';
						$dblValor = 0;
						
						$dblTotalEstado = 0;
						$dblTotalGeral  = 0;
							foreach($objResult as $objRS) {
								

								
                                	
                                if(getValue($objRS,"uf_extendido") != $strUfAnterior){
                                if 	(($dblTotalEstado != 0) ){
                                    //if (1==2){
                            ?>
                                 
                                             <tr>
                                                 <td colspan='4' align="right"></td> 
                                                 <td align="right"><font size='2'><strong>Total<?php //echo($strUfAnterior);?>: <?php echo(number_format($dblTotalEstado,2,",","."));?></strong></font></td>
												 <td align="right"></td>
												 <td align="right"></td>												 
											</tr>
											<tr><td colspan="7"><hr></td></tr>
                                             <script language='javascript'>
                                            //	console.log(<?php //echo($strodonto);?>)
                                        	</script>
                                         <?php 
                                             $dblTotalEstado = 0;
                                             
                                     } //if total	?>
                                <tr>
										<td colspan='2' align="right"><font size='2'></font></td> 			
										<td style="font-size:16px; font-weight:bold;"  ><?php echo(getValue($objRS,"uf_extendido"))?></strong></font></td>
                                </tr>
									
								
							<?php	}//if estado
								
										
								?>
										
										<tr>
											<td><font size='2'><?php echo(getValue($objRS,"vcto"));?></font></td>										
											<td><font size='2'><?php echo(getValue($objRS,"razao_social"));?></font></td>										
											<td><font size='2'><?php echo(getValue($objRS,"codigo_ans"));?></font></td>
											<td><center><font size='2'><?php echo(getValue($objRS,"num_documento"));?></font></center></td>									
											<td align="right"><font size='2'><?php echo(number_format(getValue($objRS,"vlr_conta"),2,",",".")); ?></font></td>												
											<td align="right"><font size='2'><?php echo(getValue($objRS,"descricao"));?></font></td>										
											<td align="right"><font size='2'><?php echo(getValue($objRS,"obs"));?></font></td>										
                                        </tr>
                                       
									
								<?Php
										//}
										$dblTotalEstado = $dblTotalEstado + getValue($objRS,"vlr_conta") ;	
										
											
										$dblTotalGeral += getValue($objRS,"vlr_conta");
										

										$strUfAnterior = getValue($objRS,"uf_extendido");
										
									} //fim do foreach

								?>
								
									
                                        
                                        <tr>
                                            <td colspan='4' align="right"></td> 
                                            <td align="right"><font size='2'><strong>Total<?php //echo($strUfAnterior);?>: <?php echo(number_format($dblTotalEstado,2,",","."));?></strong></font></td>
											<td align="right" ></td>
											<td align="right" ></td>
										</tr>
										<tr><td colspan="7"><hr></td></tr>
                                        <script language='javascript'>
                                            //console.log(<?php echo($strodonto);?>)
                                        </script>
                                        <tr>
                                            <td colspan='4' align="right"></td> 
                                            <td align="right"><font size='2'><strong>Total Geral: <?php echo(number_format($dblTotalGeral,2,",","."));?></strong></font></td>
											<td align="right" > </td>
											<td align="right" ></td>
                                        </tr>


											
											
									</table>
</div>

<?php

$objResult->closeCursor();
?>
</body>
</html>
<?php
	$objConn = NULL;
?>

