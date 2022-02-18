<script language="JavaScript">
/* -------------------------------------------------------------------------------
    Biblioteca de funções - específica de cada projeto								
 			 																
    Todas as funções de script JavaScript criadas ESPECIFICAMENTE para o projeto 
 em questão devem ser colocadas aqui. Este arquivo STscripts.php não é atualizado  
 pelo "updater". 
 --------------------------------------------------------------------------------- */
 
// FUNÇÃO LIMPA SELECT - 25/09/2009
function limpaSelect(prObject){
	//alert('algo.');
	//Limpa o objeto (combo) antes de adicionar os itens
	//for (var i=0; i < obj.options.length; i++) { obj.options[i] = null; }
	var objID = prObject;
    //alterado a forma de limpar o select - utilizando o while ficava muito lento para combos com muitos registros - By Vini 15.03.2013
	document.getElementById(objID).innerHTML = "";
	//while (document.getElementById(objID).options.length > 0){
	//	document.getElementById(objID).options[0] = null; 
	//}
}
 
// FUNÇÃO VALIDA NUMEROS COM ':' JUNTO
function validateNumKeyST(prEvt){
	var inputKey = window.event ? prEvt.keyCode : prEvt.which;
	//var inputKey = event.keyCode;
	// numeros e ':' permitidos
	//alert(inputKey);
	if ( inputKey > 47 && inputKey < 59 || inputKey == 32 || inputKey == 13 || inputKey == 8){ // numbers
  		prEvt.returnValue = true;
    	return true;
 	}
	else{
  		if (navigator.appVersion.indexOf("MSIE")!=-1){ 
			prEvt.cancelBubble = true;
			prEvt.returnValue = false;
  			return false;
		}
		else {
			prEvt.stopPropagation();
			return false;
		}
	}
}

// função que verifica a consistência de e-mail
// @.xyz.xx expected ------ updated by Leandro em 13/10/2009
function verifyEmail(campo, valor){
	var email = valor;
	//checkEmail = form.email.value
	if ((email.indexOf('@') < 0) || ((email.charAt(email.length-4) != '.') && (email.charAt(email.length-3) != '.'))){
		if(email != ""){
			alert("\"" + valor + "\" não é um email válido!!!");
			campo.value = "";
			return false;
		}
		else{
			return false;
		}
	}
	return true;
}

// está engessada a este código somente			
function ajaxCopiaEnderContabil(prValue,prDBName){
	//alert(prDBName);
	var strSQL = "SELECT cod_pj_contabil, razao_social, end_logradouro, end_cep, end_bairro, end_cidade, end_estado, end_numero, end_complemento, contato, email, end_fone1, end_fone2 FROM cad_pj_contabil WHERE cod_pj_contabil = " + prValue;
	var objAjax; 
	var	strReturnValue;
	var arrDados;
				
	objAjax = createAjax();
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				//alert(strReturnValue);
				//alert(strSQL);
				// verifica se retornou dados
				if(strReturnValue.indexOf('|') != -1){
					arrDados = strReturnValue.split('|');
					document.formeditor_000.dbvar_num_cod_pj_contabil.value 		= arrDados[0]; //cod_contabil
					document.formeditor_000.dbvar_str_endcobr_rotulo_000.value 		= arrDados[1]; //razao_social
					document.formeditor_000.dbvar_num_endcobr_cep_000.value 		= arrDados[2]; //cep
					document.formeditor_000.dbvar_str_endcobr_email_000.value 		= arrDados[3]; //email
					document.formeditor_000.dbvar_str_endcobr_contato_000.value 	= arrDados[4]; //contato
					document.formeditor_000.dbvar_str_endcobr_logradouro_000.value 	= arrDados[5]; //logradouro
					document.formeditor_000.dbvar_str_endcobr_numero_000.value 		= arrDados[6]; //numero
					document.formeditor_000.dbvar_str_endcobr_complemento_000.value = arrDados[7]; //complemento
					document.formeditor_000.dbvar_str_endcobr_bairro_000.value 		= arrDados[8]; //bairro
					document.formeditor_000.dbvar_str_endcobr_cidade_000.value 		= arrDados[9]; //cidade
					document.formeditor_000.dbvar_str_endcobr_estado_000.value 		= arrDados[10]; //estado
					document.formeditor_000.dbvar_str_endcobr_fone1_000.value 		= arrDados[11]; //estado
					document.formeditor_000.dbvar_str_endcobr_fone2_000.value 		= arrDados[12]; //estado
				}
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/STreturndadoscontabil.php?var_sql=" + strSQL + "&var_db="+prDBName,  true); 
	objAjax.send(null);
}

