<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"        ,2);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS"   ,getsession($strSesPfx . "_num_per_page"));    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
define("ARQUIVO_LEITURA"   ,"STconfiginc.php"); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não
$strIndice        = request("var_indice");         // Campo de filtro
$strValor         = request("var_valor");          // Campo de filtro


$strNome			= request("dbvar_str_nome"); 
$strRegiaoAtuacao	= request("dbvar_str_reg_atuacao"); 
$strAtuacao			= request("dbvar_str_atuacao"); 
$strCidade			= request("dbvar_str_cidade"); 
$strUF				= request("dbvar_str_uf"); 
$strEspecialidade	= request("dbvar_str_especialidade"); 	
$strKeyword	    	= request("dbvar_str_keyword"); 


//echo("dbvar_str_nome:".$strNome."<br>");
//echo("dbvar_str_reg_atuacao:".$strRegiaoAtuacao."<br>");
//echo("dbvar_str_atuacao:".$strAtuacao."<br>");
//echo("dbvar_str_cidade:".$strCidade."<br>");
//echo("dbvar_str_uf:".$strUF."<br>");
//echo("dbvar_str_especialidade:".$strEspecialidade."<br>");
//echo("dbvar_str_keyword:".$strKeyword."<br>");







$strExibir = request("var_exibir");
if ($strExibir == "") $strExibir = "faturar";

/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

/***        FUNÇÕES AUXILIARES - OPCIONAL         ***/
/****************************************************/
function filtro($prValue) {
	global $strIndice, $strValor;
	
	$strLine = trim($prValue);
	
	$strLine = preg_replace("/define\(|\)\;(.*)/i","",$strLine);
	$arrLine = explode(",",$strLine);
	
	$arrLine[0] = str_replace("\"","",$arrLine[0]);
	$strLineValor = (isset($arrLine[1])) ? $arrLine[1] : "";
	
	if(($strIndice == "" || strpos($arrLine[0],$strIndice) !== false) 
	   && ($strValor == "" || strpos($strLineValor,$strValor) !== false) 	
	   && (trim($strLine) != "" && trim($strLine) != "<?php" && trim($strLine) != "?>")
	   && (strpos($strLine,"//") === false)) {
	    return($prValue);
	}
}

