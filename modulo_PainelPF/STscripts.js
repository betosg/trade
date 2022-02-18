<script language="javascript">

function ajaxBuscaCEPLocal(prIDCep,prIDLog,prIDBai,prIDCid,prIDUF,prIDNum,prIDREPLACE){
	// Esta Função busca um cep através do ID de um campo CEP informado
	// e efetua busca de CEP ou em nossa base de dados TRADEUNION ou di-
	// retamente no site republicavirtual.com, que disponibiliza uma ba-
	// se de ceps atualizada de ENDEREÇOS. OBS: TODOS PARÂMETROS DEVEM
	// SER ENCAMINHADOS APENAS O ID DO CAMPO. O CAMPO de prIDNumero É O
	// QUE RECEBERÁ O FOCUS POSTERIORMENTE.
	var objCep, objLog, objBai, objCid, objEst, objNum, objAjax;
	var strReturn, arrReturn;
	// Cria os elementos para manipulação posterior
	objCep = document.getElementById(prIDCep);
	objLog = document.getElementById(prIDLog);
	objBai = document.getElementById(prIDBai);
	objCid = document.getElementById(prIDCid);
	objEst = document.getElementById(prIDUF);
	objNum = document.getElementById(prIDNum);
	objRep = document.getElementById(prIDREPLACE);
	// Testa se algum está vazio, anti-erros
	if(objCep == null || objLog == null || objBai == null || objCid == null || objEst == null || objNum == null || objCep.value == null || objCep.value == "") { return(null); }
	// LIMPEZA DOS VALORES DOS CAMPOS DE ENDEREÇO
	objLog.value = "";
	objNum.value = "";
	objBai.value = "";
	objCid.value = "";
	objEst.value = "";
	// Até aqui, campos garantidos que existem, not null
	// CRIA OBJETO AJAX
	objAjax = createAjax();
	// Caso ID de replace tenha sido informada, então tro-
	// ca o seu innerHTML por um loader, para melhor UI
	if(objRep != null){ objRep.innerHTML = "<img src='../../_tradeunion/img/icon_ajax_loader.gif' border='0' width='12' />"; }
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				// alert(objAjax.responseText);
				// Quebra a STRING DE RETORNO, no formato
				// CSV e testa se é um logradouro único,
				// inexistente ou LOGRADOURO COMPLETO

				arrReturn = objAjax.responseText.split("<br>");
				
				// Caso LOGRADOURO ÚNICO
				if(arrReturn[0] == "2"){
					// Breve Tratamento para Campos
					arrReturn[1] = (arrReturn[1] == null) ? "" : arrReturn[1]; //CIDADE
					arrReturn[2] = (arrReturn[2] == null) ? "" : arrReturn[2]; //ESTADO
					objCid.value = arrReturn[1];
					objEst.value = arrReturn[2];
				}
				// CASO LOGRADOURO COMPLETO
				if(arrReturn[0] == "1"){
					// Breve Tratamento para Campos
					arrReturn[1] = (arrReturn[1] == null) ? "" : arrReturn[1]; //TIPO DE LOGRADOURO
					arrReturn[2] = (arrReturn[2] == null) ? "" : arrReturn[2]; //LOGRADOURO
					arrReturn[3] = (arrReturn[3] == null) ? "" : arrReturn[3]; //BAIRRO
					arrReturn[4] = (arrReturn[4] == null) ? "" : arrReturn[4]; //CIDADE
					arrReturn[5] = (arrReturn[5] == null) ? "" : arrReturn[5]; //ESTADO
					objLog.value = arrReturn[1]+" "+arrReturn[2];
					objBai.value = arrReturn[3];
					objCid.value = arrReturn[4];
					objEst.value = arrReturn[5];
				}
				// CASO LOGRADOURO INEXISTENTE
				if(arrReturn[0] == "0"){
					// Insere mensagem no LOADER que LOGRADOURO NÃO EXISTE
					if(objRep != null){ 
						objRep.innerHTML = "<span style='color:red;'>(NÃO existe logradouro para o cep <em><b>"+ objCep.value +"</b></em>)";
						setTimeout("objRep.innerHTML = '';",3000);
					}
					objCep.focus();
					return(null);
				}
				// SETA O LOADER PARA VAZIO E DÁ FOCUS
				// NO CAMPO DE ENDEREÇO 'NÚMERO', JÁ AVALIADO
				if(objRep != null){ objRep.innerHTML = ""; }
				objNum.focus();
			} else { alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText); }
		}
	}
	objAjax.open("GET","../../_tradeunion/_ajax/buscacep.php?var_cep="+objCep.value, true);
	objAjax.send(null);
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções AJAX ------------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */

function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
	strLink = "../../_tradeunion/modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}


</script>