function showCnae(){
	// Esta função verifica se o ÍNDICE ZERO
	// do combo de CNAE SEÇÃO está selecionado
	// aí então faz chamada para exibir os 
	// COMBOS DE CNAE COMPLETO
	if(document.getElementById('dbvar_num_cod_cnae_n3_000').options[0].selected == true){
		document.getElementById('dbvar_num_cod_cnae_n3_000').disabled=false;
		document.getElementById('dbvar_str_cod_cnae_n1ô_000').disabled=true;
		document.getElementById('dbvar_str_cod_cnae_n2ô_000').disabled=true;
		document.getElementById('dbvar_str_cod_cnae_n3ô_000').disabled=true;
		document.getElementById('dbvar_str_cod_cnae_n4_000').disabled=true;
		document.getElementById('dbvar_str_cod_cnae_n5_000').disabled=true;
	} else{
		if(document.getElementById('dbvar_str_cod_cnae_n1ô_000').options[0].selected == true){
			document.getElementById('dbvar_num_cod_cnae_n3_000').disabled=false;
			document.getElementById('dbvar_str_cod_cnae_n1ô_000').disabled=true;
			document.getElementById('dbvar_str_cod_cnae_n2ô_000').disabled=true;
			document.getElementById('dbvar_str_cod_cnae_n3ô_000').disabled=true;
			document.getElementById('dbvar_str_cod_cnae_n4_000').disabled=true;
			document.getElementById('dbvar_str_cod_cnae_n5_000').disabled=true;
		} else{
			document.getElementById('dbvar_num_cod_cnae_n3_000').disabled=true;
			document.getElementById('dbvar_str_cod_cnae_n1ô_000').disabled=false;
			document.getElementById('dbvar_str_cod_cnae_n2ô_000').disabled=false;
			document.getElementById('dbvar_str_cod_cnae_n3ô_000').disabled=false;
			document.getElementById('dbvar_str_cod_cnae_n4_000').disabled=false;
			document.getElementById('dbvar_str_cod_cnae_n5_000').disabled=false;
		}
	}
}

function abreJanelaPageLocal(pr_link, pr_extra){
	var auxStrToChange, rExp, auxNewExtra, auxNewValue;
	if (pr_extra != ""){
		rExp = /:/gi;
		auxNewExtra = pr_extra
		if(pr_extra.search(rExp) != -1){
		    auxStrToChange = pr_extra.split(":");
		    auxStrToChange = auxStrToChange[1];
		    rExp = eval("/:" + auxStrToChange + ":/gi");
		    auxNewValue = eval("document.formeditor." + auxStrToChange + ".value");
		    auxNewExtra = pr_extra.replace(rExp, auxNewValue);
		}
		pr_link = pr_link + auxNewExtra;
	}
	
	AbreJanelaPAGE(pr_link, "800", "600");
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}

function validateEmail(prEMAIL,prMsgPARAM){
	var strEMAIL  = prEMAIL;
	var flagPARAM = prMsgPARAM;
	if((strEMAIL == "")||(strEMAIL == null)){ return(null); }
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(strEMAIL)){
		return(true);
	}else{
		if(flagPARAM){
			alert("Endereço de E-mail inválido! Por favor, digite novamente.");
		}else{
			return(false);
		}
	}
}

