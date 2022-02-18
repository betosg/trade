<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_database/STathutils.php");
include_once("STValidacaoToolsSITCS.php");

$objConn = abreDBConn(CFG_DB);

$arqName = $_POST['uploadArquivo'];
$arquivo = alteraNomeArq($arqName,"V");
$_SESSION['ValidaArquivo_Arquivo'] = $arquivo;
//Neste ponto as variáveis de sessão são preenchidas
$fp = file($arquivo);
for($z=0; $z < count($fp); $z++){
	$linha = str_split($fp[$z]);
	@$first_Number = $linha[0].$linha[1].$linha[2];
	if((!ctype_digit($first_Number)) and (trim ($fp[$z]) <> "")){
		die(mensagem("err_sql_titulo","err_arq_desc","Arquivo Inválido para Validação.","STArqUploadSITCS.php","erro",1));
	} else{
		verificaLinha($linha);
	}
}

$_SESSION['ValidaArquivo_Arquivo'] = "";
function arqImportado(){
	echo "<script language='javascript' type='text/javascript'>
			alert('O arquivo que você está tentando importar já foi importado para nossa Base de dados. Tente Novamente');
		  </script>";
}
function arqErro(){
	echo "<script language='javascript' type='text/javascript'>
			alert(\"Erro! Arquivo não importado! Clique em OK para visualizar a descrição do arquivo.\");
		  </script>";
}
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<script language="javascript" type="text/javascript">
	function confirmar(){
		document.formArqValida.submit();
	}
	function cancelar() {
		location.href="../modulo_PainelAdmin/STindex.php";
	}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession(@$strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="center" valign="middle">
			<?php athBeginFloatingBox("600","none","VALIDAÇÃO DE ARQUIVO (Validação)",CL_CORBAR_GLASS_1);?> 
				<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
					<tr>
						<td valign="top" align="center"> 
							<table width="500"  border="0" cellpadding="2" cellspacing="0">
								<tr><td><table><?php include_once("_STincludeHeaderArquivoSITCS.php");?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeHeaderLoteSITCS.php"); ?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeSegmentoTSITCS.php"); ?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeSegmentoUSITCS.php"); ?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeSegmentoFSITCS.php"); ?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeTrailerLoteSITCS.php"); ?></table></td></tr>
								<tr><td><table><?php include_once("_STincludeTrailerArquivoSITCS.php"); ?></table></td></tr>
<!-- *** VERIFICAÇÃO SE SOMENTE FOI VALIDADO OU IMPORTADO *** -->
<?php 
//echo("err: ".$_SESSION['ValidaErro']);
$id_arq = $_SESSION['ArqValida_numSeqArqHA'];
if ($id_arq != "") {
	$strSQL      = "SELECT id_arq, situacao FROM arq_retorno_cobr WHERE id_arq = ".$id_arq;
	$objResult   = $objConn->query($strSQL);
	$objRS       = $objResult->fetch();
	$valor       = getValue($objRS,"situacao");
	$nome        = $_SESSION['novoNome'];
	$nome_orig   = $arqName;
	
	if(($objResult->rowCount() == 0) || ($objResult == "")){
		if($_SESSION['ValidaErro'] ==""){
			$situacao = "val";
		} else{
			$situacao = "err";
			arqErro();
		}
		$strSQL = "	INSERT INTO arq_retorno_cobr(id_arq,nome,nome_orig,situacao,sys_dtt_ins,sys_usr_ins) 
					VALUES('".$id_arq."','".$nome."','".$nome_orig."','".$situacao."',CURRENT_TIMESTAMP,'".getsession(CFG_SYSTEM_NAME . "_id_usuario")."')";
		$objConn->query($strSQL);		
	}
	else if(($valor != "imp") && ($valor != "exe")){
			if($_SESSION['ValidaErro'] ==""){
				$situacao = "val";
			} else{
				$situacao = "err";
				arqErro();
			}
			$strSQL   = "UPDATE arq_retorno_cobr 
						 SET situacao = '".$situacao."'
						   , nome = '".$nome."'
						   , sys_dtt_upd = CURRENT_TIMESTAMP
						   , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."'
						 WHERE id_arq = ".$id_arq;
			$objConn->query($strSQL);
		} 
		else{
			$_SESSION['ValidaErro'] = "Arquivo Importado";
			arqImportado();
		}
}
else
	$_SESSION['ValidaErro'] = "ID não encontrado no arquivo";
?>
<!-- *** VERIFICAÇÃO SE SOMENTE FOI VALIDADO OU IMPORTADO *** -->
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
								<tr><td>&nbsp;</td></tr>								
								<tr>
									<td colspan="2" align="right">
										<form name="formArqValida" action="STdialogSITCS.php" method="post" >
										<?php //if($_SESSION['ValidaErro'] == ""){?>
											<input type="hidden" name="uploadArquivo" value="<?php echo $_SESSION['novoNome'];?>">		
											<button onClick="confirmar(); return false;"><?php echo(getTText("pre_importacao",C_UCWORDS));?></button>
											&nbsp;
											<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
										<?php //} ?>
										</form>
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>		
							</table>
						</td>
					</tr>
				</table>
			<?php athEndFloatingBox(); ?>
			</td>	
		</tr>
	</table>	
</body>
</html>
<?php $objConn = NULL; ?>