<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$objConn = abreDBConn(getsession(CFG_SYSTEM_NAME . "_db_name"));

$intCodPagarReceber = request("var_cod_conta_pagar_receber");

$strSQL = " SELECT 
				*
			FROM
				fin_conta_pagar_receber
			WHERE
				cod_conta_pagar_receber = ".$intCodPagarReceber." ";
				
				
$objResult = $objConn->query($strSQL);

if($objRS = $objResult->fetch()){

	
	
	if(getValue($objRS,"tipo") == "cad_pj"){
		$strSQL = "SELECT nome_fantasia AS nome FROM cad_pj WHERE cod_pj =" . getValue($objRS,"codigo");
	}else if(getValue($objRS,"tipo") == "cad_pf"){
		$strSQL = "SELECT nome AS nome FROM cad_pf WHERE cod_pf =" . getValue($objRS,"codigo");
	}
	$strEntidade = "";
	if($strSQL != "") {
		
		try{
			$objResultTipo = $objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
		
		if($objRSTipo = $objResultTipo->fetch()) { $strEntidade = getValue($objRSTipo, "nome"); } 
		$objResultTipo->closeCursor();
	}
	
	$strTitle = "receber";
	if(getValue($objRS,"pagar_receber")){
		$strTitle = "pagar";
	}
	
	$strTipoSituacao = array("cancelado"=>"CANCELADO", "lcto_parcial"=>"LANCAMENTO PARCIAL", "lcto_total"=>"LANÇAMENTO TOTAL", "aberto"=> "ABERTO","agrupado"=>"AGRUPADO" );
	
	$strRetorno = getValue($objRS,"cod_conta_pagar_receber") . ";" .
	getValue($objRS,"codigo") . ";" .
	getValue($objRS,"tipo_documento") . ";" .
	getValue($objRS,"num_documento") . ";" .
	((getValue($objRS,"dt_vcto")) ? date('d/m/Y',strtotime(getValue($objRS,"dt_emissao"))) : ''). ";" . ((getValue($objRS,"dt_vcto")) ? date('d/m/Y',strtotime(getValue($objRS,"dt_vcto"))) : '') . ";" . getValue($objRS,"historico") . ";" . getValue($objRS,"obs") . ";" .
	getValue($objRS,"cod_plano_conta") . ";" .  number_format((double) getValue($objRS,"vlr_conta"),2,",","") . ";" .  number_format((double) getValue($objRS,"vlr_pago"),2,",","") . ";" .  number_format((double) getValue($objRS,"saldo"),2,",","") . ";". getValue($objRS,"cod_centro_custo") . ";". getValue($objRS,"tipo") . ";" .$strTitle . ";" . $strEntidade . ";" . $strTipoSituacao[getValue($objRS,"situacao")];
}                                                                                    
else{
	$strRetorno = '';
}

echo(trim($strRetorno));
	
$objResult->closeCursor();
?>