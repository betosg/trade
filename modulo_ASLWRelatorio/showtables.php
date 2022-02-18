<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

$strTableName = request("var_table");

?>
<html>
	<head>
		<title><?php echo(getTText("mostrar_tabelas",C_UCWORDS)); ?></title>
		<style>
			body 	   { background-color:#FFFFFF; margin:10px 0px; }
			select 	   { font-family:Arial, Helvetica; font-size:11px; color:#111111; }
			div		   { font-family:Arial, Helvetica; color:#111111; padding-left:5px;  }
			
			.titulo    { font-size:12px; background-color:#CCCCCC; margin-bottom:1px; font-weight:bold; height:20px; vertical-align:middle; }
			.subtitulo { font-size:11px; background-color:#CCCCCC; padding-top:5px; }
			.consulta  { font-size:11px; border:1px solid #CCCCCC; padding:10px; }
			.campos	   { font-weight:bold; padding-left:20px; }
			.rodape    { height:15px; background-color:#CCCCCC; }
		</style>
	</head>
	<body>
		<div class="titulo">
			<?php echo(getTText("tabelas",C_UCWORDS)); ?><br>
		</div>
		<div class="subtitulo">
			<select name="var_table" onChange="location.href='showtables.php?var_table=' + this.value;">
				<option value=""></option>
				<?php echo(montaCombo($objConn," SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY 1", "table_name", "table_name", $strTableName)); ?>
			</select><br><br>
			<?php echo(getTText("campos",C_UCWORDS)); ?></div>
		<div class="consulta">
			<?php 
				if($strTableName != ""){
					echo("			SELECT<br>
				<div class=\"campos\"> ");
					
					$strSQL = " SELECT column_name FROM information_schema.columns WHERE table_name = '" . $strTableName . "'";
					$objResult = $objConn->query($strSQL);
					
					$charVirgula = " ";
					
					foreach($objResult as $objRS){
						echo($charVirgula . getValue($objRS,"column_name") . "<br>");
						if($charVirgula == " ") { $charVirgula = ","; } 
					}
					
					$objResult->closeCursor();
				echo("
				</div>
			FROM <br>
			<span class=\"campos\">" . $strTableName . "</span>");
				}
			?>
		</div>
		<div class="rodape"></div>
	</body>
</html>
<?php
$objConn = NULL;
?>