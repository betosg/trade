<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// para poder ser chamado de fora do sistema
$strDBConnect	= (request("var_db") == "") ? getsession(CFG_SYSTEM_NAME."_db_name") : request("var_db");
if(($strDBConnect == "") || (is_null($strDBConnect))){
	echo(
	"<center>
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
			<tr>
			<td align=\"center\" valign=\"middle\" width=\"100%\">");
			mensagem("ERRO NO PROCESSAMENTO","Base não localizada","","O banco de dados não foi informado","aviso",1);
		echo 
		   ("</td>
			</tr>
		</table>
	</center>");
	die();
}

//requests STs
$strOpcao     = request("var_opcao");
$intCPF       = request("var_cpf");
$intMatricula = request("var_matricula");

$strOperacao  = request("var_oper");     // Operação a ser realizada
$intCodDado   = request("var_chavereg"); // Código chave da página - cod_credencial
$strExec      = request("var_exec");     // Executor externo (fora do kernel)
$strPopulate  = request("var_populate"); // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");     // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

//Cores linhas
$strBGColor = CL_CORLINHA_2;
//Inicia objeto para manipulação do banco
$objConn = abreDBConn($strDBConnect);

// Só é feito a busca e exibição dos dados 
// seja enviado como parametro para este script.
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function ok() {
				document.form1.submit();
			}
			//****** Funções de ação dos botões - Fim ******
			
			function edita(caractere) {
				var campo = document.form1.var_num.value;
				switch (caractere){ 
					case "BACK" : 
	  				if (campo.length > 0) {
						document.form1.var_num.value = campo.substr(0,campo.length-1);
					}
      				break; 
   					case "CLEAR" : 
					document.form1.var_num.value = "";
      				break; 
   					default :
      				if (campo.length < 11) {
        				document.form1.var_num.value = campo + caractere;
					}
 				}
			}
			
			function MM_preloadImages() { //v3.0
				var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
   				var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
   				if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
			}	

		-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?> onLoad="form1.var_num.focus();">
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("700","none",strtoupper("<b>" . getsession(CFG_SYSTEM_NAME."_dir_cliente") . "</b> - " . getTText("valid_card",C_UCWORDS)),CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="form1" action="STvalidacardexec.php" method="post">
					<input type="hidden" name="var_db" value="<?php echo($strDBConnect);?>" />
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td style="padding: 0px 0px 20px 0px;" align="center">
							<!-- inicio teclado virtual -->
							<script language="javascript">
								<!--
								function pressButton(objeto,botao) {
								document.getElementById(objeto).src = "../img/bt_key_"+botao+"_press.gif";
								window.setTimeout(function(){document.getElementById(objeto).src = "../img/bt_key_"+botao+".gif";},100);
								}
								-->
							</script>
							<table border="0" cellpadding="8" cellspacing="0">
								<tr align="center" valign="middle">
									<td><img src="../img/bt_key_1.gif" width="80" height="80" id="bt01" onClick="pressButton('bt01','1'); edita('1');" style="cursor:pointer" /></td>
									<td><img src="../img/bt_key_2.gif" width="80" height="80" id="bt02" onClick="pressButton('bt02','2'); edita('2');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_3.gif" width="80" height="80" id="bt03" onClick="pressButton('bt03','3'); edita('3');" style="cursor:pointer" /></td>
  								</tr>
								<tr align="center" valign="middle">
									<td><img src="../img/bt_key_4.gif" width="80" height="80" id="bt04" onClick="pressButton('bt04','4'); edita('4');" style="cursor:pointer" /></td>
									<td><img src="../img/bt_key_5.gif" width="80" height="80" id="bt05" onClick="pressButton('bt05','5'); edita('5');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_6.gif" width="80" height="80" id="bt06" onClick="pressButton('bt06','6'); edita('6');" style="cursor:pointer" /></td>
								</tr>
  								<tr align="center" valign="middle">
									<td><img src="../img/bt_key_7.gif" width="80" height="80" id="bt07" onClick="pressButton('bt07','7'); edita('7');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_8.gif" width="80" height="80" id="bt08" onClick="pressButton('bt08','8'); edita('8');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_9.gif" width="80" height="80" id="bt09" onClick="pressButton('bt09','9'); edita('9');" style="cursor:pointer" /></td>
								</tr>
  								<tr align="center" valign="middle">
    								<td><img src="../img/bt_key_back.gif" width="80" height="80" id="btBACK" onClick="pressButton('btBACK','back'); edita('BACK');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_0.gif" width="80" height="80" id="bt00" onClick="pressButton('bt00','0'); edita('0');" style="cursor:pointer" /></td>
    								<td><img src="../img/bt_key_ok.gif" width="80" height="80" id="btOK" onClick="pressButton('btOK','ok'); ok();" style="cursor:pointer" /></td>
  								</tr>
							</table>
							<!-- fim teclado -->
						</td>
					</tr>
					<tr>
						<td style="padding: 0px 0px 20px 0px;">
							<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" style="padding: 0px 0px 00px 30px;">
										<img src="../img/mensagem_info.gif">
									</td>
									<td align="left" style="padding: 0px 0px 0px 10px;">
										<?php echo(getTText("aviso_impr2",C_NONE))?>
									</td>
									<td width="1%" align="left" style="padding:10px 50px 10px 10px;" nowrap>
										<input type="text" name="var_num" id="var_num" size="18" maxlength="15" value="" style="font-size:28px; font-weight:bold; border:1px solid #0000CC; text-align:center; height:35px;" onKeyPress="Javascript:return(validateNumKey(event));" />
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
	$objConn = NULL; 
?>