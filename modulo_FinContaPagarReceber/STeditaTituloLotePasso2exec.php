<?php
/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
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
	$data_antes	  		 		= request("var_data_antes");
	$data_depois 		 		= request("var_data_depois");
	$vlr_titulo_antes    		= request("var_vlr_titulo_antes");
	$vlr_titulo_depois   		= request("var_vlr_titulo_depois");
	$plano_conta_antes  		= request("var_cod_conta_antes");
	$plano_conta_depois  		= request("var_cod_conta_depois");
	$centro_custo_antes  		= request("var_cod_centro_antes");
	$centro_custo_depois 		= request("var_cod_centro_depois");
	$job_antes 					= request("var_job_antes");
	$job_depois 				= request("var_job_depois");
	$observacao_antes 			= request("var_observacao_antes");
	$observacao_depois			= request("var_observacao_depois");
	$cfg_boleto_antes  			= request("var_cfg_boleto_antes");
	$cfg_boleto_depois          = request("var_cfg_boleto_depois");

//requests debug
/*echo "<br>".	$data_antes	  		 		= request("var_data_antes");
echo "<br>".	$data_depois 		 		= request("var_data_depois");
echo "<br>".	$vlr_titulo_antes    		= request("var_vlr_titulo_antes");
echo "<br>".	$vlr_titulo_depois   		= request("var_vlr_titulo_depois");
echo "<br>".	$plano_conta_antes  		= request("var_cod_conta_antes");
echo "<br>".	$plano_conta_depois  		= request("var_cod_conta_depois");
echo "<br>".	$centro_custo_antes  		= request("var_cod_centro_antes");
echo "<br>".	$centro_custo_depois 		= request("var_cod_centro_depois");
echo "<br>".	$job_antes 					= request("var_job_antes");
echo "<br>".	$job_depois 				= request("var_job_depois");
echo "<br>".	$observacao_antes 			= request("var_observacao_antes");
echo "<br>".	$observacao_depois			= request("var_observacao_depois");
echo "<br>".	$cfg_boleto_antes 			= request("var_cfg_boleto_antes");
echo "<br>".	$cfg_boleto_depois 			= request("var_cfg_boleto_depois");
*/



$objConn = abreDBConn(CFG_DB); // Abertura de banco

$objConn->beginTransaction();
try{
			
	$strSQL =  "UPDATE  fin_conta_pagar_receber  
			SET 
			      dt_vcto	 		= '" . $data_depois . "' 
			  , vlr_conta	 		= "  .  $vlr_titulo_depois . " 
			  , cod_plano_conta 	= "  .  $plano_conta_depois . " 
			  , cod_centro_custo 	= "  .  $centro_custo_depois . " 
			  , cod_job 			= "  .  $job_depois . " 
			  , obs					= '" . $observacao_depois ."'
			  , cod_cfg_boleto      = "  . $cfg_boleto_depois ."
			WHERE fin_conta_pagar_receber.tipo = 'cad_pj' 
			  AND fin_conta_pagar_receber.situacao = 'aberto' ";
			if (($data_antes != "") and ($data_depois != ""))
				{$strSQL .=" AND to_char(fin_conta_pagar_receber.dt_vcto,'DD/MM/YYYY') = '".$data_antes."'";}
				
			if (($vlr_titulo_antes != "")	and		($vlr_titulo_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.vlr_conta = ".$vlr_titulo_antes;}
				
			if (($plano_conta_antes != "")			and		($plano_conta_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.cod_plano_conta = " . $plano_conta_antes;}
				
			if (($centro_custo_antes != "")	and		($centro_custo_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.cod_centro_custo = ".$centro_custo_antes;}
				
			if (($job_antes != "")			and		($job_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.cod_job = " . $job_antes;}	
				
   			if (($observacao_antes != "")			and		($observacao_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.obs ILIKE '" . $observacao_antes . "'";}		
				
   			if (($cfg_boleto_antes != "")			and		($cfg_boleto_depois != ""))
				{$strSQL .=" AND fin_conta_pagar_receber.cod_cfg_boleto ILIKE '" . $cfg_boleto_antes . "'";}		
		//die($strSQL);
		$objResult = $objConn->query($strSQL);		
		$objRS = $objResult->fetch();
		
	$objConn->commit();
}catch(PDOException $e){
	$objConn->rollBack();
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}
$objConn = NULL;

?>
<form name="formfatura" action="STEditaTituloLoteFim.php" method="post">
			<input type="text" name="var_data_antes"        id="var_data_antes"        value="<?php echo($data_antes); ?>">
			<input type="text" name="var_data_depois"       id="var_data_depois"       value="<?php echo($data_depois); ?>">
			<input type="text" name="var_vlr_titulo_antes"  id="var_vlr_titulo_antes"  value="<?php echo($vlr_titulo_antes); ?>">
			<input type="text" name="var_vlr_titulo_depois" id="var_vlr_titulo_depois" value="<?php echo($vlr_titulo_depois); ?>">
			<input type="text" name="var_cod_conta_antes"   id="var_cod_conta_antes"   value="<?php echo($plano_conta_antes); ?>">
			<input type="text" name="var_cod_conta_depois"  id="var_cod_conta_depois"  value="<?php echo($plano_conta_depois); ?>">
			<input type="text" name="var_cod_centro_antes"  id="var_cod_centro_antes"  value="<?php echo($centro_custo_antes); ?>">
			<input type="text" name="var_cod_centro_depois" id="var_cod_centro_depois" value="<?php echo($centro_custo_depois); ?>">
			<input type="text" name="var_job_antes"         id="var_job_antes"         value="<?php echo($job_antes); ?>">
			<input type="text" name="var_job_depois"        id="var_job_depois"        value="<?php echo($job_depois); ?>">
            <input type="text" name="var_observacao_antes"         id="var_observacao_antes"         value="<?php echo($observacao_antes); ?>">
			<input type="text" name="var_observacao_depois"        id="var_observacao_depois"        value="<?php echo($observacao_depois); ?>">
            <input type="text" name="var_cfg_boleto_antes" id="var_cfg_boleto_antes" value="<?php echo($cfg_boleto_antes); ?>">
			<input type="text" name="var_cfg_boleto_depois" id="var_cfg_boleto_depois" value="<?php echo($cfg_boleto_depois); ?>">
            
</form>
<script language="javascript">
	document.formfatura.submit();
</script>