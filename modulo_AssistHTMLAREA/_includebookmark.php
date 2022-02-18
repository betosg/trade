			<script>
				function collapseItem(prCodBookmark){
					if(document.getElementById("bookmark_" + prCodBookmark).style.display == "block"){
						document.getElementById("bookmark_" + prCodBookmark).style.display = "none";
						document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_close.gif";
					}
					else{
						document.getElementById("bookmark_" + prCodBookmark).style.display = "block";
						document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_open.gif";
					}
				}
			</script>
		<?php
			$strSQL = " SELECT rotulo, clausula, executor, categoria, target FROM sys_bookmark WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . " ORDER BY ordem " ;
			$objResult = $objConn->query($strSQL);
			
			if($objResult->rowCount() > 0){
				
				athBeginShapeBox("205","",getTText("bookmark",C_NONE),CL_CORBAR_SHAPE);
				
				echo("
				    <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"kernel_bookmark\">
						<tr>
							<td style=\"padding-left:5px;\">
								<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">");
								
				$strCategoria = "";
				$intI = 0;
				
				foreach($objResult as $objRS){
				    $strExecutor = (getValue($objRS,"executor") == "") ? "resultaslwdetail.php" : getValue($objRS,"executor") ;
					$strTarget   = (getValue($objRS,"target")   == "") ? CFG_SYSTEM_NAME . "_main" : getValue($objRS,"target") ;
					
					if(getValue($objRS,"categoria") != $strCategoria){
						if($strCategoria != ""){
							echo("	
											</table>
										</td>
									</tr>");
						}
						echo("
									<tr><td height=\"5\" colspan=\"2\"></td></tr>
									<tr onClick=\"collapseItem(" . $intI . ");\" class=\"bookmark_group\">
										<td width=\"99%\">" . getValue($objRS,"categoria") . "</td>
										<td width=\"1%\"><img id=\"bookmark_img_" . $intI . "\" src=\"../img/collapse_generic_open.gif\"></td>
									</tr>
									<tr><td height=\"5\" colspan=\"2\"></td></tr>
									<tr>
										<td colspan=\"2\">
											<table id=\"bookmark_" . $intI . "\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"display:block;\">
						");
						$strCategoria = getValue($objRS,"categoria");
					}
					
					echo("						<tr>
													<td class=\"bookmark_item\">
														<a href=\"" . $strExecutor . "?var_strparam=" . getValue($objRS,"clausula") . "\" target=\"" . $strTarget . "\">" . getValue($objRS,"rotulo") . "</a>
													</td>
												</tr>
						");
						
					$intI++;
				}
				echo("							<tr><td height=\"5\" colspan=\"2\"></td></tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>");
					
				athEndShapeBox();
			}
			
$objResult->closeCursor();	
?>