<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";
$strEntidade = "";

/*** RECEBE PARAMETROS ***/
 $intCodPJ 		= request("var_cod_pj");
 $intCodPF 		= request("var_cod_pf");
 $intCodPjPf		= request("var_cod_pj_pf");



$strLocation 		 = request("var_redirect");





$objConn->beginTransaction();
try{
	//die("update aqui".$intCodPF);

	
	// update na tabela de relacões
	
		$strSQL  = " DELETE FROM relac_pj_pf ";	
		$strSQL .= " WHERE cod_pj_pf = " . $intCodPjPf;
		$objConn->query($strSQL);
	
		//$strSQL  = " DELETE FROM cad_pf ";	
		//$strSQL .= " WHERE cod_pf = " . $intCodPF;	
		//$objConn->query($strSQL);
	
		
	$objConn->commit();
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}
$objConn = NULL;

redirect($strLocation);
?>