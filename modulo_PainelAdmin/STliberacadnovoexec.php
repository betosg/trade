<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");

$objConn = abreDBConn(CFG_DB);

$idUsuario  = request("var_id_usuario");
$strEMail   = request("var_email");
$strEmpresa = request("var_empresa");

if ($idUsuario != "") {
	/*** ATUALIZA USUÁRIO CONFORME EMPRESA ***/
	try{
		$strSQL  = " UPDATE sys_usuario ";
		$strSQL .= "    SET grp_user = 'NORMAL' ";
		$strSQL .= "      , dir_default = '../modulo_PainelPJ/STindex.php' ";
		$strSQL .= "      , sys_dtt_upd = CURRENT_TIMESTAMP ";
		$strSQL .= "      , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'"; 
		$strSQL .= "  WHERE id_usuario = '".$idUsuario."' ";
		$strSQL .= "    AND grp_user = 'PRE_CADASTRO' ";
		
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
		die();
	}
}

$strMsgLiberacao = getVarEntidade($objConn, "msg_liberacao_cadastro_pj");
$strMsgLiberacao = "<html>
						<head>
						</head>
						<body>
							<p>Prezados Senhores, </p>
							<p><TAG_EMPRESA></p>
							<p>Seu Cadastro foi concluído com sucesso!</p>
							<p>Colocamo-nos a disposição para maiores informações. </p>
							<p.Atenciosamente, </p>
							<p>Departamento Financeiro<br>
							SINDIPROM - Sindicato das Empresas de Promoção, Organização e Montagem de Feiras, Congressos e Eventos do Estado de São Paulo <br><br>
							Telefax: (55 11) 3120.7099 <br>
							E-mail: sindiprom@sindiprom.org.br<br>
							Rua Frei Caneca, 91, 11º andar - Cerqueira Cesar - CEP: 01307-001 São Paulo - SP<br>
							</p>
						</body>
					</html>";
if ($strMsgLiberacao != "") {
	$strMsgLiberacao = str_ireplace("<TAG_EMPRESA>", $strEmpresa, $strMsgLiberacao);
	$strMsgLiberacao = str_ireplace("<TAG_LINK>", "http://www.".CFG_SYSTEM_NAME.".com.br/".getsession(CFG_SYSTEM_NAME."_dir_cliente"), $strMsgLiberacao);
	
	$strCorpoEmail = $strMsgLiberacao;
	$strEMail ="";
	
	emailNotify($strCorpoEmail, getTText("novo_cadastro_aprovado",C_UCWORDS), $strEMail, CFG_EMAIL_SENDER);
}

$objConn = NULL;
redirect("STindex.php");
?>