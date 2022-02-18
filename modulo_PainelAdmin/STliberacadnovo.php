<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$intCodPJ = request("var_chavereg");
//$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não

//if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

$objConn = abreDBConn(CFG_DB);

try{
	$strSQL = "	
				SELECT 
					cad_pj.nome_fantasia,
					cad_pj.razao_social,
					cad_pj.cod_pj,
					cad_pj.cnpj,
					cad_pj.insc_est,
					cad_pj.email,
					cad_pj.capital,
					cad_pj.num_funcionarios,
					cad_pj.website,
					cad_pj.contato,
					cad_pj.endprin_fone1,
					cad_pj.arquivo_1,
					sys_usuario.cod_usuario,
					sys_usuario.sys_dtt_ins,
					sys_usuario.id_usuario
			 	FROM sys_usuario, cad_pj
				WHERE sys_usuario.codigo = cad_pj.cod_pj
				AND sys_usuario.tipo = 'cad_pj'
				AND cad_pj.cod_pj = ". $intCodPJ;
	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
				location.href="../modulo_PainelAdmin/STindex.php";
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none","LIBERAÇÃO DE CADASTRO (Confirmação)",CL_CORBAR_GLASS_1); ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    <form name="formstatic" action="../modulo_PainelAdmin/STliberacadnovoexec.php" method="post">
		<input type="hidden" name="var_id_usuario" value="<?php echo(getValue($objRS,"id_usuario"));?>">
		<input type="hidden" name="var_email" value="<?php echo(getValue($objRS,"email"));?>">
		<input type="hidden" name="var_empresa" value="<?php echo(getValue($objRS,'nome_fantasia'));?>">
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;"><strong><?php echo(getTText("aviso_libera_confirma",C_NONE)); ?></strong></td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:10px 60px 10px 60px;" width="100%">
				<table cellpadding="4" cellspacing="0" border="0"  style="padding: 0px 0px 0px 0px" width="100%">
					<tr>
						<td align="left" valign="bottom" colspan="2" height="40">
							<div style="padding-left:100px;" class="destaque_gde">
								<strong>DADOS DA EMPRESA</strong>
							</div>
						</td>
				  	</tr>
					<tr>
						<td colspan="2" height="2" background="../img/line_dialog.jpg"></td>
					</tr>
  				<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("cod",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'cod_pj'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("cnpj",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'cnpj'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("insc_est",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'insc_est'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("razao_social",C_NONE));?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'razao_social'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("nome_fantasia",C_NONE));?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'nome_fantasia'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("email",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'email'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("website",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'website'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("contato",C_NONE))?></strong>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td>
									<?php echo(getValue($objRS,'contato'))?>
								</td>
								<td align="right" width="30%">
									<strong><?php echo(getTText("endprin_fone1",C_NONE))?></strong>
								</td>
								<td style="padding-left: 08px;">
									<?php echo(getValue($objRS,'endprin_fone1'))?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td align="right" valign="top" width="30%">
						<strong><?php echo(getTText("documentos",C_NONE));?></strong>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td><div style="padding-bottom:6px;">
									<?php
										//exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_1') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_1'));
											//Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											//que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?><br>(<a href="../../<?php echo getsession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,'arquivo_1')); ?>" target="_blank"><?php echo(getvalue($objRS,'arquivo_1')); ?></a>)
									<?php } ?>
								</div></td>
							</tr>
							<tr>
								<td><div style="padding-bottom:6px;">
									<?php
										//exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_2') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_2'));
											//Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											//que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?><br>(<a href="../../<?php echo getsession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,'arquivo_2')); ?>" target="_blank"><?php echo(getvalue($objRS,'arquivo_2')); ?></a>)
									<?php } ?>
								</div></td>
							</tr>
							<tr>
								<td><div style="padding-bottom:6px;">
									<?php
										//exibe documentos de upload da empresa
			  							if(getvalue($objRS,'arquivo_3') != ''){
											$nomeArquivo = explode("_",getvalue($objRS,'arquivo_3'));
											//Chama 2x para remover duas primeiras strings que são prefixos para garantir 
											//que arquivo seja único na pasta de upload
											array_shift($nomeArquivo); 
											array_shift($nomeArquivo);
											$nomeArquivo = implode("_", $nomeArquivo);
						  			?>
									<?php echo($nomeArquivo); ?><br>(<a href="../../<?php echo getsession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/<?php echo(getvalue($objRS,'arquivo_3')); ?>" target="_blank"><?php echo(getvalue($objRS,'arquivo_3')); ?></a>)
									<?php } ?>
								</div></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("cod_usuario",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'cod_usuario'))?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
					<td align="right" width="30%">
						<strong><?php echo(getTText("id_usuario",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(getValue($objRS,'id_usuario'))?>
					</td>
				</tr>
				<tr>
					<td align="right" width="30%" bgcolor="<?php echo(CL_CORLINHA_1);?>">
						<strong><?php echo(getTText("sys_dtt_ins",C_NONE))?></strong>
					</td>
					<td>
						<?php echo(dDate(CFG_LANG,getValue($objRS,'sys_dtt_ins'),true))?>
					</td>
				</tr>
					<tr>
						<td height="1" colspan="2" bgcolor="#DBDBDB">
					</td>
				</table>
			</td>	
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding: 0px 20px 50px 70px;" align="right"><img src="../img/mensagem_info.gif"></td>
					<td style="padding: 0px 0px 50px 20px;"><?php echo(getTText("aviso_libera_cadastro",C_NONE)); ?></td>
						<td width="1%" align="right" style="padding:10px 50px 50px 10px;" nowrap>
							<button onClick="document.formstatic.submit(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<?php
$objResult->closeCursor();
$objConn = NULL; 
?>