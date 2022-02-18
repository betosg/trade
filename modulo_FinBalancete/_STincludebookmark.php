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
	athBeginShapeBox("205","",getTText("bookmark",C_NONE),CL_CORBAR_SHAPE);
	echo("
	    <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
			<tr>
				<td style=\"padding-left:5px;\">
					<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">");
	echo("	
					</table>
				</td>
			</tr>");
	echo("
			<tr><td height=\"5\" colspan=\"2\"></td></tr>
			<tr onClick=\"collapseItem(1);\">
				<td width=\"99%\" bgcolor=\"#E0E0E0\" style=\"border-bottom:1px solid #999999;padding:3px;cursor:pointer\"><b>" . getTText("links",C_NONE) . "</b></td>
				<td width=\"1%\" bgcolor=\"#E0E0E0\" style=\"border-bottom:1px solid #999999;padding:3px;cursor:pointer\"><img id=\"bookmark_img_1\" src=\"../img/collapse_generic_open.gif\"></td>
			</tr>
			<tr><td height=\"5\" colspan=\"2\"></td></tr>
			<tr>
				<td colspan=\"2\">
					<table id=\"bookmark_1\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"display:block;\">
						<tr>
							<td style=\"padding-left:5px;\">
								<a href=\"../modulo_PainelBusca/STgerataxaemlote.php\" target=\"".CFG_SYSTEM_NAME."_frmain\">- " . getTText("taxa_em_lote",C_NONE) . "</a>
							</td>
						</tr>
						<tr><td height=\"5\" colspan=\"2\"></td></tr>
					</table>
				</td>
			</tr>
		</table>");
	athEndShapeBox();
?>