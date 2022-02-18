<?php
// INCLUDES
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// Verificação de ACESSO - ENQUANTO MODULO DE MENSAGENS NAO EXISTE, NAO TESTAR DIREITOS
// $strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); 

// Abre conexão com DB
$objConn = abreDBConn(CFG_DB);

$id_mercado = getsession(CFG_SYSTEM_NAME."_id_mercado");
$id_evento	= getsession(CFG_SYSTEM_NAME."_id_evento");
$codcli		= getsession(CFG_SYSTEM_NAME."_codcli");

?>
<html>
<head>
<title>GERAL</title>
</head>
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">
<script language="javascript"> 
function ShowArea(prCodigo1, prCodigo2)
{
	if (document.getElementById(prCodigo1).style.display == 'none') {
		document.getElementById(prCodigo1).style.display = 'block';
		document.getElementById(prCodigo2).src = '../img/BulletMenos.gif';
	}
	else { 
		document.getElementById(prCodigo1).style.display = 'none';
		document.getElementById(prCodigo2).src = '../img/BulletMais.gif';
	}
}
</script>
<table cellpadding='0' cellspacing='0' border='0' width='180' bgcolor='#FFFFFF'>
<tr>
	<td>
		<table cellpadding='0' cellspacing='0' style='border:none; width:100%;'>
			<!-- CHECA SE O ARQUIVO DO DIRETÓRIO INFORMADO EXISTE PARA MONTAGEM DA BOX 'ENTIDADE' -->						
			<?php if(file_exists("../../".str_replace(CFG_SYSTEM_NAME."_","",CFG_DB_DEFAULT)."/upload/imgdin/logomarca.gif")){?>
				<tr>
					<td width="1%" align="left" valign="top" style="border:none; background:none; padding:0px 0px 0px 5px;">
						<?php include('STinfologoempresa.php');?>
					</td>
				</tr>
			<?php }?>
			<tr><td height="10">&nbsp;</td></tr>
			<tr>
				<td width="1%" align="left" valign="top" style="border:none; background:none; padding:0px 0px 0px 5px;">
					<?php include('STAtalhos.php');?>
				</td>
			</tr>
			
            <?php include('STAniverColega.php');?>
			 <tr><td height="10">&nbsp;</td></tr>
			<tr>
				<td align="center" valign="top" height="325" style="border:none;background:none;padding:0px 0px 0px 5px;">
				<?php athBeginFloatingBox("100%","325","<a href='../modulo_Agenda/' target='".CFG_SYSTEM_NAME."_frmain'><b>".getTText("agenda_evt",C_NONE)."</b></a>",CL_CORBAR_GLASS_2);?>
					<iframe id="dbvar_str_agenda" src="STagenda.php" frameborder="0" scrolling="no" width="185" height="480" style="padding-left:5px;border:1px solid #CCC;"></iframe>
                   <?php /*?> <iframe id="dbvar_str_agenda" src="#" frameborder="1" width="185" height="325" style="padding-left:5px;border:1px solid #CCC;"></iframe><?php */?>
				<?php athEndFloatingBox();?>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
<?php
	$objConn = NULL;
?>