///// funções de mascara para telefone, pode ser usada para cpf ,cnpj, cep e no que for necessário também///
// forma de chamar: onkeypress="formatar(this,'##-####-####')" especifica como voce quer que seja dividido
// depois abaixo há outra função que valida, poser adaptado para o que necessário, o que tem embaixo é especificamente para telefone
//by MARCIO PADILHA
function formatar(campo,mascara){
	var cont,saida,texto;
	cont = campo.value.length;
	saida = mascara.substring(0,1);
	texto = mascara.substring(cont);
		
	if(texto.substring(0,1)!= saida) { campo.value += texto.substring(0,1)	}
}

function maskFone(t, mask){
 var i = t.value.length;
 var saida = mask.substring(1,0);
 var texto = mask.substring(i)
 if (texto.substring(0,1) != saida){
 	t.value += texto.substring(0,1);
 	}
 }


function validatelefone(telefone){
	var numero = new String(telefone.value);
	var teste;
	teste = numero.substring(0,2) + numero.substring(3,7) + numero.substring(8,12) ;
	if(isNaN(teste)){
		alert("Digite Apenas Numero no Campo Telefone!!");
		telefone.value = "";
		telefone.focus();
		}		
}

function getVDate(prDate,prLang){
	// ATRAVÉS DO SEPARADOR ENCAMINHADO, SEJA O '/' DE DD/MM/AAAA 
	// OU O '-' DE AAAA-MM-DD, SABE-SE COMO CONVERTER A DATA ENC.
	// PARA O FORMATO DE COMPARAÇÃO ACEITÁVEL, MM/DD/AAAA
	if((prDate == null)||(prDate == "")){ return(null); }
	if((prLang == null)||(prLang == "")){ return(null); }
	var arrDATE;
	//
	// VERIFICA O LANG, PARA EXPLIT CORRETO
	switch(prLang.toLowerCase()){
		case "ptb":
			arrDATE = prDate.split("/");
			strDATE = arrDATE[1]+"/"+arrDATE[0]+"/"+arrDATE[2];
		break;
		
		case "en":
			arrDATE = prDate.split("-");
			strDATE = arrDATE[1]+"/"+arrDATE[2]+"/"+arrDATE[0];
		break;
		
		case "es":
			arrDATE = prDate.split("/");
			strDATE = arrDATE[1]+"/"+arrDATE[0]+"/"+arrDATE[2];
		break;
		
		default:
			arrDATE = prDate.split("/");
			strDATE = arrDATE[1]+"/"+arrDATE[0]+"/"+arrDATE[2];
		break;
	}
	// alert(strDATE);
	return(strDATE);
}


function getNow(prLang,prHours){
	// FORMATOS DISPONÍVEIS:
	// EN:  AAAA-MM-DD
	// PTB: DD/MM/AAAA
	// ES:  DD/MM/AAAA
	var strLang 	= ((prLang  == null) || (prLang  == "")) ? "" : prLang;
	var boolHours	= ((prHours == null) || (prHours == "")) ? "" : prHours;
	//
	// CRIA O BÁSICO DO OBJETO DATE
	var strDATE = "";
	var objDATE	= new Date();
	var strDAY	= objDATE.getDate().toString();
	var strMON	= objDATE.getMonth()+1;
	var strYEA	= objDATE.getFullYear().toString();
	var strHOU  = objDATE.getHours().toString();
	var strMIN  = objDATE.getMinutes().toString();
	var strSEC  = objDATE.getSeconds().toString();
	//
	// VERIFICAÇÃO EM RELAÇÃO ÀS CASAS UTILIZADAS
	// concatena zero caso o valor seja
	// menor que 10, para nao 2009-1-1
	if (strMON < 10){strMON = "0" + strMON;}
	if (strDAY < 10){strDAY = "0" + strDAY;}
	if (strMIN < 10){strMIN = "0" + strMIN;}
	if (strHOU < 10){strHOU = "0" + strHOU;}
	if (strSEC < 10){strSEC = "0" + strSEC;}
	//
	// VERIFICA O TIPO DE LINGUAGEM UTILIZADA
	switch(strLang.toLowerCase()){
		case "ptb":
			strDATE = strDAY+"/"+strMON+"/"+strYEA;
		break;
		
		case "en":
			strDATE = strYEA+"-"+strMON+"-"+strDAY;
		break;
		
		case "es":
			strDATE = strDAY+"/"+strMON+"/"+strYEA;
		break;
		
		default:
			strDATE = strMON+"/"+strDAY+"/"+strYEA;
		break;
	}
	//
	// CONCATENA OS MINUTOS E SEGUNDOS, SE PEDIDO
	if(boolHours == true){
		strDATE += " "+strHOU+":"+strMIN+":"+strSEC;
	}
	return(strDATE);									
}

