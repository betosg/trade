<?php 
//Constantes de CFG
@define("CFG_BUSCA_CEP","web"); //web ou local

@define("CFG_SYSTEM_NAME" ,"tradeunion");
@define("CFG_SYSTEM_TITLE","TRADE UNION");
@define("CFG_SYSTEM_THEME","imgSILVER");
@define("CFG_SYSTEM_LOGO_CAPA","../img/logomarca.gif");
@define("CFG_SYSTEM_DEBUG","false");
@define("CFG_SYSTEM_MENU_TYPE","mx");
@define("CFG_LANG",(!isset($_SESSION[CFG_SYSTEM_NAME . "_lang"]) || getsession(CFG_SYSTEM_NAME . "_lang") == "") ? "ptb" : getsession(CFG_SYSTEM_NAME . "_lang"));
//define("CFG_PREFIX_SESSION" , "ct."); // 2 letras que indicam um prefixo único para todas as variáveis de sessão deste projeto

//Constantes de DB
@define("CFG_DB_HOST",($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") ? "localhost" : "localhost");
@define("CFG_DB_PORT","5432");
@define("CFG_DB_DEFAULT","tradeunion_sindieventos");
@define("CFG_DB_PREFIX","tradeunion_");
@define("CFG_DB",(!isset($_SESSION[CFG_SYSTEM_NAME . "_db_name"]) || getsession(CFG_SYSTEM_NAME . "_db_name") == "") ? CFG_DB_DEFAULT : getsession(CFG_SYSTEM_NAME . "_db_name"));

//Constantes de ACESSO ao DB
@define("CFG_DB_USER_PREFIX","tuse_");
@define("CFG_DB_USER_DEFAULT","tradeunion");
@define("CFG_DB_USER","tradeunion");
@define("CFG_DB_PASS","ATh5#BBsi3%");

//Constantes de Dialog
@define("CFG_DIALOG_WIDTH","725");
@define("CFG_DIALOG_CONTENT_WIDTH","625");
        
//Constantes de layout
@define("CL_BUT_PREFIX","But_XPGreen_");
@define("CL_CORLINHA_1","#FFFFFF");  // Cores de linha de grade - linha 1
@define("CL_CORLINHA_2","#FAFAFA");  // Cores de linha de grade - linha 2

@define("CL_CORBAR_GLASS_1","#FFFFFF");
@define("CL_CORBAR_GLASS_2","#DBDBDB");
@define("CL_CORBAR_SHAPE"  ,"#DBDBDB");

@define("CL_LINK_WIDTH","20"); //original:25

// Constantes de funções
@define("C_NONE",0);
@define("C_UCWORDS",1);
@define("C_TOUPPER",2);
@define("C_TOLOWER",3);

// Constantes de tipos de ícones de mensagem
@define("C_MSG_INFO" ,"mensagem_info.gif");
@define("C_MSG_AVISO","mensagem_aviso.gif");
@define("C_MSG_ERRO" ,"mensagem_erro.gif");

// Constantes de e-mail
@define("CFG_SMTP_SERVER","smtp.gmail.com");
@define("CFG_SMTP_PORT",587); 
@define("CFG_EMAIL_AUDITORIA","auditormaster@gmail.com");
@define("CFG_EMAIL_SENDER","tradeunion@proevento.com.br");
@define("CFG_EMAIL_MASK","");
@define("CFG_EMAIL_PASS","ath503bbsi319");

?>