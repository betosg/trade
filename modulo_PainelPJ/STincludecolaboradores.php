<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	/***           		   INCLUDES                   ***/
	/****************************************************/
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	//session
	$intCodDado = getsession(CFG_SYSTEM_NAME."_pj_selec_codigo");
	
	//inicia conexao BD
	$objConn = abreDBConn(CFG_DB);
	
	// SQL PADRÃO DA LISTAGEM - DAS CREDENCIAIS
	try{
	 	 $strSQL = "
				SELECT    t2.cod_pf
						, t2.nome
						, t2.matricula||' - '||t2.nome as matr_nome
						, t4.nome AS cargo
				FROM cad_pj t1 
				INNER JOIN relac_pj_pf t3     ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
				INNER JOIN cad_pf t2          ON (t2.cod_pf = t3.cod_pf) 
			    LEFT OUTER JOIN cad_cargo t4  ON (t3.cod_cargo = t4.cod_cargo)  
				WHERE t1.cod_pj = ".$intCodDado." 
				ORDER BY t2.nome "; 
		
		$objResult = $objConn->query($strSQL);		
		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? "#F5FAFA" : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
<head>
    <title><?php echo(CFG_SYSTEM_TITLE);?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="_css/default.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
    <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../_scripts/tablesort.js"></script>
    <style type="text/css">
        /* suas adaptações css aqui */
        .menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
        body{ margin:0px;background-color:#FFFFFF; } ul{ margin-top:0px;margin-bottom:0px; } li{ margin-left:0px; }
    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0">
	<!-- MENU PURE CSS SUPERIOR . FIM -->
	<?php
	// TESTA SE CONSULTA ESTÁ VAZIA - NÃO REMOVER
	// Neste caso apenas mostra mensagem de consulta
	// vazia, para que seja carregado o resize do
	// frame pai - para quando este script é utilizado
	// como detail.
	if($objResult->rowCount() == 0) {
		//mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("no_contato",C_NONE),"","aviso",1,"","","");
		?>
		<table width="125px" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td style="text-align:center; vertical-align:top;"><div style="margin-top:10px;"><?php echo(getTText("no_contato",C_TOUPPER)); ?></div></td>
		</tr>
		</table>
		<?php
	} else {
	?>
	<table width="125px" align="left" cellpadding="0" cellspacing="1" class="tablesort">
      <thead>
        <tr>
          <th width="100%" class="sortable" nowrap><?php echo(getTText("nome",C_TOUPPER));?></th>          
        </tr>
      </thead>
      <tbody>
        <?php 	 
		foreach($objResult as $objRS){  
			?>
	        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
    	      <td align="left" title="<?php 
			  echo(getValue($objRS,"matr_nome"));
			  if (getValue($objRS,"cargo") != "") echo(" / ".getValue($objRS,"cargo"));
			  ?>"><a href='../modulo_PainelPJ/STColabAtivos.php' target="_parent"><?php echo(getValue($objRS,"nome"));?></a></td>
        	</tr>
        <?php } ?>
      </tbody>
    </table>
    <?php } ?>
</body>
</html>
<?php
	// SETA O OBJETO DE CONEXÃO COM BANCO PARA NULO
	// ALÉM DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>