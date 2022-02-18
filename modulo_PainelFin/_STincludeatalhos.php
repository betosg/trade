<?php athBeginFloatingBox("200","","<div class=\"padrao_gde\" style=\"padding-left:5px\"><b>Atalhos</b></div>",CL_CORBAR_GLASS_2); ?>
	<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		<tr>
			<td align="center" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="4">
					<tr><td align="left" height="20" class="texto_corpo_mdo"><strong>Contas</strong></td></tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinConta/" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Lista de Contas</a>
						</td>
					</tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinLctoConta/" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Lançamentos em Conta</a>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr><td align="left" height="20" class="texto_corpo_mdo"><strong>Programação Financeira</strong></td></tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinFluxoCaixa/STindex.php" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Fluxo de Caixa</a>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr><td align="left" height="20" class="texto_corpo_mdo"><strong>Tarefas Comuns</strong></td></tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinContaPagarReceber/" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Contas a Receber</a>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr><td align="left" height="20" class="texto_corpo_mdo"><strong>Demonstrativos</strong></td></tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinLivroCaixa/STindex.php" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Livro Caixa</a>
						</td>
					</tr>
					<tr>
						<td align="left" height="1%">
							<a href="../modulo_FinLctosGerais/STindex.php" target="<?php echo(CFG_SYSTEM_NAME);?>_frmain">Lctos Gerais</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php
			athEndFloatingBox();
		echo("<br/>");
?>