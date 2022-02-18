<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");



$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
$objConn = abreDBConn(CFG_DB);


$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
$intAberto = 0;

$strSQL = "SELECT 
               		cad_pf.cpf
					,cad_pf.nome
					,cad_pf.rg
					,cad_pf.rg_org_emiss
					,to_char(cad_pf.data_nasc,'DD/MM/YYYY') AS data_nasc
					,cad_pf.email
					,cad_pf.foto
					,cad_pf.sexo
					,cad_pf.estado_civil
					,cad_pf.nacionalidade
					,cad_pf.endprin_cep 
					,cad_pf.endprin_logradouro
					,cad_pf.endprin_numero
					,cad_pf.endprin_complemento
					,cad_pf.endprin_bairro
					,cad_pf.endprin_cidade
					,cad_pf.endprin_estado
					,cad_pf.endprin_pais
					,cad_pf.endprin_fone1
					,cad_pf.endprin_fone2
					,cad_pf_curriculo.curriculo_arquivo
					,cad_pf_curriculo.curriculo_resumido
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 17  AND COD_PF =  $intCodDado ) AS radioterapia
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 18	AND COD_PF =  $intCodDado ) AS radiodiagnostico
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 19	AND COD_PF =  $intCodDado ) AS medicina_nuclear
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 20	AND COD_PF =  $intCodDado ) AS protecao
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 21	AND COD_PF =  $intCodDado ) AS ens_superior
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 22	AND COD_PF =  $intCodDado ) AS manut_com_rep
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 23	AND COD_PF =  $intCodDado ) AS ens_medio
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 24	AND COD_PF =  $intCodDado ) AS orgao
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 25	AND COD_PF =  $intCodDado ) AS industria
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 26	AND COD_PF =  $intCodDado ) AS pesquisa
                    , (SELECT 1 FROM  CAD_PF_ATUACAO WHERE COD_ATUACAO = 27	AND COD_PF =  $intCodDado ) AS outro
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 1 AND COD_PF = $intCodDado) as norte
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 3 AND COD_PF = $intCodDado) as sul
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 4 AND COD_PF = $intCodDado) as nordeste
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 5 AND COD_PF = $intCodDado) as sudeste
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 6 AND COD_PF = $intCodDado) as centro_oeste
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 7 AND COD_PF = $intCodDado) as outro_reg
					,(SELECT 1 FROM cad_pf_atuacao_regiao where cod_regiao_pais = 8 AND COD_PF = $intCodDado) as exterior					
              FROM cad_pf
			  LEFT JOIN cad_pf_curriculo ON cad_pf_curriculo.COD_PF = cad_pf.COD_PF
			  WHERE CAD_PF.cod_pf = $intCodDado ";
