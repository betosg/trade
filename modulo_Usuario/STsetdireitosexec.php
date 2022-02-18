<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$intCodUsuario  = request("var_coduser");  // C�digo do usu�rio
$intCodApp      = request("var_codapp");   // C�digo da aplica��o
$strCodDireitos = request("var_direitos"); // C�digos dos direitos para a aplica��o
$strTodos		= request("var_todos");

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
	<?php
	if($strTodos == "T"){
		echo "<script>
			window.parent.submeterTForm(); <!-- Chama o pr�ximo formul�rio. Est� aqui para aparecer a marca depois de submeter o pr�ximo formul�rio -->
		</script>";
	}
	?>

</html>