function getDDiff(strDate1,strDate2){
	// alert(strDate1,strDate2);
	return(((Date.parse(strDate2))-(Date.parse(strDate1)))/(24*60*60*1000)).toFixed(0);
}

function showArea(prIDArea, prIDImage){
	if(document.getElementById(prIDArea).style.display != 'none'){
		document.getElementById(prIDArea).style.display = 'none';
		document.getElementById(prIDImage).src = '../img/icon_tree_plus.gif';
	}else{
		document.getElementById(prIDArea).style.display = 'block';
		document.getElementById(prIDImage).src = '../img/icon_tree_minus.gif';
	}
}

// VERIFICA SE PJ FORNEC JÁ EXISTE, COM BASE NO CNPJ
function ajaxVerificaFORNEC(prIDCnpj){
	var objAjax;
	var strReturnValue;
	var strSQL;
	var objCNPJ = document.getElementById(prIDCnpj);
	// Tratamento BREVE, caso o CNPJ ENVIADO ESTEJA VAZIO
	if(objCNPJ == null || objCNPJ.value == ""){
		return(null);
	}
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cod_pj_fornec, razao_social FROM cad_pj_fornec WHERE cnpj = '"+ objCNPJ.value +"'";
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_empresa').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				//alert(strReturnValue);
				//alert(prSQL);
				// verifica se retornou dados
				if(strReturnValue.indexOf('|') != -1){
					document.getElementById('loader_empresa').innerHTML = "<br/><span style='color:red;'>(O CNPJ <em><b>"+ objCNPJ.value +"</b></em>&nbsp; JÁ ESTÁ CADASTRADO NO SISTEMA)</span>";
					objCNPJ.value = "";
					objCNPJ.focus();
					// alert('Esta empresa já está CADASTRADA!');
				}
				setTimeout("document.getElementById('loader_empresa').innerHTML = ''",3000);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/returndados.php?var_sql="+strSQL,true); 
	objAjax.send(null); 
}

//Funções especificas modulo_FinCheque
// by GS 30/08/2011
function showCheque(prBool, prIDCheque){
	if(prBool){ document.getElementById(prIDCheque).style.display = 'block';  }
	else      { document.getElementById(prIDCheque).style.display = 'none'; }
}
			
function changeCheque(prIDBanco,prIDReplace){
	var objAjax;
	var strSQL;
	var strSRC;
	var objIDReplace = document.getElementById(prIDReplace);
	if(objIDReplace == null || prIDBanco == null || prIDBanco == ""){
		return(null);	
	} else{
		// Cria SQL para buscar IMAGEM
		strSQL = "SELECT modelo_cheque_img FROM fin_banco WHERE num_banco = '" + prIDBanco + "';";
		//Função cria objeto ajax
//		alert(strSQL);
		objAjax = createAjax();
		//Durante sua execução, faz-se a verificação do estado atual do ajax.
		objAjax.onreadystatechange = function() {
			if(objAjax.readyState == 4){
				if(objAjax.status == 200){
					var arrReturn = objAjax.responseText.split("|");
					//alert(arrReturn[0]);
					if(arrReturn[0] != null || arrReturn[0] != ""){
						strSRC = arrReturn[0];
						//objIDReplace.src = "../img/" + strSRC;
						document.getElementById(prIDReplace).innerHTML  = "<img id='img_cheque' src='../img/" + strSRC + "' width='210' />"  ;
					} else{
						return(null);
					}
				} else {
					alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
				}
			}
		}
		objAjax.open("GET", "../_ajax/returndados.php?var_sql="+strSQL, true);
		objAjax.send(null);
	}
}


