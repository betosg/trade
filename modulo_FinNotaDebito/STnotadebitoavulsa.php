<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	$objConn 			= abreDBConn(CFG_DB); // Abertura de banco
	$var_chave			= request("var_chavereg");
    $strDirCliente      = getsession(CFG_SYSTEM_NAME . "_dir_cliente");

	/***            VERIFICAÇÃO DE ACESSO              ***/
	/*****************************************************/
	$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
	$strPopulate = ( request("var_populate") == "" ) ? "yes" : request("var_populate");
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"PRINT"); //Verificação de acesso do usuário corrente
	
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/	
	$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
	$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não
	
	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	function convertem($term, $tp) { 
		if ($tp == "1") $palavra = strtr(strtoupper($term),"àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß"); 
		elseif ($tp == "0") $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ"); 
		return $palavra; 
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
<script language="JavaScript" type="text/javascript">
		function switchColor(prObj, prColor){
		
			prObj.style.backgroundColor = prColor;
			
		}
		
		function abrirJanela(){ 
			//parent.window.resizeTo(700,600);
			var w = document.body.offsetWidth;
			var h = document.body.offsetHeight;
			
			parent.window.resizeTo(w+120, h+170);		 
		} 	
				
	</script>
<style type="text/css">

<!--
table.bordasimples {border-collapse: collapse;}
table.bordasimples tr td {border:1px solid #000000;}
-->

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
<STYLE TYPE="text/css">
.folha {
    page-break-after: always;
}
</STYLE>
</head>
<body style="margin:10px 0px 10px 0px;">
<?php

	// SQL Principal	
	try{
		$strSQL = "SELECT 
					  cod_nota_debito,
					  codigo,
					  tipo,
					  razao_social,
					  cnpj_cpf,
					  cod_conta_pagar_receber,
					  dt_vcto,
					  dt_emissao,
					  descricao_despesas,
					  end_cep,
					  end_logradouro,
					  end_numero,
					  end_complemento,
					  end_bairro,
					  end_cidade,
					  end_estado,
					  end_pais,
					  vlr_total,
					  obs
				  FROM fin_nota_debito
				  WHERE cod_nota_debito = '".$var_chave."';";
		//echo($strSQL);
		$objResult = $objConn->query($strSQL); // execução da query	
	}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
	}		
foreach($objResult as $objRS)  {
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
			$objResult2 = $objConn->query($strSQL); // execução da query	
		}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
		}  
	}
	foreach($objResult2 as $objRS2){
		$strClienteInscEst = getValue($objRS2,"insc_est");
	}
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
					<?php 
						// UPDATE - LOGO UTILIZADO
						// $logo = '../img/logos/cab_'.$datawide_lang.'_'.$id_evento.'.jpg';
 					    
						//logotipo foi passado para dentro da pasta do cliente - by Vini 14.03.2013
						//$logo = "../img/".$id_mercado."_logo_big.jpg"; 
						$logo = getVarEntidade($objConn,"logotipo_empresa");
					?>
					<img width="300" align="center" src="<?php echo $logo; ?>"><br><br>
				</td>
				<td width="99%" style="padding-left:20px;padding-bottom:10px;text-align:left;font-size:10px;vertical-align:bottom;"><?php echo preg_replace("/(\\r)?\\n/i", "<br/>", $strEmprEnderCompleto);?></td>
			</tr>
		</table>
	</td>
  </tr>
  <tr height="25"><td colspan="2">&nbsp;</td></tr>
  <tr>
    <td colspan="2"><div align="center"> <font size="3"> <b>NOTA DE DÉBITO <b></font></div></td>
  </tr>
  <tr>
    <td width="62%">&nbsp;</td>
    <td width="38%"><div align="left"> <font size="2">CNPJ..................: <?php echo($strEmprCNPJ); ?><br>
        Insc. Estadual.....: <?php echo($strEmprInscEst); ?> <br>
        Data da Emissão.: <?php echo(dDate("PTB", getValue($objRS,"dt_emissao"), false)); ?></font><br>
      </div></td>
  </tr>
</table>
	<table width="100%" border="0" class="bordasimples">
	  <thead>
	  	<tr style="">
			<th width="33%" style="border:1px solid black;border-right:none;"><font size="2">Nota de Débito N°</font></th>
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
    <td><table width="100%" border="0">
        <tr>
          <td width="11%"><font size="2">Cliente:</font></td>
          <td width="89%"><font size="2"><b><?php echo getValue($objRS,"razao_social") ?></b></font></td>
        </tr>
        <tr>
          <td><font size="2">Endereço:</font></td>
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
      </table></td>
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
<font size="2">Devem à <?php  echo $strEmpresaRazaoSocial ?>, a importância correspondente às despesas abaixo: </font><br>
<br>
<table width="100%"  border="1" style="border-collapse:collapse">
  <tr>
    <td>
    	<table width="100%" border="0">
            <tr><td colspan="5" ><font size="2">Descrição das Despesas</font></td></tr>        
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
<?php }; ?>
</body>
</html>
<?php $objConn = NULL; ?>