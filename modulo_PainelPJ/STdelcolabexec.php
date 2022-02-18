<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
$objConn = abreDBConn(CFG_DB);
$intCodDado	= request("codDado");		
$id = request("var_chavereg");   // Código chave da página

function sair(){
	echo "<script> 
				
				location.href = 'STColabAtivos.php';	
				
		</script>";
}

try{
	$strSQL = "DELETE FROM relac_pj_pf
		   		WHERE cod_pf =".$id;
	$objConn->query($strSQL);
	$strSQL = "DELETE FROM cad_pf
		   		WHERE cod_pf =".$id;
	$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	// exibe mensagem de confirmação de exclusão da PF	
	echo "
		<center>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
				<tr>
				<td align=\"center\" valign=\"middle\" width=\"100%\">";
				mensagem("info_exclusao_realizada","info_exclusao_pf","","STColabAtivos.php","info",1);		
	echo "		</td>
				</tr>
			</table>
		</center>";
	die();	
	//mensagem("info_exclusao_realizada","","", "STpainelPJ.php","info",1);	


$objConn = NULL; 
?>
