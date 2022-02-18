<?php

$strMes = date('m');
$strAno = date('Y');

$strSQL = "SELECT SUM(vlr_saldo) AS total FROM FIN_CONTA WHERE dtt_inativo IS NULL";

try{
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$strSaldo = 0;
$objRS = $objResult->fetch();

if(getvalue($objRS, 'total') != ''){
	$strSaldo = getvalue($objRS, 'total');
}
$objResult->closecursor();

$strSQL = " SELECT 
				SUM(vlr_conta) AS entrada,
				(SELECT SUM(vlr_conta) FROM FIN_CONTA_PAGAR_RECEBER WHERE pagar_receber=TRUE 
					AND to_char(DT_VCTO,'MM')= '" . $strMes . "' AND to_char(DT_VCTO,'YYYY')= '" . $strAno . "'
					AND SITUACAO <> 'cancelada'
				) AS saida 
			FROM 	
				FIN_CONTA_PAGAR_RECEBER 
			WHERE 
				PAGAR_RECEBER=FALSE  
			AND SITUACAO <> 'cancelada' 
			AND 
				to_char(DT_VCTO,'MM')= '" . $strMes . "'
			AND 
				to_char(DT_VCTO,'YYYY') = '" . $strAno . "' ";




try{
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$strReceitaPrevista = 0;
$strDespesaPrevista = 0;
foreach($objResult as $objRS){

	if(getvalue($objRS,'entrada') != ''){
		$strReceitaPrevista += getvalue($objRS,'entrada');
	}
	if(getvalue($objRS,'saida')){
		$strDespesaPrevista += getvalue($objRS,'saida');
	}
}
$objResult->closecursor();


$strSQL = "	SELECT 
				SUM(VLR_LCTO) AS ENTRADA1,
				SUM(VLR_DESC) AS ENTRADA2,
				SUM(VLR_MULTA) AS ENTRADA3,
				SUM(VLR_JUROS) AS ENTRADA4,
				(SELECT SUM(VLR_LCTO) FROM FIN_LCTO_ORDINARIO ORD INNER JOIN FIN_CONTA_PAGAR_RECEBER PR ON 				
					(ORD.COD_CONTA_PAGAR_RECEBER=PR.COD_CONTA_PAGAR_RECEBER) 
					WHERE 	PR.PAGAR_RECEBER=TRUE 
					AND to_char(PR.DT_VCTO, 'MM')= '" . $strAno . "'
					AND to_char(PR.DT_VCTO, 'YYYY')= '" . $strAno . "'  ) AS SAIDA1,
				(SELECT SUM(VLR_DESC) FROM FIN_LCTO_ORDINARIO ORD INNER JOIN FIN_CONTA_PAGAR_RECEBER PR ON 
					(ORD.COD_CONTA_PAGAR_RECEBER=PR.COD_CONTA_PAGAR_RECEBER)
					WHERE PR.PAGAR_RECEBER=TRUE 
					AND to_char(PR.DT_VCTO, 'MM')= '" . $strAno . "'
					AND to_char(PR.DT_VCTO, 'YYYY')= '" . $strAno . "'  ) AS SAIDA2,
				(SELECT SUM(VLR_MULTA) FROM FIN_LCTO_ORDINARIO ORD INNER JOIN FIN_CONTA_PAGAR_RECEBER PR ON 
					(ORD.COD_CONTA_PAGAR_RECEBER=PR.COD_CONTA_PAGAR_RECEBER) 
					WHERE PR.PAGAR_RECEBER=TRUE 
					AND to_char(PR.DT_VCTO, 'MM')= '" . $strAno . "'
					AND to_char(PR.DT_VCTO, 'YYYY')= '" . $strAno . "' ) AS SAIDA3,
				(SELECT SUM(VLR_JUROS) FROM FIN_LCTO_ORDINARIO ORD INNER JOIN FIN_CONTA_PAGAR_RECEBER PR ON 
					(ORD.COD_CONTA_PAGAR_RECEBER=PR.COD_CONTA_PAGAR_RECEBER) 
					WHERE PR.PAGAR_RECEBER=TRUE 
					AND to_char(PR.DT_VCTO, 'MM')= '" . $strAno . "'
					AND to_char(PR.DT_VCTO, 'YYYY')= '" . $strAno . "' ) AS SAIDA4 
			 FROM 
				FIN_LCTO_ORDINARIO ORD 
			INNER JOIN FIN_CONTA_PAGAR_RECEBER PR ON (ORD.COD_CONTA_PAGAR_RECEBER = PR.COD_CONTA_PAGAR_RECEBER) 
			WHERE 
				PR.PAGAR_RECEBER=FALSE 
			AND 
				to_char(PR.DT_VCTO, 'MM')= '" . $strMes . "'  
			AND 
				to_char(PR.DT_VCTO, 'YYYY')= '" . $strAno . "'";

?>