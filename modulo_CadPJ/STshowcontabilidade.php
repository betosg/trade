<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// COMO esta página está embutida em um iframe,
// nao é possível passar o registro corrente da
// dialog. Por isso, coletamos cod_pj atual pe-
// la sessão mesmo. ATENÇÃO! sempre buscará o
// cod_pj corrente da ULTIMA DIALOG ABERTA.
$intCodDado = getsession("cadpj_cod_pj");

if($intCodDado != ""){
	// Abertura de conexão com o banco
	$objConn    = abreDBConn(CFG_DB);

	// Busca cod_pj_contabil para exibição dos
	// radios que controlam o enable / disable
	// dos campos de endereço de cobrança
	try{
		$strSQL    = "SELECT cod_pj_contabil FROM cad_pj WHERE cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("hereerr_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();	
	}
	// Fetch do dado encontrado
	$objRS 			= $objResult->fetch();
	$intCodContabil = getValue($objRS,"cod_pj_contabil");
	
	// caso o cod_pj_contabil vazio, para
	// nao rodar query com cod_pj_contabil
	// = 'nada'
	
	$strRazaoSocial = "";
	
	if($intCodContabil != ""){
		// Busca dados da PJ_CONTABIL para exibi
		// cao dos mesmos na opcao RADIO com base
		// no cod_pj_contabil localizado acima
		try{
			$strSQL    = "SELECT razao_social FROM cad_pj_contabil WHERE cod_pj_contabil = ".$intCodContabil;
			$objResult = $objConn->query($strSQL);
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();	
		}
		// Fetch do dado encontrado
		$objRS 			= $objResult->fetch();
		$strRazaoSocial = getValue($objRS,"razao_social");
	}
}
?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript">
	<!--
		// copia os dados do frame parent
		// para recolocá-los posteriormente
		// caso fique alternando entre os
		// radios, dados atuais ou antigos
		function copyOldValues(){
			document.formeditor_000.dbvar_num_cod_pj_contabil.value = parent.document.formeditor_000.dbvar_num_cod_pj_contabil.value;		//COD_PJ_CONTABIL
			document.formeditor_000.dbvar_num_endcobr_cep_000.value = parent.document.formeditor_000.dbvar_num_endcobr_cep_000.value;		//CEP
			document.formeditor_000.dbvar_str_endcobr_rotulo_000.value = parent.document.formeditor_000.dbvar_str_endcobr_rotulo_000.value;     //ROTULO
			document.formeditor_000.dbvar_str_endcobr_logradouro_000.value = parent.document.formeditor_000.dbvar_str_endcobr_logradouro_000.value;	//LOGRADOURO
			document.formeditor_000.dbvar_str_endcobr_numero_000.value = parent.document.formeditor_000.dbvar_str_endcobr_numero_000.value;		//NUMERO
			document.formeditor_000.dbvar_str_endcobr_complemento_000.value = parent.document.formeditor_000.dbvar_str_endcobr_complemento_000.value;//COMPLEMENTO
			document.formeditor_000.dbvar_str_endcobr_bairro_000.value = parent.document.formeditor_000.dbvar_str_endcobr_bairro_000.value;		//BAIRRO
			document.formeditor_000.dbvar_str_endcobr_cidade_000.value = parent.document.formeditor_000.dbvar_str_endcobr_cidade_000.value;		//CIDADE
			document.formeditor_000.dbvar_str_endcobr_estado_000.value = parent.document.formeditor_000.dbvar_str_endcobr_estado_000.value;		//ESSTADO
			document.formeditor_000.dbvar_str_endcobr_email_000.value = parent.document.formeditor_000.dbvar_str_endcobr_email_000.value;		//EMAIL
			document.formeditor_000.dbvar_str_endcobr_contato_000.value = parent.document.formeditor_000.dbvar_str_endcobr_contato_000.value;	//CONTATO
			document.formeditor_000.dbvar_str_endcobr_fone1_000.value = parent.document.formeditor_000.dbvar_str_endcobr_fone1_000.value;		//FONE1
			document.formeditor_000.dbvar_str_endcobr_fone2_000.value = parent.document.formeditor_000.dbvar_str_endcobr_fone2_000.value;		//FONE2 
			document.formeditor_000.dbvar_str_endcobr_fone3_000.value = parent.document.formeditor_000.dbvar_str_endcobr_fone3_000.value;		//FONE3
			document.formeditor_000.dbvar_str_endcobr_fone4_000.value = parent.document.formeditor_000.dbvar_str_endcobr_fone4_000.value;		//FONE4
		}
		
		function setOldValues(){
			parent.document.formeditor_000.dbvar_num_cod_pj_contabil.value         	= document.formeditor_000.dbvar_num_cod_pj_contabil.value; //COD_CONTABIL
			parent.document.formeditor_000.dbvar_num_endcobr_cep_000.value         	= document.formeditor_000.dbvar_num_endcobr_cep_000.value; //CEP
			parent.document.formeditor_000.dbvar_str_endcobr_rotulo_000.value      	= document.formeditor_000.dbvar_str_endcobr_rotulo_000.value; //ROTULO
			parent.document.formeditor_000.dbvar_str_endcobr_logradouro_000.value  	= document.formeditor_000.dbvar_str_endcobr_logradouro_000.value; //LOGRA
			parent.document.formeditor_000.dbvar_str_endcobr_numero_000.value      	= document.formeditor_000.dbvar_str_endcobr_numero_000.value; //NUMERO
			parent.document.formeditor_000.dbvar_str_endcobr_complemento_000.value 	= document.formeditor_000.dbvar_str_endcobr_complemento_000.value; //COMPL
			parent.document.formeditor_000.dbvar_str_endcobr_bairro_000.value      	= document.formeditor_000.dbvar_str_endcobr_bairro_000.value; //BAIRRO
			parent.document.formeditor_000.dbvar_str_endcobr_cidade_000.value      	= document.formeditor_000.dbvar_str_endcobr_cidade_000.value; //CIDADE
			parent.document.formeditor_000.dbvar_str_endcobr_estado_000.value 	   	= document.formeditor_000.dbvar_str_endcobr_estado_000.value; //ESSTADO
			parent.document.formeditor_000.dbvar_str_endcobr_email_000.value       	= document.formeditor_000.dbvar_str_endcobr_email_000.value; //EMAIL
			parent.document.formeditor_000.dbvar_str_endcobr_contato_000.value     	= document.formeditor_000.dbvar_str_endcobr_contato_000.value; //CONTATO
			parent.document.formeditor_000.dbvar_str_endcobr_fone1_000.value 		= document.formeditor_000.dbvar_str_endcobr_fone1_000.value; //FONE1
			parent.document.formeditor_000.dbvar_str_endcobr_fone2_000.value 		= document.formeditor_000.dbvar_str_endcobr_fone2_000.value; //FONE2 
			parent.document.formeditor_000.dbvar_str_endcobr_fone3_000.value 		= document.formeditor_000.dbvar_str_endcobr_fone3_000.value; //FONE3
			parent.document.formeditor_000.dbvar_str_endcobr_fone4_000.value 		= document.formeditor_000.dbvar_str_endcobr_fone4_000.value; //FONE4
		}
				
		// como esta pag. é um iframe,
		// seta seu ID para visible ou
		// invisible
		function setVisible(prIDField){
			var strIDField = prIDField;
			parent.document.getElementById('dbvar_file_busca_cep_000').style.display = '<?php echo(($intCodContabil == "") ? "block": "none");?>';
			parent.document.getElementById('dbvar_file_busca_contabil_000').style.display = '<?php echo(($intCodContabil == "") ? "block": "none");?>';			
			parent.document.getElementById(strIDField).style.display                 = '<?php echo(($intCodContabil == "") ? "none": "block");?>';
		}
		
		// esta função seta os campos de endereço 
		// de cobrança da pagina pai para disable
		// or enable, dependendo do param
		function setState(prBoolParam){
			//alert(prBoolParam);
			var boolParam = prBoolParam; // true or false expected
			// seta campos para desabilitado ou nao
			parent.document.formeditor_000.dbvar_num_endcobr_cep_000.disabled     	  = boolParam; //CEP
			parent.document.formeditor_000.dbvar_str_endcobr_rotulo_000.disabled  	  = boolParam; //ROTULO
			parent.document.formeditor_000.dbvar_str_endcobr_logradouro_000.disabled  = boolParam; //LOGRADOURO
			parent.document.formeditor_000.dbvar_str_endcobr_numero_000.disabled 	  = boolParam; //NUMERO
			parent.document.formeditor_000.dbvar_str_endcobr_complemento_000.disabled = boolParam; //COMPLEMENTO
			parent.document.formeditor_000.dbvar_str_endcobr_bairro_000.disabled 	  = boolParam; //BAIRRO
			parent.document.formeditor_000.dbvar_str_endcobr_cidade_000.disabled 	  = boolParam; //CIDADE
			parent.document.formeditor_000.dbvar_str_endcobr_estado_000.disabled 	  = boolParam; //ESSTADO
			parent.document.formeditor_000.dbvar_str_endcobr_email_000.disabled 	  = boolParam; //EMAIL
			parent.document.formeditor_000.dbvar_str_endcobr_contato_000.disabled 	  = boolParam; //CONTATO
			parent.document.formeditor_000.dbvar_str_endcobr_fone1_000.disabled 	  = boolParam; //FONE1
			parent.document.formeditor_000.dbvar_str_endcobr_fone2_000.disabled 	  = boolParam; //FONE2 
			parent.document.formeditor_000.dbvar_str_endcobr_fone3_000.disabled 	  = boolParam; //FONE3
			parent.document.formeditor_000.dbvar_str_endcobr_fone4_000.disabled 	  = boolParam; //FONE4
			parent.document.formeditor_000.dbvar_str_endcobr_fone5_000.disabled 	  = boolParam; //FONE5
			parent.document.formeditor_000.dbvar_str_endcobr_fone6_000.disabled 	  = boolParam; //FONE6
			if(boolParam == true){
				parent.document.getElementById('dbvar_file_busca_cep_000').style.display = 'none';
				parent.document.getElementById('dbvar_file_busca_contabil_000').style.display = 'none';
			} else{
				// seta a imagem de busca de endereço
				// para visivel novamente, no parent
				parent.document.getElementById('dbvar_file_busca_cep_000').style.display = 'block';
				parent.document.getElementById('dbvar_file_busca_contabil_000').style.display = 'block';
				// limpa campos, para novo endereço e 
				// seta o campo hidden cod_pj_contabil
				// da pagina pai para nulo
				parent.document.formeditor_000.dbvar_num_cod_pj_contabil.value         	= ''; //COD_CONTABIL
				parent.document.formeditor_000.dbvar_num_endcobr_cep_000.value         	= ''; //CEP
				parent.document.formeditor_000.dbvar_str_endcobr_rotulo_000.value      	= ''; //ROTULO
				parent.document.formeditor_000.dbvar_str_endcobr_logradouro_000.value  	= ''; //LOGRA
				parent.document.formeditor_000.dbvar_str_endcobr_numero_000.value      	= ''; //NUMERO
				parent.document.formeditor_000.dbvar_str_endcobr_complemento_000.value 	= ''; //COMPL
				parent.document.formeditor_000.dbvar_str_endcobr_bairro_000.value      	= ''; //BAIRRO
				parent.document.formeditor_000.dbvar_str_endcobr_cidade_000.value      	= ''; //CIDADE
				parent.document.formeditor_000.dbvar_str_endcobr_estado_000.value 	   	= ''; //ESSTADO
				parent.document.formeditor_000.dbvar_str_endcobr_email_000.value       	= ''; //EMAIL
				parent.document.formeditor_000.dbvar_str_endcobr_contato_000.value     	= ''; //CONTATO
				parent.document.formeditor_000.dbvar_str_endcobr_fone1_000.value 		= ''; //FONE1
				parent.document.formeditor_000.dbvar_str_endcobr_fone2_000.value 		= ''; //FONE2 
				parent.document.formeditor_000.dbvar_str_endcobr_fone3_000.value 		= ''; //FONE3
				parent.document.formeditor_000.dbvar_str_endcobr_fone4_000.value 		= ''; //FONE4
				parent.document.formeditor_000.dbvar_str_endcobr_fone5_000.value 		= ''; //FONE5
				parent.document.formeditor_000.dbvar_str_endcobr_fone6_000.value 		= ''; //FONE6
			}
		}			
	//-->
	</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;" onLoad="setVisible('dbvar_iframe_contabilidade');<?php echo(($intCodContabil != "") ? "setState(true);" : "")?> copyOldValues();">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
	<form  name="formeditor_000" action="STshowcontabilidade.php" method="post">
	<input type="hidden" name="dbvar_num_cod_pj_contabil" value=""/>
	<input type="hidden" name="dbvar_num_endcobr_cep_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_rotulo_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_logradouro_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_numero_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_complemento_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_bairro_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_cidade_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_estado_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_email_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_contato_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_fone1_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_fone2_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_fone3_000" value="" />
	<input type="hidden" name="dbvar_str_endcobr_fone4_000" value="" />
	<!-- OP. UM -->
	<tr>
		<td align="left" valign="top" nowrap="nowrap">
			<table>
				<tr>
					<td align="left" valign="top">
						<input type="radio"  name="dbvar_str_change_end_cobr_000" 
						value="" onClick="setState(true);setOldValues();" <?php echo(($intCodContabil == "") ? "": "checked=\"checked\"");?>
						class="inputclean" />
					</td>
					<td align="left" valign="top">
						<?php echo("<strong>".getTText("manter_vinculo_contabil",C_NONE).$intCodContabil." - ".$strRazaoSocial."</strong>");?>			
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- OP. DOIS -->
	<tr>
		<td align="left" valign="top" nowrap="nowrap">
			<table>
				<tr>
					<td align="left" valign="top">
						<input type="radio" name="dbvar_str_change_end_cobr_000" value="" onClick="setState(false);" 
						class="inputclean" />
					</td>
					<td align="left" valign="top">					
						<?php echo("<strong>".getTText("alterar_dados_contabil",C_NONE)."</strong>");?>			
					</td>
				</tr>
			</table>
		</td>
	</tr>
</form>
</table>
</body>
</html>