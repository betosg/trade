<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo(CFG_SYSTEM_TITLE);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script>
<!--
	/*******************************************
		Para exportação funcionar nas STdata
		o filtro delas deve estar com o form
		repassando por GET
	 ------------------- by Alan 28/08/2009 ---
	*******************************************/
	function imprimir(){
		parent.window.frames[2].frames[1].focus();
		parent.window.frames[2].frames[1].print();
	}
	
	function exportarAdobe(){
		strFileName = getCurFileName();
		
		document.formAcao.var_acao.value = ".pdf";
		document.formAcao.action = strFileName;
		document.formAcao.submit();
	}
	
	function getCurFileName(){
		/* var strPagina = parent.document.frames[2].document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_main"); ?>").src;
		var strPath   = parent.document.frames[2].frames[1].parent.location.toString(); 
			
		retValue = strPath.substr(0,strPath.lastIndexOf("/")+1) + strPagina;
		alert(strPath);
		
		return(retValue); */
		return(parent.window.frames[2].frames[1].location.toString());
	}
	
	function exportDocument(prType){
	   /* Esta função faz o export do CONTEÚDO 
		* que está no FRAME da direita, para um
 		* tipo de documento informado como param. 
		* O conteúdo é coletado via javascript
		* e o formulário atual de export é atuali-
		* zado e aberto em pop-up, onde o conteú-
		* do é carregado.
		*/
		var objBODY;
		var objFORM;
		var objCONT;
		var objACAO;
		var objLINK;
		var strACAO;
				
		// PASSAGEM DE PARÂMETROS, INICIALIZACAO
		objACAO = document.getElementById("var_acao");
		objCONT = document.getElementById("var_content");
		objLINK = document.getElementById("var_link");
		objFORM = document.getElementById("formexport");
		strACAO = prType;
		
		// TRATAMENTO CONTRA PARAMS NULL
		if(parent.window.frames[2] == null){
			alert('Documento corrente NÃO está dentro da Estrutura de Frames Correta!');
		}
		else if(parent.window.frames[2].frames[1] == null){ 
			objBODY = parent.window.frames[2].document.getElementsByTagName("body");
		} else{
			objBODY = parent.window.frames[2].frames[1].document.getElementsByTagName("body");
		}
		
		
		// @DEBUG:
		// alert(objBODY[0].innerHTML);
		
		// ATUALIZAÇÃO DE VALUES, ETC
		objCONT.value = objBODY[0].innerHTML;
		objACAO.value = strACAO;
		objLINK.value = parent.window.frames[2].location.toString();
		objFORM.submit();
	}

	function changeLang() {
		window.open('STchangelang.php','','width=420,height=220');
	}
	
	function Refr() {
		alert('11');	
	}
//-->
</script>
<style>
	#lingua { 
		font-family: Arial;
		background-color: #BBB;
		padding:0px 3px;
	}
	
	#lingua:hover { 
		background-color: #CCC;
		color:#333;
	}
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="formexport" id="formexport" action="../modulo_Principal/STexport.php" target="_blank" method="post">
	<input type="hidden" name="var_content" id="var_content" value="" />
	<input type="hidden" name="var_acao"    id="var_acao"    value="" />
	<input type="hidden" name="var_link"    id="var_link"    value="" />
</form>
<form name="formAcao" action="" method="post" target="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>">
	<input type="hidden" name="var_acao" value="">
