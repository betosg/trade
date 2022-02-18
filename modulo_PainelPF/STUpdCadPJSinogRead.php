<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$idCadPJ = getsession(CFG_SYSTEM_NAME."_pj_selec_codigo");

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

//Cores linhas
$strBGColor = CL_CORLINHA_2;

if($idCadPJ != ""){
	//Inicia objeto para manipulação do banco
	$objConn = abreDBConn(CFG_DB);
	
	$strSQL = " SELECT cnpj, insc_est, razao_social, nome_fantasia, email, capital,
					   email_extra, website, arquivo_1, img_logo, endprin_cep, 
					   endprin_logradouro, endprin_numero, endprin_complemento,
					   endprin_bairro, endprin_cidade, endprin_estado, endprin_pais,
					   endprin_fone1, endprin_fone2, endcobr_cep, endcobr_rotulo,	   
					   endcobr_logradouro, endcobr_numero, endcobr_complemento,
					   endcobr_bairro, endcobr_cidade, endcobr_estado,	endcobr_pais,
					   endcobr_fone1, endcobr_fone2, cod_pj
				FROM cad_pj
				WHERE cod_pj =".$idCadPJ;
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
}
else{
	mensagem("Aviso: Usuário sem vinculo com alguma empresa","O seu cadastro no momento não está vinculado a nenhuma empresa.<br>","","","standard",1);
	die();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<script>
function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}
		<!--
			function cancelar(){document.location = "login.php";}
			
			function copiaCamposEndereco(){
				document.getElementById('dbvar_str_logradouro_cob').value =	 document.getElementById('dbvar_str_logradouro').value;
				document.getElementById('dbvar_str_numero_cob').value = document.getElementById('dbvar_str_numero').value;
				document.getElementById('dbvar_str_complemento_cob').value = document.getElementById('dbvar_str_complemento').value;
				document.getElementById('dbvar_str_cidade_cob').value = document.getElementById('dbvar_str_cidade').value;
				document.getElementById('dbvar_str_bairro_cob').value = document.getElementById('dbvar_str_bairro').value;
				document.getElementById('dbvar_str_uf_cob').value = document.getElementById('dbvar_str_uf').value;
				document.getElementById('dbvar_str_cep_cob').value = document.getElementById('dbvar_str_cep').value;
				document.getElementById('dbvar_str_pais_cob').value = document.getElementById('dbvar_str_pais').value;
				document.getElementById('dbvar_str_telefone_cob').value = document.getElementById('dbvar_str_telefone').value;
				document.getElementById('dbvar_str_telefone_2_cob').value = document.getElementById('dbvar_str_telefone_2').value;
				document.getElementById('dbvar_str_rotulo_entrega').focus();
			}
			
			
			function validaDat(campo,valor) {
				var date=valor;
				var ardt=new Array;
				var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
				ardt=date.split("/");
				erro=false;
				if ( date.search(ExpReg)==-1){
					erro = true;
					}
				else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30))
					erro = true;
				else if ( ardt[1]==2) {
					if ((ardt[0]>28)&&((ardt[2]%4)!=0))
						erro = true;
					if ((ardt[0]>29)&&((ardt[2]%4)==0))
						erro = true;
				}
				if (erro) {
					alert("\"" + valor + "\" não é uma data válida!!!");
					//campo.focus();
					campo.value = "";
					return false;
				}
				return true;
			}
  			
			-->
		  </script>

	</head>
	<body bgcolor="#F5F5F5"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  	<!-- <tr><td valign="top" height="35%"><img src="../img/system_logo.gif" border="0" hspace="10" vspace="10"><td></tr> -->
	  		<tr>
				<td align="center" valign="middle">
				<?php athBeginFloatingBox("600","","VISUALIZAÇÃO DE DADOS FILIADO - EMPRESA",CL_CORBAR_GLASS_1); ?>
					<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
					<form name="formeditor" action="STUpdCadPJexec.php" method="post" enctype="multipart/form-data">
					<input type="hidden" id="var_db" name="var_db" value="<?php echo(CFG_DB);?>" >
					<input type="hidden" id="dbvar_str_cnpj" name="dbvar_str_cnpj" value="<?php echo getValue($objRS,"cnpj");?>" >
					<input type="hidden" id="cod_pj" name="cod_pj" value="<?php echo getValue($objRS,"cod_pj");?>" >
						<tr>
						 	<td align="center" valign="top"> 
								<table width="500" cellpadding="3" cellspacing="0">
									<tr>
										<td height="3" colspan="2"></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>	
									<tr>
										<td width="1%" colspan="3" nowrap="nowrap">
											<div style="padding-left:10px;">
												<strong><?php echo(getTText("alterar_dados",C_NONE));?></strong>
											</div>
										</td>
									</tr>
									<tr>
										<td align="left" valign="bottom" colspan="2" height="40">
											<div style="padding-left:130px;" class="destaque_gde">
												<strong>DADOS DA EMPRESA</strong>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2" height="2" background="../img/line_dialog.jpg">
										</td>
									</tr>
									<tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF">
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right">
											<b>*CNPJ: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<b><?php echo getValue($objRS,"cnpj"); ?></b>
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right">
											<b>Inscrição Estadual: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td> <?php echo getValue($objRS,"insc_est"); ?></td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="120" nowrap="nowrap" align="right">
											<b>*Razão Social: </b>
										</td>
										<td>
											<div style="padding-left:10px">											
											<?php echo getValue($objRS,"razao_social"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="120" nowrap="nowrap" align="right">
											<b>*Nome Fantasia: </b>
										</td>
										<td>
											<div style="padding-left:10px">
											
												<?php echo getValue($objRS,"nome_fantasia"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>Capital Social: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo FloatToMoeda(getValue($objRS,"capital")); ?>
											</div>
										</td>
									</tr>
                                    <tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>*E-Mail: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"email"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td align="right" >
											<b>E-Mail Extra: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"email_extra"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>Website: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"website"); ?>
											</div>
										</td>
									</tr>
									<tr>
										<td>&nbsp;
											
										</td>
									</tr>
									<tr>
										<td align="left" valign="bottom"  colspan="3" height="40">
											<div style="padding-left:130px;" class="destaque_gde">
												<strong>ENDEREÇO PRINCIPAL</strong>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2" height="2" background="../img/line_dialog.jpg">
										</td>
									</tr>
									<tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF">
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>*CEP: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endprin_cep"); ?>
														</td>
														<td>
															<!--div style="cursor: pointer;">
																<img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="Javascript:ajaxBuscaCEP('dbvar_str_cep','dbvar_str_logradouro','dbvar_str_bairro','dbvar_str_cidade','dbvar_str_uf','dbvar_str_numero','loader_cep');" style="cursor:pointer" />
																&nbsp;<span id="loader_cep"></span>
															</div-->
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td align="right">
											<b>*Logradouro: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endprin_logradouro"); ?>
														</td>
														<td>
															<!--div class="comment_peq">(Ex: Rua, Avenida, etc.)</div-->
														</td>
													</tr>
												</table>
											</div>
										</td>	
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>*Num. / Compl.: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table>
													<tr>
														<td>
															<?php echo getValue($objRS,"endprin_numero"); ?>
														</td>
														<td>
															<div style="padding-left:8px">
																<?php echo getValue($objRS,"endprin_complemento"); ?>
															</div>
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td align="right" >
											<b>*Bairro: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"endprin_bairro"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="FAFAFA">
										<td align="right">
											<b>*Cidade: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endprin_cidade"); ?>
														</td>
														<td align="right" style="padding-left: 10px;" >
															<b>*UF: </b>
														</td>
														<td>
															<div style="padding-left:5px;">
																
																	<?php echo(getValue($objRS,"endprin_estado")); ?>
											 					
															</div>
														</td>
														<td align="right" style="padding-left: 10px;" ><b>BRASIL</b></td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<!-- Forçamos BRASIL -->
									<input type="hidden" id="dbvar_str_pais" name="dbvar_str_pais" value="BRASIL" >
									<!-- 
									<tr>
										<td align="right">
											<b>*País: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<input type="text" name="dbvar_str_pais" id="dbvar_str_pais" size="15" value="<?php $strPaisRequest = request('dbvar_str_pais'); echo ($strPaisRequest == "") ? "Brasil" : $strPaisRequest;?>">
											</div>
										</td>
									</tr>
									-->
									<tr>
										<td align="right">
											<b>*Telefone 1: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"endprin_fone1"); ?>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right">
											<b>Telefone 2: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"endprin_fone2"); ?>
											</div>
										</td>
									</tr>
									<tr>
										<td>&nbsp;
											
										</td>
									</tr>		
									<tr>
										<td align="left" valign="bottom"  colspan="3" height="40">
											<div style="padding-left:130px;" class="destaque_gde">
												<strong>ENDEREÇO PARA COBRANÇA</strong>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2" height="2" background="../img/line_dialog.jpg">
										</td>
									</tr>
									<tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF">
										</td>
									</tr>
									<!--tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF" valign="top">
											<div class="comment_peq" style="padding: 0px 100px 0px 130px;">Obs: Caso sua empresa possua um contador que realize pagamentos, insira o endereço abaixo. Além disso, preencha o campo 'Rotulo de Entrega' com o destinatário da cobrança.
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF" valign="top">
											<div class="comment_peq" style="padding: 0px 100px 0px 130px;">Obs: Se seu endereço de cobrança deve ser o mesmo endereço principal, clique <div onClick="Javascript:copiaCamposEndereco();" style="display: inline; cursor:pointer;"><u>aqui</u></div>
											</div>
										</td>
									</tr-->
									<tr>
										<td align="right" >
											<b>CEP: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endcobr_cep"); ?>
														</td>
														<td>
															<!--div style="cursor: pointer;">
																<img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="Javascript:ajaxBuscaCEP('dbvar_str_cep_cob','dbvar_str_logradouro_cob','dbvar_str_bairro_cob','dbvar_str_cidade_cob','dbvar_str_uf_cob','dbvar_str_numero_cob','loader_cep_cobr');" style="cursor:pointer" />
																&nbsp;<span id="loader_cep_cobr"></span>
															</div-->
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>Rótulo de Entrega: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endcobr_rotulo"); ?>
														</td>
														<!--td>
															<div class="comment_peq">(Ex: Empresa XYZ Ltda)</div>
														</td-->
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td align="right">
											<b>Logradouro: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endcobr_logradouro"); ?>
														</td>
														<td>
															<!--div class="comment_peq">(Ex: Rua, Avenida, etc.)</div-->
														</td>
													</tr>
												</table>
											</div>
										</td>	
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" >
											<b>Num. / Compl.: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table>
													<tr>
														<td>
															<?php echo getValue($objRS,"endcobr_numero"); ?>
														</td>
														<td>
															<div style="padding-left:8px">
																<?php echo getValue($objRS,"endcobr_complemento"); ?>
															</div>
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td align="right" >
											<b>Bairro: </b>
										</td>
										<td>
											<div style="padding-left:10px"><?php echo getValue($objRS,"endcobr_bairro"); ?>	</div>
										</td>
									</tr>
									<tr bgcolor="FAFAFA">
										<td align="right">
											<b>Cidade: </b>
										</td>
										<td>
											<div style="padding-left:8px">
												<table align="left">
													<tr>
														<td>
															<?php echo getValue($objRS,"endcobr_cidade"); ?>
														</td>
														<td align="right" style="padding-left: 10px;" >
															<b>UF: </b>
														</td>
														<td>
															<div style="padding-left:5px;">
																<?php echo(getValue($objRS,"endcobr_estado")); ?>											 					
															</div>
														</td>
														<td align="right" style="padding-left: 10px;" ><b>BRASIL</b></td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<!-- Forçamos BRASIL -->
									<input type="hidden" id="dbvar_str_pais_cob" name="dbvar_str_pais_cob" value="BRASIL" >
									<!--
									<tr>
										<td align="right">
											<b>País: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<input type="text" name="dbvar_str_pais_cob" id="dbvar_str_pais_cob" size="15" value="<?php $strPaisRequestCob = request('dbvar_str_pais_cob'); echo ($strPaisRequestCob == "") ? "Brasil" : $strPaisRequestCob; ?>">
											</div>
										</td>
									</tr>
									-->
									<tr>
										<td align="right">
											<b>Telefone 1: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"endcobr_fone1"); ?>
											</div>
										</td>
									</tr>
									<tr  bgcolor="#FAFAFA">
										<td align="right">
											<b>Telefone 2: </b>
										</td>
										<td>
											<div style="padding-left:10px">
												<?php echo getValue($objRS,"endcobr_fone2"); ?>
											</div>	
										</td>
									</tr>
									<tr>
										<td>&nbsp;
										</td>
									</tr>													
									<!--tr>
										<td align="left" valign="bottom" colspan="2" height="40">
											<div style="padding-left:130px;" class="destaque_gde">
												<strong>LOGOTIPO DA EMPRESA</strong>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2" height="2" background="../img/line_dialog.jpg"></td>
									</tr>
									<tr>
										<td colspan="2" height="5" bgcolor="#FFFFFF"></td>
									</tr>
									<?php if (getValue($objRS,"img_logo") != "") { ?>
										<tr>
											<td colspan="2" bgcolor="#FFFFFF" align="center" valign="top"><img src="../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/imgdin/<?php echo getValue($objRS,"img_logo"); ?>" border="0"></td>
										</tr>
										<tr>
											<td colspan="2" height="5" bgcolor="#FFFFFF"></td>
										</tr>
									<?php } ?>
									<tr>
										<td colspan="2" height="10" bgcolor="#FFFFFF" valign="top">
											<div class="comment_peq" style="padding: 0px 100px 0px 130px;">Obs: O arquivo deverá ser uma imagem com extensões jpg, bmp, gif ou png. Preferencialmente no formato 224x120 e com fundo branco</div>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" valign="top"><b><?php echo(getTText("imagem",C_NONE)); ?>:</b></td>
										<td>
										  <div style="padding-left:10px">
												<input type="text" name="var_img_logo" id="var_img_logo" size="50"  value="<?php echo getValue($objRS,"img_logo"); ?>"readonly="true" title="Foto">
												<input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor','var_img_logo','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/imgdin/','','');"><br />
											  &nbsp;<span onClick="document.getElementById('var_img_logo').value='';" style="cursor:pointer;"><b>Clique aqui</b></span> para limpar o campo de imagem.<span class="comment_med">&nbsp;</span>											</div>
										</td>
									</tr-->
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td height="1" colspan="2" bgcolor="#DBDBDB"></td>
									</tr>
									<tr>
										<td height="5" colspan="2"></td>
									</tr>
									<!--tr> 
								  		<td colspan="2">
											<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
									  			<tr>
													<td colspan="2" align="right" style="padding-bottom:10px;">
														<button onClick="document.formeditor.submit();">
															<?php echo(getTText("OK",C_NONE)); ?>
														</button>	
														<button onClick="javascript:window.location='../modulo_PainelPJ/STindex.php';return false">
															<?php echo( getTText("Cancelar",C_NONE)); ?>
														</button>
									  				</td>
									  			</tr>
											</table>
								  		</td>
									</tr-->
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
<?php 
	$objConn = NULL;
?>