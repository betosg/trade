<?php
/***         * DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 



//REQUESTS
	$strIdentificador	  		 		= request("var_identificador");
	



$objConn = abreDBConn(CFG_DB); // Abertura de banco

$objConn->beginTransaction();
try{
			
	 $strSQL =  "DELETE FROM  fin_conta_pagar_receber
					WHERE /*fin_conta_pagar_receber.tipo = 'cad_pj' */
						    fin_conta_pagar_receber.situacao = 'aberto' 
						AND sys_dtt_cancel is null
						AND fin_conta_pagar_receber.situacao = 'aberto' 
						AND fin_conta_pagar_receber.identificador_lote = '".$strIdentificador ."'";
		//die($strSQL);
	 $objConn->query($strSQL);		
		//$objRS = $objResult->fetch();
		
	$objConn->commit();
}catch(PDOException $e){
	$objConn->rollBack();
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}
$objConn = NULL;

?>
<form name="formfatura" action="STEdeletaTituloLoteFim.php" method="post">
			<input type="text" name="var_identificador"        id="var_identificador"        value="<?php echo($strIdentificador); ?>">
</form>
<script language="javascript">
	document.formfatura.submit();
</script>