</form>
<table width="100%" height="22" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="99%" valign="middle" background="../img/bgFooterLeft.jpg">
	  <table width="100%" height="22" cellpadding="0" cellspacing="0" border="0">
	    <tr>
			<td width="23%" align="left" valign="middle" class="texto_corpo_peq">
			<div style="padding-left:2px; padding-right:2px;">
				<?php 
				echo("<a href=\"javascript:changeLang()\" id=\"lingua\">" . strtoupper(getsession(CFG_SYSTEM_NAME . "_lang")) . "</a>");
				echo("&nbsp;&nbsp;");
				echo("<span title='" . getsession(CFG_SYSTEM_NAME . "_db_user") . "'>" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "</span>");
				echo(" (" . getsession(CFG_SYSTEM_NAME . "_grp_user") . ")");
				if(getsession(CFG_SYSTEM_NAME . "_su_passwd")){ echo("*"); }
				?>
			</div>
		  </td>
			<td width="57%" align="center" valign="middle" class="pj_selec_peq" nowrap="nowrap">
				<?php
				 $strAux  = "";
				 if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != ''){
					$strAux = strtoupper("CLIENTE: " . getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") . " - " . getsession(CFG_SYSTEM_NAME . "_pj_selec_nome"));
				}
				?>
			   <table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
			   <tr>
					<td align="center">
					   <form name='formPrjAtv' action=''>
						 <input type='text' readonly='readonly' name='var_prj_atv' id='var_prj_atv' value='<?php echo($strAux)?>' class='pj_selec_peq' style="width:450px;text-align:center;" title="<?php echo($strAux);?>" alt="<?php echo($strAux);?>">
					   </form>
					</td>
			   </tr>
			   </table>
          </td>	
          <td width="20%" align="right" valign="middle" style="padding-right:5px;" title="<?php echo(session_id());?>" alt="<?php echo(session_id());?>">
		     <font color="#dadada"><?php echo(session_id());?></font><a href='http://www.grupoproevento.com.br' target='_blank' class="copyright"><b>PROEVENTO</b>&nbsp;<small>TECNOLOGIA</small></a>
          </td>
		</tr>
	  </table>
	</td>
    <td background="../img/bgFooterRight.jpg"> 
	  <table width="120" height="22" cellpadding="0" cellspacing="0" border="0">
	    <tr>
			<td valign="middle" style="padding:1px 0px 0px 8px;"><!-- Tabela preparada para os ícones de impressão, exportação, e-mail, etc...-->
				<table border="0" cellpadding="0" cellspacing="0" width="75" style="display:inline;">
					<tr>
						<td align="center" width="18"><img src="../img/iconfooter_print.gif" border="0" onClick="imprimir();"             style="cursor:pointer;"  title="<?php echo(getTText("imprimir",C_UCWORDS));?>"></td>
						<td align="center" width="18"><img src="../img/iconfooter_word.gif"  border="0" onClick="exportDocument('.doc');" style="cursor:pointer;"  title="<?php echo(getTText("exportar_word",C_UCWORDS));?>"></td>
						<td align="center" width="18"><img src="../img/iconfooter_excel.gif" border="0" onClick="exportDocument('.xls');" style="cursor:pointer;"  title="<?php echo(getTText("exportar_excel",C_UCWORDS));?>"></td>
						<td align="center" width="18"><img src="../img/iconfooter_null.gif"  border="0" style="cursor:pointer;"></td>
						<td align="center" width="18"><iframe width="25" height="16" style=" margin-left:5px; border:0px solid #999999;" src="kbps_leitura.php" scrolling="no" frameborder="0"></iframe></td>
					</tr>
				</table>

			</td>
		</tr>
	  </table>
	</td>
  </tr>
</table>
<?php

if (getsession("tradeunion_db_name")=="tradeunion_abfm"){
		$mediaType = "application/json";
		$charSet   = "utf-8";
		//die();
		$headers = array();
		$headers[] = "Accept: ".$mediaType;
		$headers[] = "Accept-Charset: ".$charSet;
		$headers[] = "Accept-Encoding: ".$mediaType;
		$headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
		//$headers[] = "Api-Access-Key:". $ApiAccessKey;
		//$headers[] = "Transaction-Hash: ".trim($AuthorizationHeaderBase64);

		$url =  "https://tradeunion.proevento.com.br/_tradeunion/modulo_ASLWRelatorio/STExtratoBepayQuita_ws.php";

		$ch = curl_init();
		//echo $ch;
		//echo $data_post;
		//die();
		curl_setopt($ch, CURLOPT_URL, $url.$PostData);
		//curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		//echo($ch);
		// extract header
		$result = curl_exec($ch);
		print($result);
}
?>
</body>
</html>