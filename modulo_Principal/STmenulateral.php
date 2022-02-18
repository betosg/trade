<?php
// INCLUDES
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// Verificação de ACESSO - ENQUANTO MODULO DE MENSAGENS NAO EXISTE, NAO TESTAR DIREITOS
// $strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); 

// Abre conexão com DB
$objConn = abreDBConn(CFG_DB);

$id_mercado = getsession(CFG_SYSTEM_NAME."_id_mercado");
$id_evento	= getsession(CFG_SYSTEM_NAME."_id_evento");
 $codcli		= getsession(CFG_SYSTEM_NAME."_codcli");

?>
<html>
<head>
<title>GERAL</title>
</head>
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">
<script language="javascript"> 
function ShowArea(prCodigo1, prCodigo2)
{
	if (document.getElementById(prCodigo1).style.display == 'none') {
		document.getElementById(prCodigo1).style.display = 'block';
		document.getElementById(prCodigo2).src = '../img/BulletMenos.gif';
	}
	else { 
		document.getElementById(prCodigo1).style.display = 'none';
		document.getElementById(prCodigo2).src = '../img/BulletMais.gif';
	}
}
</script>
<?php if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != '')

	{ //echo(getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") );?>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td>
			<?php
			try{
				$strSQL = " SELECT cod_painel, cod_feira, rotulo, ordem, descricao
							FROM sys_ar_painel 
							WHERE dtt_inativo IS NULL
							ORDER BY ordem, rotulo ";
				$objResultPai = $objConn->query($strSQL);
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			} 
			
			foreach($objResultPai as $objRSPai){
				$iCodigo = getValue($objRSPai,"cod_painel");
				$strRotulo = "<strong>".getTText(getValue($objRSPai,"rotulo"),C_TOUPPER)."</strong>";
				
				echo "<table width='99%' height='20' cellpadding='2' cellspacing='0'>";
				echo "<tr><td width='16' align='center'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
				echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
				echo "<td>".$strRotulo."</td></tr>";
				echo "</table>";
				echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
				
				try{
					$strSQL = " SELECT t1.cod_painel_item, t1.rotulo, t1.atalho, t1.params, t1.target, t1.tipo, t1.dt_ini, t1.dt_fim
									 , t1.grupo, t1.icone, t1.tabela_ftrans, t1.ordem, t1.obrigatorio, t1.arquivo
									 , COUNT(t2.cod_marcacao) AS total
								FROM sys_ar_painel_item t1
								LEFT OUTER JOIN sys_ar_marcacao t2 ON (t1.tabela_ftrans = t2.tabela_ftrans 
									AND t2.idmercado ILIKE '".$id_mercado."' 
									AND t2.idevento = '".$id_evento."' 
									AND t2.codcli = '".$codcli."')
								WHERE t1.cod_painel = ".getValue($objRSPai,"cod_painel")."
								AND t1.dtt_inativo IS NULL
								 AND ((t1.dt_ini IS NULL AND t1.dt_fim IS NULL)  
				                      OR (t1.dt_ini IS NOT NULL AND t1.dt_fim IS NOT NULL AND CURRENT_TIMESTAMP BETWEEN t1.dt_ini AND t1.dt_fim)) 
								GROUP BY t1.cod_painel_item, t1.rotulo, t1.atalho, t1.params, t1.target, t1.tipo, t1.dt_ini, t1.dt_fim
									   , t1.grupo, t1.icone, t1.tabela_ftrans, t1.ordem, t1.obrigatorio, t1.arquivo
								ORDER BY t1.ordem, t1.rotulo ";
					$objResultFilho = $objConn->query($strSQL);
				}
				catch(PDOException $e){
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				} 
				
				echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0'>";
				foreach($objResultFilho as $objRSFilho){
					echo "<tr>";
					echo "  <td width='5'></td>";
					echo "  <td bgcolor='#DBDBDB' height='1' colspan='3'></td>";
					echo "  <td width='5'></td>";
					echo "</tr>";
					echo "<tr>";
					echo "  <td width='5'></td>";
					echo "  <td width='16'></td>";
					echo "  <td height='16' >";
					echo "<a ";
					if (getValue($objRSFilho,"tipo") == "texto")
						echo "href='..\modulo_PainelPJ\STshowconteudo.php?var_chavereg=".getValue($objRSFilho,"cod_painel_item")."' target='".CFG_SYSTEM_NAME."_frmain'";
					elseif (getValue($objRSFilho,"tipo") == "arquivo") {
						echo "href='../upload/arquivos/".getValue($objRSFilho,"arquivo")."' ";
						if (getValue($objRSFilho,"target") != "")
							echo " target='".getValue($objRSFilho,"target")."'";
						else
							echo " target='".CFG_SYSTEM_NAME."_frmain'";
					}
					else {
						if (stripos(getValue($objRSFilho,"atalho"), "javascript") === false)
							echo "href='".getValue($objRSFilho,"atalho")."'";
						else
							echo "href='#' onClick=\"".getValue($objRSFilho,"atalho")."\" ";
						if (getValue($objRSFilho,"target") != "")
							echo " target='".getValue($objRSFilho,"target")."'";
						else
							echo " target='".CFG_SYSTEM_NAME."_frmain'";
					}
					echo ">";
					if (getValue($objRSFilho,"obrigatorio") == true) echo "<font color='#FF0000'>";
					if (getValue($objRSFilho,"dt_fim") != "") {
						if (getValue($objRSFilho,"dt_ini") != "")
							echo "<span title='Período de ".dDate(CFG_LANG,getValue($objRSFilho,"dt_ini"),false)." a ".dDate(CFG_LANG,getValue($objRSFilho,"dt_fim"),false)."'>";
						else
							echo "<span title='Limite até ".dDate(CFG_LANG,getValue($objRSFilho,"dt_fim"),false)."'>";
					}
					echo getTText(getValue($objRSFilho,"rotulo"),C_NONE);
					if (getValue($objRSFilho,"dt_fim") != "") echo "</span>";
					if (getValue($objRSFilho,"obrigatorio") == true) echo "</font>";
					echo "</a>";
					echo "</td>";
					echo "  <td width='16' style='text-align:center; vertical-align:top;'>";
					if (getValue($objRSFilho,"total") > 0) echo "<img src='../img/iconstatus_completo.gif' vspace='2' alt='".getTText("circ_completa",C_NONE)."' title='".getTText("circ_completa",C_NONE)."'>";
					echo "</td>";
					echo "  <td width='5'></td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
			}
			$objResultPai->closeCursor();
			?>
			</td>
		</tr>
		</table>
		</body>
		</html>
		<?php
			$objConn = NULL;}
		?>