<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$strOBS      = request("var_motivo_cancelamento");
	$flagEMAIL   = request("var_flag_email");
	$strREDIRECT = request("DEFAULT_LOCATION");
	$strNOME	 = request("var_nome");
	$strRAZAO	 = request("var_razao_social");
	$strEMAILPF	 = request("var_email_pf");
	$strEMAILPJ  = request("var_email_pj");

	// CONTROLE DE ACESSO
	// if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// TRANSAÇÃO
	$objConn->beginTransaction();
	try{
		// MARCA A DATA DE HOMOLOGAÇÃO
		// MARCAÇÃO DA AGENDA É VIA TRIGGER
		$strSQL  = "UPDATE sd_homologacao SET situacao = 'cancelado', obs = '".prepStr($strOBS)."',sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."',sys_dtt_upd = CURRENT_TIMESTAMP WHERE cod_homologacao = ".$intCodDado;
		// die($strSQL);
		$objConn->query($strSQL);
		
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollback();
		die();
	}
	
	if($flagEMAIL != ""){
		// MONTA O CORPO DO EMAIL
		$strBodyEmail  = '';
		$strBodyEmail .= '
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
				<tr>
					<td colspan="2">
						<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
							<tr>
								<td class="td_label">&nbsp;</td>
								<td align="left"><strong>'.getTText("homologacao_cancelada",C_NONE).'</strong></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="td_label">'.getTText("nome_colaborador",C_NONE).':</td>
								<td align="left">'.$strNOME.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("razao_social_empresa",C_NONE).':</td>
								<td align="left">'.strtoupper($strRAZAO).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("motivo_cancelamento",C_NONE).':</td>
								<td align="left">'.$strOBS.'</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									Estaremos a sua disposição para sanar eventuais dúvidas quanto ao nosso processo de homologação ou sobre os
									motivos aqui apresentados para o cancelamento desta homologação.
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">Atenciosamente,</td></tr>
							<tr><td colspan="2">SINDIEVENTOS</td></tr>
						</table>
					</td>
				</tr>
			</table>';
		
		// CONFIGURA LINHA DE DESTINATÁRIOS
		$strEmailLINE  = "";
		$strEmailLINE .= ($strEMAILPJ == "") ? "" : $strEMAILPJ.",";
		$strEmailLINE .= ($strEMAILPF == "") ? "" : $strEMAILPF.",";
		$strEmailLINE  = trim($strEmailLINE,",");
		// echo($strEmailLINE);
		
		// CONFIGURA TÍTULO DO EMAIL / SUBJECT
		$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("cancelamento_de_homologacao",C_NONE);
		
		// Encaminha o email somente se estiver ONLINE
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
			emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
		}
	}
	
	// DESTRÓI OBJETO DE CONEXÃO COM DB
	$objConn = NULL;
	
	redirect($strREDIRECT);
?>