try{
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

	foreach ($objResult as $objRS){
    //DADOS DA EMPRESA
	$strCPF				    = getValue($objRS,"cpf");
	$strEmail 		 		= getValue($objRS,"email");
	$strNome			  	= getValue($objRS,"nome");
	$strRG				    = getValue($objRS,"rg");
	$strRGOrgao				= getValue($objRS,"rg_org_emiss");
	$strDataNasc            = getValue($objRS,"data_nasc");
	$strNacionalidade       = getValue($objRS,"nacionalidade");
	$strSexo                = getValue($objRS,"sexo");
	$strEstadoCivil         = getValue($objRS,"estado_civil");
	$strImgLogo				= getValue($objRS,"foto");
		                     
	                         
	//ENDEREÇO PRINCIPAL       
	$intCep 				= getValue($objRS,"endprin_cep");
	$strLogradouro 			= getValue($objRS,"endprin_logradouro");
	$intNumero 	 			= getValue($objRS,"endprin_numero");
	$strComplemento 		= getValue($objRS,"endprin_complemento");
	$strBairro 				= getValue($objRS,"endprin_bairro");
	$strCidade		 		= getValue($objRS,"endprin_cidade");
	$strUF 					= getValue($objRS,"endprin_estado");
	$strPais 				= getValue($objRS,"endprin_pais");
	$intTelefone			= getValue($objRS,"endprin_fone1");
	$intTelefone2			= getValue($objRS,"endprin_fone2");
	                         
	// DOCUMENTOS    
	$strArqCurriculo		= getValue($objRS,"curriculo_arquivo");	
	$strResumo				= getValue($objRS,"curriculo_resumido");
	
	// AREA ATUACAO
	$strAtua_radioterapia = getValue($objRS,"radioterapia");   
	$strAtua_radiodiagnostico  = getValue($objRS,"radiodiagnostico");   
	$strAtua_medicina_nuclear  = getValue($objRS,"medicina_nuclear");   
	$strAtua_protecao = getValue($objRS,"protecao"); 
	$strAtua_ens_superior      = getValue($objRS,"ens_superior");   
	$strAtua_manut_com_rep     = getValue($objRS,"manut_com_rep");   
	$strAtua_ens_medio = getValue($objRS,"ens_medio");
	$strAtua_orgao = getValue($objRS,"orgao");    
	$strAtua_industria = getValue($objRS,"industria");
	$strAtua_pesquisa = getValue($objRS,"pesquisa"); 
	$strAtua_outro    = getValue($objRS,"outro"); 
	
	//REGIAO ATUACAO
	$strRegiao_nordeste = getValue($objRS,"nordeste");
	$strRegiao_norte = getValue($objRS,"norte");     
	$strRegiao_centro_oeste = getValue($objRS,"centro_oeste");
	$strRegiao_sudeste = getValue($objRS,"sudeste");   
	$strRegiao_sul = getValue($objRS,"sul");       
	$strRegiao_exterior = getValue($objRS,"exterior");  
	$strRegiao_outro = getValue($objRS,"outro_reg");   
	
	
	
	
	
      
      }

	
		// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - ".getTText("cad_novo_filiado",C_NONE));?></title>
<style type="text/css">
	.span_manual{
		float:right;
		background-image:url(../../_tradeunion/img/icon_document_pdf.png);
		background-repeat:no-repeat;
		background-position:right;
		height:15px;
		padding-top: 2px;
		padding-right:20px;
		margin-right:5px;
		cursor:pointer;
		font-size:10px;
		color:#009900;
		font-weight:600;
	}
</style>
<script>
<!--

function validaCampos(){
	// Esta função faz uma pré-validação via
	// js dos campos marcados com asterisco
	var strMSG  = "";
	// Tratamento contra campos vazios
	// GUARDA PARA DADOS DA EMPRESA
	// alert(document.getElementById('dbvar_str_data_fundacao').value);
	// alert(getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_fundacao').value,'ptb')));
//	strMSG += (
//			   (document.getElementById('dbvar_str_nome').value 		 == "")	||
//			   (document.getElementById('dbvar_str_email').value 				 == "")	||
//			   (document.getElementById('dbvar_str_rg').value 				 == "")	||
//			   (document.getElementById('dbvar_str_rg_orgao').value 				 == "")	||
//			   (document.getElementById('dbvar_str_data_nasc').value 				 == "")	||
//			   (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_nasc').value,'ptb'))>0) ||
//			   (document.getElementById('dbvar_str_nacionalidade').value 				 == "")	||
//			   (document.getElementById('dbvar_str_sexo').value 				 == "")	||
//			   (document.getElementById('dbvar_str_img_logo').value 				 == "")	||
//			   (document.getElementById('dbvar_str_estado_civil').value 				 == "")			   		   
//			   ) ? "\n\nDADOS PESSOAIS:" : "";
//				strMSG += (document.getElementById('dbvar_str_nome').value 				== "") ? "\nNome"	: "";
//				strMSG += (document.getElementById('dbvar_str_email').value 				== "") ? "\nE-mail"	: "";
//				strMSG += (document.getElementById('dbvar_str_rg').value 				== "") ? "\nRG"	: "";
//				strMSG += (document.getElementById('dbvar_str_rg_orgao').value 				== "") ? "\nRG Orgão"	: "";
//				strMSG += (document.getElementById('dbvar_str_data_nasc').value 				== "") ? "\nData Nascimento"	: "";
//				strMSG += (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_nasc').value,'ptb'))>0) ? "\nData de Nascimento Maior que Data Atual"	: "";
//				strMSG += (document.getElementById('dbvar_str_nacionalidade').value 				== "") ? "\nNacionalidade"	: "";
//				strMSG += (document.getElementById('dbvar_str_sexo').value 				== "") ? "\nSexo"	: "";		
//				strMSG += (document.getElementById('dbvar_str_img_logo').value 				== "") ? "\nFoto"	: "";			
//				strMSG += (document.getElementById('dbvar_str_estado_civil').value 				== "") ? "\nEstado Civil"	: "";		
//	
//	strMSG += (
//			   (document.getElementById('dbvar_str_cep').value 					== "") ||
//			   (document.getElementById('dbvar_str_logradouro').value 			== "") ||
//			   (document.getElementById('dbvar_str_numero').value 				== "") ||
//			   (document.getElementById('dbvar_str_bairro').value 				== "") ||
//			   (document.getElementById('dbvar_str_cidade').value 				== "") ||
//			   (document.getElementById('dbvar_str_uf').value 					== "") ||
//			   (document.getElementById('dbvar_str_telefone').value 			== "")
//			   ) ? "\n\nENDEREÇO PRINCIPAL:" : "";
//	strMSG += (document.getElementById('dbvar_str_cep').value 					== "") ? "\nCep" 				: "" ;
//	strMSG += (document.getElementById('dbvar_str_logradouro').value 			== "") ? "\nLogradouro" 		: "" ;
//	strMSG += (document.getElementById('dbvar_str_numero').value 				== "") ? "\nNúmero" 			: "" ;
//	strMSG += (document.getElementById('dbvar_str_bairro').value 				== "") ? "\nBairro" 			: "" ;
//	strMSG += (document.getElementById('dbvar_str_cidade').value 				== "") ? "\nCidade" 			: "" ;
//	strMSG += (document.getElementById('dbvar_str_uf').value 					== "") ? "\nEstado / UF" 		: "" ;
//	strMSG += (document.getElementById('dbvar_str_telefone').value 				== "") ? "\nTelefone Um (1)" 	: "" ;
	

	
	strMSG += (
			   (document.getElementById('dbvar_str_curriculo_resumido').value 					== "") ||
			   (document.getElementById('dbvar_str_curriculo').value 			== "") 

			   ) ? "\n\nDADOS PROFISSIONAIS:" : "";
	strMSG += (document.getElementById('dbvar_str_curriculo_resumido').value 					== "") ? "\nCurrículo Resumido" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_curriculo').value 			== "") ? "\nArquivo Currículo" 		: "" ;
	
	
	
	if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:'+strMSG); return(false); }
	else { return(true); }
}

function ok(){
	if(validaCampos()){

		document.getElementById('forminsert').submit();
	} else{
		return(false);
	}
}

function cancelar(){
	window.history.back();
}
-->

function setRegiaoAtuacao(campo) {	
  if (campo.checked == true) {
	  campo.value = 1;
  }
  if (campo.checked == false) {
  	  campo.value = 0;
  }
}

</script>
</head>
<body bgcolor="#F5F5F5" background="../../_tradeunion/img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
			<table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
			<form name="formeditor_000" id="forminsert" action="STUpdSocioABFMexec.php" method="post" enctype="multipart/form-data">
				<input type="hidden" id="var_db" name="var_db" value="<?php echo($strDB);?>" />

                <!--//-->
                <!--//-->

				<tr>
					<td align="center" valign="top" style="padding:0px 80px 0px 80px;"> 
						<table width="100%" cellpadding="3" cellspacing="0">
                        <!-- <tr><td colspan="2" align="right" ><img src="https://tradeunion.proevento.com.br/_tradeunion/img/LogoMarca_ABFM.gif" /></td></tr> //-->
                        <!--
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" height="40" class="destaque_gde"><strong><?php echo(getTText("dados_empresa",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                             <tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="25%" align="right" valign="top"><b>*<?php echo(getTText("cpf",C_NONE));?>:</b></td>
								<td width="75%">
									<input type="text" name="dbvar_str_cpf" id="dbvar_str_cpf" style="width:90px;" maxlength="11" value="<?php echo $strCPF; ?>" readonly="readonly">
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nome",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_nome" id="dbvar_str_nome" maxlength="120" value="<?php echo $strNome; ?>" style="width:280px;" readonly="readonly"></td>
							</tr>
                              <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("email",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_email" id="dbvar_str_email" maxlength="255" value="<?php echo $strEmail; ?>"  onBlur="javascript:if(!validateEmail(this.value,true)) this.value='';" style="width:280px;"></td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("img_foto",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_img_logo" id="dbvar_str_img_logo" size="40" value="<?php echo $strImgLogo; ?>" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_img_logo','/abfm/upload/fotospf/','','');">
									<br><span class="comment_med">A foto deve ser em formato 3x4 (vertical) com resolução de 300 DPI</span>
								</td>
							</tr>
                           
                          
							
							
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("rg",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_rg" id="dbvar_str_rg" value="<?php echo $strRG; ?>" style="text-transform:uppercase; width:120px;">
									&nbsp;*Orgão Emissor: <input type="text" name="dbvar_str_rg_orgao" id="dbvar_str_rg_orgao" value="<?php echo $strRGOrgao; ?>" style="text-transform:uppercase; width:50px;">
								</td>
							</tr>
							
                            
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("data_nasc",C_UCWORDS));?>:</b></td>
								<td>
                                <input type="text" name="dbvar_str_data_nasc" id="dbvar_str_data_nasc" maxlength="10" value="<?php echo $strDataNasc; ?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" style="width:70px;" />
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nacionalidade",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_nacionalidade" id="dbvar_str_nacionalidade" maxlength="255" value="<?php echo $strNacionalidade; ?>"  style="width:200px;"></td>
							</tr>
                            <tr>
									<td align="right"><b>*<?php echo(getTText("genero",C_UCWORDS));?>:</b></td>
									<td>										
										<select name="dbvar_str_sexo" id="dbvar_str_sexo" style="width:100px;">
                                          <option value="" <?php if($strSexo == ""){echo("selected");}?>>Selecione</option>
										  <option value="F" <?php if($strSexo == "F"){echo("selected");}?>>Feminino</option>
  										  <option value="M" <?php if($strSexo == "M"){echo("selected");}?>>Masculino</option>
                                          <option value="I" <?php if($strSexo == "I"){echo("selected");}?>>Prefiro não informar</option>
										</select>                                        
                                       
                                        
									</td>
							</tr>
                             
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("estado_civil",C_UCWORDS));?>:</b></td>
									<td>										
										<select name="dbvar_str_estado_civil" id="dbvar_str_estado_civil" style="width:100px;">
                                          <option value="" <?php if($strEstadoCivil == ""){echo("selected");}?>>Selecione</option>                                        
										  <option value="Casado(a)" <?php if($strEstadoCivil == "Casado(a)"){echo("selected");}?>>Casado(a)</option>
  										  <option value="Solteiro(a)" <?php if($strEstadoCivil == "Solteiro(a)"){echo("selected");}?>>Solteiro(a)</option>
                                          <option value="Divorciado(a)" <?php if($strEstadoCivil == "Divorciado(a)"){echo("selected");}?>>Divorciado(a)</option>
   										  <option value="Viúvo(a)" <?php if($strEstadoCivil == "Viúvo(a)"){echo("selected");}?>>Viúvo(a)</option>
										</select>                                        
                                        
									</td>
							</tr>  
                           
                            
                            
                            
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" class="destaque_gde"><strong><?php echo(getTText("endereco_principal",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr>
								<td></td>
								<td height="10" bgcolor="#FFFFFF" valign="top"><span class="comment_peq"><?php echo(getTText("este_endereco_usado_boleto_titulo",C_NONE));?></span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("cep",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_cep" id="dbvar_str_cep" maxlength="8" value="<?php echo $intCep; ?>" onKeyPress="return validateNumKey(event)" style="width:80px;">
									&nbsp;<span><img src="../../_tradeunion/img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="ajaxBuscaCEPLocal('dbvar_str_cep','dbvar_str_logradouro','dbvar_str_bairro','dbvar_str_cidade','dbvar_str_uf','dbvar_str_numero','loader_cep');" style="cursor:pointer" /></span>
									&nbsp;<span id="loader_cep"></span>
						        </td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("logradouro",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_logradouro" id="dbvar_str_logradouro" maxlength="255" value="<?php echo $strLogradouro; ?>" style="width:280px;" /></span>
								</td>	
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("num_complemento",C_NONE));?></b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="30%"><input type="number" name="dbvar_str_numero" id="dbvar_str_numero" maxlength="20" value="<?php echo $intNumero; ?>" style="width:80px;"></td>
									<td width="50%"><input type="text" name="dbvar_str_complemento" id="dbvar_str_complemento" maxlength="50" value="<?php echo $strComplemento; ?>" style="width:90px;"></td>
								</tr>                                
								</table>
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("bairro",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_bairro" id="dbvar_str_bairro" maxlength="30" value="<?php echo $strBairro; ?>" style="width:150px;"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>"F>
								<td align="right"><b>*<?php echo(getTText("cidade",C_NONE));?>:</b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><input type="text" name="dbvar_str_cidade" id="dbvar_str_cidade" maxlength="30" value="<?php echo $strCidade; ?>" style="width:150px;"></td>
									<td>
										<b>*<?php echo(getTText("uf",C_NONE));?>:</b>
										<select name="dbvar_str_uf" id="dbvar_str_uf" style="width:45px;">
                                          <option value="" selected>UF</option>                                        
										<?php $strUFRequest = $strUF ; $strUFRequest = ($strUFRequest == "") ? "SP" : $strUFRequest ; echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado",$strUFRequest)); ?>
										</select>
										<b><?php echo(getTText("brasil",C_TOUPPER));?></b>
										<input type="hidden" id="dbvar_str_pais" name="dbvar_str_pais" value="BRASIL" >
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("telefone_1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_telefone" id="dbvar_str_telefone" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" maxlength="12" value="<?php echo $intTelefone; ?>" style="width:80px;">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("telefone_2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_telefone_2" id="dbvar_str_telefone_2" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" maxlength="12" value="<?php echo $intTelefone2; ?>" style="width:80px;">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							
                            
                           //-->
						   <tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            <tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_curriculo",C_TOUPPER));?></strong></td>
							</tr>		
                            <tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("curriculo",C_NONE));?>:</b></td>
								<td><br><span class="comment_med">
                                <strong>As informações aqui apresentadas são de responsabilidade do associado e serão utilizadas no sistema de pesquisa de sócio da ABFM</strong>
                                </span><br /><br />
									<textarea rows="10" cols="80" name="dbvar_str_curriculo_resumido" id="dbvar_str_curriculo_resumido" maxlength="2000"><?php echo $strResumo; ?></textarea>
                                    <br><span class="comment_med">Este texto será apresentado no sistema de busca entre os sócios. Até 2.000 caracteres.</span>
								</td>
							</tr>												

                            <tr valign="top" bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("curriculo_completo",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_curriculo" id="dbvar_str_curriculo"  value="<?php echo $strArqCurriculo; ?>" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_curriculo','/abfm/upload/docspf/','','');">									
								</td>
							</tr>                           
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							 <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"><b>*<?php echo(getTText("regiao_atuacao",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td></td>
									<td>							
                                    <table width="100%">
                                    	<tr valign="top">
                                        <td  width="50%">
                                        <table>
                                    	<tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_nordeste == 1){echo(1);}?>" <?php if($strRegiao_nordeste == 1){echo("checked");}?>  name="dbvar_regiao_nordeste" onclick="setRegiaoAtuacao(this)">
											</td>
                                            <td align="left">Nordeste</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_norte == 1){echo(1);}?>" <?php if($strRegiao_norte == 1){echo("checked");}?> name="dbvar_regiao_norte" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Norte</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_centro_oeste == 1){echo(1);}?>" <?php if($strRegiao_centro_oeste == 1){echo("checked");}?> name="dbvar_regiao_centro_oeste" onclick="setRegiaoAtuacao(this)">
											</td>
											<td align="left">Centro-oeste</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_sudeste == 1){echo(1);}?>" <?php if($strRegiao_sudeste == 1){echo("checked");}?> name="dbvar_regiao_sudeste" onclick="setRegiaoAtuacao(this)">
											</td>
											<td align="left">Sudeste</td>	</tr>
                                        </table>
                                        </td>
                                        <td  width="50%">
                                        <table>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_sul == 1){echo(1);}?>" <?php if($strRegiao_sul == 1){echo("checked");}?> name="dbvar_regiao_sul" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Sul</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_exterior == 1){echo(1);}?>" <?php if($strRegiao_exterior == 1){echo("checked");}?> name="dbvar_regiao_exterior" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Não atuo no Brasil</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strRegiao_outro == 1){echo(1);}?>" <?php if($strRegiao_outro == 1){echo("checked");}?> name="dbvar_regiao_outro" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Outro</td>	</tr>                
                                        </table>
                                        </td>
                                        </tr>                    
                                          
                                        </table>
									</td>
							</tr>  
                            <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"></td>
                            </tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"><b>*<?php echo(getTText("area_atuacao",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td></td>
									<td width="">				
                                    <table>
                                    	<tr valign="top">
                                        <td>
                                        <table width="100%">
                                    	<tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_radioterapia == 1){echo(1);}?>" <?php if($strAtua_radioterapia == 1){echo("checked");}?> name="dbvar_atuacao_radioterapia" onclick="setRegiaoAtuacao(this)">
											</td>
                                            <td  align="left">Radioterapia</td>											
										</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox"  value="<?php if($strAtua_radiodiagnostico == 1){echo(1);}?>" <?php if($strAtua_radiodiagnostico == 1){echo("checked");}?>  name="dbvar_atuacao_radiodiagnostico" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Radiodiagnóstico</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox"  value="<?php if($strAtua_medicina_nuclear == 1){echo(1);}?>" <?php if($strAtua_medicina_nuclear == 1){echo("checked");}?>  name="dbvar_atuacao_medicina_nuclear" onclick="setRegiaoAtuacao(this)">
											</td>
											<td  align="left">Medicina Nuclear</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_protecao == 1){echo(1);}?>" <?php if($strAtua_protecao == 1){echo("checked");}?>   name="dbvar_atuacao_protecao" onclick="setRegiaoAtuacao(this)">
											</td>
											<td  align="left">Proteção Radiológica</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_ens_superior == 1){echo(1);}?>" <?php if($strAtua_ens_superior == 1){echo("checked");}?>  name="dbvar_atuacao_ens_superior" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Ensino Superior</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_manut_com_rep == 1){echo(1);}?>" <?php if($strAtua_manut_com_rep == 1){echo("checked");}?>  name="dbvar_atuacao_manut_com_rep" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Manutenção, Comércio e Representação</td>	
                                         </tr> 
                                        </table>
                                        </td>
                                        <td>
                                        <table width="100%">
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_ens_medio == 1){echo(1);}?>" <?php if($strAtua_ens_medio == 1){echo("checked");}?>  name="dbvar_atuacao_ens_medio" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Ensino Médio</td>	</tr>      
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_orgao == 1){echo(1);}?>" <?php if($strAtua_orgao == 1){echo("checked");}?>   name="dbvar_atuacao_orgao" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Órgão Regulatório</td>	</tr>   
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_industria == 1){echo(1);}?>" <?php if($strAtua_industria == 1){echo("checked");}?>    name="dbvar_atuacao_industria" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Indústria</td>	</tr>   
                                                                                  <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_pesquisa == 1){echo(1);}?>" <?php if($strAtua_pesquisa == 1){echo("checked");}?>   name="dbvar_atuacao_pesquisa" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Pesquisa</td>	</tr>

                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value="<?php if($strAtua_outro == 1){echo(1);}?>" <?php if($strAtua_outro == 1){echo("checked");}?>   name="dbvar_atuacao_outro" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Outro</td>	</tr>                                 
                                          </table>
                                          </td>
                                          </tr>
                                        </table>				
                                        
									</td>
							</tr>  
                            
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	                            
                            
							
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
							<tr><td height="10" colspan="2"></td></tr>
							<tr> 
								<td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
									<tr>
										<td width="10%"><img src="../../_tradeunion/img/mensagem_info.gif" border="0"></td>
										<td width="60%"><?php echo(getTText("info_cad_nova_empresa",C_NONE));?></td>
										<td width="30%" colspan="2" align="right" style="padding-bottom:10px;">
											<button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>	
											<button onClick="cancelar();return false;"><?php echo( getTText("cancelar",C_UCWORDS));?></button>
										</td>
									</tr>
									</table>
								</td>
							</tr>
                            
                             
                            
						</table>
					</td>
				</tr>
				</form>
			</table>
			<?php athEndFloatingBox(); ?>
			</td>
		</tr>
	</table>	
</body>
</html>
<?php $objConn = NULL; ?>
