<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");

$strmsg = "Os campos abaixos n&atilde;o est&atilde;o preenchidos e/ou estão incorretos:<br><br>";

/*** RECEBE NOME DO CLIENTE ATUAL, PARA SER USADO NO UPLOAD ***/

$objConn = abreDBConn(CFG_DB);  


/*** FOREACH DE TODOS CAMPOS DO SCRIPT ANTERIOR PARA REENVIO NO CASO DE FALHA ***/
$queryString = "?";
foreach($_POST as $var=>$valor){
	if($var != 'dbvar_str_senha' && $var != 'dbvar_str_senha_confirma'){
		$queryString .=  $var."=".$valor."&";
	}
}
$queryString = substr($queryString,0,strlen($queryString)-1);

/*** RECEBIMENTO DE FIELDS DO SCRIPT ANTERIOR ***/
$intCodPJ					= request("cod_pj");
$intCnpj					= request("dbvar_str_cnpj");
$intInscEst					= strtoupper(request("dbvar_str_insc_est"));
$strRazaoSocial				= strtoupper(request("dbvar_str_razao_social"));
$strNomeFantasia			= strtoupper(request("dbvar_str_nome_fantasia"));

$strEmail 		 			= strtoupper(request("dbvar_str_email"));
$strEmail2			  		= strtoupper(request("dbvar_str_email_2"));
$strWebsite					= strtoupper(request("dbvar_str_website"));

$intCep 					= strtoupper(request("dbvar_str_cep")); //Endereço Principal - Recebimento de Dados
$strLogradouro 			   	= strtoupper(request("dbvar_str_logradouro"));
$intNumero 	 				= strtoupper(request("dbvar_str_numero"));
$strComplemento 			= strtoupper(request("dbvar_str_complemento"));
$strBairro 					= strtoupper(request("dbvar_str_bairro"));
$strCidade		 			= strtoupper(request("dbvar_str_cidade"));
$strUF 					    = strtoupper(request("dbvar_str_uf"));
$strPais 					= strtoupper(request("dbvar_str_pais"));
$intTelefone				= strtoupper(request("dbvar_str_telefone"));
$intTelefone2				= strtoupper(request("dbvar_str_telefone_2"));

$strRotuloEntregaCob		= strtoupper(request("dbvar_str_rotulo_entrega")); //Endereço para Cobrança - Recebimento de Dados
$intCepCob					= strtoupper(request("dbvar_str_cep_cob"));
$strLogradouroCob		   	= strtoupper(request("dbvar_str_logradouro_cob"));
$intNumeroCob 	 			= strtoupper(request("dbvar_str_numero_cob"));
$strComplementoCob 			= strtoupper(request("dbvar_str_complemento_cob"));
$strBairroCob 				= strtoupper(request("dbvar_str_bairro_cob"));
$strCidadeCob				= strtoupper(request("dbvar_str_cidade_cob"));
$strUFCob 				    = strtoupper(request("dbvar_str_uf_cob"));
$strPaisCob 				= strtoupper(request("dbvar_str_pais_cob"));
$intTelefoneCob				= strtoupper(request("dbvar_str_telefone_cob"));
$intTelefone2Cob 			= strtoupper(request("dbvar_str_telefone_2_cob"));
$dblCapital	  			    = MoedaToFloat(request("dbvar_num_capital"));

$strImgLogo = (request("var_img_logo") == "") ? "NULL" : "'".request("var_img_logo")."'";
$strUsuario = getSession(CFG_SYSTEM_NAME . "_id_usuario");

