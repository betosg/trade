<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body>
<!-- ATENÇÃO: 
  
  	 Esta página DUMMY é necessáriao para o processo onde as DIALOGS (inupdelmastereditor.php) são chamadas em POPUP.
	
	 No processo elas reconhecem que estão em POPUP e modificam o DEFALUT_LPOCATION que é repassadoa para uma '[..]toDB.php', 
	 para que ela depois de executar a tarefa, simplesmente redirecione para esta página (aqui) que tem apenas a função de 
	 fechar a janela corrente, já que a mesma esta em POPUP. (e por padrão manda também dar um reload no OPENER)
//-->
<script language="javascript" type="text/javascript">
  //alert('Ação realizada com sucesso!');
  window.opener.location.reload(); 
  window.close();
</script>
</body>
</html>