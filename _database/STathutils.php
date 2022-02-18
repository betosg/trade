<?php
/* ---------------------------------------------------------------------------
    Biblioteca de funções - específica de cada projeto								
 			 																
    Todas as funções de script PHP criadas especificamente para o projeto em 
 questão devem ser colocadas aqui. Este arquivo STathutil.php não é atualizado  
 pelo "updater" assim como o STconfig.php
 --------------------------------------------------------------------------- */

function getVarEntidade($probjConn, $prIDVar) {
	$strLocalSQL = " SELECT valor FROM sys_var_entidade WHERE id_var = '" . $prIDVar . "' ";
	
	$objLocalResult = $probjConn->query($strLocalSQL);
	$objLocalRS = $objLocalResult->fetch();
	
	if($objLocalRS !== array())
		$Valor = getValue($objLocalRS, "valor");
	else 
		$Valor = "";
	$objLocalResult->closeCursor();
	
	return($Valor);
}

function getDadosPJSelected($prObjConn, $prPesq1, $prPesq2) {
	$intPJCodigo = "";
	$strPJNome = "";
	$strPJFoto = "";
	$intPJCodUsuario = "";
	
	try {
		//BUSCA DADOS DA PJ
		$strSQL = " SELECT t1.cod_pj, t1.razao_social, t2.cod_usuario, t2.foto
		            FROM cad_pj t1, sys_usuario t2 
		            WHERE t1.cod_pj = t2.codigo 
				    AND t2.tipo = 'cad_pj' ";
		if ($prPesq1 != "") $strSQL .= " AND t1.cod_pj = " . $prPesq1;
		if ($prPesq2 != "") $strSQL .= " AND t2.id_usuario = '" . $prPesq2 . "' ";
		//echo $strSQL;
		//die();
		$objResult = $prObjConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($objResult->rowCount() > 0) {
		$objRS = $objResult->fetch();
		
		$intPJCodigo = getValue($objRS,"cod_pj");
		$strPJNome = getValue($objRS,"razao_social");
		$strPJFoto = getValue($objRS,"foto");
		$intPJCodUsuario = getValue($objRS,"cod_usuario");
	}
	
	$objResult->closeCursor();
	
	setsession(CFG_SYSTEM_NAME . "_pj_selec_codigo", $intPJCodigo);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_nome", $strPJNome);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_foto", $strPJFoto);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_cod_usuario", $intPJCodUsuario);
	
	?>
	<script type="text/javascript" language="javascript">
	var strAUX = "<?php echo( strtoupper("CLIENTE: " . getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") . " - " . getsession(CFG_SYSTEM_NAME . "_pj_selec_nome"))); ?>";
	parent.frames["tradeunion_frfooter"].document.formPrjAtv.var_prj_atv.value = strAUX;
	</script>
	<?php
}


function getDadosPFSelected($prObjConn, $prPesq1, $prPesq2) {
	$intPJCodigo = "";
	$strPJNome = "";
	$strPJFoto = "";
	$intPJCodUsuario = "";
	
	try {
		//BUSCA DADOS DA PJ
		$strSQL = "  SELECT t1.cod_pf, t1.nome , t2.cod_usuario, t2.foto
						FROM cad_pf as t1, sys_usuario as t2 
						WHERE t1.cod_pf = t2.codigo 
						AND t2.tipo = 'cad_pf'   ";
		if ($prPesq1 != "") $strSQL .= " AND t1.cod_pf = " . $prPesq1;
		if ($prPesq2 != "") $strSQL .= " AND t2.id_usuario = '" . $prPesq2 . "' ";
		
		$objResult = $prObjConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($objResult->rowCount() > 0) {
		$objRS = $objResult->fetch();
		
		$intPJCodigo = getValue($objRS,"cod_pf");
		$strPJNome = getValue($objRS,"nome");
		$strPJFoto = getValue($objRS,"foto");
		$intPJCodUsuario = getValue($objRS,"cod_usuario");
	}
	
	$objResult->closeCursor();
	
	setsession(CFG_SYSTEM_NAME . "_pj_selec_codigo", $intPJCodigo);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_nome", $strPJNome);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_foto", $strPJFoto);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_cod_usuario", $intPJCodUsuario);
	
	?>
	<script type="text/javascript" language="javascript">
	var strAUX = "<?php echo( strtoupper("CLIENTE: " . getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") . " - " . getsession(CFG_SYSTEM_NAME . "_pj_selec_nome"))); ?>";
	parent.frames["tradeunion_frfooter"].document.formPrjAtv.var_prj_atv.value = strAUX;
	</script>
	<?php
}




