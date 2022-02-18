<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS");
	
	// abertura de conexão com o BD
	$objConn = abreDBConn(CFG_DB);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<title><?php echo(CFG_SYSTEM_NAME." - ".getTText("agenda",C_NONE));?></title>
<link rel="stylesheet" href="../_class/dhtmlXScheduler_v43/codebase/dhtmlxscheduler_classic.css" type="text/css" charset="utf-8">

<script src="../_class/dhtmlXScheduler_v43/codebase/dhtmlxscheduler.js" type="text/javascript" charset="utf-8"></script>
<script src="../_class/dhtmlXScheduler_v43/codebase/sources/locale/locale_pt.js" type="text/javascript" charset="utf-8"></script>
<!--script src="../_class/dhtmlXScheduler_v43/codebase/sources/locale/locale_recurring_pt.js" type="text/javascript" charset="utf-8"></script//-->

<style type="text/css" media="screen">
    /* Suas adaptações CSS aqui */
    html, body{
        margin:0px;
        padding:0px;
        height:100%;
        overflow:hidden;
    }
    .context_menu{
        display:none;
        height:100px;
        width:90px;
        position:absolute;
        background-color:#FFFFFF;
        border:1px solid #C9C9C9;
    }
    a:link	 { text-decoration:none;color:#000000; }
    a:hover	 { text-decoration:none; }
    a:active { text-decoration:none;color:#000000; }
    a:visited{ text-decoration:none;color:#000000; }
    tr.foo:hover{background-color:#C9C9C9;}
    
    .dhx_delete_btn{
        /* Removemos botão de Deleção da Lightbox */
        background-image:url('./imgs/controls.gif');
        background-position:-42px 0px;
        width:0px;
    }
    .dhx_cal_light{
        /* Reescrevemos classe Lightbox para ajuste de tamanho */
        height:400px;
        light:200;		
        background-color:#FFE763;
        font-family:Tahoma;
        font-size:8pt;
        border:1px solid #B7A64B;
        color:#887A2E;		
        position:absolute;
        z-index:10001;
        width:408px;
        height:300px;
    }
</style>
<script type="text/javascript" charset="utf-8" language="javascript">
	
    document.oncontextmenu = new Function('return false');
    function showBox(prEvent){
        // Esta função monta uma caixa simulando
        // um menu de contexto, com opções a serem
        // alteradas no div do html na seção html
        // da pagina
        // debug //alert(prEvent.button);//alert('adafs');//alert(prEvent.clientY);
        var posX, posY;
		// Adicionei o prEvent.button == 0 para capturar o clique nas versões do IE anteriores a 10. By Lumertz 21.05.2013
        if((prEvent.button == 2)||(prEvent.button == 0)){
            posX = prEvent.clientX;
            posY = (prEvent.clientY > 300) ? prEvent.clientY - 100 : prEvent.clientY;
            //alert(posX);
            //alert(posY);
            document.getElementById('menu_contexto').style.marginTop  = posY+"px";
            document.getElementById('menu_contexto').style.marginLeft = posX+"px";
            document.getElementById('menu_contexto').style.display    = "block";
            //alert('caixa!');
            oncontextmenu = 'return false';
            return false;
        }
        else{
            document.getElementById('menu_contexto').style.display="none";
            return false;
        }
    }
    
    function hideBox(event){
        // Esta função esconde a caixa de menu
        // de contexto criada pela função acima,
        // caso o botão pressionado seja 2
        
        // debug
        // alert(event.button);
        if(event.button != 2){
            document.getElementById('menu_contexto').style.display="none";
            return false;
        }
    }
    //document.onmousedown = showBox(event);

    function insupddelScheduler(prOper, prIDEvent){
        // Variáveis utilizadas no OBJ SCHEDULER
        var strMatch;
        var strOper = prOper;      // INS, UPD, DEL
        var event_id = prIDEvent;  // id_evento recebido do scheduler
        var strReturnValue = '';   // retorno do ajax - novo event_id
        var strSQL 		   = '';
        var strTitulo      = (event_id != "") ? returnChar(scheduler.getEvent(event_id).text)			: "";
        var dtEventoInicio = (event_id != "") ? convertUTCDate(scheduler.getEvent(event_id).start_date) : "";
        var dtEventoFim    = (event_id != "") ? convertUTCDate(scheduler.getEvent(event_id).end_date)  	: "";
        var strDetails	   = (event_id != "") ? scheduler.getEvent(event_id).details				    : ""; 
        var strCategoria   = (event_id != "") ? scheduler.getEvent(event_id).ag_categoria				: ""; 
        var strPrioridade  = (event_id != "") ? scheduler.getEvent(event_id).ag_prioridade				: "";
        
        // forçando prioridade para NORMAL, se
        // for necessario utilizar o combo de
        // prioridades, remova a linha abaixo
        // e descomente a configuração do com-
        // bo de prioridades [~ na linha 188]
        strPrioridade = 'NORMAL';
        
        // trancamos o lenght do titulo para
        // 250 para nao dar a msg de erro do
        // ajax de campo com maior valor do
        // que permitido
        strTitulo = strTitulo.substr(0,249);
        
        // categoria default
        strCategoria = ((strCategoria == '')) ? 'OUTROS' : strCategoria;
        
        // update novo:
        // faz replace de aspa simples ['] por 
        // aspa dupla [''] para poder ser gra-
        // vado no banco de dados
        // update novo:
        // remove caracteres especiais com '&'
        // para que seja feito a inserção no DB via ajax
        strTitulo 	  = returnChar(strTitulo);
        strDetails 	  = returnChar(strDetails);
        strCategoria  = returnChar(strCategoria);
        strPrioridade = returnChar(strPrioridade);
                
        // monta SQL de INSERÇÃO - fixo mesmo //			
        if(strOper == 'INS'){
            strSQL = "INSERT INTO ag_agenda (prev_dtt_ini, prev_dtt_fim, titulo, descricao, id_responsavel";
            strSQL = strSQL + ", categoria, prioridade, sys_dtt_ins, sys_usr_ins) ";
            strSQL = strSQL + "VALUES('"+dtEventoInicio+"','"+dtEventoFim+"'";
            strSQL = strSQL + ", '"+strTitulo+"','"+strDetails+"','<?php echo getsession(CFG_SYSTEM_NAME."_id_usuario");?>'";
            strSQL = strSQL + ", '" + strCategoria +"','"+ strPrioridade +"', CURRENT_TIMESTAMP,";
            strSQL = strSQL + "'<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>')";
        }
        // monta SQL de UPDATE   - fixo mesmo // 
        if(strOper == 'UPD'){ 
            strSQL = "UPDATE ag_agenda SET prev_dtt_ini = '"+dtEventoInicio+"'";
            strSQL = strSQL + ", prev_dtt_fim = '"+dtEventoFim+"', descricao = '"+strDetails.replace("&","<PARAM_EC>")+"',";
            strSQL = strSQL + " titulo = '"+strTitulo.replace("&","<PARAM_EC>")+"', sys_usr_upd = '<?php echo getsession(CFG_SYSTEM_NAME."_id_usuario");?>'";
            strSQL = strSQL + " , sys_dtt_upd = CURRENT_TIMESTAMP, categoria = '"+strCategoria+"'";
            strSQL = strSQL + " , prioridade = '"+strPrioridade+"' WHERE cod_agenda = "+event_id;
        }
        // monta SQL de DELEÇÃO  - fixo mesmo //
        if(strOper == 'DEL'){
            strSQL = "DELETE FROM ag_agenda WHERE cod_agenda = "+ event_id;
        }
        
        // cria OBJ ajax
        objAjax = createAjax();
        
        objAjax.onreadystatechange = function(){
            if(objAjax.readyState == 4){
                if(objAjax.status == 200){
                    strReturnValue = objAjax.responseText;
                    // caso a operação seja inserção, agora
                    // com base no evento altera a ID pelo
                    // codigo recem inserido - coleta do return
                    // value [processa na insupddelScheduler
                    // com base no tipo de operação
                    // scheduler.load("STgetscheduler.php",'');
                    if(strOper == 'INS'){ 
                        // troca ID aleatório atual por sequencial do DB recém inserido
                        scheduler.changeEventId(event_id,strReturnValue);
                        // Old: Chamava janela POP-UP dos citados 
                        // AbreJanelaPAGE('STcitados.php?var_chavereg=' + strReturnValue,'675','620'); 
                    }
                    return true;
                }
                else{
                    alert("Erro no processamento da página:"+objAjax.status+"\n\n"+objAjax.responseText);
                    return false;
                }
            }
        }
        //alert('../_ajax/STinsupddelscheduler.php?var_oper='+strOper+'&var_sql='+strSQL+'&var_event_id='+event_id);
        objAjax.open('GET','../_ajax/STinsupddelscheduler.php?var_oper='+strOper+'&var_sql='+strSQL+'&var_event_id='+event_id,true);
        objAjax.send(null);
    }
	
    function init() {
        // ================================================================================ //
        //       SEÇÃO CAMPOS LIGHTBOX - DEFINE ORDEM E TIPO DE CAMPOS UTILIZADOS LBX       //
        // ================================================================================ //
        scheduler.config.lightbox.sections=[	
            // Campo TITULO do LiGHTBOX
            {name:"titulo", height:20, type:"textarea", map_to:"text", focus:true},
            // Campo DESCRIÇÃO do LiGHTBOX
            {name:"description", height:45, map_to:"details", type:"textarea"},
            // Campo CATEGORIA do LiGHTBOX
            {name:"categoria", height:21, type:"select", map_to:"ag_categoria", options:[
                {key:"", label:""},
                {key:"REUNIAO", label:"REUNIAO"},
                {key:"ENCONTRO",label:"ENCONTRO"},
                {key:"CONFERENCIA", label:"CONFERENCIA"},
                {key:"ALMOCO",  label:"ALMOCO"},
                {key:"JANTAR", label:"JANTAR"},
                {key:"VISITA",  label:"VISITA"},
                {key:"VIAGEM", label:"VIAGEM"},
                {key:"ANIVERSARIO",  label:"ANIVERSARIO"},
                {key:"COMEMORACAO", label:"COMEMORACAO"},
                {key:"FERIADO",  label:"FERIADO"},
                {key:"OUTROS", label:"OUTROS"}
                <?php 
                    // try{
                    // $strSQL = "SELECT cod_categoria, nome FROM ag_categoria WHERE dtt_inativo IS NULL";
                    // $objResult = $objConn->query($strSQL);
                    // }catch(PDOException $e){//}// caso a consulta ache algum cadastro de categoria
                    // $intAuxCounter = 1;// if($objResult->rowCount() > 0){
                    // foreach($objResult as $objRS){// echo(($intAuxCounter = 1) ? "," : "");
                    // echo("{key:\"".getValue($objRS,"cod_categoria")."\", label:\""
                    // .strtoupper(getValue($objRS,"nome"))."\"}");// $intAuxCounter++;//}//}
                ?>
            ]}, 
            // Campo PRIORIDADE do LiGHTBOX
            // {name:"prioridade", width:21, type:"select", map_to:"ag_prioridade", options:[
            //		{key:"", label:""},
            //		{key:"BAIXA", label:"BAIXA"},
            // 		{key:"NORMAL",label:"NORMAL"},
            //		{key:"MEDIA", label:"MEDIA"},
            //		{key:"ALTA",  label:"ALTA"}
            // ]}
            // Campo DURAÇÃO [PREV_DTT_INI, PREV_DTT_FIM] do LiGHTBOX
            {name:"time", height:72, type:"time", map_to:"auto"}
        ];
        
        
        // ================================================================================ //
        //      SEÇÃO CONFIGS GERAIS SCHEDULER - CONFIGURA FUNCIONALIDADES DA SCHEDULER     //
        // ================================================================================ //
        // CONFIGURAÇÃO DE TODO O OBJETO SCHEDULER //
        scheduler.config.agenda_start = new Date(2010,1,1);				// Start da DATA
        scheduler.config.xml_date     = "%Y-%m-%d %H:%i";             	// Formato do Horário
        scheduler.config.icons_select = ["icon_details","icon_edit"];	// Ícones Padrão para exibição na Lightbox
        
        // CONFIGS DE ABAS DE ANO E AGENDA
        // scheduler.locale.section_location = "Título";
        // scheduler.locale.labels.agenda_tab = "Agenda";
        // scheduler.locale.labels.year_tab = "Ano";
        // scheduler.config.year_x = 4;
        // scheduler.config.year_y = 3;
        // scheduler.locale.labels.date = "Date";
        // scheduler.locale.labels.description = "Description";
        // scheduler.templates.agenda_time;
        // scheduler.templates.agenda_text;
                    
        scheduler.config.default_date;			// header da tab DIA e SEMANA;
        scheduler.config.month_date;			// header da tab MES
        scheduler.config.week_date;				// sub header DIAS DA SEMANA da tab MES
        scheduler.config.day_date;				// sub header, label DIA nas tabs DIA e SEMANA;
        scheduler.config.hour_date;				// string que pode substituir HORAS, para DIA e SEMANA [interessante]
        scheduler.config.month_day;				// STRING para o HEADER de cada dia da semana na tab MES
        scheduler.config.api_date;	 			// data usada nos eventos dos metodos da API
        //scheduler.config.xml_date 			// STRING usada para formatar o formato da data XML "%Y-%m-%d %H:%i"
        
        scheduler.config.hour_size_px 	= 35; 	// TAMANHO da altura de cada hora na VIEW de DIA e SEMANA
        scheduler.config.time_step 		= 30;	// TAMANHO mínimo de tempo que cada evento tem [minutos]
        scheduler.config.scroll_hour	= 7;	// hora posição [hora] que o scroll vai sair posicionado
        scheduler.config.limit_time_select = true; 
        scheduler.config.first_hour		= 7;	// primeira hora de START da listagem de eventos para DIA e SEMANA
        scheduler.config.last_hour  	= 20;	// ultima hora de START da listagem de eventos para DIA e SEMANA.
        
        scheduler.config.readonly 		= false;// caso TRUE, seta todos os eventos p n serem editáveis, etc. [upd/del/ins]
        scheduler.config.show_loading 	= true; // exibe barra de loading, ideal para loading dinamico
        scheduler.config.drag_resize 	= false;// permite resize dos eventos por drag n drop
        scheduler.config.drag_move 		= true;	// permite movimentação ou não por arraste
        scheduler.config.drag_create 	= false;// permite ou não a criação de eventos clicando e arrastando
        scheduler.config.dblclick_create= true;	// permite fazer a inserção de evento pelo duplo click
        scheduler.config.edit_on_create = true; // exibe formulário na nova criação de evento
        scheduler.config.details_on_create 		 = true; // exibe form de detalhes no momento da criação
        scheduler.config.details_on_dblclick 	 = true; // utiliza um FORM exten no EVENTO DBL CLICK (on exitng event)
        scheduler.config.start_on_monday 		 = false;// START do CALENDÁRIO na SEGUNDA-FEIRA
        scheduler.config.multi_day 				 = true; // permite criação de evento em somente um dia
        scheduler.config.drag_resize 			 = true; // resize do tempo [horas]
        scheduler.locale.labels.section_location = "Location";
        
        
        // ================================================================================ //
        //       SEÇÃO ATTACH EVENTS - REPROGRAMA OS EVENTOS DISPONÍVEIS NA SCHEDULER       //
        // ================================================================================ //
        scheduler.attachEvent("onBeforeLightbox", function (event_id){
            //alert(event_id);
            var strTitulo      = (event_id != "") ? returnChar(scheduler.getEvent(event_id).text)			: "";
            var dtEventoInicio = (event_id != "") ? convertUTCDate(scheduler.getEvent(event_id).start_date) : "";
            var dtEventoFim    = (event_id != "") ? convertUTCDate(scheduler.getEvent(event_id).end_date)  	: "";
            // alert(convertUTCDate(scheduler.getEvent(event_id).end_date));
            document.location.href="STinsevent.php?var_dt_ini=" + dtEventoInicio + "&var_dt_fim=" + dtEventoFim + "&var_titulo=" + strTitulo;
        });
        
        // INS EVENT
        scheduler.attachEvent("onEventAdded", function(event_id,event_object){
            insupddelScheduler('INS',event_id);
            return true;
        });
        // UPD EVENT
        scheduler.attachEvent("onEventChanged", function(event_id,event_object){
            insupddelScheduler('UPD',event_id);				
            return true;
        });
        // DEL EVENT
        scheduler.attachEvent("onBeforeEventDelete", function(event_id,event_object){
            insupddelScheduler('DEL',event_id);				
            return true;
        });
        // MENU DE CONTEXTO
        scheduler.attachEvent("onContextMenu", function(event_id,event_object){
            if(event_id != null){
                // agora deve ser chamada a funçao
                // que faz a abertura do menu de contexto
                // alert('abc');
				document.getElementById('var_event_temp').value = event_id;
				//testando se o browser é o firefox
				if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){ 
					showBox(event_object);
					return true;
				}else{
					showBox(window.event);
					return true;			
				}               
            } else { return false; }
        });
        // VERIFICA DIREITO UPD
        scheduler.attachEvent("onClick", function(event_id, event_object){
            var auxBool;
            auxBool = verificaDireito('UPD');
            if(auxBool){
                location.href = 'STupdevent.php?var_chavereg=' + event_id + '&var_flag_redir=AGENDA';
                return true;
            }else{
                return false;
            }
        });
        // VERIFICA DIREITO DEL
        scheduler.attachEvent("onBeforeEventDelete", function(event_id,event_object){
            var auxBool;
            auxBool = verificaDireito('DEL');
            if(auxBool){
                return true;
            }else{
                return false;
            }
        });
        
        // Inicializa o objeto scheduler na DIV HTML 'scheduler_here'
        scheduler.init('scheduler_here',null ,"month");
        scheduler.load("STgetscheduler.php"); // Agora funciona no Chrome e FF mas não no IE // monta XML conforme o SQL do filtro [FF doesn't WORK]
    }
    
    function callDelEvent(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a pop up
        // de deleção de evento.
        var event_temp = document.getElementById('var_event_temp').value;
        location.href  = "STdeleteevent.php?var_chavereg=" + event_temp + "&var_flag_redir=AGENDA";
    }
    function callEditEvent(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a pop up
        // de deleção de evento.
        var event_temp = document.getElementById('var_event_temp').value;
        location.href  = "STupdevent.php?var_chavereg=" + event_temp + "&var_flag_redir=AGENDA";
    }
    function callViewEvent(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a pop up
        // de exibição de evento.
        var event_temp = document.getElementById('var_event_temp').value;
        location.href  = "STviewevent.php?var_chavereg=" + event_temp + "&var_redirect=STdatascheduler.php";
    }
    function callCitados(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a abertura 
        // de pop up ja com o cod_evento correto
        var event_temp = document.getElementById('var_event_temp').value;
        AbreJanelaPAGE("STcitados.php?var_chavereg=" + event_temp,'675','620');
    }
    function callInsResp(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a abertura 
        // de pop up ja com o cod_evento correto
        var event_temp = document.getElementById('var_event_temp').value;
        AbreJanelaPAGE("STrespostas.php?var_chavereg=" + event_temp,'600','610');
    }
    function callExecAction(){
        // esta função coleta o evento_id passado 
        // na abertura do menu de contexto, e o 
        // envia para a var local, chama a pop up
        // de deleção de evento.
        var event_temp = document.getElementById('var_event_temp').value;
        location.href  = "STexecutaracao.php?var_chavereg=" + event_temp + "&var_redirect=../modulo_Agenda/STdatascheduler.php";
    }
    

    function carregaDireitos(){
        // Esta fuuncao carrega os direitos que 
        // o usuario possui para este modulo de 
        // maneira genérica, nao importando quais
        // direitos a aplicação possua.
        objAjax = createAjax();
        objAjax.onreadystatechange = function(){
            if(objAjax.readyState == 4){
                if(objAjax.status == 200){
                    // retorna os direitos que o usuario tem para
                    // a aplicacao corrente. ex: INS,UPD,DEL,VIE
                    strReturnValue = objAjax.responseText;
                    document.getElementById('var_direitos').value = strReturnValue;
                }
                else{
                    alert("Erro no processamento da página:"+objAjax.status+"\n\n"+objAjax.responseText);
                    return false;
                }
            }
        }
        objAjax.open('GET','STverifydireitosscheduler.php',true);
        objAjax.send(null);
    }
    
    function verificaDireito(prDireito){
        // esta Função recebe um Direito como
        // parametro [em forma de string] e
        // testa com o hidden populado no on-
        // load da pagina.
        var strDireitos = document.getElementById('var_direitos').value;
        var strDireito  = prDireito;
        var strErrMSG   = ''; 
		
        // -1 para quando não é encontrado
        if(strDireitos.indexOf(strDireito + ",") == -1){
            strErrMSG += 'ACESSO NEGADO!\n\n';
            strErrMSG += 'Ação a realizar: ' + strDireito + '\n';
            strErrMSG += 'Permissões do usuário: ' + strDireitos;
            
            alert(strErrMSG);
            
            return false;
        }else{
            return true;
        }
        return false;
    }
</script>
</head>
<body onLoad="init();carregaDireitos();" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg" onmouseup="hideBox(event);">
	<input type="hidden" name="var_event_temp" id="var_event_temp" value="" />
	<input type="hidden" name="var_direitos"   id="var_direitos"   value="" />
	<div id="scheduler_here" class="dhx_cal_container" style='width:99%; height:99%; border:1px solid #57697d;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_today_button"></div>
			<div class="dhx_cal_date"></div>
			<div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
			<div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
			<div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
			<div id="menu_contexto" class="context_menu">
				<table cellspacing="0" cellpadding="1" border="0" width="94%" style="margin:3px 3px 3px 3px;font-size:11px;font-weight:bold;">
					<tr class="foo" style="cursor:pointer;" id="line_1" onClick="callDelEvent();">
						<td width="10%"><img src="../img/icon_trash.gif" alt='<?php echo(getTText("remover",C_UCWORDS));?>' title='<?php echo(getTText("remover",C_UCWORDS));?>' /></td>
						<td width="90%" align="left"style="padding-left:5px;"><?php echo(getTText("remover",C_UCWORDS));?></td>
					</tr>
					<tr class="foo" style="cursor:pointer;" onClick="callEditEvent();">
						<td width="10%"><img src="../img/icon_write.gif" alt='<?php echo(getTText("editar",C_UCWORDS));?>' title='<?php echo(getTText("editar",C_UCWORDS));?>' /></td>
						<td width="90%" align="left"style="padding-left:5px;"><?php echo(getTText("editar",C_UCWORDS));?></td>
					</tr>
					<tr class="foo" style="cursor:pointer;" onClick="callViewEvent();">
						<td width="10%"><img src="../img/icon_zoom.gif" alt='<?php echo(getTText("visualizar",C_UCWORDS));?>' title='<?php echo(getTText("visualizar",C_UCWORDS));?>' /></td>
						<td width="90%" align="left"style="padding-left:5px;"><?php echo(getTText("visualizar",C_UCWORDS));?></td>
					</tr>
					<tr class="foo" style="cursor:pointer;" onClick="callCitados();">
						<td width="10%"><img src="../img/icon_pessoa.gif" alt="<?php echo(getTText("ins_citado",C_UCWORDS));?>" editar="<?php echo(getTText("ins_citado",C_UCWORDS));?>" /></td>
						<td width="90%" align="left"style="padding-left:5px;" class="td_hover"><?php echo(getTText("ins_citado",C_UCWORDS));?></td>
					</tr>
					<tr class="foo" style="cursor:pointer;" onClick="callInsResp();">
						<td width="10%"><img src="../img/icon_respostas.gif" alt="<?php echo(getTText("respostas",C_UCWORDS));?>O" editar="<?php echo(getTText("respostas",C_UCWORDS));?>" /></td>
						<td width="90%" align="left"style="padding-left:5px;" class="td_hover"><?php echo(getTText("respostas",C_UCWORDS));?></td>
					</tr>
					<tr class="foo" style="cursor:pointer;" onClick="callExecAction();">
						<td width="10%"><img src="../img/icon_executar_acao_link.gif" alt="<?php echo(getTText("link_acao",C_UCWORDS));?>" editar="<?php echo(getTText("link_acao",C_UCWORDS));?>" /></td>
						<td width="90%" align="left" style="padding-left:5px;margin-bottom:3px;" class="td_hover"><?php echo(getTText("link_acao",C_UCWORDS));?></td>
					</tr>
				</table>
			</div>
			<!--<div class="dhx_cal_tab" name="year_tab"   style="right:12px;"></div>-->
			<!--<div class="dhx_cal_tab" name="agenda_tab" style="right:12px;"></div>-->
		</div>
		<div class="dhx_cal_header"></div>
		<div class="dhx_cal_data"></div>
	</div>
</body>
</html>