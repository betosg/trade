<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPJ 	 = request("var_cod_pj");
$intCodPF 	 = request("var_chavereg");
$strCPF  	 = request("var_cpf");
$strRedirect = request("var_redirect");

$objConn = abreDBConn(CFG_DB);

$strRotulo = "Tipo";

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script src="../_scripts/jquery-1.3.min.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
function verifica(prLocation){
	var var_msg = "";
	var strLocation = prLocation;
	
	
    if (validateRequestedFields("formeditor") == true) {  
	//if (var_msg == ''){
		if(strLocation != ""){ document.getElementById('var_redirect').value = strLocation; }
		document.formeditor.submit();
	}
	else {
		//alert("Informar campos abaixo:\n" + var_msg);
        return false;
	}
}

function cancelar(){
	window.location= "STviewpfsSinog.php?var_chavereg=<?php echo($intCodPJ);?>";
}





function swapInputCombo(prCombo,prInput) {
  //alert('aqui' + document.getElementById(prCombo).style.display);
  if (document.getElementById(prCombo).style.display == "none") {
	  document.getElementById(prCombo).style.display = "block";  document.getElementById(prCombo).disabled = 0;
	  document.getElementById(prInput).style.display = "none";   document.getElementById(prInput).disabled = 1;
  } else {
	  document.getElementById(prCombo).style.display = "none"; 	  document.getElementById(prCombo).disabled = 1;
	  document.getElementById(prInput).style.display = "block";	  document.getElementById(prInput).disabled = 0;
  }
}


</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;" onload="carregaFuncao(<?php echo($intCodPF)?>);">
<table border="0" cellpadding="0" cellspacing="0" width="700" height="100%" align="center">
 <tr>
  <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","CONTATO (" . $strRotulo . ")",CL_CORBAR_GLASS_1); ?>
		<table width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	   		<form name="formeditor" action="" method="post" id="formeditor">
				
		<tr><td height="22" style="padding-left:35px;padding-top:15px;"><b>Preencha corretamente os campos abaixo:</b></td></tr>
		<tr> 
			<td align="center" valign="top">

				<table style="border:0px; width:550px;" cellspacing="0" cellpadding="4"> 
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>DADOS</strong></td></tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                                        
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_funcao"><strong>* Tipo:</strong></label></td>
                        <td nowrap align="left" width="99%">
           	                <select name="var_trab_funcao" id="var_trab_funcao_combo" class="edtext" style="display:block; float:left; width:200px;">
								<option value="" selected></option>
								<?php $strSQL = " SELECT 'OUTROS_CONTATOS' as valor      , 'OUTROS_CONTATOS'  as tipo   ";
								    $strSQL .= " UNION   SELECT 'ATENDIMENTO_ABRAMGE'  , 'ATENDIMENTO_ABRAMGE' ";
								    $strSQL .= " UNION   SELECT 'JURÍDICO'   , 'JURÍDICO'  ";
                                    $strSQL .= " UNION   SELECT 'FINANCEIRO'     , 'FINANCEIRO'    ";
                                    $strSQL .= " UNION   SELECT 'PRINCIPAL'     , 'PRINCIPAL'    ";
                                    $strSQL .= " UNION   SELECT 'COMUNICAÇÃO'     , 'COMUNICAÇÃO'    ";
                                    $strSQL .= " UNION SELECT 'PRESIDÊNCIA','PRESIDÊNCIA'";
                                    $strSQL .= " UNION SELECT 'RECURSOS_HUMANOS','RECURSOS_HUMANOS'";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"valor","tipo","")); 
								?>
								


							</select>
							<input type="text" name="var_trab_funcao" id="var_trab_funcao_input" size="60" style="display:none; float:left; width:200px;" value="<?php echo $strFuncao;?>" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
														alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_funcao_combo','var_trab_funcao_input'); return false;"></span>
							<p><span style="cursor:pointer;" onclick="adicionaFuncao(<?php echo($intCodPF)?>,<?php echo($intCodPJ);?>)">Adicionar</span></p>
						</td>
						
					</tr>
					<tr bgcolor="#FAFAFA">
						<td></td>
						<td><span id="funcoes">
							
							</span>
					</td>
					</tr>
                    



                  

                																
				</table>

			</td>
		</tr>
		<tr>
			<td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
				<!--button onClick="verifica('');return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button-->
				<button onClick="cancelar();return false;">Sair</button>
				
				
			</td>
		</tr>					
			</form>
		</table>
	<?php athEndFloatingBox(); ?>
  </td>
 </tr>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
  function adicionaFuncao(prCodigoPf, prCodigoPj) {
	var strFuncao
	$(document).ready(function() {
		if (document.getElementById("var_trab_funcao_input").style.display == "none"){
			strFuncao = document.getElementById("var_trab_funcao_combo").value;
		}
		else{			
			strFuncao = document.getElementById("var_trab_funcao_input").value;
		}	
		//alert(strFuncao)			;
		if (strFuncao != ""){
		$.ajax({url: "insereFuncaoExec.php?var_cod_pj="+prCodigoPj+"&var_funcao="+strFuncao+"&var_action=ins&var_chavereg="+prCodigoPf, success: function(result){																		
			carregaFuncao(prCodigoPf)	
		   }
	    });
		}else{
			alert("Preencha o tipo de contato.")
			document.getElementById("var_trab_funcao_combo").style.backgroundColor="#FFFFCC";
			document.getElementById("var_trab_funcao_input").style.backgroundColor="#FFFFCC";
		}
    });		
}

function carregaFuncao(prCodigoPf) {
	var i=0;
	var j;
	var objResult,objResult2;
	var texto;
	texto = "<table>"
	$(document).ready(function() {
		
		$.ajax({url: "insereFuncaoExec.php?var_action=vie&var_chavereg="+prCodigoPf, success: function(result){																		
				//alert(result);
				arrResultPrincipal = result.split("//");
				
				for (i=0; i< arrResultPrincipal.length-1; i++) {
					arrResultSecundario = arrResultPrincipal[i].split("|")
					texto += "<tr><td><span style='cursor:pointer' onclick='removeFuncao("+arrResultSecundario[0]+","+arrResultSecundario[1]+")'>[ x ]</span></td><td>"+arrResultSecundario[2]+"</td>";
				}
				document.getElementById("funcoes").innerHTML = texto;
				resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
		   }
	    });
    });	
}

function removeFuncao(prCodigo,prCodigoPf){
	$(document).ready(function() {
		
		$.ajax({url: "insereFuncaoExec.php?&var_action=del&var_chavereg="+prCodigo, success: function(result){																		
			carregaFuncao(prCodigoPf)	
		   }
	    });
    });		

}

</script>
</html>

<?php
$objConn = NULL;
?>
