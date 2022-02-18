<?php
			$objConn = abreDBConn(CFG_DB);
			try{
				$strSQL = " SELECT cod_msg_pasta, pasta FROM msg_pasta WHERE cod_user = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'";
				$objResult = $objConn->query($strSQL);
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
				die();
			}
			
			if($objResult->rowCount() > 0){
				
				athBeginShapeBox("205","",getTText("pastas_locais",C_NONE),"#DBDBDB");
				
				echo("
				    <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
						<tr>
							<td style=\"padding-left:5px;\">
								<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">");
								
				$strCategoria = "";
				$intI = 0;
				
				foreach($objResult as $objRS){
					try{
						$strSQL = " SELECT COUNT(cod_mensagem) as num_msgs FROM msg_mensagem WHERE lido = false AND cod_msg_pasta = " . $objRS["cod_msg_pasta"];
						$objResultLido = $objConn->query($strSQL);
						$objRSLido = $objResultLido->fetch();
					}
					catch(PDOException $e){
						mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
						die();
					}
					
					if($objRSLido["num_msgs"] > 0){
						$strLinhaPasta = "style=\"font-weight:bold;\">" .  getTText($objRS["pasta"],C_NONE) . " (" . $objRSLido["num_msgs"] . ")";
					}
					else{
						$strLinhaPasta = ">" . getTText($objRS["pasta"],C_NONE);
					}
					
					$objResultLido->closeCursor();
					
					echo("			<tr>
										<td style=\"padding-left:5px;\">
											<a id=\"" . $objRS["pasta"] . "\" href=\"datapreparepasta.php?var_pasta=" . $objRS["pasta"] . "\" target=\"" . CFG_SYSTEM_NAME . "_main\" 
											" . $strLinhaPasta . "
											</a>
										</td>
									</tr>
						");
					$intI++;
				}
				echo("
								</table>
							</td>
						</tr>
					</table>");
					
				athEndShapeBox();
			}
		?>