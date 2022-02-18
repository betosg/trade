<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strTitulo    = request("var_titulo");
$strDescricao = request("var_descricao");

if($strTitulo != "" && $strDescricao != ""){
	try{
		$objResult = $objConn->query(" SELECT cod_categoria FROM tl_categoria ORDER BY cod_categoria ASC LIMIT 1 ");
		
		if($objRS = $objResult->fetch()){
			$strSQL = " 
			  INSERT INTO tl_todolist (cod_categoria, id_responsavel, situacao, prioridade, titulo, descricao, prev_dt_ini, sys_dtt_ins, sys_id_usuario_ins) 
			  VALUES (
				" . $objRS["cod_categoria"] . "
				,'" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'
				,'status_img_aberto'
				,'status_img_normal'
				,'" . $strTitulo . "'
				,'" . $strDescricao . "'
				,current_timestamp
				,current_timestamp,
				'" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "') 
			          ";
			$objConn->query($strSQL);
		}
		
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
}
?>
<script>window.close();</script>