<?php include_once("../_database/athdbconn.php"); 
/* --------------------------------------------------------------------------------------------------------------------  	
   1) $intAba - seta aba inicial para cada grupo
   neste caso a aba defalut deve ser setada manualmente aqui, por grupo de de usu�rio, at� que 
   tenhamos no sistema isso configurado tamb�m por user
   
   2) $strRows - ajusta a altura dos frames principais: header(menumx), content e footer 
   s� � necess�rio no caso de grupos de usu�rios que n�o tenham menu algu, pois nestes casos 
   o header inteiro deve diminuir de altura .
   por default  "145,*,22"  -  alturas de  (header, content, footer)
   145 - altura do HEADER para caber o menuMX com Aba e Container
    22 - altura do FOOTER
     * - altura da �rea de conte�do, ou seja, tudo que sobra

   Obs.: 46 - altura do HEADER para quando n�o houver aba dispon�vel para o referido grupo,
   o frame do header deve ser reduzido at� esta altura de 46px
   
   3) $strMapaCols - liga (250)  ou desliga( 0) o mapa para um determinado grupo de usu�rios
   normalmente paineis e arNETs dependem do mapa e suas abas com menu pr�prio por grupo 
   ou gen�rico
    --------------------------------------------------------------------------------------- by Aless Jan/2011 - */
$strGrpUsr = strtoupper(getsession(CFG_SYSTEM_NAME . "_grp_user"));

$intAba		 = 12;
$strRows	 = "145,*,22";
$strMapaCols = "250,*";

if ($strGrpUsr == 'SU')		{ $intAba = 172; }
if ($strGrpUsr == 'ADMIN')	{ $intAba = 173; }
if ($strGrpUsr == 'NORMAL') { $strRows = "46,*,22"; } //neste caso o $intAba  � irrelevante 
if ($strGrpUsr == 'GUEST')  { $strRows = "46,*,22"; } 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset id="frprincipal" name="frprincipal" rows="<?php echo($strRows); ?>" cols="*" frameborder="no" border="0" framespacing="0">
  <frame src="mxmenu.php?var_chavereg=<?php echo($intAba);?>" id="<?php echo(CFG_SYSTEM_NAME . "_frmenumx"); ?>" name="<?php echo(CFG_SYSTEM_NAME . "_frmenumx"); ?>" scrolling="no">
	<frameset cols="<?php echo $strMapaCols; ?>" rows="*" id="<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>" name="<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>"  frameborder="no" border="0" framespacing="0">
		<frame src="mapa.php?var_stats=FRAME" id="<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>" name="<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>" scrolling="no">
		<frame src="<?php echo(getsession(CFG_SYSTEM_NAME . "_dir_default")); ?>" id="<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>" name="<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>">
	</frameset>
  <frame src="mxfooter.php" id="<?php echo(CFG_SYSTEM_NAME . "_frfooter"); ?>" name="<?php echo(CFG_SYSTEM_NAME . "_frfooter"); ?>" scrolling="no">
</frameset>
<noframes>
  <body></body>
</noframes>
</html>