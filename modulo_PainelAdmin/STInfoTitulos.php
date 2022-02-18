<?php

	/*try {
			//BUSCA TITULOS - VIA PROCEDURE
			$objResult = $objConn->query($strSQL);
		}
		catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
	}*/

?>
<table cellpadding="10" cellspacing="0" style="border:none; width:100%; margin-bottom:0px;">
	<tr>
	<td style="padding:0px 20px 0px 0px; border:none;">
	<?php	athBeginFloatingBox("100%","","<b>Títulos abertos</b>",CL_CORBAR_GLASS_2);	?>
	<table style="border:none; width:100%; margin-bottom:0px; background-color:#FFFFFF">
		<tr>
			<td align="center" valign="top" style="border:none;">
				<table border="0" cellspacing="0" cellpading="0" style="border:none;">
				<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Cont. Assist.: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none;padding:0px 20px 0px 0px;"><strong>Cont. Sindical: </strong></td>
						<td align="left" style="border:none;">104</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Carteirinhas: </strong></td>
						<td align="left" style="border:none;">12</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Homologações: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php	athEndFloatingBox(); ?>
	</td>
	
	<td style="padding:0px 0px 0px 20px; border:none">
	<?php	athBeginFloatingBox("100%","","<b>Títulos Pagos</b>",CL_CORBAR_GLASS_2);	?>
	<table style="border:none; width:100%; margin-bottom:0px; background-color:#FFFFFF">
		<tr>
			<td align="center" valign="top" style="border:none;">
				<table border="0" cellspacing="0" cellpading="0" style="border:none;">
				<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Cont. Assist.: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none;padding:0px 20px 0px 0px;"><strong>Cont. Sindical: </strong></td>
						<td align="left" style="border:none;">104</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Carteirinhas: </strong></td>
						<td align="left" style="border:none;">12</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Homologações: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php	athEndFloatingBox(); ?>
	</td>

	<td style="padding:0px 0px 0px 40px; border:none;">
	<?php	athBeginFloatingBox("100%","","<b>Títulos cancelados</b>",CL_CORBAR_GLASS_2);	?>
	<table style="border:none; width:100%; margin-bottom:0px; background-color:#FFFFFF">
		<tr>
			<td align="center" valign="top" style="border:none;">
				<table border="0" cellspacing="0" cellpading="0" style="border:none;">
				<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Cont. Assist.: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none;padding:0px 20px 0px 0px;"><strong>Cont. Sindical: </strong></td>
						<td align="left" style="border:none;">104</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Carteirinhas: </strong></td>
						<td align="left" style="border:none;">12</td>
					</tr>
					<tr><td style="border:none;"></td></tr>
					<tr>
						<td align="right" style="border:none; padding:0px 20px 0px 0px;"><strong>Homologações: </strong></td>
						<td align="left" style="border:none;">223</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php	athEndFloatingBox(); ?>
	</td>
	</tr>
	</table>
	<?php
	//quando buscar sumário de títulos por 
	//Procedure, processar ao topo da página e
	//quitar o objResult.
	//$objResult->closeCursor();
?>