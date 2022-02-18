<?php 
 athBeginWhiteBox("120"); 
 
 //echo getsession(CFG_SYSTEM_NAME."_foto_usuario");
 // die;
		  $strImage = (getsession(CFG_SYSTEM_NAME . "_foto_usuario") != "") ? "../../" . getsession(CFG_SYSTEM_NAME . "_dir_cliente") . "/upload/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_foto_usuario") : "../img/unknownuser.jpg";
					
		  //$arrImageInfo = getimagesize($strImage); // Coloca num array algumas informações sobre o arquivo selecionado.
		  //$intWidth = $arrImageInfo[0];               // Largura em pixels da imagem
		  //$intWidth = ($intWidth < 100) ? $intWidth : 100; // Se largura é menor que 100 ele mantém a largura, caso contrário ele fixa em 100
		  $intWidth = 100;
		  echo("<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\">");
	
	/*athBeginWhiteBox("120"); 
	//echo CFG_SYSTEM_NAME;
	//die;
	$strImage = (getsession(CFG_SYSTEM_NAME . "_foto_usuario") != "") ? "/tradeunion/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_foto_usuario") : "../img/unknownuser.jpg";
	$arrImageInfo = getimagesize($strImage); // Coloca num array algumas informações sobre o arquivo selecionado.
	$intWidth = $arrImageInfo[0];               // Largura em pixels da imagem
	$intWidth = ($intWidth < 100) ? $intWidth : 100; // Se largura é menor que 100 ele mantém a largura, caso contrário ele fixa em 100
	echo("
			<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\"><br><br>
			<b>" . getsession(CFG_SYSTEM_NAME . "_nome_usuario") . "</b> (<small>" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "</small>)<br>
			" . getsession(CFG_SYSTEM_NAME . "_grp_user"));
	athEndWhiteBox();
  */
  
 athEndWhiteBox();	 
?>
