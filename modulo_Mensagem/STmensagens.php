<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// Verificação de ACESSO - ENQUANTO MODULO DE MENSAGENS NAO EXISTE, NAO TESTAR DIREITOS
	// $strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); 
	
	// Abre conexão com DB
	$objConn = abreDBConn(CFG_DB);
	
	// localiza MENSAGENS que foram enviadas
	// para o usuário corrente da sessão
	try{
		$strSQL = "
			SELECT msg_mensagem.cod_mensagem
				  ,msg_mensagem.assunto
				  ,msg_mensagem.dtt_envio
				  ,msg_destino.id_usuario
				  ,msg_mensagem.remetente
			 FROM msg_mensagem
			INNER JOIN msg_destino ON (msg_destino.cod_mensagem = msg_mensagem.cod_mensagem)
			WHERE msg_destino.id_usuario = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
			  AND msg_destino.dtt_lido IS NULL";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td align="center">
<?php athBeginFloatingBox("200","","<span style='float:right;padding-right:3px;'><img src='../img/icon_inserir.gif' alt='".getTText("ins_mensagem",C_NONE)."' title='".getTText("ins_mensagem",C_NONE)."' style='cursor:pointer;' onclick=\"AbreJanelaPAGE('../modulo_Mensagem/STinsmensagem.php','600','450');\"/></span>"."<strong>".getTText("mensagens",C_UCWORDS)."</strong>",CL_CORBAR_GLASS_2);?>
	<table cellpadding="0" cellspacing="0" border="0" width="180">
	<?php if($objResult->rowCount() > 0){?>
	<?php foreach($objResult as $objRS){?>
	<tr onclick="AbreJanelaPAGE('../modulo_Mensagem/STviewmensagem.php?var_chavereg=<?php echo(getValue($objRS,"cod_mensagem"));?>','700','500');" 
	 style="cursor:pointer;" title="<?php echo(getValue($objRS,"assunto"));?>">
		<td align="center" valign="top" style="padding:3px 0px 0px 0px;" width="100%">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px dashed #CCC;background-color:#FFF; padding:0px 0px 3px 3px;">
				<tr>
					<!-- td width="20%" align="center">
						<img src="../img/icon_mensagem_recebida.gif" alt="<?php echo(getValue($objRS,"assunto"));?>"
						 title="<?php echo(getValue($objRS,"assunto"));?>" style="cursor:pointer;" border="0" />
					</td -->
					<td>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="right"><strong><?php echo(getTText("from",C_NONE));?></strong></td>
								<td align="left" style="padding-left:5px;">
									<?php echo(getValue($objRS,"remetente"));?>
								</td>
							</tr>
							<!-- tr>
								<td align="right"><strong><?php echo(getTText("to",C_NONE));?></strong></td>
								<td align="left" style="padding-left:5px;">
									<?php echo(getValue($objRS,"id_usuario"));?>
								</td>
							</tr -->
							<tr>
								<td align="right"><!-- strong><?php echo(getTText("assunto",C_NONE));?>:</strong --></td>
								<td align="left" style="padding-left:5px;">
									<!-- span style="height:15px;width:70px;overflow:hidden;">
										
									</span -->
									<?php echo(getValue($objRS,"assunto"));?>
								</td>
							</tr>
							<tr>
								<td align="right"></td>
								<td style="text-align:right; color:#CCC; padding-left:5px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_envio"),true));?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php }?>
	<?php } else{?>
	<tr>
	<td align="center" valign="top" style="padding:0px 0px;" width="100%">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px dashed #CCC;background-color:#FFF;">
		<tr>
			<td width="20%" align="center" style="font-size:10px;color:#CCC;font-style:italic;padding:5px;"><?php echo(getTText("nenhuma_mensagem",C_NONE));?></td>
		</tr>
		</table>
	</td>
	</tr>
	<?php }?>
	</table>
<?php athEndFloatingBox();?>
</td>
</tr>
</table>