<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");


// requests STs
// Recebe banco como parametro para poder funcionar
// mesmo sendo chamada de 'fora'
$strDBConnect = request("var_db");
$intNum       = request("var_num");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página - cod_credencial
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

//Cores linhas
$strBGColor = CL_CORLINHA_2;
//Inicia objeto para manipulação do banco
$objConn = abreDBConn($strDBConnect);

// campo venha em branco
	if($intNum == ""){
		echo "
			<center>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
					<tr>
					<td align=\"center\" valign=\"middle\" width=\"100%\">";
					mensagem("err_dados_titulo","err_sql_desc_card",getTText("cpf_matricula_null",C_NONE),"STvalidacard.php?var_db=".$strDBConnect,"aviso",1);
		echo "		</td>
					</tr>
				</table>
			</center>";
		die();
	}
	// caso passe pelas validações anteriores, certo que opcao e valor estão preenchidos.
	
	// busca uma ocorrencia de uma PF cadastrada. É possível que umaPF esteja cadastrada 
	// mas não esteja com uma carteirinha impressa. Dessa forma, quando o verificador 
	// digitar a matricula ou cpf, ele buscara uma pf válida e uma credencial tbm. 
	// Havendo, informa ocorrencia.
	$intNumFull = str_pad($intNum,8,'0',STR_PAD_LEFT);
	try{
		$strSQL  = "SELECT cad_pf.cod_pf ";
		$strSQL .= "  FROM cad_pf ";
		$strSQL .= " WHERE cad_pf.cpf = '" . $intNum . "'";
		$strSQL .= "    OR cad_pf.matricula = '" . $intNum . "'";     // se for digitad é sem aeros na frente.
		$strSQL .= "    OR cad_pf.matricula = '" . $intNumFull . "'"; // se vem de algum leitor, deverá vir com zeros a frente num total de 8 digitos.
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// caso não exista nenhuma ocorrencia
	// da pf procurada, entao avisa que a
	// pf nao existe
	if(($objResult->rowCount()) <= 0){
		echo "
			<center>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
					<tr>
					<td align=\"center\" valign=\"middle\" width=\"100%\">";
					mensagem("err_colab_null","err_sql_desc_card",getTText("pf_invalida",C_NONE),"STvalidacard.php?var_db=".$strDBConnect,"aviso",1);
		echo "		</td>
					</tr>
				</table>
			</center>";
		die();
	}
	else{
		$objRS = $objResult->fetch();
		$intCodPF = getValue($objRS,"cod_pf");
		
		// Busca os dados da credencial para exibição posterior. Só busca cards
		// que não estão inativos e válidos caso exista maior do que um, exibe
		// todos em tela
		try {
			$strSQL  = " SELECT	 sd_credencial.cod_credencial "; 
			$strSQL .= "		,sd_credencial.qtde_impresso "; 
			$strSQL .= "		,sd_credencial.dt_validade "; 
			$strSQL .= "		,sd_credencial.pf_matricula "; 
			$strSQL .= "		,sd_credencial.pf_empresa "; 
			$strSQL .= "		,sd_credencial.pf_nome "; 
			$strSQL .= "		,sd_credencial.pf_cpf "; 
			$strSQL .= "		,sd_credencial.pf_funcao " ;
			$strSQL .= "		,sd_credencial.dtt_inativo "; 
			$strSQL .= "		,sd_credencial.dt_validade ";
			$strSQL .= "		,sd_credencial.cod_pf "; 
			$strSQL .= "		,sd_credencial.sys_dtt_ins "; 
			$strSQL .= "   FROM sd_credencial "; 
			$strSQL .= "  WHERE sd_credencial.cod_pf = '" . $intCodPF . "'  "; 
			$strSQL .= "    AND sd_credencial.dtt_inativo IS NULL "; 
			$strSQL .= "    AND CURRENT_DATE <= dt_validade "; 
			$strSQL .= "  ORDER BY sys_dtt_ins DESC, dt_validade"; 
			$objResultP = $objConn->query($strSQL);
			$objResult  = $objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
		if(($objResult->rowCount()) <= 0){
			echo "
				<center>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
						<tr>
						<td align=\"center\" valign=\"middle\" width=\"100%\">";
						mensagem("err_busca_dados","err_sql_desc_card",getTText("pf_sem_card",C_NONE),"STvalidacard.php?var_db=".$strDBConnect,"aviso",1);
			echo "		</td>
						</tr>
					</table>
				</center>";
			die();
		}
		else {
			// fetch dos dados para exibição
			// na tabela abaixo 
			$objRSP = $objResultP->fetch();
			$intCodCredencial = getValue($objRSP,"cod_credencial");
			$intQtdeImpresso  = getValue($objRSP,"qtde_impresso");
			$dtDataValidade   = getValue($objRSP,"dt_validade");
			$intMatricula     = getValue($objRSP,"pf_matricula");
			$strRazaoSocial   = getValue($objRSP,"pf_empresa");
			$strNome          = getValue($objRSP,"pf_nome");
			$intCPF           = getValue($objRSP,"pf_cpf");
			$strFuncao        = getValue($objRSP,"pf_funcao");
			$intCodPF         = getValue($objRSP,"cod_pf");
			$dttDataInativo   = getValue($objRSP,"dtt_inativo");
			$dtDataValidade   = getValue($objRSP,"dt_validade");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			function resizeAgain(){
				document.getElementById('STCard').width = 390;
				document.getElementById('STCard').height = 255;
			}
			
			var strLocation = null;
			function ok() {
				window.history.back();
			}

			function cancelar() {
				window.history.back();
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<?php if(($objResult->rowCount()) > 0) {?>	
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none",strtoupper("<b>" . getsession(CFG_SYSTEM_NAME."_dir_cliente") . "</b> - " . getTText("valid_card",C_UCWORDS)),CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td style="padding: 0px 0px 20px 0px;" align="center" width="100%">
							<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 10px 10px">
								<tr>
									<td nowrap="nowrap" align="center" width="100%">
										<table width="412" border="0" cellpadding="4" cellspacing="0" >
											<tr>
												<td align="left" valign="bottom" colspan="2" height="40">
													<div style="padding-left:30px;" class="destaque_gde">
														<strong>DADOS LOCALIZADOS</strong>
													</div>
												</td>
											</tr>
											<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
											<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
											<!--
												OLD - NAO EXIBE MAIS NUM_IMPR
												POIS LISTA N CARDS, NAO SE SABE ENTAO
												DE QUAL DELAS É O NUM_IMPR
											<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
												<td width="149" nowrap="nowrap" align="right">
													<?php echo("<b>".getTText("num_impresso",C_NONE).":</b>")?>												</td>
												<td width="241" align="left" style="padding-left: 15px;"><?php echo($intQtdeImpresso) ?></td>
											</tr>
											-->
											<!-- 
												OLD - NAO EXIBE VALIDADE EM RAZAO
												DE EXIBIR MAIS DE UMA CARD
											<tr>
												<td nowrap="nowrap" align="right">
													<?php echo("<b>".getTText("dt_validade",C_NONE).":</b>")?>
												</td>
												<td align="left" style="padding-left: 15px;"><?php echo(dDate(CFG_LANG,$dtDataValidade,false)) ?></td>
											</tr>
											-->
											<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
												<td nowrap="nowrap" align="right">
													<?php echo("<b>".getTText("pf_matricula",C_NONE).":</b>")?>
												</td>
												<td align="left" style="padding-left: 15px; font-size:16px;"><?php echo($intMatricula . " <small>(Cód.Cred " . $intCodCredencial . ")</small>" )?></td>
											</tr>
											<!-- 
												OLD - NAO USARAÁ MAIS EMPRESA EM RAZÃO
												DE PODER POSSUIR 'N' CREDENCIAIS EM MAIS
												DE UMA EMPRESA
											<tr>
												<td nowrap="nowrap" align="right">
													<?php echo("<b>".getTText("empresa",C_NONE).":</b>")?>
												</td>
												<td align="left" style="padding-left: 15px;"><?php echo($strRazaoSocial) ?></td>
											</tr>
											-->
											<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
												<td align="right" nowrap="nowrap">
													<?php echo("<b>".getTText("pf_nome",C_NONE).":</b>")?>
												</td>
												<td align="left" style="padding-left: 15px; font-size:16px;"><?php echo($strNome) ?></td>
											</tr>
											<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
											<td align="right" nowrap="nowrap">
													<?php echo("<b>".getTText("pf_cpf",C_NONE).":</b>")?>
											</td>
											<td align="left" style="padding-left: 15px; font-size:16px;"><?php echo($intCPF . " <small>(Cód.PF " . $intCodPF . ")</small>" ) ?></td>
											</tr>
											<!--
												OLD - NAO EXIBE POR PODER POSSUIR MAIS
												DE UMA EMPRESA
											<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
											<td nowrap="nowrap" align="right">
													<?php echo("<b>".getTText("pf_funcao",C_NONE).":</b>")?>
											</td>
											<td align="left" style="padding-left: 15px;"><?php echo($strFuncao) ?></td>
											</tr>
											-->
                                			<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>

											<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
												<td colspan="2" align="left" style="padding-left: 5px;">
												<?php
													try{
														$strSQL  = " SELECT  cad_pj.nome_fantasia ";
														$strSQL .= "		,cad_pj.cnpj ";
														$strSQL .= "		,relac_pj_pf.tipo ";
														$strSQL .= "		,(SELECT cad_cargo.nome FROM cad_cargo WHERE cad_cargo.cod_cargo = relac_pj_pf.cod_cargo) as cargo ";
														$strSQL .= "		,relac_pj_pf.funcao ";
														$strSQL .= "		,relac_pj_pf.categoria ";
														$strSQL .= "		,relac_pj_pf.departamento ";
														$strSQL .= "		,relac_pj_pf.dt_admissao ";
														$strSQL .= "  FROM relac_pj_pf, cad_pj ";
														$strSQL .= "  WHERE relac_pj_pf.cod_pf = " . $intCodPF;
														$strSQL .= "    AND relac_pj_pf.cod_pj = cad_pj.cod_pj ";
														$strSQL .= "    AND relac_pj_pf.dt_inativo IS NULL ";
														$strSQL .= "    AND relac_pj_pf.dt_demissao IS NULL ";

														$objResultREL = $objConn->query($strSQL);
													}
													catch(PDOException $e){
														mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
														//die();
													}
												?>        
                                                <table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
                                                    <thead>
                                                        <tr>
                                                           	<th width="1%">&nbsp;</th>
                                                           	<th align="left" class="sortable" nowrap><?php echo(getTText("empresa",C_UCWORDS)); ?></th>
															<!-- th align="left" class="sortable" nowrap><?php echo(getTText("cnpj",C_UCWORDS)); ?></th //-->
															<th align="left" class="sortable" nowrap><?php echo(getTText("tipo",C_UCWORDS)); ?></th>
															<th align="left" class="sortable" nowrap><?php echo(getTText("cargo",C_UCWORDS)); ?></th>
															<th align="left" class="sortable" nowrap><?php echo(getTText("funcao",C_UCWORDS)); ?></th>
															<th align="left" class="sortable" nowrap><?php echo(getTText("categ.",C_UCWORDS)); ?></th>
															<!--
                                                            th align="left" class="sortable" nowrap><?php echo(getTText("depart.",C_UCWORDS)); ?></th>
															<th align="left" class="sortable" nowrap><?php echo(getTText("admissão",C_UCWORDS)); ?></th
                                                            //-->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach($objResultREL as $objRSRel){ ?>
                                                        <tr>
                                                           	<td>&nbsp;</td>
                                                            <td align="left"><?php echo(getValue($objRSRel,"nome_fantasia")); ?></td>
															<!-- td align="left"><?php echo(getValue($objRSRel,"cnpj")); ?></td //-->
															<td align="left"><?php echo(getValue($objRSRel,"tipo")); ?></td>
															<td align="left"><?php echo(getValue($objRSRel,"cargo")); ?></td>
															<td align="left"><?php echo(getValue($objRSRel,"funcao")); ?></td>
															<td align="right" style="font-size:12px; color:<?php echo(getValue($objRSRel,"categoria")=="GERAL" ? "#090" : "#C30"); ?> "><b><?php echo(getValue($objRSRel,"categoria")); ?></b></td>
															<!--
                                                            td align="left"><?php echo(getValue($objRSRel,"departamento")); ?></td>
															<td align="left"><?php echo(getValue($objRSRel,"dt_admissao")); ?></td 
                                                            //-->
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>		
                                                </table>
                                                                                        
                                                </td>
											</tr>

                                            
									  		<tr>
												<td align="left" valign="bottom" colspan="2" height="40">
													<div style="padding-left:30px;" class="destaque_gde">
														<strong>CREDENCIAIS ATIVAS</strong>
													</div>
												</td>
											</tr>
											<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
											
									  </table>
								   </td>
								</tr>
								
							<?php 
							foreach($objResult as $objRS) {?>	
							<tr>
							<td nowrap="nowrap" align="center" valign="middle" style="padding:10px 20px 0px 30px;">
								<?php athBeginWhiteBox("405","none","<strong>".getTText("credencial",C_UCWORDS)."</strong>",CL_CORBAR_GLASS_1)?>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
								  <td align="center" valign="middle" style="padding-left:20px; text-align:center">
								  <iframe src="<?php echo("../modulo_SdCredencial/STcardreader.php?var_chavereg=".getValue($objRS,"cod_pf")."&var_db=".$strDBConnect."&var_cod_credencial=".getValue($objRS,"cod_credencial")); ?>" 
                                          width="330" height="220" 
                                          allowtransparency="true" 
                                          frameborder="0" 
                                          name="STCard" 
                                          id="STCard"></iframe>
								  </td>
								</tr>
								</table>
								<?php athEndWhiteBox();?>
							</td>
							</tr>
							<tr><td nowrap="nowrap">&nbsp;</td></tr>
							<?php } ?>
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="right">
							<button onClick="ok(); return false;" style="margin-right:20px;">
								<?php echo(getTText("ok",C_UCWORDS)); ?>
							</button>
						</td>	
					</tr>
					</form>	 
				</table>
		<?php athEndFloatingBox(); ?>
  			</td>
  		</tr>
	</table>
<?php 
	}
	else{
		echo '<center>';
		athBeginWhiteBox("600","none","",CL_CORBAR_GLASS_1);
		mensagem("err_busca_dados","","Nenhum registro localizado para o valor inserido.","STvalidacard.php","aviso",1);
		athEndWhiteBox();
		echo '</center>';
		die();				
	}
?>
</body>
</html>
<?php 
	$objConn = NULL; 
?>