function resetDadosPJSelected() {
	setsession(CFG_SYSTEM_NAME . "_pj_selec_codigo", "");
	setsession(CFG_SYSTEM_NAME . "_pj_selec_nome"  , "");
	setsession(CFG_SYSTEM_NAME . "_pj_selec_foto", "");
	setsession(CFG_SYSTEM_NAME . "_pj_selec_cod_usuario", "");
	
	?>
	<script type="text/javascript" language="javascript">
	alert("teste");
//	parent.frames["tradeunion_frfooter"].document.formPrjAtv.var_prj_atv.value = "";
	parent.frames["tradeunion_frfooter"].reload();
	</script>
	<?php
}

function translateDate($prDate){
	// Função translateDate()
	// criado por: Leandro (ATH)
	// data >>> 27/10/2009
	// Descrição: Esta função recebe uma
	// data [independente de caracter de
	// separação, seja '-' ou '/'] e re-
	// torna uma string por extenso. Por
	// exemplo, translateDate('21/12/2012')
	// retornará a string '21 de dezembro 
	// de 2012'.
	
	$strReturnValue = "";
	
	// Recebimento de parâmetros
	$auxDate = $prDate;
	
	// array de meses. Em minúsculo
	// para poder ser tratado com 
	// outras funções de string
	$arrMonths[0]  = '';
	switch(CFG_LANG){
		case 'ptb':
			$strConcat	   = 'de';
			$arrMonths[1]  = 'janeiro';
			$arrMonths[2]  = 'fevereiro';
			$arrMonths[3]  = 'março';
			$arrMonths[4]  = 'abril';
			$arrMonths[5]  = 'maio';
			$arrMonths[6]  = 'junho';
			$arrMonths[7]  = 'julho';
			$arrMonths[8]  = 'agosto';
			$arrMonths[9]  = 'setembro';
			$arrMonths[10] = 'outubro';
			$arrMonths[11] = 'novembro';
			$arrMonths[12] = 'dezembro';
			break;
		case 'en':
			$strConcat	   = 'of';
			$arrMonths[1]  = 'january';
			$arrMonths[2]  = 'february';
			$arrMonths[3]  = 'march';
			$arrMonths[4]  = 'april';
			$arrMonths[5]  = 'may';
			$arrMonths[6]  = 'june';
			$arrMonths[7]  = 'july';
			$arrMonths[8]  = 'august';
			$arrMonths[9]  = 'september';
			$arrMonths[10] = 'october';
			$arrMonths[11] = 'november';
			$arrMonths[12] = 'december';
			break;
		case 'es':
			$strConcat	   = 'de';
			$arrMonths[1]  = 'enero';
			$arrMonths[2]  = 'febrero';
			$arrMonths[3]  = 'marzo';
			$arrMonths[4]  = 'abril';
			$arrMonths[5]  = 'mayo';
			$arrMonths[6]  = 'junio';
			$arrMonths[7]  = 'julio';
			$arrMonths[8]  = 'agosto';
			$arrMonths[9]  = 'septiembre';
			$arrMonths[10] = 'octubre';
			$arrMonths[11] = 'noviembre';
			$arrMonths[12] = 'diciembre';	
	}
	
	// faz procura de barras e traços
	// na data informada
	preg_match('/\d{2}(\/|\-)\d{2}(\/|\-)\d{4}/',$auxDate,$arrAuxSubject);
	// var_dump($arrAuxSubject);
	// caso nada tenha sido retornado
	// entao retorna string vazia
	if(@$arrAuxSubject[0] == ""){
		$strReturnValue = "";
		return($strReturnValue);
	}
	else{
		// ER - \d = [0-9]
		// Substitui barras ou traços de data
		// por traços somente, para o explode
		// que será feito posteriormente
		$auxDate = preg_replace('/\/|\-/','-',$auxDate);
				
		// transforma a data em formato correto
		// para explode. Após, realiza explode.
		//echo($auxDateFormat = cDate(CFG_LANG,$auxDate,false));
		$arrDate = explode('-',$auxDate);
		
		// debugs
		// echo $arrDate[2]."<br />";
		// echo $arrDate[1]."<br />";
		// echo $arrDate[0]."<br />";
		// cria um Contador do tamanho do número
		// de meses existentes
		
		// pode ser que a data enviada tenha como mes
		// um numero maior que 12, por exemplo.
		if($arrDate[1] > 12 || $arrDate[1] < 0){
			$strReturnValue = "";
			return($strReturnValue);
		}
		$auxCounter = count($arrMonths);
		$auxCount   = 1;
		
		// testa enquanto o contador for diff
		// do numero de meses - vai testando
		// sucessivamente
		while($auxCount != $auxCounter){
			// se o mes digitado for igual a
			// posicao corrente do contador 
			// entao preenche retorno
			if($arrDate[1] == $auxCount){
				$strReturnValue = $arrDate[0]." ".$strConcat." ".$arrMonths[$auxCount]." ".$strConcat." ".$arrDate[2];
				$auxCount = $auxCounter;
			}else{
				$auxCount++;
			}
		}
		return($strReturnValue);
	}	
}