/*
Esta função abaixo é o meio "força-bruta" para controle 
dos limites de dias e meses e não calcula ano bissextoi

fórmula pra ano bissexto 
  bisexto = (ano % 4 == 0) && ( (ano % 100 != 0) || (ano % 400 == 0) );  
*/  
function checkDate(DATA) 
{
	var expReg = /^(([0-2]\d|[3][0-1])\/([0]\d|[1][0-2])\/[1-2][0-9]\d{2})$/;        
	var msgErro = 'Formato inválido de data.';       
	var vdt = new Date();        
	var vdia = vdt.getDay();      
	var vmes = vdt.getMonth();    
    var vano = vdt.getYear();     

	if ((DATA.value.match(expReg)) && (DATA.value!='')){
		var dia = DATA.value.substring(0,2);            
		var mes = DATA.value.substring(3,5);            
		var ano = DATA.value.substring(6,10);          
		if((mes==04 && dia > 30) || (mes==06 && dia > 30) || (mes==09 && dia > 30) || (mes==11 && dia > 30)) {
			alert("Dia incorreto !!! O mês especificado contém no máximo 30 dias.");                       
			DATA.focus();  
			return false;  
		} else { //1 
			if(ano%4!=0 && mes==2 && dia>28){                     
				alert("Data incorreta!! O mês especificado contém no máximo 28 dias.");   
				DATA.focus();                                    
				return false; 
			} else{ //2  
			if(ano%4==0 && mes==2 && dia>29  && ((ano % 100 != 0) || (ano % 400 == 0))){ 
				alert("Data incorreta!! O mês especificado contém no máximo 29 dias.");    
				DATA.focus();                                                              
				return false;  
			} else{ //3 
				if (ano > vano) { 
					alert("Data incorreta!! Ano informado maior que ano atual.");  
					DATA.focus();                                                  
					return false;                                                  
				}else{ //4                                                     
					//alert ("Data correta!");                                                   
					return true;                                                                
					} //4-else  
				} //3-else  
			}//2-else 
		}//1-else    
		} 
}


function populacadastrodadoscatalogo(prValue){
//	alert(prValue);
	var objAjax;
	var intCodCadastro = prValue;
	
	//Função cria objeto ajax
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_empresa').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
	//Durante sua execução, faz-se a verificação do estado atual do ajax.
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				if (objAjax.responseText != "") {
					var arrValues = objAjax.responseText.split("|");
					if (arrValues.length > 0) {
						//document.forms[0].dbvar_str_codcli.value          	=  arrValues[0];
						document.forms[0].dbvar_str_razao_social_000.value		=  arrValues[1].replace(/^\s|\s$/,"");
						document.forms[0].dbvar_str_cnpj_000.value				=  arrValues[2].replace(/^\s|\s$/,"");
						document.forms[0].dbvar_str_insc_est_000.value			=  arrValues[3];
						document.forms[0].dbvar_str_insc_munic_000.value		=  arrValues[4];
						document.forms[0].dbvar_str_end_cep_000.value			=  arrValues[5];
						document.forms[0].dbvar_str_end_logradouro_000.value	=  arrValues[6];
						document.forms[0].dbvar_str_end_numero_000.value		=  arrValues[7];
						document.forms[0].dbvar_str_end_complemento_000.value	=  arrValues[8];
						document.forms[0].dbvar_str_end_bairro_000.value		=  arrValues[9];
						document.forms[0].dbvar_str_end_cidade_000.value		=  arrValues[10];
						document.forms[0].dbvar_str_end_estado_000.value		=  arrValues[11];
						document.forms[0].dbvar_str_end_pais_000.value			=  arrValues[12];
						document.forms[0].dbvar_str_end_fone1_000.value			=  arrValues[13];																		            
					}
					setTimeout("document.getElementById('loader_empresa').innerHTML = ''",3000);
				}
			} 
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/STretornaCadastroFornecedor.php?var_chavereg="+intCodCadastro, true);
	objAjax.send(null);	
}