/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation) {
	if($strAcao == ".pdf") {
		redirect("exportpdf.php"); //Redireciona para página que faz a exportação para adode reader
	}
	else{
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
} 

$objConn = abreDBConn(CFG_DB); // Abertura de banco

$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");

if ($intCodDado != "") {
?>
<script>
	var allTrTags = new Array();
	var detailTrFrameAnt = '';
	var moduloDetailAnt = '';
	
	function showDetailGrid(prChave_reg,prLink, prField){
		if(prLink.indexOf("?") == -1){
			strConcactQueryString = "?"
		}else{
			strConcactQueryString = "&"
		}
		var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
		if(detailTr == 'none'){
			 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
	
			var allTrTags  = document.getElementsByTagName("tr");
			for( i=0; i < allTrTags.length; i++){
				if(allTrTags[i].className == 'iframe_detail'){
					allTrTags[i].style.display = 'none';
				}
			}
			document.getElementById("detailtr_"+prChave_reg).style.display = '';
		}else{
			if( moduloDetailAnt == prLink){
					document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
			}else{
				if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
					 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
				}
			}
		}
		moduloDetailAnt = prLink;
	}
	
	function SetIFrameSource(prPage,prId) {
		document.getElementById(prId).src = prPage;
	}
	
	<?php if(getsession($strSesPfx . "_field_detail") != '') { 	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php }	?>
	
</script>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<?php 
		if(!$boolIsExportation || $strAcao == "print"){
			echo("
				  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
			      <link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
			      <script type='text/javascript' src='../_scripts/tablesort.js'></script>
			      <style>
			  	    ul{ margin-top: 0px; margin-bottom: 0px; }
				    li{ margin-left: 0px; }
			      </style>
			      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				");
		}
	?>
	<script language="JavaScript" type="text/javascript">
		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}
	</script>
  </head>
<body style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table align="center" cellpadding="0" cellspacing="0" style="border:none;width:80%;">
		<tr>
        	<td align="center">
            	<table>                 
                   <form name="formrg1" id="formrg1" method="POST" action="STBuscaSocio.php">
                    <tr >
                        <td align="right"><font size="2"><b>*Nome Sócio:</b></font></td>
                        <td align="left" colspan="3" ><input type="text" name="dbvar_str_nome" id="dbvar_str_nome" maxlength="255"  style="width:550px" value="<?php echo($strNome); ?>"></td>
        			</tr>  
                    <tr><td colspan="4">&nbsp;</td></tr>

			          <tr>
                           <td align="right"><font size="2"><b>*Região Atuação:</b></font></td>
                            <td>										
                                <select name="dbvar_str_reg_atuacao" id="dbvar_str_reg_atuacao" style="width:100%;">
                                  <option value="" selected>Selecione...</option>                                        
										<?php $strRegiaoRequest = $strRegiaoAtuacao; $strRegiaoRequest = ($strRegiaoRequest == "") ? "" : $strRegiaoRequest ; echo(montaCombo($objConn,"SELECT cod_regiao_pais, nome FROM lc_regiao_pais ORDER BY nome","cod_regiao_pais","nome",$strRegiaoRequest)); ?>
										</select>                        
                               
                                
                            </td>
                   
                            <td align="right"><font size="2"><b>*Area Atuação:</b></font></td>
                            <td>										
                                <select name="dbvar_str_atuacao" id="dbvar_str_atuacao" style="width:100%;">
                                  <option value="" selected>Selecione...</option>                                        
										<?php $strAreaRequest = $strAtuacao; $strAreaRequest = ($strAreaRequest == "") ? "" : $strAreaRequest ; echo(montaCombo($objConn,"SELECT cod_atuacao, nome FROM cad_atuacao WHERE nome not like 'x_Nao_Usar_%%'ORDER BY nome","cod_atuacao","nome",$strAreaRequest)); ?>
										</select>                        
                               
                                
                            </td>
                    </tr>                 
                    <tr><td colspan="4">&nbsp;</td></tr>                                                             
                    <tr >
                        <td align="right"><font size="2"><b>*Cidade:</b></font></td>
                        <td><input type="text" name="dbvar_str_cidade" id="dbvar_str_cidade" maxlength="255" value="<?php echo($strCidade); ?>" style="width:100%;"></td>
                   
                            <td align="right"><font size="2"><b>*Estado:</b></td>
                            <td>										
                                <select name="dbvar_str_uf" id="dbvar_str_uf" style="width:45px;">
                                          <option value="" selected>Selecione...</option>                                        
										<?php $strUFRequest = $strUF; $strUFRequest = ($strUFRequest == "") ? "" : $strUFRequest ; echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado",$strUFRequest)); ?>
										</select>                                 
                               
                                
                            </td>
                    </tr>
					<tr><td colspan="4">&nbsp;</td></tr>   
					 <tr>
                            <td align="right"><font size="2"><b>*Especialidade:</b></font></td>
                            <td>						
                            
                            <select name="dbvar_str_especialidade" id="dbvar_str_especialidade" style="width:100%;">
                                          <option value="" selected>Selecione...</option>                                        
										<?php $strEspecRequest = $strEspecialidade; $strEspecRequest = ($strEspecRequest == "") ? "" : $strEspecRequest ; echo(montaCombo($objConn,"SELECT cod_especialidade, nome FROM cad_especialidade ORDER BY nome","cod_especialidade","nome",$strEspecRequest)); ?>
										</select>                            
                            
                            	
                                
                            </td>
							<td align="right"><font size="2"><b>*Palavra-chave:</b></font></td>
							 <td>
							<input type="text" name="dbvar_str_keyword" id="dbvar_str_keyword" maxlength="255" placeholder="busca em currículo resumido"  style="width:250px" value="<?php echo($strKeyword); ?>"></td>
                   
                            
                    </tr>  

					
				</table>
            </td>
		</tr>             
        <tr>
             
        <tr><td>&nbsp;</td><td></td></tr>        
        <tr>
        	<td align="center" colspan="4"><button onClick="document.formrg1.submit();">Buscar</button></td>
        </tr>
        <tr><td>&nbsp;</td><td></td></tr>    
                            	
											
                            
                            
 <tr>
 	<td  colspan="2" width="100%"  align="left" style="border:none; background:none; padding-left: 15px;">
	<?php 
	$strCount = 0;
	try {
		
		if (($strNome == "")&&($strCidade == "")&&($strUF == "")&&($strAtuacao == "")&&($strRegiaoAtuacao == "")&&($strEspecialidade == "")&&($strKeyword == "")){
			$strSQL  = "SELECT 1 FROM CAD_PF WHERE COD_PF = 0 ";
		}else{
		
		$strSQL  = " SELECT  DISTINCT cad_pf.cod_pf, UPPER(cad_pf.nome) AS NOME, CASE WHEN RIGHT(cad_pf.foto,3) ILIKE 'pdf' THEN NULL ELSE cad_pf.foto END AS foto , cad_pf.email, UPPER(cad_pf.endprin_cidade) AS endprin_cidade, UPPER(cad_pf.endprin_estado) AS endprin_estado";
		$strSQL .= "      , cad_pf_curriculo.curriculo_arquivo , cad_pf_curriculo.curriculo_resumido, UPPER(cad_pf_curriculo.graducao_curso) AS graduacao ";
		$strSQL .= "      , cad_categoria.nome as categoria ";
		$strSQL .= "      , cad_pf.endprin_fone1, cad_pf.endprin_fone2";				
		$strSQL .= "      , (SELECT String_agg(DISTINCT T1.nome, '<br>') FROM lc_regiao_pais T1 INNER JOIN cad_pf_atuacao_regiao T2 ON ( T2.cod_regiao_pais = T1.cod_regiao_pais ) AND T2.COD_PF = cad_pf.cod_pf) AS regiao ";
        $strSQL .= "      , (SELECT String_agg(DISTINCT T1.nome, '<br>') FROM cad_especialidade T1 INNER JOIN cad_pf_especialidade T2 ON ( T2.cod_especialidade = T1.cod_especialidade ) AND T2.COD_PF = cad_pf.cod_pf) AS especialidade ";
        $strSQL .= "      , (SELECT String_agg(DISTINCT T1.nome, '<br>') FROM cad_atuacao T1 INNER JOIN cad_pf_atuacao T2 ON ( T2.cod_atuacao = T1.cod_atuacao  AND T2.COD_PF = cad_pf.cod_pf) where  t1.nome NOT LIKE 'x_Nao_Usar%') AS atuacao ";
		$strSQL .= " FROM cad_pf ";
		$strSQL .= " LEFT OUTER JOIN  cad_pf_curriculo ON (cad_pf_curriculo.cod_pf = cad_pf.cod_pf) ";
		$strSQL .= " LEFT OUTER JOIN  cad_pf_atuacao ON (cad_pf_atuacao.cod_pf = cad_pf.cod_pf) ";
		$strSQL .= " LEFT OUTER JOIN  cad_pf_atuacao_regiao ON (cad_pf_atuacao_regiao.cod_pf = cad_pf.cod_pf) ";
		$strSQL .= " LEFT OUTER JOIN  cad_pf_especialidade ON (cad_pf_atuacao_regiao.cod_pf = cad_pf.cod_pf) ";		
		$strSQL .= " LEFT OUTER JOIN  cad_categoria ON (cad_categoria.cod_categoria = cad_pf.cod_categoria) ";		
		$strSQL .= " WHERE cad_pf.status like 'Ativo' AND cad_pf.cod_categoria <> 23 ";
		$strSQL .= " AND cad_pf.nome not like '% TESTE' AND cad_pf.nome not like 'TESTE %' ";
		if ($strNome != "") {$strSQL .= " AND UPPER(unaccent(cad_pf.nome)) ILIKE UPPER(unaccent('%".$strNome."%')) ";}
		if ($strUF != "") {$strSQL .= " AND UPPER(cad_pf.endprin_estado) ILIKE UPPER('".$strUF."') ";}
		if ($strCidade != "") {$strSQL .= " AND UPPER(unaccent(cad_pf.endprin_cidade)) ILIKE UPPER(unaccent('%".$strCidade."%')) ";}
		if ($strKeyword != "") { $strSQL .= " AND UPPER(unaccent(cad_pf_curriculo.curriculo_resumido)) ILIKE UPPER(unaccent('%".$strKeyword."%')) "; }
		if ($strAtuacao != "") { $strSQL .= " AND cad_pf_atuacao.cod_atuacao = ".$strAtuacao; }
		if ($strRegiaoAtuacao != "") {   $strSQL .= " AND cad_pf_atuacao_regiao.cod_regiao_pais = ".$strRegiaoAtuacao; }
		if ($strEspecialidade != "") {   $strSQL .= " AND cad_pf_especialidade.cod_especialidade = ".$strEspecialidade;	}
		$strSQL .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 ";
		$strSQL .= " ORDER BY 2";
		//echo($strSQL);
		}
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	athBeginFloatingBox("100%","","<b>Sócios</b>",CL_CORBAR_GLASS_2);
	?>
	<table valign="middle" bgcolor="#FFFFFF" style=" width:100%;  margin-bottom:0px; " class="tablesort">
		<?php
		if($objResult->rowCount() > 0) {
			?>
			<thead>
			<tr>				
				<th class="sortable"><?php echo getTText("foto",C_NONE); ?></th>
				<th width="20%" class="sortable"><?php echo getTText("socio",C_NONE); ?></th>
				<th width="15%" class="sortable"><?php echo getTText("contato",C_NONE); ?></th>
				<th width="10%" class="sortable"><?php echo getTText("cidade_estado",C_NONE); ?></th>
				<th width="27%" class="sortable"><?php echo getTText("curriculo",C_NONE); ?></th>
				<th width="5%" class="sortable"><?php echo getTText("regiao",C_NONE); ?></th>
				<th width="15%" class="sortable"><?php echo getTText("atuacao",C_NONE); ?></th>
				<th width="8%" class="sortable"><?php echo getTText("especialidade",C_NONE); ?></th>
				
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($objResult as $objRS){
				if(getValue($objRS,"foto") != ""){
					$strFoto = getValue($objRS,"foto");
				}else{
					$strFoto = "avatar.jpg";
				}
				$strCount = $strCount + 1;
				
				?>
				<tr>
					
					<td valign="middle" ><font size="2"><?php echo("<img src='https://tradeunion.proevento.com.br/abfm/upload/fotospf/".$strFoto."' width='100px'>"); ?></font></td>
					<td valign="middle"><font size="2"><strong><?php echo(getValue($objRS,"nome")); ?></strong></font><br><?php echo(getValue($objRS,"graduacao")); ?></td>					
					<td valign="middle"><font size="2"><?php echo(getValue($objRS,"email")); ?><br>
					<?php if (getValue($objRS,"endprin_fone1") != ""){echo(getValue($objRS,"endprin_fone1")."<br>");}?>
					<?php if (getValue($objRS,"endprin_fone2") != ""){echo(getValue($objRS,"endprin_fone2")."<br>");}?></font></td>
					<td valign="middle"><font size="2"><?php echo(getValue($objRS,"endprin_cidade")); ?> / <?php echo(getValue($objRS,"endprin_estado")); ?></font></td>
					<td valign="middle"><font size="2"><?php if (getValue($objRS,"curriculo_arquivo") != ""){echo("<u><a href='https://tradeunion.proevento.com.br/abfm/upload/docspf/".getValue($objRS,"curriculo_arquivo")."' style='color:blue;' target='_blank'><strong>Currículo PDF</strong></a></u><br><br>");} if (getValue($objRS,"curriculo_arquivo") != ""){echo(getValue($objRS,"curriculo_resumido"));} ?></font></td>
					<td valign="middle"><font size="2"><?php echo(getValue($objRS,"regiao")); ?></font></td>
					<td valign="middle"><font size="2"><?php echo(getValue($objRS,"atuacao")); ?></font></td>
					<td valign="middle"><font size="2"><?php echo(getValue($objRS,"especialidade")); ?></font></td>
				</tr>
				<?php 
			}
			?>
			<tr>
				<td colspan="7" align="center"><div style="padding-top:2px; padding-bottom:2px;"><strong><?php echo($strCount); ?> registro(s) encontrado(s)</strong></div></td>
			</tr>
			</tbody>
			<?php 
		}
		else {
			if (($strNome == "")&&($strCidade == "")&&($strUF == "")&&($strAtuacao == "")&&($strRegiaoAtuacao == "")&&($strEspecialidade == "")&&($strKeyword == "")){
			?>
			<tbody>
			<tr>
				<td colspan="7" align="center"><div style="padding-top:2px; padding-bottom:2px;">Informe pelo menos 1 parâmetro para consulta</div></td>
			</tr>
			</tbody>
			<?php
			} else {
			?>
			<tbody>
			<tr>
				<td colspan="7" align="center"><div style="padding-top:2px; padding-bottom:2px;"><?php echo(getTText("alert_consulta_vazia_titulo",C_NONE)); ?></div></td>
			</tr>
			</tbody>
			<?php
			}
		}
		?>
	</table>
	<?php
	athEndFloatingBox();
	$objResult->closeCursor();
	?>
	<br>
	</td>
 </tr>
</table>

<br><br><br><br><br>
</body>
</html>
<?php
}
else {
	echo(mensagem("err_selec_empresa_titulo","err_selec_empresa_desc","","","erro",1));
}
$objConn = NULL;
?>
