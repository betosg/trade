<link rel='stylesheet' href='../_css/<?php echo(CFG_SYSTEM_NAME) ?>.css' type='text/css'>
<link rel='stylesheet' type='text/css' href='../_css/tablesort.css'>
<script type='text/javascript' src='../_scripts/tablesort.js'></script>
<script type="text/javascript" language="javascript">
	function swapDisplay(prImg, prObj) {
		if (document.getElementById(prObj).style.display == "none") {
		  prImg.src = '../img/icon_tree_minus.gif';
		  document.getElementById(prObj).style.display = "block";
		} else {
		  prImg.src = '../img/icon_tree_plus.gif';
		  document.getElementById(prObj).style.display = "none";
		}
	}
	
	//Rotinas de exportação e impressão ----------------------------------------
	function imprimir(){
	    var objDiv;
	    //objDiv = document.getElementById("divHeader");  
	    objDiv = document.getElementById("divIcons");   
					   
	    objDiv.style.display = "none";
	    window.print();
	    objDiv.style.display = "block";
	}

	function exportarAdobe(){
	    var objDiv;
	    //objDiv = document.getElementById("divHeader");  
	    objDiv = document.getElementById("divIcons");   
					   
	    objDiv.style.display = "none";
	    window.print();
	    objDiv.style.display = "block";
	}
	
	function exportDocument(prType){
	   /* Esta função faz o export do CONTEÚDO 
		* que está no FRAME da direita, para um
 		* tipo de documento informado como param. 
		* O conteúdo é coletado via javascript
		* e o formulário atual de export é atuali-
		* zado e aberto em pop-up, onde o conteú-
		* do é carregado.
		*/
		var objBODY;
		var objFORM;
		var objCONT;
		var objACAO;
		var objLINK;
		var strACAO;
				
		// PASSAGEM DE PARÂMETROS, INICIALIZACAO
		objACAO = document.getElementById("var_acao");
		objCONT = document.getElementById("var_content");
		objLINK = document.getElementById("var_link");
		objFORM = document.getElementById("formexport");
		strACAO = prType;
		
		//objBODY = window.document.getElementsByTagName("body");
		objBODY = window.document.getElementById("mainPage");
		
		//alert(objBODY.innerHTML); // @DEBUG:
		
		// ATUALIZAÇÃO DE VALUES, ETC
		//objCONT.value = objBODY[0].innerHTML;
		objCONT.value = objBODY.innerHTML;
		objACAO.value = strACAO;
		objLINK.value = "<?php echo($strDIR);?>";
		objFORM.submit();	
	}
</script>