function buscadadosproduto(prValue, prCampo){
	var objAjax;
	var intCodProduto = prValue;
	var strDesc;
	var strObs;
	var strValor;
	var strHTML;
	
	//Função cria objeto ajax
	objAjax = createAjax();
	//Durante sua execução, faz-se a verificação do estado atual do ajax.
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				if (objAjax.responseText != "") {
					var arrValues = objAjax.responseText.split("|");
					if (arrValues.length > 0) {
						//arrValues[0] igual a intCodProduto
						strDesc = arrValues[2].replace(/^\s|\s$/,"");
						strObs = arrValues[3].replace(/^\s|\s$/,"");
						strValor = FloatToMoeda(arrValues[4]);
						
						strHTML = "";
						if (strDesc != "") strHTML += "<u>Descrição</u>:&nbsp;"+strDesc+"<br>";
						if (strObs != "") strHTML += "<u>Obs</u>:&nbsp;"+strObs+"<br>";
						if (strValor != "") strHTML += "<u>Valor</u>:&nbsp;"+strValor;
						
						document.getElementById(prCampo).innerHTML = strHTML;
					}
				}
			} 
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/STretornaDadosProduto.php?var_chavereg="+intCodProduto, true);
	objAjax.send(null);	
}

function buscanomeentidade(prIDLoader, prIDForm, prIDCodigo, prIDTipo){
	var objAjax;
	var strReturnValue;
	var strSQL, intCodigo, strNome;
	var objForm   = document.getElementById(prIDForm);
	var objCodigo = document.getElementById(prIDCodigo);
	var objTipo   = document.getElementById(prIDTipo);
	
	// Tratamento BREVE dos parametros
	if((prIDForm == null || prIDForm.value == "") || (objCodigo == null || objCodigo.value == "") || (objTipo == null || objTipo.value == "")){ 
		document.getElementById(prIDLoader).innerHTML = "";
		return(null);
	}
	if((objTipo.value != "cad_pf") && (objTipo.value != "cad_pj") && (objTipo.value != "cad_pj_fornec")){ 
		document.getElementById(prIDLoader).innerHTML = "";
		return(null);
	}
	
	// Seta o SQL, cria o AJAX
	strSQL = "";
	if (objTipo.value == "cad_pf")        strSQL = "SELECT cod_pf, nome                FROM cad_pf        WHERE cod_pf = " + objCodigo.value;
	if (objTipo.value == "cad_pj")        strSQL = "SELECT cod_pj, razao_social        FROM cad_pj        WHERE cod_pj = " + objCodigo.value;
	if (objTipo.value == "cad_pj_fornec") strSQL = "SELECT cod_pj_fornec, razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = " + objCodigo.value;
	
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById(prIDLoader).innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				if (objAjax.responseText != "") {
					var arrValues = objAjax.responseText.split("|");
					if (arrValues.length > 0) {
						intCodigo = arrValues[0];
						strNome = arrValues[1].replace(/^\s|\s$/,"");
						document.getElementById(prIDLoader).innerHTML = strNome;
					}
				}
				else document.getElementById(prIDLoader).innerHTML = "";
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/returndados.php?var_sql="+strSQL,true); 
	objAjax.send(null); 
}