/*** DATA DA INSERÇÃO ***/
$strAuxValue = (dDate(CFG_LANG,now(),true));
$strAuxValue = cDate(CFG_LANG, $strAuxValue, true);
$strAuxValue = (($strAuxValue == "") || (!is_date($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = $strAuxValue ;

/*** TESTA SE ALGUM DOS CAMPOS OBRIGATÓRIOS ESTÁ EM BRANCO ***/
if (($strRazaoSocial == "")||($strEmail == "")||
	($strNomeFantasia == "")||(	$strLogradouro == "")||
	($intCep == "")||($strCidade == "")||
	($strBairro == "")||($strUF == "")||
	($strPais == "")||($intNumero == "")||
	($intTelefone == "")){
	if(($strRazaoSocial == "")||($strEmail == "")||($intCnpj == "")||($strNomeFantasia == "")){
		$strmsg .= "<br><b>DADOS DA EMPRESA</b><br>";
	}
	$strmsg   	.= ($strRazaoSocial == "")? "Razão Social<br>":""; 
	$strmsg 	.= ($strNomeFantasia == "")? "Nome Fantasia<br>":"";
	$strmsg 	.= ($strEmail == "")? "Email<br>":"";
	if(($intCep == "")||($strLogradouro == "")||($intNumero == "")||($strBairro == "")||($strCidade == "")||($strUF == "")||($strPais == "")||($intTelefone == "")||($intTelefone2 == "")){
		$strmsg .= "<br><b>ENDEREÇO PRINCIPAL</b><br>";
	}
	$strmsg		.= ($intCep == "")? "CEP<br>":"";
	$strmsg		.= ($strLogradouro == "")? "Logradouro<br>":"";
	$strmsg 	.= ($intNumero == "") ? "Número<br>":"";
	$strmsg		.= ($strBairro == "")? "Bairro<br>":"";
	$strmsg		.= ($strCidade == "")? "Cidade<br>":"";
	$strmsg		.= ($strUF == "")? "UF<br>":"";
	$strmsg 	.= ($strPais == "")? "País<br>":""; 
	$strmsg 	.= ($intTelefone == "")? "Telefone 1<br>":"";
	
	die(mensagem("err_dados_titulo","err_dados_obj_desc",$strmsg,"STUpdCadPJ.php".$queryString." ","erro",1));
}
else {
	$objConn->beginTransaction();
	try{
		/*** INSERE DADOS NA PESSOA JURIDICA  ***/
		$strSQL = "UPDATE cad_pj SET email = '". $strEmail ."',email_extra = '". $strEmail2 ."',img_logo=".$strImgLogo.",
									 website = '". $strWebsite ."',endprin_cep = '". $intCep ."', capital = " . $dblCapital . ",
									 endprin_logradouro='". $strLogradouro ."',endprin_numero= '". $intNumero ."',
									 endprin_complemento='". $strComplemento ."',endprin_bairro='". $strBairro ."', endprin_cidade = '". $strCidade ."', endprin_estado = '". $strUF ."', 
									 endprin_pais = '". $strPais ."', endprin_fone1 = '". $intTelefone ."', 
									 endprin_fone2 = '". $intTelefone2 ."', endcobr_cep = '". $intCepCob ."',
									 endcobr_rotulo='".$strRotuloEntregaCob."',endcobr_logradouro='".$strLogradouroCob."',
									 endcobr_numero='".$intNumeroCob."',endcobr_complemento='".$strComplementoCob."',
									 endcobr_bairro='". $strBairroCob ."', endcobr_cidade='". $strCidadeCob ."', 
									 endcobr_estado='". $strUFCob ."',	endcobr_pais='". $strPaisCob ."',
									 endcobr_fone1='". $intTelefoneCob ."', endcobr_fone2='". $intTelefone2Cob ."',
									 sys_dtt_upd='".$strAuxValue."', sys_usr_upd='".$strUsuario."'
				WHERE cod_pj =".$intCodPJ;
		$objConn->query($strSQL);
		
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	/*** ENVIAR EMAIL ***/
	$strCorpoEmail = '
				<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="-moz-opacity:1.5 !important; z-index:100;">
					 <tr>
						  <td align="left" valign="top"> 
							  <table width="600" cellpadding="0" cellspacing="0">
								<tr><td height="3" colspan="2"></td></tr>	
								<tr>
									<td colspan="3" nowrap="nowrap"></td>
								</tr>
								<tr><td height="5" colspan="2"></td></tr>			
								<tr bgcolor="#FFFFFF">
									<td width="165" nowrap="nowrap" align="right"><b>Razão Social: </b></td>
								  <td width="433"><div style="padding-left:10px">'. $strRazaoSocial .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td width="165" nowrap="nowrap" align="right"><b>Nome Fantasia: </b></td>
									<td><div style="padding-left:10px">'. $strNomeFantasia .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td width="165" nowrap="nowrap" align="right"><b>Capital: </b></td>
									<td><div style="padding-left:10px">'. MoedaToFloat($dblCapital) .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right"><b>CNPJ: </b></td>
									<td><div style="padding-left:10px">'. $intCnpj .' </div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right"><b>Inscrição Estadual: </b></td>
									<td><div style="padding-left:10px">'. $intInscEst .' </div></td>
								</tr>
									<tr bgcolor="#FAFAFA">
									<td align="right" ><b>E-Mail: </b></td>
									<td><div style="padding-left:10px">'. $strEmail .'</div></td>
								</tr>
								<tr>
									<td align="right" ><b>E-Mail Extra: </b></td>
									<td><div style="padding-left:10px">'. $strEmail2 .'</div></td>
								</tr>
								<tr>
									<td align="right" ><b>Website: </b></td>
									<td><div style="padding-left:10px">'. $strWebsite .'</div></td>
								</tr>
								<tr>
									<td colspan="2" height="40"><div style="padding-left:100px;" class="destaque_gde"><strong>DOCUMENTO DIGITALIZADOS</strong></div></td>
								</tr>';
								 /*?><tr bgcolor="#FAFAFA">
									<td align="right"><b>Documento: </b></td>
									<td><div style="padding-left:10px">'.$_FILES['var_img_logo']['name'].'</div></td>
								</tr><?php */
$strCorpoEmail .= '				<tr><td colspan="3" height="40"><div style="padding-left:100px;" class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></div></td></tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>CEP: </b></td>
									<td><div style="padding-left:10px">'. $intCep .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Endereço: </b></td>
									<td><div style="padding-left:10px">'. $strLogradouro .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Número: </b></td>
									<td><div style="padding-left:10px">'. $intNumero .'</td>
								</tr>
								<tr>
									<td align="right"><b>Complemento: </b></td>
									<td><div style="padding-left:10px">'. $strComplemento .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Bairro: </b></td>
									<td><div style="padding-left:10px">'. $strBairro .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Cidade: </b></td>
									<td><div style="padding-left:10px">'. $strCidade .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Estado: </b></td>
									<td><div style="padding-left:10px">'. $strUF .'</div>
									</td>
								</tr>					
								<tr>
									<td align="right"><b>País: </b></td>
									<td><div style="padding-left:10px">'. $strPais .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Telefone: </b></td>
									<td><div style="padding-left:10px">'. $intTelefone .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Telefone 2: </b></td>
									<td><div style="padding-left:10px">'. $intTelefone2 .'</div></td>
								</tr>
								<tr><td colspan="3" height="40"><div style="padding-left:100px;" class="destaque_gde"><strong>ENDEREÇO PARA COBRANÇA</strong></div></td></tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Rótulo de Entrega: </b></td>
									<td><div style="padding-left:10px">'. $strRotuloEntregaCob .'</div></td>
								</tr><tr bgcolor="#FAFAFA">
									<td align="right" ><b>CEP: </b></td>
									<td><div style="padding-left:10px">'. $intCepCob .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Endereço: </b></td>
									<td><div style="padding-left:10px">'. $strLogradouroCob .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Número: </b></td>
									<td><div style="padding-left:10px">'. $intNumeroCob .'</td>
								</tr>
								<tr>
									<td align="right"><b>Complemento: </b></td>
									<td><div style="padding-left:10px">'. $strComplementoCob .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Bairro: </b></td>
									<td><div style="padding-left:10px">'. $strBairroCob .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Cidade: </b></td>
									<td><div style="padding-left:10px">'. $strCidadeCob .'</div></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right" ><b>Estado: </b></td>
									<td><div style="padding-left:10px">'. $strUFCob .'</div>
									</td>
								</tr>					
								<tr>
									<td align="right"><b>País: </b></td>
									<td><div style="padding-left:10px">'. $strPaisCob .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Telefone: </b></td>
									<td><div style="padding-left:10px">'. $intTelefoneCob .'</div></td>
								</tr>
								<tr>
									<td align="right"><b>Telefone 2: </b></td>
									<td><div style="padding-left:10px">'. $intTelefone2Cob .'</div></td>
								</tr>							
								<tr><td colspan="3" height="40"><div style="padding-left:100px;" class="destaque_gde"><strong>LOGIN</strong></div></td></tr>							
								<tr bgcolor="#FAFAFA">
									<td align="right"><b>User ID: </b></td>
									<td> <div style="padding-left:7px">
										<table align="left">
											<tr><td>'. $strUsuario .'</td></tr>
										</table>
										</div>
									</td>
								</tr>
							</table>';
	emailNotify($strCorpoEmail, getTText("alteracao_cadastro",C_NONE), "", CFG_EMAIL_SENDER);
	redirect("../modulo_PainelPJ/STindex.php");
}
 
$objConn = NULL;
?>