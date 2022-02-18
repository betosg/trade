<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$intCodUsuario  = request("var_coduser");  // Código do usuário
$intCodApp      = request("var_codapp");   // Código da aplicação
$strCodDireitos = request("var_direitos"); // Códigos dos direitos para a aplicação

try{
	$objConn->beginTransaction();
	
	$objStatement = $objConn->prepare("SELECT sp_set_direitos(:in_cod_usuario, :in_cod_app, :in_cod_direitos);");
	$objStatement->bindParam(":in_cod_usuario",$intCodUsuario);
	$objStatement->bindParam(":in_cod_app",$intCodApp);
	$objStatement->bindParam(":in_cod_direitos",$strCodDireitos);
	$objStatement->execute();
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
	$objConn->rollBack();
	die();
}
?>
<html>
	<body style="margin:0px;background-color:transparent;"><img src="../img/icon_encerrado.gif" border="0"></body>
	<script>window.parent.submeter();</script> <!-- Chama o próximo formulário. Está aqui para aparecer a marca depois de submeter o próximo formulário -->
</html>