function LimitaTamanho(prCampo, prTam, prAviso) { 
  var tamanho = document.getElementById(prCampo).value.length; 
  var texto = document.getElementById(prCampo).value; 
  if (tamanho > prTam) {
	 if (prAviso != "") {
        alert(prAviso);
     }
     document.getElementById(prCampo).value = texto.substring(0,prTam-1);
  } 
  return true; 
} 
function teste(){alert('aki ST');}
/*-----------------------------------------------------*/
/*Devolve dados de uma entidade - By Lumertz 06/02/2013*/
/*-----------------------------------------------------*/
function populaDadosEntidade(prCod,prEnt){
	if((prCod!='')&&(prEnt!='')){
		var objAjax;
		//Documento da entidade (CNPJ ou CPF)
		var strDoc;
		//Função cria objeto ajax
		objAjax = createAjax();
		//Durante sua execução, faz-se a verificação do estado atual do ajax.
		objAjax.onreadystatechange = function() {
			if(objAjax.readyState == 4) {
				if(objAjax.status == 200) {				
					var arrValues = objAjax.responseText.split("|");
					document.forms[0].dbvar_str_nomeô.value = arrValues[0];								
					if(arrValues[1]!=null){
						document.forms[0].dbvar_str_num_documento.value = arrValues[1].replace(/^\s|\s$/,"");	
					}else{document.forms[0].dbvar_str_num_documento.value='';}
				} else {
					alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
				}
			}
		}
		objAjax.open("GET", "../_ajax/STretornaDadosEntidade.php?var_codcadastro="+prCod+"&var_tipo="+prEnt, true);
		objAjax.send(null);	
	}else{
		document.forms[0].dbvar_str_nomeô.value         = '';
		document.forms[0].dbvar_str_num_documento.value = '';
	}
}

function popula_endereco_nd(prValue, prTipo){ //BY Vini - 20.03.2013
	var objAjax;
	var codigo = prValue;
	var tipoEntidade = prTipo;
	//alert(codigo);
	//Função cria objeto ajax
	objAjax = createAjax();
	//Durante sua execução, faz-se a verificação do estado atual do ajax.
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				
				var arrValues = objAjax.responseText.split(";");				
				document.forms[0].dbvar_str_razao_social.value = arrValues[0];
				document.forms[0].dbvar_str_end_cep.value = arrValues[1];	
				document.forms[0].dbvar_str_end_logradouro.value = arrValues[2];	
				document.forms[0].dbvar_str_end_numero.value = arrValues[3];	
				document.forms[0].dbvar_str_end_complemento.value = arrValues[4];	
				document.forms[0].dbvar_str_end_bairro.value = arrValues[5];					
				document.forms[0].dbvar_str_end_cidade.value = arrValues[6];					
				document.forms[0].dbvar_str_end_estado.value = arrValues[7];					
				document.forms[0].dbvar_str_end_pais.value = arrValues[8];					
				document.forms[0].dbvar_str_cnpj_cpf.value = arrValues[9];
				document.forms[0].dbvar_str_obs.value = arrValues[10];
				
			} else { alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText); }
		}
	}
	objAjax.open("GET", "../_ajax/STretornaenderecond.php?var_codigo="+codigo+"&var_tipo="+tipoEntidade, true);
	objAjax.send(null);	
}


function busca_produto(prValue){ //BY Gabriel- 14.11.2016
	var objAjax;
	var codigo = prValue;
	
	//alert(codigo);
	//Função cria objeto ajax
	objAjax = createAjax();
	//Durante sua execução, faz-se a verificação do estado atual do ajax.
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				
				var arrValues = objAjax.responseText.split(";");				
				document.forms[0].var_vlr_conta.value = arrValues[0];
				
				
			} else { alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText); }
		}
	}
	objAjax.open("GET", "../_ajax/STreturnVlrProduto.php?var_codigo="+codigo, true);
	objAjax.send(null);	
}

