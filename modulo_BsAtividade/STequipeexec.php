<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

	// REQUESTS
	$strAcao 		= request("var_acao");
	$intCodDado		= request("var_chavereg");			// cod_agenda para o qual irá ser update da agenda
	$strRedirect	= request("var_redirect");			// pagina que sera feito o redir
	$strCitados		= request("var_usuarios_concat");	// listagem de usuarios concatenados por ';'
	$strCriadorUsr	= request("var_criador_usr");
	
	// caso o codigo da agenda tenha sido enviado
	// vazio, entao exibe mensagem de erro
	if($intCodDado == ""){
		mensagem("err_ag_vazio","err_envio_ag",getTText("agenda_cod_null"),'','erro','1');
		die();
	}
	
	// CONCATENAÇÃO DA EQUIPE
	$strCitados = ($strCitados == "") ? $strCriadorUsr : trim($strCitados,';');
	
	// transforma a string de citados em um array
	// que será inserido no banco - um para cada
	// usuario localizado
	$strCitados = str_replace("&quot;",'"',$strCitados);
	$arrCitados = explode(";",$strCitados);
	// debug
	// echo(var_dump($arrCitados)."<br /><br />");
	
	// cria um contador para o for que irá controlar
	// a inserção de usuarios. com base no tam do array
	$intContador = count($arrCitados);
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// transação para inserção / atualização de
	// equipe na tabela bs_equipe
	// esta transacao consiste em deletar todos
	// os equipe onde o codigo da atividade seja
	// igual ao codigo da atividade recebido por pa
	// rametro aqui, depois inserir novamente um a um
	try{
		$objConn->beginTransaction();
		
		// deleção de todo mundo pertencente
		// o cod_atividade enviado para esta pag
		$strSQL = "DELETE FROM bs_equipe WHERE cod_atividade = ".$intCodDado;
		$objConn->query($strSQL);
		
		// inserção / atualização de EQUIPE
		for($i=0; $i<$intContador; $i++){
			$strSQL = "INSERT INTO bs_equipe (cod_atividade,id_usuario,sys_usr_ins,sys_dtt_ins)
					   VALUES(".$intCodDado.",'".prepStr($arrCitados[$i])."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
		}
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// caso o redirect venha diferente de vazio
	// entao a pagina joga-o para lá, caso con-
	// trario, fechamos a pagina por ser pop-up
	if($strRedirect == ""){
		echo("<script type='text/javascript'>window.close();</script>");
	}else{
		redirect($strRedirect);
	}
?>