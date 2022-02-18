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

// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// fun��o para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		return($prColor);
	}
	
	// MENSAGEM DE ERRO, PARA CAMPOS OBRIGAT�RIOS
	$strMSG = "";

	// REQUESTS
	// REQUEST - DADOS DA EMPRESA
//	$strEmail 		 		= strtoupper(request("dbvar_str_email"));
//	$strRG				    = strtoupper(request("dbvar_str_rg"));
//	$strRGOrgao				= strtoupper(request("dbvar_str_rg_orgao"));
//	$strDataNasc            = strtoupper(request("dbvar_str_data_nasc"));
//	$strNacionalidade       = strtoupper(request("dbvar_str_nacionalidade"));
//	$strSexo                = strtoupper(request("dbvar_str_sexo"));
//	$strEstadoCivil         = request("dbvar_str_estado_civil");	
//	$strImgLogo				= request("dbvar_str_img_logo");
//		
//	
//	// REQUEST - ENDERE�O PRINCIPAL
//	$intCep 				= strtoupper(request("dbvar_str_cep"));
//	$strLogradouro 			= strtoupper(request("dbvar_str_logradouro"));
//	$intNumero 	 			= strtoupper(request("dbvar_str_numero"));
//	$strComplemento 		= strtoupper(request("dbvar_str_complemento"));
//	$strBairro 				= strtoupper(request("dbvar_str_bairro"));
//	$strCidade		 		= strtoupper(request("dbvar_str_cidade"));
//	$strUF 					= strtoupper(request("dbvar_str_uf"));
//	$strPais 				= strtoupper(request("dbvar_str_pais"));
//	$intTelefone			= strtoupper(request("dbvar_str_telefone"));
//	$intTelefone2			= strtoupper(request("dbvar_str_telefone_2"));
	
	// REQUEST - DOCUMENTOS DIGITALIZADOS
	$strArqCurriculo		= request("dbvar_str_curriculo");
	$strResumo				= request("dbvar_str_curriculo_resumido");	
	

	// REQUEST - AREA DE ATUA��O
	$strAtuacoesSQL		 		= "";
	//$strDebug		 		= request("dbvar_regiao_nordeste");
	$strRegioesSQL		 		= "";	
	
	if (request("dbvar_atuacao_radioterapia") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."17," ;
	if (request("dbvar_atuacao_radiodiagnostico") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."18," ;
	if (request("dbvar_atuacao_medicina_nuclear") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."19," ;
	if (request("dbvar_atuacao_protecao") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."20,"	 ;
	if (request("dbvar_atuacao_ens_superior") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."21," ;
	if (request("dbvar_atuacao_manut_com_rep") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."22," ;
	if (request("dbvar_atuacao_ens_medio") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."23," ;
	if (request("dbvar_atuacao_orgao") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."24," ;
	if (request("dbvar_atuacao_industria") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."25," ;
	if (request("dbvar_atuacao_pesquisa") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."26," ;
	if (request("dbvar_atuacao_outro") == "1") $strAtuacoesSQL	= $strAtuacoesSQL."27," ;
	
	
	// REQUEST - REGI�O DE ATUA��O
	if (request("dbvar_regiao_nordeste") == "1") $strRegioesSQL = $strRegioesSQL."4," ;	
	if (request("dbvar_regiao_norte") == "1") $strRegioesSQL = $strRegioesSQL."1,";
	if (request("dbvar_regiao_centro_oeste") == "1") $strRegioesSQL = $strRegioesSQL."6,";
	if (request("dbvar_regiao_sudeste") == "1")	$strRegioesSQL	= $strRegioesSQL."5,";
	if (request("dbvar_regiao_sul") == "1")	$strRegioesSQL	= $strRegioesSQL."3," ;
	if (request("dbvar_regiao_exterior") == "1") $strRegioesSQL = $strRegioesSQL."8," ;	
	if (request("dbvar_regiao_outro") == "1") $strRegioesSQL = $strRegioesSQL."7," ;	

//echo("aqui: ".$strRegioesSQL);
	//die();
//die("aqui:".$strDebug."<br>".$strAtuacoesSQL);	

	// CONSIST�NCIA PARA CAMPOS OBRIGAT�RIOS
//	if(($strEmail == "")||($strRG == "")||($strRGOrgao == "")||($strDataNasc == "")||($strNacionalidade == "")||($strSexo == "")||($strEstadoCivil == "")||($strImgLogo == "")){
//		$strMSG .= "<br><b>DADOS DA EMPRESA</b><br>";
//	}
//	$strMSG 	.= ($strEmail			== "") ? "E-mail<br>"			:"";
//	$strMSG 	.= ($strRG 				== "") ? "RG<br>"				:"";
//	$strMSG     .= ($strRGOrgao 		== "") ? "�rg�o RG<br>"			:""; 
//	$strMSG 	.= ($strDataNasc 		== "") ? "Data Nascimento<br>"	:"";
//	$strMSG     .= ($strNacionalidade 	== "") ? "Nacionalidade<br>"	:""; 
//	$strMSG 	.= ($strSexo 			== "") ? "Sexo<br>"				:"";
//	$strMSG     .= ($strEstadoCivil 	== "") ? "Estado Civil<br>"		:""; 
//	$strMSG 	.= ($strImgLogo 		== "") ? "Foto<br>"				:"";
//	
//	
//	if(($intCep == "")||($strLogradouro == "")||($intNumero == "")||($strBairro == "")||($strCidade == "")||($strUF == "")||($intTelefone == "")){
//		$strMSG .= "<br><b>ENDERE�O PRINCIPAL</b><br>";
//	}
//	$strMSG		.= ($intCep 			== "") ? "CEP<br>"				:"";
//	$strMSG		.= ($strLogradouro 		== "") ? "Logradouro<br>"		:"";
//	$strMSG 	.= ($intNumero 			== "") ? "N�mero<br>"			:"";
//	$strMSG		.= ($strBairro 			== "") ? "Bairro<br>"			:"";
//	$strMSG		.= ($strCidade 			== "") ? "Cidade<br>"			:"";
//	$strMSG		.= ($strUF 				== "") ? "UF<br>"				:"";
//	$strMSG 	.= ($intTelefone 		== "") ? "Celular<br>"		:"";

	
		
	if(($strArqCurriculo == "") || ($strResumo == "")){
		$strMSG .= "<br><b>DOCUMENTOS DIGITALIZADOS</b><br>";
	}
	$strMSG 	.= ($strArqCurriculo 	== "") ? "Curr�culo<br>" :""; 
	$strMSG 	.= ($strResumo	 		== "") ? "Resumo do Curr�culo<br>" :""; 

	
	if($strMSG != ""){
		mensagem_local("err_dados_titulo","err_dados_obj_desc","Os campos abaixos n&atilde;o est&atilde;o preenchidos e/ou est�o incorretos:<br><br>".$strMSG,"","erro",1);
		die();
	}
	

	
	
	// INICIALIZA A TRANSA��O PARA INSER��O DE PJ E SEU RESPECTIVO USU�RIO, ETC
	$objConn->beginTransaction();
	try{
		// INSERE PESSOA F�SICA
		$strSQL = "
			UPDATE cad_pf SET
					 rg                             = '".$strRG."'
					, rg_org_emiss                  = '".$strRGOrgao."'
					, data_nasc                     = '".$strDataNasc."'
					, email                         = '".$strEmail."'
					, foto                          = '".$strImgLogo."'
					, sexo                          = '".$strSexo."'
					, estado_civil                  = '".$strEstadoCivil."'
					, nacionalidade                 = '".$strNacionalidade."'
					, endprin_cep                   = '".$intCep."'
					, endprin_logradouro            = '".$strLogradouro."'
					, endprin_numero                = '".$intNumero."'
					, endprin_complemento           = '".$strComplemento."'
					, endprin_bairro                = '".$strBairro."'
					, endprin_cidade                = '".$strCidade."'
					, endprin_estado                = '".$strUF."'
					, endprin_pais                  = '".$strPais."'
					, endprin_fone1                 = '".$intTelefone."'
					, endprin_fone2                 = '".$intTelefone2."'
					, sys_dtt_ins                   = '".cDate(CFG_LANG,dateNow(),true)."' 
					, sys_usr_ins                   = '".$strUsuario."'
					
					WHERE COD_PF = $intCodDado 
			";
		//die($strSQL);
		//$objConn->query($strSQL);
//	echo($strSQL."\n");
		
		
	try{	
		$strSQL = "SELECT COD_PF FROM cad_pf_curriculo WHERE COD_PF = $intCodDado LIMIT 1";
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	} catch(PDOException $e){
		mensagem_local("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	if (getValue($objRS,"cod_pf") != ""){
		//echo(getValue($objRS,"cod_pf")." aquiiiiiiiiii");
		//die();
		$strSQL = "update cad_pf_curriculo SET				    
					curriculo_arquivo = '".$strArqCurriculo."'
					, curriculo_resumido = '".$strResumo."'
				   WHERE COD_PF = $intCodDado";
	} else {
			$strSQL = "
			INSERT INTO cad_pf_curriculo (
				    	cod_pf
					, curriculo_arquivo
					, curriculo_resumido

			) VALUES (	  	      	
					  ".getValue($objRS,"cod_pf")."
					, '".$strArqCurriculo."'
					, '".$strResumo."'
			)";
		//die($strSQL);				
	}
	$objConn->query($strSQL);
//	echo($strSQL."\n");
		
		
		// INSERE ATUACAO DA PESSOA FISICA
		
		$strSQL = "	DELETE FROM cad_pf_atuacao WHERE COD_PF = $intCodDado";			
		$objConn->query($strSQL);
//echo($strSQL."\n");
		
		$strSQL = "
			INSERT INTO cad_pf_atuacao (cod_pf, cod_atuacao) 
			SELECT $intCodDado, COD_ATUACAO 
			FROM CAD_ATUACAO WHERE COD_ATUACAO IN (".$strAtuacoesSQL."0)
			";			
		$objConn->query($strSQL);
//echo($strSQL."\n");
		
		
		// INSERE REGIAO ATUACAO DA PESSOA FISICA
		//die($strRegioesSQL);
		
		
		$strSQL = "	DELETE FROM cad_pf_atuacao_regiao WHERE COD_PF = $intCodDado";			
		$objConn->query($strSQL);
//echo($strSQL."\n");
		
		$strSQL = "
			INSERT INTO cad_pf_atuacao_regiao (cod_pf, cod_regiao_pais) 
			SELECT $intCodDado, COD_REGIAO_PAIS 
			FROM LC_REGIAO_PAIS WHERE COD_REGIAO_PAIS IN (".$strRegioesSQL."0)
			";			
		$objConn->query($strSQL);
//echo($strSQL."\n");
		
		
		
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem_local("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	// ENVIO DE EMAIL CONFIRMANDO CADASTRO
	// CORPO DO EMAIL ENVIADO AO SINDICATO CONFIRMANDO
	$strEMAILSINDI = '
		<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left" valign="top"> 
			<table width="100%" cellpadding="2" cellspacing="2">
				<tr>
					<td width="20%"></td>
					<td width="80%"></td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				<tr><td colspan="2"><strong>Novo cadastro de empresa Filiada efetuado! Este cadastro n�o � definitivo, tendo ainda de ser verificado.</strong></td></tr>
				<tr><td colspan="2"><strong>O cadastro da empresa j� est� dispon�vel para an�lise e aprova��o no seu Painel Geral, dentro do sistema TRADEUNION.</strong></td></tr>
				<tr>
					<td></td>
					<td align="left" valign="bottom" height="40" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>						
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>E-Mail:</b></td>
					<td>'.$strEmail.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Inscri��o Estadual:</b></td>
					<td>'.$strRG.'/'.$strRGOrgao.'</td>
				</tr>				
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>DOCUMENTO DIGITALIZADOS</strong></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Curr�culo:</b></td>
					<td>'.$strArqCurriculo.'</td>
				</tr>
				
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>ENDERE�O PRINCIPAL</strong></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>CEP:</b></td>
					<td>'.$intCep.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'"> 
					<td align="right"><b>Endere�o:</b></td>
					<td>'.$strLogradouro.'</div></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>N�mero:</b></td>
					<td>'.$intNumero.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Complemento:</b></td>
					<td>'.$strComplemento.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Bairro:</b></td>
					<td>'.$strBairro.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Cidade:</b></td>
					<td>'.$strCidade.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Estado:</b></td>
					<td>'.$strUF.'</td>
				</tr>					
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Pa�s:</b></td>
					<td>'.$strPais.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Telefone:</b></td>
					<td>'.$intTelefone.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Telefone 2:</b></td>
					<td>'.$intTelefone2.'</td>
				</tr>
				<tr><td colspan="2" height="5">&nbsp;</td></tr>				
				
			</table>
			</td>
		</tr>
		</table>';
	
	// CORPO DO EMAIL PARA EMPRESA FILIADA
	$strEMAILFILIA = '
		<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left" valign="top"> 
			<table width="100%" cellpadding="2" cellspacing="2">
				<tr><td width="100%"></td></tr>
				<tr><td style="font-size:12px;">PREZADO <strong>'.$strNome.'</strong>,</td></tr>
				<tr><td style="font-size:12px;">Sua solicita��o de ingresso junto ao sindicato foi efetuada com sucesso!</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style="font-size:12px;"><strong>Como Nosso Processo Funciona</strong></td></tr>
				<tr><td style="font-size:12px;">
					Informamos que seu Cadastro j� est� em nosso sistema � espera de aprova��o. Para isso, informamos que � necess�rio o 
					correto enquadramento do objeto do Contrato Social no CNAE (Classifica��o Nacional de Atividades Econ�micas) e sua 
					comprova��o atrav�s da documenta��o obrigat�ria enviada. 
					
					<br /><br />
					Caso alguma informa��o ou documento que ainda esteja faltando, 
					entraremos em contato atrav�s dos telefones ou e-mails informados no Cadastro para solicit�-los. 
					
					<br /><br />
					Solicitamos o aguardo da confirma��o da Aprova��o do Cadastro que tamb�m ser� enviada por e-mail.
				</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style="font-size:12px;">Atenciosamente,</td></tr>
				<tr><td style="font-size:12px;">SINDIPROM</td></tr>
			</table>
			</td>
		</tr>
		</table>';
	
	// BUSCA O EMAIL PARA O QUAL A MENSAGEM SER� ENVIADA
	//$strEMailDestino =  'tatianaf@gmail.com'; //getVarEntidade($objConn,"email");
	//emailNotify($strEMAILSINDI,getTText("novo_cadastro",C_NONE),$strEMailDestino,CFG_EMAIL_SENDER);
	//emailNotify($strEMAILFILIA,getTText("cadastro_concluido",C_NONE),$strEmail,CFG_EMAIL_SENDER);
	
	// DESTR�I OBJETO
	$objConn = NULL;
?>
<script type="text/javascript">
alert("Cadastro atualizado com sucesso");
window.location = "STUpdSocioABFM.php"
</script>