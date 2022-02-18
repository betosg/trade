<?php 
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/STathutils.php");

	/// ABERTURA DE CONEXÃO COM DB
	$strDB 	 = request("var_db");
	$objConn = abreDBConn($strDB);
	
	// MENSAGEM DE ERRO, PARA CAMPOS OBRIGATÓRIOS
	$strMSG = "";

	// FOREACH DE TODOS CAMPOS DO SCRIPT 
	// ANTERIOR PARA REENVIO NO CASO DE FALHA
	$chrConcat = "?";
	foreach($_POST as $var=>$valor){
		if($var != 'dbvar_str_senha' && $var != 'dbvar_str_senha_confirma'){
			$chrConcat .=  $var."=".$valor."&";
		}
	}
	$chrConcat = substr($queryString,0,strlen($queryString)-1);
	//Debug - echo $queryString; die();

	// REQUESTS
	// REQUEST - DADOS DA EMPRESA
	$intCnpj				= request("dbvar_str_cnpj");
	$intInscEst				= strtoupper(request("dbvar_str_insc_est"));
	$strRazaoSocial			= strtoupper(request("dbvar_str_razao_social"));
	$strNomeFantasia		= strtoupper(request("dbvar_str_nome_fantasia"));
	$strEmail 		 		= strtoupper(request("dbvar_str_email"));
	$strEmail2			  	= strtoupper(request("dbvar_str_email_2"));
	$strWebsite				= strtoupper(request("dbvar_str_website"));
	$strCapital             = strtoupper(request("dbvar_str_capital"));
	$strDataFundacao        = (request("dbvar_str_data_fundacao") == "") ? 'NULL' : "'".request("dbvar_str_data_fundacao")."'";
	// REQUEST - CNAES
	$intCodCnaeSecao		= (request("var_num_cad_cnae_divisao__cod_cnae_secao") 		 == "") ? 'NULL' : request("var_num_cad_cnae_divisao__cod_cnae_secao");
	$intCodCnaeDivisao 		= (request("var_num_cad_cnae_grupo__cod_cnae_divisao") 	 	 == "") ? 'NULL' : request("var_num_cad_cnae_grupo__cod_cnae_divisao");
	$intCodCnaeGrupo		= (request("var_num_cad_cnae_classe__cod_cnae_grupo")	 	 == "") ? 'NULL' : request("var_num_cad_cnae_classe__cod_cnae_grupo");
	$intCodCnaeClasse		= (request("var_num_cad_cnae_classe__cod_cnae_classe")  	 == "") ? 'NULL' : request("var_num_cad_cnae_classe__cod_cnae_classe");
	$intCodCnaeSubClasse 	= (request("var_num_cad_cnae_subclasse__cod_cnae_subclasse") == "") ? 'NULL' : request("var_num_cad_cnae_subclasse__cod_cnae_subclasse");
	// REQUEST - ENDEREÇO PRINCIPAL
	$intCep 				= strtoupper(request("dbvar_str_cep"));
	$strLogradouro 			= strtoupper(request("dbvar_str_logradouro"));
	$intNumero 	 			= strtoupper(request("dbvar_str_numero"));
	$strComplemento 		= strtoupper(request("dbvar_str_complemento"));
	$strBairro 				= strtoupper(request("dbvar_str_bairro"));
	$strCidade		 		= strtoupper(request("dbvar_str_cidade"));
	$strUF 					= strtoupper(request("dbvar_str_uf"));
	$strPais 				= strtoupper(request("dbvar_str_pais"));
	$intTelefone			= strtoupper(request("dbvar_str_telefone"));
	$intTelefone2			= strtoupper(request("dbvar_str_telefone_2"));
	// REQUEST - ENDEREÇO DE COBRANÇA
	$intCodContabil			= (request("dbvar_num_cod_pj_contabil") == "") ? 'NULL' : request("dbvar_num_cod_pj_contabil");
	$strRotuloEntregaCob	= strtoupper(request("dbvar_str_endcobr_rotulo_000")); 
	$intCepCob				= strtoupper(request("dbvar_num_endcobr_cep_000"));
	$strLogradouroCob		= strtoupper(request("dbvar_str_endcobr_logradouro_000"));
	$intNumeroCob 	 		= strtoupper(request("dbvar_str_endcobr_numero_000"));
	$strComplementoCob 		= strtoupper(request("dbvar_str_endcobr_complemento_000"));
	$strBairroCob 			= strtoupper(request("dbvar_str_endcobr_bairro_000"));
	$strCidadeCob			= strtoupper(request("dbvar_str_endcobr_cidade_000"));
	$strUFCob 				= strtoupper(request("dbvar_str_endcobr_estado_000"));
	$strPaisCob 			= strtoupper(request("dbvar_str_endcobr_pais_000"));
	$intTelefoneCob			= strtoupper(request("dbvar_str_endcobr_fone1_000"));
	$intTelefone2Cob 		= strtoupper(request("dbvar_str_endcobr_fone2_000"));
	$strEmailCobr           = strtoupper(request("dbvar_str_endcobr_email_000"));
	$strContatoCobr         = strtoupper(request("dbvar_str_endcobr_contato_000"));
	// REQUEST - DOCUMENTOS DIGITALIZADOS
	$strArquivo1			= request("dbvar_str_arquivo_1");
	$strArquivo2			= request("dbvar_str_arquivo_2");
	$strArquivo3			= request("dbvar_str_arquivo_3");
	// REQUEST - DADOS DE LOGIN
	$strUsuario 			= strtolower(request("dbvar_str_usuario"));
	$strSenha 				= request("dbvar_str_senha");
	$strSenhaConfirma 		= request("dbvar_str_senha_confirma");

	// VERIFICA A EXISTENCIA DE UM MESMO CNPJ - 
	// Por Default, não é possível cadastrar duas 
	// PJs com um mesmo CNPJ; Caso False, verifica 
	// se há mais campos em branco - CONSISTÊNCIA
	try{	
		$strSQL = "SELECT cod_pj FROM cad_pj WHERE cnpj = '".$intCnpj."'";
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	} catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	if (getValue($objRS,"cod_pj") != ""){
		mensagem("err_cnpj_ja_cadastrado_titulo","err_cnpj_ja_cadastrado_desc","","STinsformlogin.php".$queryString."","erro",1);
		die();
	}

	// VERIFICA A EXISTENCIA DE UM MESMO ID
	try{
		$strSQL = "SELECT id_usuario FROM sys_usuario WHERE id_usuario = '".$strUsuario."'";
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	} catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	if (getValue($objRS,"id_usuario") != ""){
		mensagem("err_dados_titulo","err_dados_obj_desc","Já existe um usuário com este User ID cadastrado","STinsformlogin.php".$queryString."","erro",1);
		die();
	}

	// CONSISTÊNCIA PARA CAMPOS OBRIGATÓRIOS
	if(($strRazaoSocial == "")||($strEmail == "")||($intCnpj == "")||($strNomeFantasia == "")){
		$strMSG .= "<br><b>DADOS DA EMPRESA</b><br>";
	}
	$strMSG   	.= ($strRazaoSocial 	== "") ? "Razão Social<br>"		:""; 
	$strMSG 	.= ($strNomeFantasia	== "") ? "Nome Fantasia<br>"	:"";
	$strMSG     .= ($intCnpj 			== "") ? "CNPJ<br>"				:""; 
	$strMSG 	.= ($strEmail 			== "") ? "Email<br>"			:"";
	
	if(($intCep == "")||($strLogradouro == "")||($intNumero == "")||($strBairro == "")||($strCidade == "")||($strUF == "")||($strPais == "")||($intTelefone == "")){
		$strMSG .= "<br><b>ENDEREÇO PRINCIPAL</b><br>";
	}
	$strMSG		.= ($intCep 			== "") ? "CEP<br>"				:"";
	$strMSG		.= ($strLogradouro 		== "") ? "Logradouro<br>"		:"";
	$strMSG 	.= ($intNumero 			== "") ? "Número<br>"			:"";
	$strMSG		.= ($strBairro 			== "") ? "Bairro<br>"			:"";
	$strMSG		.= ($strCidade 			== "") ? "Cidade<br>"			:"";
	$strMSG		.= ($strUF 				== "") ? "UF<br>"				:"";
	$strMSG 	.= ($strPais 			== "") ? "País<br>"				:""; 
	$strMSG 	.= ($intTelefone 		== "") ? "Telefone 1<br>"		:"";
	
	if($strArquivo1 == ""){
		$strMSG .= "<br><b>DOCUMENTOS DIGITALIZADOS</b><br>";
	}
	$strMSG 	.= ($strArquivo1 		== "") ? "Documento Um (1)<br>" :""; 
	
	if(($strUsuario == "")||($strSenha == "")||($strSenhaConfirma == "")||($strSenha != $strSenhaConfirma)){
		$strMSG .= "<br><b>LOGIN</b><br>";
	}
	$strMSG 	.= ($strUsuario 		== "") ? "Usuário<br>"			:""; 
	$strMSG 	.= (($strSenha != $strSenhaConfirma)||($strSenha == "")||($strSenhaConfirma == "")) ? "Senha<br>" :"";  
	
	if($strMSG != ""){
		mensagem("err_dados_titulo","err_dados_obj_desc","Os campos abaixos n&atilde;o est&atilde;o preenchidos e/ou estão incorretos:<br><br>".$strMSG,"STinsformlogin.php".$chrConcat,"erro",1);
		die();
	}
	
	// INICIALIZA A TRANSAÇÃO PARA INSERÇÃO
	// DE PJ E SEU RESPECTIVO USUÁRIO, ETC
	$objConn->beginTransaction();
	try{
		// INSERE PESSOA JURÍDICA
		$strSQL = "
			INSERT INTO cad_PJ (
				  razao_social 
				, nome_fantasia 
				, cnpj
				, insc_est
				, capital
				, dtt_fundacao
				, email
				, email_extra
				, website
				, cod_cnae_n1
				, cod_cnae_n2
				, cod_cnae_n3
				, cod_cnae_n4
				, cod_cnae_n5
				, arquivo_1
				, arquivo_2
				, arquivo_3 
				, endprin_cep 
				, endprin_logradouro
				, endprin_numero
				, endprin_complemento
				, endprin_bairro
				, endprin_cidade
				, endprin_estado
				, endprin_pais
				, endprin_fone1
				, endprin_fone2
				, cod_pj_contabil
				, endcobr_cep
				, endcobr_rotulo
				, endcobr_email
				, endcobr_contato
				, endcobr_logradouro
				, endcobr_numero
				, endcobr_complemento
				, endcobr_bairro
				, endcobr_cidade
				, endcobr_estado
				, endcobr_pais
				, endcobr_fone1
				, endcobr_fone2
				, sys_usr_ins 
				, sys_dtt_ins
			) VALUES (
			  	  '".$strRazaoSocial."'
				, '".$strNomeFantasia."'
				, '".$intCnpj."'
				, '".$intInscEst."'
				, '".$strCapital."'
				,  ".$strDataFundacao."
				, '".$strEmail."'
				, '".$strEmail2."'
				, '".$strWebsite."'
				,  ".$intCodCnaeSecao."
				,  ".$intCodCnaeDivisao. "
				,  ".$intCodCnaeGrupo."
				,  ".$intCodCnaeClasse."
				,  ".$intCodCnaeSubClasse."
				, '".$strArquivo1."'
				, '".$strArquivo2."'
				, '".$strArquivo3."'
				, '".$intCep."'
				, '".$strLogradouro."'
				, '".$intNumero."'
				, '".$strComplemento."'
				, '".$strBairro."'
				, '".$strCidade."'
				, '".$strUF."'
				, '".$strPais."'
				, '".$intTelefone."'
				, '".$intTelefone2."'
				,  ".$intCodContabil."
				, '".$intCepCob."'
				, '".$strRotuloEntregaCob."'
				, '".$strEmailCobr."'
				, '".$strContatoCobr."'
				, '".$strLogradouroCob."'
				, '".$intNumeroCob."'
				, '".$strComplementoCob."'
				, '".$strBairroCob."'
				, '".$strCidadeCob."'
				, '".$strUFCob."
				, '".$strPaisCob."'
				, '".$intTelefoneCob."'
				, '".$intTelefone2Cob."'
				, '".$strUsuario."'
				, '".cDate(CFG_LANG,now(),true)."' 
			)";
		$objConn->query($strSQL);
		
		// LOCALIZA O ÚLTIMO CODIGO DE PJ INSERIDO
		$strSQL = "SELECT currval('cad_pj_cod_pj_seq') AS cod_pj FROM cad_PJ ";
		$objResult = $objConn->query($strSQL2);
		$objRS 	   = $objResult->fetch();
		if (getValue($objRS,"cod_pj") == ""){
			mensagem("err_sql_titulo","err_sql_desc","Erro ao buscar dados","","erro",1);
			die();	
		} 
					
		// INSERE USUÁRIO DA PESSOA JURIDICA
		$strSQL = "
			INSERT INTO sys_usuario ( 
				  id_usuario, 
				, grp_user, 
				, codigo, 
				, tipo, 
				, nome, 
				, senha, 
				, email, 
				, lang, 
				, dir_default, 
				, sys_dtt_ins,
				, oculto 
			) VALUES (
				  '".$strUsuario."'
				, 'PRE_CADASTRO'
				,  ".getValue($objRS,"cod_pj")."
				, 'cad_pj'
				, '".$strRazaoSocial."'
				, '".md5($strSenha)."'
				, '".$strEmail."'
				, 'ptb'
				, '../modulo_PainelPJ/STManutencao.php'
				, '".$strAuxValue."'
				, false
			)";
		$objConn->query($strSQL);
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	// ENVIAO DE EMAIL CONFIRMANDO CADASTRO
	$strCorpoEmail = '
		<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="-moz-opacity:1.5 !important; z-index:100;">
		<tr>
			<td align="left" valign="top"> 
			<table width="600" cellpadding="0" cellspacing="0">
				<tr><td height="3" colspan="2"></td></tr>	
				<tr><td height="3" colspan="2"></td></tr>
				<tr><td height="5" colspan="2"></td></tr>			
				<tr bgcolor="#FFFFFF">
					<td width="30%" nowrap="nowrap" align="right"><b>Razão Social:</b></td>
				  	<td width="70%">'.$strRazaoSocial.'</div></td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td nowrap="nowrap" align="right"><b>Nome Fantasia:</b></td>
					<td>'.$strNomeFantasia.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>CNPJ:</b></td>
					<td>'.$intCnpj.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Inscrição Estadual:</b></td>
					<td>'.$intInscEst.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>E-Mail:</b></td>
					<td>'.$strEmail.'</td>
				</tr>
				<tr>
					<td align="right"><b>E-Mail Extra:</b></td>
					<td>'.$strEmail2.'</td>
				</tr>
				<tr>
					<td align="right"><b>Website:</b></td>
					<td>'.$strWebsite.'</td>
				</tr>
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>DOCUMENTO DIGITALIZADOS</strong></td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Documento Um:</b></td>
					<td>'.$strArquivo1.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Documento Dois:</b></td>
					<td>'.$strArquivo2.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Documento Três:</b></td>
					<td>'.$strArquivo3.'</td>
				</tr>
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>CEP:</b></td>
					<td>'.$intCep.'</td>
				</tr>
				<tr>
					<td align="right"><b>Endereço:</b></td>
					<td>'.$strLogradouro.'</div></td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Número:</b></td>
					<td>'.$intNumero.'</td>
				</tr>
				<tr>
					<td align="right"><b>Complemento:</b></td>
					<td>'.$strComplemento.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Bairro:</b></td>
					<td>'.$strBairro.'</td>
				</tr>
				<tr>
					<td align="right"><b>Cidade:</b></td>
					<td>'.$strCidade.'</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>Estado:</b></td>
					<td>'.$strUF.'</td>
				</tr>					
				<tr>
					<td align="right"><b>País:</b></td>
					<td>'.$strPais.'</td>
				</tr>
				<tr>
					<td align="right"><b>Telefone:</b></td>
					<td>'.$intTelefone.'</td>
				</tr>
				<tr>
					<td align="right"><b>Telefone 2:</b></td>
					<td>'.$intTelefone2.'</td>
				</tr>
				<tr>
					<td></td>
					<td class="destaque_gde"><strong>LOGIN</strong></td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right"><b>User ID:</b></td>
					<td>'.$strUsuario.'</td>
				</tr>
			</table>
			</td>
		</tr>
		</table>';
	
	// Não deve mandar email para Sindiprom e Ubrafe, pedido do Cabrera no chamado 22293
	// BUSCA O EMAIL PARA O QUAL A MENSAGEM SERÁ ENVIADA
	if (getsession(CFG_SYSTEM_NAME."_dir_cliente") == "sindieventos") {
		$strEMailDestino = getVarEntidade($objConn,"email");
		emailNotify($strCorpoEmail,getTText("novo_cadastro",C_NONE),$strEMailDestino,CFG_EMAIL_SENDER);
	}
	
	// DESTRÓI OBJETO
	$objConn = NULL;
	
	// REDIRECT PARA A PÁGINA DE MENSAGEM
	redirect("../modulo_Principal/login.php");

?>