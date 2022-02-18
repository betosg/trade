<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Leitura de eventos + Agendas
	$intDay   = (request("var_dia") != "") ? request("var_dia") : date('d');
	$intMonth = (request("var_mes") != "") ? request("var_mes") : date('m');
	$intYear  = (request("var_ano") != "") ? request("var_ano") : date('Y');
	$strDate  = NULL;
	$resp = false;
	if($intDay != "" && $intMonth != "" && $intYear != ""){
		if($intMonth >= 13){ $intMonth=1;  $intYear++; }
		if($intMonth <= 0) { $intMonth=12; $intYear--; }
		$strDate = strtotime($intMonth . "/" . $intDay . "/" . $intYear);
	}


function eventos($dia, $mes, $ano){
	// Abre Conexão com o BD
	$objConn = abreDBConn(CFG_DB);
	//contador para montar a div, ela sera montada caso haja 4 ou mais eventos
	$intCount = 0;
	// Concatena data a ser enviada
	$data = "'".$ano."-".$mes."-".$dia."'";
	// Localiza eventos + Agenda
	$strSQL = "	SELECT DISTINCT
					  ag_agenda.id_responsavel
					, ag_agenda.cod_agenda
					, ag_agenda.prev_dtt_ini
					, ag_agenda.prev_dtt_fim 
					, ag_agenda.titulo
					, ag_agenda.prioridade
					, ag_agenda.categoria
					, SUBSTRING(ag_agenda.descricao,0,35) AS descricao
				FROM ag_agenda
				INNER JOIN ag_agenda_citado ON (ag_agenda.cod_agenda = ag_agenda_citado.cod_agenda)
				WHERE prev_dtt_ini BETWEEN (".$data.") AND (CAST(".$data." AS DATE) + INTERVAL '3 DAYS')
				AND ag_agenda_citado.id_usuario = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
				AND dtt_realizado IS NULL 
				ORDER BY prev_dtt_ini ASC";
	$objResult = $objConn->query($strSQL); 			
 /*	Este codigo comentado monta a div com scroll, caso haja necessidade dela novamente basta descomenta-la, descomentar tambem as linhas: 136 e 139 pois elas fecham esse div
 	By GS 24/11/2011
 		$strSQLCount = "
				SELECT count(ag_agenda.cod_agenda) as qtda					
				FROM ag_agenda
				INNER JOIN ag_agenda_citado ON (ag_agenda.cod_agenda = ag_agenda_citado.cod_agenda)
				WHERE prev_dtt_ini BETWEEN (".$data.") AND (CAST(".$data." AS DATE) + INTERVAL '3 DAYS')
				AND ag_agenda_citado.id_usuario = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
				AND dtt_realizado IS NULL 
				GROUP BY  ag_agenda.id_responsavel
					, ag_agenda.cod_agenda
					, ag_agenda.prev_dtt_ini
					, ag_agenda.prev_dtt_fim 
					, ag_agenda.titulo
					, ag_agenda.prioridade
					, ag_agenda.categoria
					, SUBSTRING(ag_agenda.descricao,0,35)
				ORDER BY prev_dtt_ini ASC";
		// die($strSQL);
		$objResultCount = $objConn->query($strSQLCount); 

		foreach($objResultCount as $objRSCount) { 
			$intCount = $intCount + getValue($objRSCount,"qtda");
		}
		
		
		// monta o resultado em uma tabela 
		// listando os eventos em auxiliar
		if ($intCount >= 4)
			{
				echo ("<div style='overflow:auto; width:187px; height:297px; border:0px solid #CCC; padding-botton=5px;vertical-align:top;' id='str_div_frame'>");
			} */
	echo "<table border='0' width='170' height='52' align='center' style='padding-bottom:5px;'>";
	
	//foreach do resultado de eventos
	foreach($objResult as $objRS) { 
		$dtWDay = diaSemana(getValue($objRS,"prev_dtt_ini"));
		// echo time(dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false));
		// $data = date("N",time(dDate(CFG_LANG,getValue($objRS,"prev_dt_ini")));
		// OLD - ANTIGA MODELAGEM DE AGENDA. AGORA PRIORIDADE É SETADA DI-
		// RETAMENTE
		// @$altTitle = getValue($objRS,"prioridade");
		// if($altTitle=="status_img_normal"){$altTitle = "PRIORIDADE: ";}
		// if($altTitle=="status_img_alta") {$altTitle = "PRIORIDADE: ";}
		// if($altTitle=="status_img_media"){$altTitle = "PRIORIDADE: MEDIA";}
		// if($altTitle=="status_img_baixa"){$altTitle = "PRIORIDADE: BAIXA";}
		$strImgPrioridade  = "";
		$strImgPrioridade .= (getValue($objRS,"prioridade") == "BAIXA" ) ? "status_img_baixa"  : "";
		$strImgPrioridade .= (getValue($objRS,"prioridade") == "NORMAL") ? "status_img_normal" : "";
		$strImgPrioridade .= (getValue($objRS,"prioridade") == "MEDIA" ) ? "status_img_media"  : "";
		$strImgPrioridade .= (getValue($objRS,"prioridade") == "ALTA"  ) ? "status_img_alta"   : "";
		
		if ($strImgPrioridade != "") $strImgPrioridade = "<img alt='".getValue($objRS,"prioridade")."' src='../img/".$strImgPrioridade.".gif' style='border: none;' />";
		
		echo("
			<tr>
				<td title='".getValue($objRS,"titulo")."\nPRIODIDADE ".getValue($objRS,"prioridade")."' style='padding:10px 0px 10px 10px;border:1px solid #CCC;width:auto'> 
					<div class='comment_peq' style='border:none; cursor: pointer;' onclick=\"redirecionaResp('".getValue($objRS,"cod_agenda")."');\">							
						<table cellspacing='0' cellpadding='0' border='0' style='width:auto;'>
						<tr>
							<td width='1%' align='left' style='vertical-align:top;'>".$strImgPrioridade."</td>
							<td width='99%' style='padding:0px 5px 0px 5px;'>
								<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td align='left' class='titulo_agenda'><strong>".$dtWDay."</strong></td>
									<td align='left' class='titulo_agenda'> - ".substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),0,5)." ".substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),11,5)."</td>
								</tr>
								<tr>
									<td colspan='2' align='justify'><div style='width:108px;height:15px;overflow:hidden;'>".strtoupper(getValue($objRS,"titulo"))."</div></td>
								</tr>
								<tr>
									<td colspan='2' align='right' class='comment_peq'>(".getValue($objRS,"categoria").")</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</div>
				</td>
			</tr>"); 
	}
	
	// Finaliza a Table
	echo("<tr><td colspan='2'>&nbsp;</td></tr></table>");
	
	//finaliza a div com rolagem
	/*if ($intCount >= 4)
		{
			echo ("</div>");
		}*/
	}
	
function diaSemana($prData){
	$dtDate = $prData;
	$dtDate = @explode("-",$dtDate);
	$dtDate = @mktime(0,0,0,$dtDate[1],$dtDate[2],$dtDate[0]);
	$dtDate = @getdate($dtDate);
	$dtDate = $dtDate["wday"];
	switch($dtDate){
		case 0: $dtDate = 'DOM';break;
		case 1: $dtDate = 'SEG';break;
		case 2: $dtDate = 'TER';break;
		case 3: $dtDate = 'QUA';break;
		case 4: $dtDate = 'QUI';break;
		case 5: $dtDate = 'SEX';break;
		case 6: $dtDate = 'SÁB';break;
		default : $dtDate = '---';break; 
	}
	return($dtDate);
}

echo("
	<html>
		<head>
	 		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
	 		<link href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" rel=\"stylesheet\" type=\"text/css\">
	 		<style>
				 body          { margin:0px; background-color:transparent; }
				.hoje  		   { border:1px solid orange;font-weight:bold;cursor:pointer;}
				.dia_anterior  { border:1px solid red;   font-weight:bold;cursor:pointer; }
				.dia_posterior { border:1px solid blue;  font-weight:bold;cursor:pointer; }
				.dia_atual     { border:1px solid silver;font-weight:bold;cursor:pointer; }
				.titulo_agenda { font-size:11px;color:#666;}
	 		</style> 
			<script>
				var winpopup       = null;
				var winpopup_prostudio = null;
				
				function AbreJanelaPAGE(prpage, prwidth, prheight) { 
					var auxstr;
					auxstr  = 'width=' + prwidth;
					auxstr  = auxstr + ',height=' + prheight;
					auxstr  = auxstr + ',top=30,left=30,scrollbars=1,resizable=yes,status=yes';
					if (winpopup_prostudio != null){
						winpopup_prostudio.close();
					}
						winpopup_prostudio = window.open(prpage, 'winpopup_prostudio', auxstr);
				}
				
				function recarrega(){");
						//if ($intCount >= 4)
							//{
								echo("\nwindow.parent.document.getElementById('dbvar_str_agenda').height=340;");
						//	}
						//else
						//	{
						//		echo("\nwindow.parent.document.getElementById('dbvar_str_agenda').height = document.getElementById('dbvar_str_conteudo').scrollHeight + 0;");			
						//	}
					
				echo("\n}\n				
				function redirecionaResp(prInt){
					var intCod = prInt;
					AbreJanelaPAGE('../modulo_Agenda/STinsresposta.php?var_flag_close=CLOSE&var_chavereg='+intCod,'620','675');
				}
			</script>
		</head>
	<body  style='margin:0px 0px 0px 0px;'>");
	
	$resultado = eventos($intDay, $intMonth, $intYear);
	echo $resultado;
?>
</body>
</html>