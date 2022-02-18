<?php
	$strDirCliente = getsession(CFG_SYSTEM_NAME. "_dir_cliente");
	
	try{
		$strSQL = " SELECT arquivo, atalho, params, target
					FROM sys_ar_painel_item
					WHERE tipo ILIKE 'banner_top'
					AND CURRENT_DATE BETWEEN dt_ini AND dt_fim
					AND dtt_inativo IS NULL
					ORDER BY CAST(random() AS NUMERIC) ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	} 
	
	if($objResult->rowCount()>0){
		$objRS = $objResult->fetch();
		
		if (stripos(getValue($objRS,"arquivo"), ".cgi") !== false) {
			echo "<iframe src='".getValue($objRS,"arquivo")."' ".getValue($objRS,"params");
			if (getValue($objRS,"target") != "") echo " target='".getValue($objRS,"target")."'";
			echo "></iframe>";
		}
		elseif (stripos(getValue($objRS,"arquivo"), ".php") !== false) {
			echo "<iframe src='".getValue($objRS,"arquivo")."' ".getValue($objRS,"params");
			if (getValue($objRS,"target") != "") echo " target='".getValue($objRS,"target")."'";
			echo "></iframe>";
		}
		elseif (stripos(getValue($objRS,"arquivo"), ".asp") !== false) {
			echo "<iframe src='".getValue($objRS,"arquivo")."' ".getValue($objRS,"params");
			if (getValue($objRS,"target") != "") echo " target='".getValue($objRS,"target")."'";
			echo "></iframe>";
		}
		elseif (stripos(getValue($objRS,"arquivo"), ".swf") !== false) {
			echo "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' ".getValue($objRS,"params").">";
			echo "  <param name='movie' value='../../".$strDirCliente."/upload/imgdin/".getValue($objRS,"arquivo")."'>";
			echo "  <param name='quality' value='high'>";
			echo "  <embed src='../../".$strDirCliente."/upload/imgdin/".getValue($objRS,"arquivo")."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' ".getValue($objRS,"params")."></embed>";
			echo "</object>";
		}
		else {
			if (getValue($objRS,"atalho") != "") {
				if (stripos(getValue($objRS,"atalho"), "javascript") !== false)
					echo "<a href='#' onClick=\"".getValue($objRS,"atalho")."\"";
				else
					echo "<a href='".getValue($objRS,"atalho")."'";
				if (getValue($objRS,"target") != "") echo " target='".getValue($objRS,"target")."'";
				echo ">";
			}
			echo "<img src='../../".$strDirCliente."/upload/imgdin/".getValue($objRS,"arquivo")."' border='0' ".getValue($objRS,"params").">";
			if (getValue($objRS,"atalho") != "") echo "</a>";
		}
	}
	$objResult->closeCursor();
?>
	