function getMesExtensoFromMes($prMes){
	// Função getMesExtensoFromMes()
	// criado por: Leandro / CLV (ATH)
	// data >>> 13/11/2009
	// Descrição: Esta função recebe um
	// numero de mes e retorna uma string 
	// string com o nome do mes por extenso. 
	// Por exemplo, getMesExtensoFromMes('12')
	// retornará a string 'dezembro'.
	
	$strReturnValue = "";
	
	// Recebimento de parâmetros
	$auxMes = $prMes;
	
	// array de meses. Em minúsculo
	// para poder ser tratado com 
	// outras funções de string
	$arrMonths[0]  = '';
	$arrMonths[1]  = 'janeiro';
	$arrMonths[2]  = 'fevereiro';
	$arrMonths[3]  = 'março';
	$arrMonths[4]  = 'abril';
	$arrMonths[5]  = 'maio';
	$arrMonths[6]  = 'junho';
	$arrMonths[7]  = 'julho';
	$arrMonths[8]  = 'agosto';
	$arrMonths[9]  = 'setembro';
	$arrMonths[10] = 'outubro';
	$arrMonths[11] = 'novembro';
	$arrMonths[12] = 'dezembro';
	
	// pode ser que a data enviada tenha como mes
	// um numero maior que 12, por exemplo.
	if($auxMes > 12 || $auxMes < 0){
		$strReturnValue = "";
		return($strReturnValue);
	}
	$auxCounter = count($arrMonths);
	$auxCount   = 1;
	
	// testa enquanto o contador for diff
	// do numero de meses - vai testando
	// sucessivamente
	while($auxCount != $auxCounter){
		// se o mes digitado for igual a
		// posicao corrente do contador 
		// entao preenche retorno
		if($auxMes == $auxCount){
			$strReturnValue = $arrMonths[$auxCount];
			$auxCount = $auxCounter;
		}else{
			$auxCount++;
		}
	}
	return($strReturnValue);
}


function getMesExtensoFromDate($prDate){
	// Função getMesExtensoFromDate()
	// criado por: Leandro / CLV (ATH)
	// data >>> 13/11/2009
	// Descrição: Esta função recebe uma
	// data [independente de caracter de
	// separação, seja '-' ou '/'] e re-
	// torna o mês por extenso. Por
	// exemplo, getMesExtensoFromDate('21/12/2012')
	// retornará a string 'dezembro'.
	
	$strReturnValue = "";
	
	// Recebimento de parâmetros
	$auxDate = $prDate;
	
	// array de meses. Em minúsculo
	// para poder ser tratado com 
	// outras funções de string
	$arrMonths[0]  = '';
	$arrMonths[1]  = 'janeiro';
	$arrMonths[2]  = 'fevereiro';
	$arrMonths[3]  = 'março';
	$arrMonths[4]  = 'abril';
	$arrMonths[5]  = 'maio';
	$arrMonths[6]  = 'junho';
	$arrMonths[7]  = 'julho';
	$arrMonths[8]  = 'agosto';
	$arrMonths[9]  = 'setembro';
	$arrMonths[10] = 'outubro';
	$arrMonths[11] = 'novembro';
	$arrMonths[12] = 'dezembro';
	
	// faz procura de barras e traços
	// na data informada
	preg_match('/\d{2}(\/|\-)\d{2}(\/|\-)\d{4}/',$auxDate,$arrAuxSubject);
	// var_dump($arrAuxSubject);
	// caso nada tenha sido retornado
	// entao retorna string vazia
	if(@$arrAuxSubject[0] == ""){
		$strReturnValue = "";
		return($strReturnValue);
	}
	else{
		// ER - \d = [0-9]
		// Substitui barras ou traços de data
		// por traços somente, para o explode
		// que será feito posteriormente
		$auxDate = preg_replace('/\/|\-/','-',$auxDate);
				
		// transforma a data em formato correto
		// para explode. Após, realiza explode.
		$auxDateFormat = cDate(CFG_LANG,$auxDate,false);
		$arrDate       = explode('-',$auxDateFormat);
		// $arrDate[2];
		// $arrDate[1];
		// $arrDate[0];
		// cria um Contador do tamanho do número
		// de meses existentes
		
		// pode ser que a data enviada tenha como mes
		// um numero maior que 12, por exemplo.
		if($arrDate[1] > 12 || $arrDate[1] < 0){
			$strReturnValue = "";
			return($strReturnValue);
		}
		$auxCounter = count($arrMonths);
		$auxCount   = 1;
		
		// testa enquanto o contador for diff
		// do numero de meses - vai testando
		// sucessivamente
		while($auxCount != $auxCounter){
			// se o mes digitado for igual a
			// posicao corrente do contador 
			// entao preenche retorno
			if($arrDate[1] == $auxCount){
				$strReturnValue = $arrMonths[$auxCount];
				$auxCount = $auxCounter;
			}else{
				$auxCount++;
			}
		}
		return($strReturnValue);
	}	
}


