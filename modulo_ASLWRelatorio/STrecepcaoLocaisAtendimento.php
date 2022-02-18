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


//echo $strSQL;


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
<body style="margin:5px 0px 5px 0px;">
<?php


  $strEmpresaRazaoSocial = getVarEntidade($objConn,"razao_social");
  $strEmprEnderCompleto = getVarEntidade($objConn,"endereco_completo");  
  $strEmprTelefone = getVarEntidade($objConn,"telefone_sindicato");
  $strLogotipo     = getVarEntidade($objConn,"logotipo_empresa");  

?>
<!--tr>
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
	</tr-->
<table width="100%" border="0" bgcolor="#FFFFFF">
	
 



			<tr >
				<td colspan="2" valign="middle" style="background: #366092;color: white; width: 100%; height: 30px;font-size: 20px;text-align:center;" ><strong>RECEP��O DOS LOCAIS DE ATENDIMENTO</strong></td>
            </tr>
            <tr >
				<td colspan="2" valign="middle" style="background: white;color: black; height: 30px;font-size: 15px;text-align:center;"><strong>EMPRESAS PARTICIPANTES DO ATENDIMENTO ABRAMGE</strong></div></td>
			</tr>				
			<tr><td colspan="2"></td></tr>
            <tr>
                <td>
                    <table width="100%" border="0" class="bordasimples" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%" style="background: #366092;color: white; font-size: 8px;text-align:center;"><font size="2">N� ANS</font></th>
                                <th width="20%"style="background: #366092;color: white; font-size: 8px;text-align:center;"><font size="2">EMPRESA</font></th>
                                <th width="25%"style="background: #366092;color: white; font-size: 8px;text-align:center;"><font size="2">CIDADE/U.F SEDE DA OPERADORA SEM ATENDIMENTO ABRAMGE</font></th>
                                <th width="25%"style="background: #366092;color: white; font-size: 8px;text-align:center;"><font size="2">LOCAIS COBERTOS PELA PR�PRIA OPERADORA ONDE SEUS BENEFICI�RIOS N�O DEVEM SER ATENDIDOS PELO ATENDIMENTO ABRAMGE</font></th>
                                <th width="25%"style="background: #366092;color: white; font-size: 8px;text-align:center;"><font size="2">RAZ�O SOCIAL DA OPERADORA</font></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            try{  		
                                $objResult = $objConn->query($strSQL);
                            }
                            catch(PDOException $e){
                                mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
                                die();	
                            }
                                foreach($objResult as $objRS) {
                                        $i++;
                                        if ($i%2==0){
                                           $style ="background: #366092;color: white;"; 
                                        }else{
                                           $style="background: white;color: black;";
                                        }
                            ?>
                            <tr align="center">
                                <td style="border:1px solid black;<?php echo($style);?>" align="center"><font size="2"><?php echo(getValue($objRS,"out_codigo_ans"));?></font></td>
                                <td style="border:1px solid black;<?php echo($style);?>" align="center"><font size="2"><?php echo(getValue($objRS,"out_marca"));?></font></td>
                                <td style="border:1px solid black;<?php echo($style);?>" align="left"><font size="2"><?php echo(getValue($objRS,"out_cidade_estado"));?></font></td>                                
                                <td style="border:1px solid black;<?php echo($style);?>" align="left"><font size="2"><?php echo(getValue($objRS,"out_area_codbertura"));?></font></td>
                                <td style="border:1px solid black;<?php echo($style);?>" align="center"><font size="2"><?php echo(getValue($objRS,"out_razao_social"));?></font></td>
                            </tr>
                            <?php } //fim foreach?>
                        </tbody>
                    </table>

                </td>
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
