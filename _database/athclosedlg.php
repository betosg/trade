<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body>
<!-- ATEN��O: 
  
  	 Esta p�gina DUMMY � necess�riao para o processo onde as DIALOGS (inupdelmastereditor.php) s�o chamadas em POPUP.
	
	 No processo elas reconhecem que est�o em POPUP e modificam o DEFALUT_LPOCATION que � repassadoa para uma '[..]toDB.php', 
	 para que ela depois de executar a tarefa, simplesmente redirecione para esta p�gina (aqui) que tem apenas a fun��o de 
	 fechar a janela corrente, j� que a mesma esta em POPUP. (e por padr�o manda tamb�m dar um reload no OPENER)
//-->
<script language="javascript" type="text/javascript">
  //alert('A��o realizada com sucesso!');
  window.opener.location.reload(); 
  window.close();
</script>
</body>
</html>