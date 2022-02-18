	<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	// Abre conexão com BD
	$objConn = abreDBConn(CFG_DB);
	
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
	
	
	function verificaData($dia, $mes, $ano){
		// Abre conexão com o BD
		$objConn = abreDBConn(CFG_DB);
		$data = "'".$ano."-".$mes."-".$dia."'";
		
		// Busca todas agendas/eventos onde a data
		// encaminhada seja igual a data de inicio
		// e que o usuario corrente seja responsavel
		$strSQL       = "SELECT ag_agenda.cod_agenda, ag_agenda.prev_dtt_ini FROM ag_agenda
						 LEFT JOIN ag_agenda_citado ON (ag_agenda.cod_agenda = ag_agenda_citado.cod_agenda)
					     WHERE ag_agenda_citado.id_usuario = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
						 AND (CAST(prev_dtt_ini AS DATE) = ".$data.")
						 AND dtt_realizado IS NULL";
		$objResult    = $objConn->query($strSQL);
		
		// se o usuario é criador da agenda ou é um citado
		if( ($objResult->rowCount() > 0) ){ return true; }
		else{ return false; }
	}
	
	function eventos($dia, $mes, $ano){
		// Abre Conexão com o BD
		$objConn = abreDBConn(CFG_DB);
		//contador para montar a div, ela sera montada caso haja 4 ou mais eventos
		$intCount = 0;
		// Concatena data a ser enviada
		$data = "'".$ano."-".$mes."-".$dia."'";
		// Localiza eventos + Agenda
		$strSQL = "
				SELECT DISTINCT
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
		$objResult = $objConn->query($strSQL); 
		
		// monta o resultado em uma tabela 
		// listando os eventos em auxiliar
		if ($intCount >= 4)
			{
				echo ("<div style='overflow:auto; width:187px; height:297px; border:0px solid #CCC; padding-botton=5px;vertical-align:top;' id='str_div_frame'>");
			}
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
			echo("
				<tr>
					<td title='".getValue($objRS,"titulo")."\nPRIODIDADE ".getValue($objRS,"prioridade")."' style='padding:10px 0px 10px 10px;border:1px solid #CCC;width:auto'> 
						<div class='comment_peq' style='border:none; cursor: pointer;' onclick=\"redirecionaResp('".getValue($objRS,"cod_agenda")."');\">							
							<!-- MIOLO -->
							<table cellspacing='0' cellpadding='0' border='0' style='width:auto;'>
								<tr>
									<td width='1%' align='left' style='vertical-align:top;'>
										<img alt='".getValue($objRS,"prioridade")."' src='../img/".$strImgPrioridade.".gif' style='border: none;' />
									</td>
									<td width='99%' style='padding:0px 5px 0px 5px;'>
										<table cellspacing='0' cellpadding='0' border='0' width='100%'>
											<tr>
												<td align='left' class='titulo_agenda'><strong>".$dtWDay."</strong></td>
												<td align='left' class='titulo_agenda'> 
													 - ".substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),0,5)." ".substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),11,5)."
												</td>
											</tr>
											<tr>
												<td colspan='2' align='justify'><div style='width:108px;height:15px;overflow:hidden;'>".strtoupper(getValue($objRS,"titulo"))."</div></td>
											</tr>
											<tr>
												<td colspan='2' align='right' class='comment_peq'>
													(".getValue($objRS,"categoria").")
												</td>
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
		if ($intCount >= 4)
			{
				echo ("</div>");
			}
	}

	function getLastDay($prMes, $prAno){
		if(($prMes % 2 == 1 && $prMes <= 7) || ($prMes % 2 == 0 && $prMes >= 8)){
			$retValue = 31; // (1,3,5,7,8,10,12)
		}elseif($prMes != 2) {
			$retValue = 30; // (4,6,9,11)
		}
		elseif($prAno % 4 == 0 && $prAno % 100 <> 0 || $prAno % 400 == 0) {
			$retValue = 29; //(2, ano bissexto)
		}else{
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
				
				function mudaHref(prDia, prMes, prAno){\n

					window.parent.document.getElementById('atualizaCalendar').dt_dia=prDia;
					window.parent.document.getElementById('atualizaCalendar').dt_mes=prMes;
					window.parent.document.getElementById('atualizaCalendar').dt_ano=prAno;

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
	<body  style='margin:0px 0px 0px 0px;'>
		<div id='dbvar_str_conteudo' style='' align='center'>
			<br />
			<table border='0' cellpadding='0' cellspacing='0' width='170' style='margin:0px 0px 0px 2px;border:1px solid #CCC;'>
			<!-- < <   NOME DO MES   > > -->
	 		<tr bgcolor='".CL_CORLINHA_2."' height='25'>
				<td align='center' style='border-bottom:1px solid #E9E9E9;cursor:pointer;'><strong><a href='STagendaCalendar.php?var_mes=".intval($intMonth-1)."&var_ano=".$intYear."'>&lt;&lt;</strong></a>
				</td>
				<td align='center' colspan='5' align='center' style='border-bottom:1px solid #E9E9E9;'>
					<strong>".strtoupper($arrMes[$intMonth-1])." - ".$intYear."</strong>
				</td>
				<td align='center' style='border-bottom:1px solid #E9E9E9;cursor:pointer;'><strong><a href='STagendaCalendar.php?var_mes=".intval($intMonth+1)."&var_ano=".$intYear."'>&gt;&gt;</strong></a>
				</td>
			</tr>
		<!-- NOME DO MES . FIM -->
			<tr bgcolor=".CL_CORLINHA_1.">\n");
	foreach($arrDiaSemana as $strDiaSemana){
		echo("\t\t\t<td align='center' width='25'>
					<strong>".$strDiaSemana."<strong>
					</td>\n"); 
	}
	echo("\t\t</tr>\n\t\t<tr>\n");
	$intAuxDay     = 1;
	$intAuxWeekday = 0;
	
	while(date("w",strtotime($intMonth . "/" . $intAuxDay . "/" . $intYear)) != $intAuxWeekday){
		echo("\t\t\t<td></td>\n");
		$intAuxWeekday++;
	}
	
	while($intDaysMonth >= $intAuxDay){
		$resp = verificaData($intAuxDay, $intMonth, $intYear);
		echo("\t\t\t<td align='center'>");
		
		if($objRS != NULL && in_array("#".$intAuxDay, $objRS)){
		/*$strStyle = (($intYear > intval(date("Y"))) || 
				($intYear == intval(date("Y")) && getValue($objRS,"month") > intval(date("m"))) || 
				($intAuxDay > $intDay && $intYear == intval(date("Y")) && getValue($objRS,"month") == 
				intval(date("m")))) ? "dia_posterior" : "dia_anterior"; 
				echo("<div onClick=\"viewChamado('" . getValue($objRS,"cod_todolist") . "');\" class=\"" . $strStyle . "\">
		 " . $intAuxDay . "</div>");
		$objRS = $objResult->fetch();
		*/
		} else{
			if(($intAuxDay == $intDay) and ($intMonth==date('n'))){
				$strStyle = "dia_atual";
			}else{
				$strStyle = "";
			}	
			if(($intAuxDay < $intDay) and ($intMonth <= date('m')) and ($resp == true)){
				$strStyle = "dia_anterior";
			?>
		<div class="<?php echo($strStyle);?>"><a href='STagenda.php?var_dia=<?php echo($intAuxDay);?>&var_mes=<?php echo($intMonth);?>&var_ano=<?php echo( $intYear);?>' target="ifrEventos" onclick="mudaHref(<?php echo($intAuxDay);?>,<?php echo($intMonth);?>,<?php echo( $intYear);?>);"><?php echo($intAuxDay);?></a></div>
			
			<?php 
				} else if(($intMonth < date('m')) and ($resp == true)){
				$strStyle = "dia_anterior";
			?> 
            
				<div class="<?php echo($strStyle);?>"><a href='STagenda.php?var_dia=<?php echo($intAuxDay);?>&var_mes=<?php echo($intMonth);?>&var_ano=<?php echo( $intYear);?>' target="ifrEventos" onclick="mudaHref(<?php echo($intAuxDay);?>,<?php echo($intMonth);?>,<?php echo( $intYear);?>);"><?php echo($intAuxDay);?></a></div>
			<?php			
			}else if(($intAuxDay == $intDay) and ($resp == true) and ($intMonth==date('n'))){
				$strStyle = "hoje";
				?>
				<div class="<?php echo $strStyle; ?>"\><a href='STagenda.php?var_dia=<?php echo($intAuxDay);?>&var_mes=<?php echo($intMonth);?>&var_ano=<?php echo( $intYear);?>' target="ifrEventos" onclick="mudaHref(<?php echo($intAuxDay);?>,<?php echo($intMonth);?>,<?php echo( $intYear);?>);"><?php echo($intAuxDay);?></a></div>
			<?php		
			}else if((($intMonth) >= date('m')) and ($intAuxDay > $intDay) and ($resp == true)){
				$strStyle = "dia_posterior";
				?>
				<div class="<?php echo $strStyle; ?>"\><a href='STagenda.php?var_dia=<?php echo($intAuxDay);?>&var_mes=<?php echo($intMonth);?>&var_ano=<?php echo( $intYear);?>' target="ifrEventos" onclick="mudaHref(<?php echo($intAuxDay);?>,<?php echo($intMonth);?>,<?php echo( $intYear);?>);"><?php echo($intAuxDay);?></a></div>
                
			<?php		
			}else{
				echo("<div class=\"" . $strStyle . "\">" . $intAuxDay . "</div>");
			}
		
			$strStyle = "";
		}
		echo("</td>\n");
		$intAuxDay++;	$intAuxWeekday++;
		if($intAuxWeekday == 7) { echo("\t\t</tr>\n\t\t<tr>\n"); $intAuxWeekday = 0; }
	}
	
	while($intAuxWeekday < 7 && $intAuxWeekday != 0) { echo("\t\t\t<td>&nbsp;</td>\n"); $intAuxWeekday++; }
	$dia= "";
	$mes= "";
	$ano= "";
	@$dia= ($_GET['dia'] == "") ? date("d") : $_GET['dia'];
	@$mes= ($_GET['mes'] == "") ? date("m") : $_GET['mes'];
	@$ano= ($_GET['ano'] == "") ? date("Y") : $_GET['ano'];
	echo("\t\t</tr>\n\t<tr bgcolor=".CL_CORLINHA_2."><td colspan=\"15\" align=\"right\"><a href=STagendaCalendar.php?var_dia=".$dia."&var_mes=".$mes."&var_ano=".$ano.">".$dia."/".$mes."/".$ano."</a></td></tr></table><br>");?>
	
	<?php
    //if(($dia<>"") and ($mes<>"") and ($ano<>"")){
	//	$resultado = eventos($dia, $mes, $ano);
	//}else{
	//	$resultado = eventos($intDay, $intMonth, $intYear);
	//}
//	echo $resultado;
	echo "\n</div></div></body></html>";
?>