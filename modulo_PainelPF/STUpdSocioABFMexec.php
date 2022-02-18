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
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		return($prColor);
	}
	
	// MENSAGEM DE ERRO, PARA CAMPOS OBRIGATÓRIOS
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
//	// REQUEST - ENDEREÇO PRINCIPAL
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
	

	// REQUEST - AREA DE ATUAÇÃO
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
	
	
	// REQUEST - REGIÃO DE ATUAÇÃO
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

	// CONSISTÊNCIA PARA CAMPOS OBRIGATÓRIOS
//	if(($strEmail == "")||($strRG == "")||($strRGOrgao == "")||($strDataNasc == "")||($strNacionalidade == "")||($strSexo == "")||($strEstadoCivil == "")||($strImgLogo == "")){
//		$strMSG .= "<br><b>DADOS DA EMPRESA</b><br>";
//	}
//	$strMSG 	.= ($strEmail			== "") ? "E-mail<br>"			:"";
//	$strMSG 	.= ($strRG 				== "") ? "RG<br>"				:"";
//	$strMSG     .= ($strRGOrgao 		== "") ? "Órgão RG<br>"			:""; 
//	$strMSG 	.= ($strDataNasc 		== "") ? "Data Nascimento<br>"	:"";
//	$strMSG     .= ($strNacionalidade 	== "") ? "Nacionalidade<br>"	:""; 
//	$strMSG 	.= ($strSexo 			== "") ? "Sexo<br>"				:"";
//	$strMSG     .= ($strEstadoCivil 	== "") ? "Estado Civil<br>"		:""; 
//	$strMSG 	.= ($strImgLogo 		== "") ? "Foto<br>"				:"";
//	
//	
//	if(($intCep == "")||($strLogradouro == "")||($intNumero == "")||($strBairro == "")||($strCidade == "")||($strUF == "")||($intTelefone == "")){
//		$strMSG .= "<br><b>ENDEREÇO PRINCIPAL</b><br>";
//	}
//	$strMSG		.= ($intCep 			== "") ? "CEP<br>"				:"";
//	$strMSG		.= ($strLogradouro 		== "") ? "Logradouro<br>"		:"";
//	$strMSG 	.= ($intNumero 			== "") ? "Número<br>"			:"";
//	$strMSG		.= ($strBairro 			== "") ? "Bairro<br>"			:"";
//	$strMSG		.= ($strCidade 			== "") ? "Cidade<br>"			:"";
//	$strMSG		.= ($strUF 				== "") ? "UF<br>"				:"";
//	$strMSG 	.= ($intTelefone 		== "") ? "Celular<br>"		:"";

	
		
	if(($strArqCurriculo == "") || ($strResumo == "")){
		$strMSG .= "<br><b>DOCUMENTOS DIGITALIZADOS</b><br>";
	}
	$strMSG 	.= ($strArqCurriculo 	== "") ? "Currículo<br>" :""; 
	$strMSG 	.= ($strResumo	 		== "") ? "Resumo do Currículo<br>" :""; 

	
	if($strMSG != ""){
		mensagem_local("err_dados_titulo","err_dados_obj_desc","Os campos abaixos n&atilde;o est&atilde;o preenchidos e/ou estão incorretos:<br><br>".$strMSG,"","erro",1);
		die();
	}
	

	
	
	// INICIALIZA A TRANSAÇÃO PARA INSERÇÃO DE PJ E SEU RESPECTIVO USUÁRIO, ETC
	$objConn->beginTransaction();
	try{
		// INSERE PESSOA FÍSICA
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
				<tr><td colspan="2"><strong>Novo cadastro de empresa Filiada efetuado! Este cadastro não é definitivo, tendo ainda de ser verificado.</strong></td></tr>
				<tr><td colspan="2"><strong>O cadastro da empresa já está disponível para análise e aprovação no seu Painel Geral, dentro do sistema TRADEUNION.</strong></td></tr>
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
					<td align="right"><b>Inscrição Estadual:</b></td>
					<td>'.$strRG.'/'.$strRGOrgao.'</td>
				</tr>				
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>DOCUMENTO DIGITALIZADOS</strong></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Currículo:</b></td>
					<td>'.$strArqCurriculo.'</td>
				</tr>
				
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>CEP:</b></td>
					<td>'.$intCep.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'"> 
					<td align="right"><b>Endereço:</b></td>
					<td>'.$strLogradouro.'</div></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td align="right"><b>Número:</b></td>
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
					<td align="right"><b>País:</b></td>
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
				<tr><td style="font-size:12px;">Sua solicitação de ingresso junto ao sindicato foi efetuada com sucesso!</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style="font-size:12px;"><strong>Como Nosso Processo Funciona</strong></td></tr>
				<tr><td style="font-size:12px;">
					Informamos que seu Cadastro já está em nosso sistema à espera de aprovação. Para isso, informamos que é necessário o 
					correto enquadramento do objeto do Contrato Social no CNAE (Classificação Nacional de Atividades Econômicas) e sua 
					comprovação através da documentação obrigatória enviada. 
					
					<br /><br />
					Caso alguma informação ou documento que ainda esteja faltando, 
					entraremos em contato através dos telefones ou e-mails informados no Cadastro para solicitá-los. 
					
					<br /><br />
					Solicitamos o aguardo da confirmação da Aprovação do Cadastro que também será enviada por e-mail.
				</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style="font-size:12px;">Atenciosamente,</td></tr>
				<tr><td style="font-size:12px;">SINDIPROM</td></tr>
			</table>
			</td>
		</tr>
		</table>';
	
	// BUSCA O EMAIL PARA O QUAL A MENSAGEM SERÁ ENVIADA
	//$strEMailDestino =  'tatianaf@gmail.com'; //getVarEntidade($objConn,"email");
	//emailNotify($strEMAILSINDI,getTText("novo_cadastro",C_NONE),$strEMailDestino,CFG_EMAIL_SENDER);
	//emailNotify($strEMAILFILIA,getTText("cadastro_concluido",C_NONE),$strEmail,CFG_EMAIL_SENDER);
	
	// DESTRÓI OBJETO
	$objConn = NULL;
?>
<script type="text/javascript">
alert("Cadastro atualizado com sucesso");
window.location = "STUpdSocioABFM.php"
</script>