function getWeekDay($prData){
	// Função getWeekDay()
	// criado por: Leandro / MARCIO (ATH)
	// data >>> 18/11/2009
	// Descrição: Esta função recebe uma
	// data [esperado que esteja no formato
	// UNIX de datas Y-m-d] e retorna uma
	// string contendo o dia da semana. Por
	// exemplo, getWeekDay('2012-12-21')
	// retornará a string 'sexta'.
	// OBS: PARA CHEGAR EM UMA DATA DE FOR-
	// MATO UNIX, UTILIZE A FUNÇÃO cDate();
	// $dtDate = cDate(CFG_LANG,'21/12/2012',true); 

	$dtDate = "";
	
	if(!is_date($prData)){
		return($dtDate);
	} else {
		$dtDate = $prData;
		$dtDate = @explode("-",$dtDate);
		$dtDate = @mktime(0,0,0,$dtDate[1],$dtDate[2],$dtDate[0]);
		$dtDate = @getdate($dtDate);
		$dtDate = $dtDate["wday"];
		switch($dtDate){
			case  0: $dtDate = "domingo";	break;
			case  1: $dtDate = "segunda";	break;
			case  2: $dtDate = "terça";		break;
			case  3: $dtDate = "quarta";	break;
			case  4: $dtDate = "quinta";	break;
			case  5: $dtDate = "sexta";		break;
			case  6: $dtDate = "sabado";	break;
			default: $dtDate = " --- ";		break; 
		}
		return($dtDate);
	}
}

function getWeekDayFromNumber($prNumber=""){
	// Esta função recebe um número e retorna
	// o dia da semana, com range de 1 à 7
	$intNumber = $prNumber;
	$strReturn = "";
	if($intNumber == "" || (($intNumber < 1) || ($intNumber > 7)) ){
		return(false);
	} else {
		switch($intNumber)
		{
			case 1 : $strReturn = "domingo";	break;
			case 2 : $strReturn = "segunda";	break;
			case 3 : $strReturn = "terça";		break;
			case 4 : $strReturn = "quarta";		break;
			case 5 : $strReturn = "quinta";		break;
			case 6 : $strReturn = "sexta";		break;
			case 7 : $strReturn = "sábado";		break;
		}
		return($strReturn);
	}
}


function diffMes($prData1, $prData2){
//Calcula a diferença de meses entre duas datas
//o retorno da função é o numero de meses. 
//08/11/2011 By GS
	$data1 = $prData1; 
	$arr = explode('/',$data1); 
	
	$data2 = $prData2; 
	$arr2 = explode('/',$data2); 
	
	$dia1 = $arr[0]; 
	$mes1 = $arr[1]; 
	$ano1 = $arr[2]; 
	
	$dia2 = $arr2[0]; 
	$mes2 = $arr2[1]; 
	$ano2 = $arr2[2]; 
	
	$a1 = ($ano1 - $ano2)*12;
	$m1 = ($mes1 - $mes2);
	$m3 = ($m1 + $a1);
	return $m3;
}

?>