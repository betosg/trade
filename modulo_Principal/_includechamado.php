<?php
try{
	$strSQL = "
		SELECT
			  cod_todolist
			, titulo
			, situacao
			, prioridade
			, CASE WHEN prev_dt_ini > current_date THEN 'status_img_antes'
				   WHEN prev_dt_ini = current_date THEN 'status_img_atual'
				   WHEN prev_dt_ini < dt_realizado THEN 'status_img_depois'
				   WHEN prev_dt_ini = dt_realizado THEN 'status_img_atual'
				   WHEN prev_dt_ini > dt_realizado THEN 'status_img_antes'
				   WHEN prev_dt_ini < current_date THEN 'status_img_depois'
			  END
		 FROM tl_todolist INNER JOIN tl_categoria ON (tl_todolist.cod_categoria = tl_categoria.cod_categoria) 
		WHERE id_responsavel  = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'
		  AND situacao <> 'status_img_fechado'
		 ORDER BY prev_dt_ini DESC, cod_todolist DESC
		 LIMIT 2
			";
	$objResultTodolist = $objConn->query($strSQL);
	
	if($objResultTodolist->rowCount() > 0){
		echo("
<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">
	<tr>
		<td colspan=\"5\" bgcolor=\"#EEEEEE\" style=\"border-bottom:2px solid #CCCCCC;\">
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
				<tr>
					<td>" . getTText("ultimos_chamados",C_UCWORDS) . "</td>
					<td width=\"1%\">
						<select name=\"var_acao_todolist\" onChange=\"selectAction(this);\">
							<option value=\"\">" . getTText("selecione",C_UCWORDS) . "...</option>
							<option value=\"chamadoins.php\">" . getTText("inserir",C_UCWORDS) . "</option>
							<option value=\"../modulo_Todolist/\">" . getTText("ver_todos",C_UCWORDS) . "</option>
						</select>	
					</td>
				</tr>
			</table>
		</td>
	</tr>");

		$strBgColor = "";
		
		foreach($objResultTodolist as $objRSTodolist){
			$strBgColor = ($strBgColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
			
			$strDadoSit  = str_replace("status_img_","",$objRSTodolist["situacao"]);
			$strDadoPri  = str_replace("status_img_","",$objRSTodolist["prioridade"]);
			$strDadoCond = str_replace("status_img_","",$objRSTodolist["case"]);
			
			echo("
	<tr bgcolor=\"" . $strBgColor . "\" style=\"cursor:pointer\" onMouseOver=\"switchColor(this,'#CCCCCC');\" onMouseOut=\"switchColor(this,'" . $strBgColor . "');\"
		onClick=\"window.open('chamadoview.php?var_oper=VIE&var_chavereg=" . $objRSTodolist["cod_todolist"] . "','" . CFG_SYSTEM_NAME . "_chamado','popup=yes,width=650,height=400,scrollbars=yes');\">
		<td width=\"1%\">"  . $objRSTodolist["cod_todolist"] . "</td>
		<td width=\"96%\">" . $objRSTodolist["titulo"]       . "</td>
		<td width=\"1%\"><img src=\"../img/imgstatus_"  . $strDadoSit  . ".gif\" title=\"" . getTText($strDadoSit,C_TOUPPER) . "\"></td>
		<td width=\"1%\"><img src=\"../img/imgstatus_"  . $strDadoPri  . ".gif\" title=\"" . getTText($strDadoPri,C_TOUPPER) . "\"></td>
		<td width=\"1%\"><img src=\"../img/imgstatus_"  . $strDadoCond . ".gif\" title=\"" . getTText($strDadoCond,C_TOUPPER) . "\"></td>
	</tr>
				");
		}
		
		echo("
</table>");
	}
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
	die();
}
?>