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
table.bordasimples tr td {}
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
					<font size="2" color="#696969">Emiss�o:<?php echo date('d/m/y')?></font><br><br>
				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height="25"><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<div align="center"> <font size="4" color="#696969"><strong>Associados em ordem Alfab�tica</strong></font></div><br/>
					<div align="center"> <font size="3" color="#696969"><strong><i><?php echo($strEmpresaRazaoSocial);?></i></strong></font></div>
				</td>
			</tr>	
</font>			
</table><br><br>

<table width="100%" border="0" class="bordasimples" cellspacing="0">
				
			
<?php 
				try{  		
							$objResult = $objConn->query($strSQL);
						}
						catch(PDOException $e){
							mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
							die();	
						}
						$strUfAnterior = '';
							foreach($objResult as $objRS) {
								
								if(getValue($objRS,"uf_extendido") != $strUfAnterior){?>
									<div style="font-size:16px; font-weight:bold;" ><?php echo(getValue($objRS,"uf_extendido"))?></div>
                                    <hr>
						<?php	}
								$strUfAnterior = getValue($objRS,"uf_extendido") ;
						?>
							<table width="100%" border="0" bgcolor="#FFFFFF">
								<tr>
									<td colspan='3'><font size='2'><strong><?php echo(getValue($objRS,"cod_pj"));?></strong></font></td>
								</tr>
								<tr>
									<td colspan='3'><font size='2'><strong><i><?php echo(getValue($objRS,"razao_social"));?></i></strong></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><?php echo(getValue($objRS,"endprin_end"));?></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><?php echo(getValue($objRS,"endprin_bairro"));?></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><?php echo(getValue($objRS,"endprin_cidade"));?></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><label>CNPJ: </label><?php echo(getValue($objRS,"cnpj"));?></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><label>I.E: </label><?php echo(getValue($objRS,"insc_est"));?></font></td>
								</tr>
								<tr>
									<td  colspan='3'><font size='2'><label>FONE(1): </label><?php echo(getValue($objRS,"endprin_fone1"));?></font></td>
								</tr>
								<?php if ((getValue($objRS,"endprin_fone2")) <> ''){?>
								<tr> 
									<td  colspan='3'><font size='2'><label>FONE(2): </label><?php echo(getValue($objRS,"endprin_fone2"));?></font></td>
								</tr>
								<?php }?>
								<?php if ((getValue($objRS,"endprin_fone3")) <> ''){?>
								<tr> 
									<td  colspan='3'><font size='2'><label>FONE(3): </label><?php echo(getValue($objRS,"endprin_fone3"));?></font></td>
								</tr>
								<?php }?>
								<?php if ((getValue($objRS,"endprin_fone4")) <> ''){?>
								<tr> 
									<td  colspan='3'><font size='2'><label>FONE(4): </label><?php echo(getValue($objRS,"endprin_fone4"));?></font></td>
								</tr>
								<?php }?>
								<?php if ((getValue($objRS,"endprin_fone5")) <> ''){?>
								<tr> 
									<td  colspan='3'><font size='2'><label>FONE(5): </label><?php echo(getValue($objRS,"endprin_fone5"));?></font></td>
								</tr>
								<?php }?>
								<?php $strSQL= "select t1.email, t1.nome, t1.endprin_fone1";
												$strSQL.= " from cad_pf t1";
												$strSQL.= " INNER JOIN relac_pj_pf t2 ON t1.cod_pf = t2.cod_pf";
												$strSQL.= " WHERE t2.tipo ILIKE '%".getValue($objRS,"tipo_contato")."%' AND t2.cod_pj =" . getValue($objRS,"cod_pj");
												$strSQL.= " order by t1.nome asc";
												
										try{  		
											$objResultContato = $objConn->query($strSQL);
										}
										catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
											die();	
										} 
										if($objResultContato->rowCount() > 0) {?>
												<tr>
													<td  colspan='3'>
														<table>
											<?php	foreach($objResultContato as $objRSContato) {?>
															<tr>	
																<td>	
																	<font size='2'><?php echo(getValue($objRSContato,"nome"));?></font>
																</td>
																<td style="padding-left:8px;padding-right:8px;">	
																	<font size='2'><?php echo(getValue($objRSContato,"email"));?></font>
																</td>
																<td>	
																	<font size='2'><?php echo(getValue($objRSContato,"endprin_fone1"));?></font>
																</td>
															</tr>
											<?php	}	?>
														</table>
													</td>
												</tr>
										<?php }?>
								<tr>
									<td  colspan='3'><font size='2'><label>ANS: </label><?php echo(getValue($objRS,"codigo_ans"));?></font></td>
								</tr>
						</table>
                        <br>

                    
                        <br>
                        <br>
								
<?php	
						}//fim do foreach
?>
					
					
			
			
</table>
<?php

$objResult->closeCursor();
?>
</body>
</html>
<?php
	$objConn = NULL;
?>
