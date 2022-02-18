<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$intDay   = (request("var_dia") != "") ? request("var_dia") : date('d');
$intMonth = (request("var_mes") != "") ? request("var_mes") : date('m');
$intYear  = (request("var_ano") != "") ? request("var_ano") : date('Y');
$strDate  = NULL;

if($intDay != "" && $intMonth != "" && $intYear != ""){
	if($intMonth >= 13){ $intMonth=1;  $intYear++; }
	if($intMonth <= 0) { $intMonth=12; $intYear--; }
	$strDate = strtotime($intMonth . "/" . $intDay . "/" . $intYear);
}

function getLastDay($prMes, $prAno){
 	if(($prMes % 2 == 1 && $prMes <= 7) || ($prMes % 2 == 0 && $prMes >= 8)){
		$retValue = 31; // (1,3,5,7,8,10,12)
	}
	elseif($prMes != 2) {
		$retValue = 30; // (4,6,9,11)
	}
	elseif($prAno % 4 == 0 && $prAno % 100 <> 0 || $prAno % 400 == 0) {
		$retValue = 29; //(2, ano bissexto)
	}
	else{
		$retValue = 28; //'(2, ano normal)
	}
	
	return($retValue);
}

$intWeekday   = date('w',$strDate);
$intDaysMonth = getLastDay($intMonth, $intYear);

$arrDiaSemana[] = getTText("dom",C_NONE);
$arrDiaSemana[] = getTText("seg",C_NONE);
$arrDiaSemana[] = getTText("ter",C_NONE);
$arrDiaSemana[] = getTText("qua",C_NONE);
$arrDiaSemana[] = getTText("qui",C_NONE);
$arrDiaSemana[] = getTText("sex",C_NONE);
$arrDiaSemana[] = getTText("sab",C_NONE);

$arrMes[] = getTText("janeiro",C_UCWORDS);
$arrMes[] = getTText("fevereiro",C_UCWORDS);
$arrMes[] = getTText("marco",C_UCWORDS);
$arrMes[] = getTText("abril",C_UCWORDS);
$arrMes[] = getTText("maio",C_UCWORDS);
$arrMes[] = getTText("junho",C_UCWORDS);
$arrMes[] = getTText("julho",C_UCWORDS);
$arrMes[] = getTText("agosto",C_UCWORDS);
$arrMes[] = getTText("setembro",C_UCWORDS);
$arrMes[] = getTText("outubro",C_UCWORDS);
$arrMes[] = getTText("novembro",C_UCWORDS);
$arrMes[] = getTText("dezembro",C_UCWORDS);

$objConn = abreDBConn(CFG_DB);

try{
	$strSQL = " SELECT '#' || date_part('day',prev_dt_ini), date_part('month',prev_dt_ini) AS month, cod_todolist 
				 FROM tl_todolist 
				WHERE prev_dt_ini BETWEEN '" . $intYear . "-" . $intMonth . "-1' AND '" . $intYear . "-" . $intMonth . "-" . $intDaysMonth . "'
				  AND (id_responsavel = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "' OR id_ult_executor = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "')
				  AND dt_realizado IS NULL";
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_title","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

($objRS == NULL) ? $objRS = array() : NULL; // Para preencher com um array caso venha NULL da consulta

echo("
<html>
<head>
 <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
 <link href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" rel=\"stylesheet\" type=\"text/css\">
 <style>
	body           { margin:0px; background-color:transparent; }
	.dia_anterior  { border:1px solid red;    font-weight:bold; cursor:pointer; }
	.dia_posterior { border:1px solid blue;   font-weight:bold; cursor:pointer; }
	.dia_atual     { border:1px solid silver; font-weight:bold; cursor:pointer; }
 </style>
 <script>
	function viewChamado(prCod){
		window.open(\"chamadoview.php?var_chavereg=\" + prCod,\"" . CFG_SYSTEM_NAME . "_chamado\",\"popup=yes,width=650,height=400,scrollbars=1\");
	}
 </script>
</head>
<body>
	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"175\">
		<tr bgcolor=\"#CCCCCC\" height=\"15\">
			<td align=\"center\" style=\"cursor:pointer;\" onClick=\"location.href='calendar.php?var_mes=" . intval($intMonth-1) . "&var_ano=" . $intYear . "'\">&lt;&lt;</td>
			<td align=\"center\" colspan=\"5\" align=\"center\">" . $arrMes[$intMonth-1] . " - " . $intYear . "</td>
			<td align=\"center\" style=\"cursor:pointer;\" onClick=\"location.href='calendar.php?var_mes=" . intval($intMonth+1) . "&var_ano=" . $intYear . "'\">&gt;&gt;</td>
		</tr>
		<tr bgcolor=\"#BFBFBF\">\n");
	
foreach($arrDiaSemana as $strDiaSemana){ echo("\t\t\t<td align=\"center\" width=\"25\">" . $strDiaSemana . "</td>\n"); }

echo("\t\t</tr>\n\t\t<tr>\n");

$intAuxDay     = 1;
$intAuxWeekday = 0;

while(date("w",strtotime($intMonth . "/" . $intAuxDay . "/" . $intYear)) != $intAuxWeekday){
	echo("\t\t\t<td></td>\n");
	$intAuxWeekday++;
}

while($intDaysMonth >= $intAuxDay){
	echo("\t\t\t<td align=\"center\">");
	
	if($objRS != NULL && in_array("#".$intAuxDay, $objRS)){
		$strStyle = (($intYear > intval(date("Y"))) || ($intYear == intval(date("Y")) && getValue($objRS,"month") > intval(date("m"))) || ($intAuxDay > $intDay && $intYear == intval(date("Y")) && getValue($objRS,"month") == intval(date("m")))) ? "dia_posterior" : "dia_anterior";	
		echo("<div onClick=\"viewChamado('" . getValue($objRS,"cod_todolist") . "');\" class=\"" . $strStyle . "\">
			 " . $intAuxDay . "
			 </div>");
		$objRS = $objResult->fetch();
	}
	else{
		($intAuxDay == $intDay) ? $strStyle = "dia_atual" : $strStyle = "";
		echo("<div class=\"" . $strStyle . "\">" . $intAuxDay . "</div>");
	}
	
	echo("</td>\n");
	
	$intAuxDay++;	$intAuxWeekday++;
	
	if($intAuxWeekday == 7) { echo("\t\t</tr>\n\t\t<tr>\n"); $intAuxWeekday = 0; }
}

while($intAuxWeekday < 7 && $intAuxWeekday != 0) { echo("\t\t\t<td>&nbsp;</td>\n"); $intAuxWeekday++; }

echo("\t\t</tr>\n\t</table>\n</body></html>");
?>