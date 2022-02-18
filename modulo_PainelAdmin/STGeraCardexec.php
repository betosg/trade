<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

$strOperacao  = request("var_oper");       			// Operação a ser realizada
$intCodDado   = request("var_chavereg");   			// Código chave da página
$strExec      = request("var_exec");       			// Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   			// Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      			// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$intCodPF     = request("var_cod_pf");  			// requests STs - Código da PF, caso exista
$intCodPJ     = request("var_cod_pj");   			// request Cod_PJ, enviado pelo script pai  
$strAcaoRadio = request("var_cobr"); 				// verifica que acao tomar referente a opcao selecionada na gerac. de carteirinha
$intCodCredencial = request("var_cod_credencial"); 	// request Codigo credencial
$intQtdeImpr  = request("var_qtde_impr"); 			// Quantidade de impressões da carteirinha
$intValorPed  = str_replace(",",".",request("var_valor")); //valor do pedido

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

// caso nenhuma opção do radio tenha sido selecionada,
// e o numero de impressoes for maior que zero (segunda
// possibilidade de tela), exibe erro.
if(($strAcaoRadio == "") && ($intQtdeImpr > 0)){
	$strErro = "Nenhuma opção selecionada.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"../modulo_PainelAdmin/STGeraCard.php?var_chavereg=".$intCodCredencial,"aviso",1);
	die();
}
else{
	if( (($intQtdeImpr == 0)||($intQtdeImpr == ""))  ||  (($strAcaoRadio == "cobr_none") && ($intQtdeImpr > 0))  ){
		// incrementa o número de impressões da carteirinha
		// e redireciona para página de impressão (html card).
		if(is_null($intQtdeImpr)||($intQtdeImpr == "")){
			try{
				$strSQL  = " UPDATE sd_credencial ";
				$strSQL .= " SET qtde_impresso = 1 ";
				$strSQL .= "   , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
				$strSQL .= "   , sys_dtt_upd = CURRENT_TIMESTAMP ";
				$strSQL .= " WHERE qtde_impresso IS NULL ";
				$strSQL .= " AND cod_credencial = ".$intCodCredencial;
				
				$objResult = $objConn->query($strSQL);		
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STGeraCard.php?var_chavereg=".$intCodCredencial,"aviso",1);
				die();
			}
			redirect("../modulo_SdCredencial/STcardreader.php?var_chavereg=".$intCodPF."&var_widht=300&var_height=350&var_cod_credencial=".$intCodCredencial);
		}
		//incremento no número de impressões caso impr = 0
		else if(($intQtdeImpr == 0)||(($strAcaoRadio == "cobr_none") && ($intQtdeImpr >0))){
			try{
				$strSQL  = " UPDATE sd_credencial ";
				$strSQL .= " SET qtde_impresso = qtde_impresso + 1 ";
				$strSQL .= "   , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
				$strSQL .= "   , sys_dtt_upd = CURRENT_TIMESTAMP ";
				$strSQL .= " WHERE cod_credencial = ".$intCodCredencial;
				
				$objResult = $objConn->query($strSQL);
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STGeraCard.php?var_chavereg=".$intCodCredencial,"aviso",1);
				die();
			}
			redirect("../modulo_SdCredencial/STcardreader.php?var_chavereg=".$intCodPF."&var_width=350&var_height=300&var_cod_credencial=".$intCodCredencial);
		}
	}
	else if($strAcaoRadio == "cobr_novo"){
		try{
			$objConn->beginTransaction();
			
			$objStatement = $objConn->prepare("SELECT sp_gera_ped_impr_card(:in_cod_credencial, :in_valor_ped, :in_id_usuario);");
			$objStatement->bindParam(":in_cod_credencial",$intCodCredencial);
			$objStatement->bindParam(":in_valor_ped",$intValorPed);
			$objStatement->bindParam(":in_id_usuario",getsession(CFG_SYSTEM_NAME . "_id_usuario"));
			$objStatement->execute();
			
			$objConn->commit();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"../modulo_PainelAdmin/STGeraCard.php?var_chavereg=".$intCodCredencial,"aviso",1);
			$objConn->rollBack();
			die();
		}
		echo("<script type=\"text/javascript\" language=\"javascript\">
					window.close();
				 </script>");
	}
}
$objConn = NULL;
?>