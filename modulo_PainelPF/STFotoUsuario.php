<?php 
athBeginWhiteBox("120"); 

// Coleta foto do usuario referente a PJ SELECIONADA no footer
// Caso a imagem do usuario nao exista entao posiciona a foto
// de usuario 'unknowuser'
$strImage = (getsession(CFG_SYSTEM_NAME . "_pj_selec_foto") != "") ? "../../" . getsession(CFG_SYSTEM_NAME . "_dir_cliente") . "/upload/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_pj_selec_foto") : "../img/unknownuser.jpg";

$intWidth = 100;
echo("<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\">");
athEndWhiteBox();

/* Debugs Antigos
// Coloca num array algumas informações sobre o arquivo selecionado.
$arrImageInfo = getimagesize($strImage);
// Largura em pixels da imagem
$intWidth = $arrImageInfo[0];              
// Se largura é menor que 100 ele mantém a largura, caso contrário ele fixa em 100
$intWidth = ($intWidth < 100) ? $intWidth : 100; 
*/

/* Debugs antigos
athBeginWhiteBox("120"); 
$strImage = (getsession(CFG_SYSTEM_NAME . "_foto_usuario") != "") ? "/tradeunion/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_foto_usuario") : "../img/unknownuser.jpg";

// Coloca num array algumas informações sobre o arquivo selecionado.
$arrImageInfo = getimagesize($strImage);
// Largura em pixels da imagem
$intWidth = $arrImageInfo[0];

// Se largura é menor que 100 ele mantém a largura, caso contrário ele fixa em 100
$intWidth = ($intWidth < 100) ? $intWidth : 100;
echo("<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\"><br><br>
<b>" . getsession(CFG_SYSTEM_NAME . "_nome_usuario") . "</b> (<small>" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "</small>)<br>" . getsession(CFG_SYSTEM_NAME . "_grp_user"));
athEndWhiteBox();
*/ 
?>