<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
$objConn = abreDBConn(CFG_DB);
$intCodDado	= request("codDado");		
$id = request("var_chavereg");   // Código chave da página

function sair(){
	echo "<script> 
				
				location.href = '../modulo_PainelPJ/STIndex.php';	
				
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
	
mensagem("info_exclusao_realisada","","", "javascript:window.close();","info",1);	


$objConn = NULL; 
?>