function validateFloatKey4CD(objTextBox, e){
    /*Exemplos de uso
		Funções clonadas da validateFloatKeyNew Adaptada GS/CLV em 09/06/2011
	<input type="text" dir="rtl" onkeypress="return validateFloatKeyNew(this, event);" />
	<input type="text" dir="rtl" onkeypress="return validateFloatKeyNew(this, event,'yes');" /> */
	var sep = 0;
    var key = '';
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '0123456789';
    var aux = aux2 = '';
	var SeparadorMilesimo = '.';
	var SeparadorDecimal  = ',';
	var SinalNegativo 	  = '';
	var auxVlrFinal       = ''
    var whichCode;

	if (typeof negativo == "undefined") { negativo = "NO";  }

    //whichCode = (window.Event) ? e.which : e.keyCode;  // Assim não funiona com o DOCTYPE
	if (!e) { var e=window.event; }  //Assim funciona :-)
	if (e.keyCode) { whichCode=e.keyCode; } else if (e.which) { whichCode=e.which; } //Assim funciona :-)

    // 13=enter, 8=backspace, 45=hífen(-) as demais retornam 0(zero)
    // whichCode==0 faz com que seja possivel usar todas as teclas como delete, setas, etc    
    if ((whichCode == 0) || (whichCode == 13) || (whichCode == 8) ) { return true; }
    //Permitir Negativos 
	if ( ( negativo.toLowerCase()=='sim') || (negativo.toLowerCase()=='yes') || (negativo.toLowerCase()=='true') ){
		if (whichCode == 45) { 
		 SinalNegativo = '-';
		 if (objTextBox.value.indexOf(SinalNegativo) == -1) { return true; } else { return false; }
		}
	}

    key = String.fromCharCode(whichCode); // Valor para o código da Chave
    if (strCheck.indexOf(key) == -1) { return false; } // Chave inválida

    len = objTextBox.value.length;
    for(i = 0; i < len; i++) { if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal) ) { break; } }			

    aux = '';
    for(; i < len; i++) { if ( (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) ) { aux += objTextBox.value.charAt(i); } }

    aux += key;
    len = aux.length;
    if (objTextBox.value.indexOf("-") != -1) { SinalNegativo = '-'; }
    if (len == 0) {	auxVlrFinal = ''; }
    if (len == 1) {	auxVlrFinal = '0'+ SeparadorDecimal + '000' + aux }
    if (len == 2) {	auxVlrFinal = '0'+ SeparadorDecimal + '00' + aux }
	if (len == 3) {	auxVlrFinal = '0'+ SeparadorDecimal + '0' + aux }
	if (len == 4) {	auxVlrFinal = '0'+ SeparadorDecimal + aux }
    if (len > 4)  {
        aux2 = '';
        for (j = 0, i = len - 5; i >= 0; i--) {
            if (j == 3) { aux2 += SeparadorMilesimo; j = 0; }
            aux2 += aux.charAt(i);
            j++;
        }
		auxVlrFinal = '';
        len2 = aux2.length;
        for (i=len2 - 1; i >= 0; i--) { auxVlrFinal += aux2.charAt(i); }
        auxVlrFinal += SeparadorDecimal + aux.substr(len - 4, len);
    }
	objTextBox.value = '';

    //Obs.: O certo seria usar essa funão com o DIR do INPUT setado para RTL, mas como nem sempre isso é possível
	//tento fazer aqui a correção da posição do sinal, no caso de números negatidos e do DIR não estar como RTL.
    if (objTextBox.dir.toLowerCase() == 'rtl') { objTextBox.value = auxVlrFinal + SinalNegativo; }
	else 
	  { objTextBox.value = SinalNegativo + auxVlrFinal;
	    //objTextBox.value = objTextBox.value.replace("-.", "-0.");
	    objTextBox.value = objTextBox.value.replace("-00", "-");
	    objTextBox.value = objTextBox.value.replace("-0.0","-");  }
	
    return false;
}


</script>