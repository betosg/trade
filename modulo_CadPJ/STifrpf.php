<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

$objConn  = abreDBConn(CFG_DB);

$intVlrCampoChaveDetail  = request("var_chavereg");
$strNomeCampoChaveDetail = request("var_field_detail");

try {

	$strSQL = "	SELECT 
 					distinct 
					cad_pf.cod_pf, 
 					cad_pf.nome, 
 					cad_pf.data_nasc 
				FROM cad_pf  
				JOIN cad_pf_pj ON cad_pf.cod_pf = cad_pf_pj.cod_pf AND cad_pf_pj.cod_pj = ".$intVlrCampoChaveDetail."  AND dt_demissao is null 
					ORDER BY cad_pf.nome ASC";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) { 	
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
?>
<html>
<head>
	 <title></title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	 <!-- <link href="../_css/fpsproject.css" rel="stylesheet" type="text/css"> -->
	 <link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
	  <link rel="stylesheet" type="text/css" href="../_css/tradeunion.css">
	 <script type="text/javascript" src="../_scripts/tablesort.js"></script>
     <script language="javascript" type="text/javascript">
		function novo(prValue){
			if(prValue != ""){
				parent.location.href = prValue;
			}
		}
		
		function gerarAll(){
			document.frm_credencial.submit();
		}
		
		function gerar(prCod){
			window.open('STgeracredencial.php?var_chavereg=' + prCod , "" , "status , scrollbars=no ,width=400, height=200 , top=0 , left=0");
		}
		
		function checkall(checked){
			for(var i = 0 ; i < document.frm_credencial.elements.length ; i++){
				if(document.frm_credencial.elements[i].type == 'checkbox'){
					document.frm_credencial.elements[i].checked = checked;
				}
			}
		}
		
		/*window.onload = function(){
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = document.body.scrollHeight;
		
			if(window.parent.document.frmSizeBody){
				var codAvo = window.parent.document.frmSizeBody.codAvo.value;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = window.parent.document.body.scrollHeight;
			}
			
		}*/
	window.onload = function (){
		alert ( <?php echo $intVlrCampoChaveDetail;  ?>);
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = document.body.scrollHeight;
			document.frmSizeBody.sizeBody.value = document.body.scrollHeight;
		}	
	 </script>
</head>
<!-- <body style="margin:10px 0px 10px 0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(getsession("sys_theme")); ?>_main.jpg"> -->
<body style="margin:10px 0px 10px 0px;" bgcolor="#CFCFCF">
<form name='frmSizeBody'>
	<input type='hidden' value='' name='sizeBody'>
	<input type='hidden' value='<?php echo($intVlrCampoChaveDetail); ?>' name='codAvo'>
</form>
<table align="center" cellpadding="0" cellspacing="0" style="width:100%" class="tablesort">
 <thead>
  <tr> 
	<th width="1%"></th>
	<th width="1%"></th>
	<!-- th width="5%" align="left"><input type="checkbox" onclick="checkall(this.checked)" class="inputclean"></th -->
	<th width="14%" class="sortable-numeric" nowrap><?php echo(getTText("nome",C_NONE)); ?></th>
	<th width="20%" class="sortable" nowrap><?php echo(getTText("data_nasc",C_NONE)); ?></th>
	<th width="20%" class="sortable" nowrap><?php echo(getTText("sexo",C_NONE)); ?></th>
  </tr>
 </thead>

 <tbody>
  <?php 
	if($objResult->rowCount() == 0 || $objResult == ""){
		echo("<tr><td colspan=\"10\" align=\"center\"></td></tr>");
	}
	else { 
	 $Ct=1;  
	 foreach($objResult as $objRS){ 
		
	 
	    $strCOLOR = (($Ct%2)==0)?"#FFFFFF":"#F5FAFA";
   ?>			 
		  <tr bgcolor=<?php echo($strCOLOR) ?>> 
			<td width="1%" style="vertical-align:middle;">
				<a href="../modulo_CadPF/insupddelmastereditor.php.php?var_oper=DEL&var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_value_detail=<?php echo($intVlrCampoChaveDetail); ?>&var_field_detail=cod_pf&var_populate=yes"><img alt="Remover" src="../img/icon_trash.gif" style="cursor:pointer;"></a>
			</td>
			<td width="1%" style="vertical-align:middle;">
				<a href="../modulo_CadPF/insupddelmastereditor.php?var_oper=UPD&var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_value_detail=<?php echo($intVlrCampoChaveDetail); ?>&var_field_detail=cod_pj=cod_pf&var_populate=yes"><img alt="Editar" src="../img/icon_write.gif" style="cursor:pointer;"></a>
			</td>
			<td><?php echo(getValue($objRS,"nome")) ?></td>
			<td><?php echo(date("d/m/Y",strtotime(getValue($objRS,"data_nasc")))) ?></td>
			<td><?php echo(getValue($objRS,"sexo")) ?></td>
		  </tr>			 
   <?php  
	   $Ct++;
	  }
	  $objResult->closeCursor();
	}
   ?>
 </tbody>
</table>
</body>
</html>
