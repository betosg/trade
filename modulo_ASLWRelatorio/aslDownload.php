<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	
	// REQUESTS
	$strFILE  = request("var_file");

	$strFileExt = strtolower(pathinfo($strFILE, PATHINFO_EXTENSION));
	//Se o arquivo html que está sendo baixado é um HTML utilizamos o método basico de force-download - By Vini 18.03.2013
	if(($strFileExt == "html") || ($strFileExt =="htm"))
    {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($strFILE));
		
		header("Content-Description: File Transfer");
		// @readfile($strFILE);
		
		$strStream = file_get_contents($strFILE);

		// Troca dos caminhos relativos de CSS, JAVASCRIT, IMG, etc... para caminhos LÒGICOS absolutos)
		// Acho que mais adiante poderemos melhorar essa lógica
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/_css/".CFG_SYSTEM_NAME.".css"		 , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/_css/".CFG_SYSTEM_NAME.".css" 	 ,$strStream);
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/_css/tablesort.css"				 , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/_css/tablesort.css" 			 ,$strStream);
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/_scripts/tablesort.js"			 	 , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/_scripts/tablesort.js"			 ,$strStream);
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/img/"								 , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/img/"							 ,$strStream);
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/modulo_ASLWRelatorio/execaslw.php"  , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/modulo_ASLWRelatorio/execaslw.php"  ,$strStream);
		$strStream = str_replace("../../_".CFG_SYSTEM_NAME."/modulo_ASLWRelatorio/aslExport.php" , "http://".$_SERVER["SERVER_NAME"]."/".((CFG_SYSTEM_NAME!="kernelps")?CFG_SYSTEM_NAME:"@".CFG_SYSTEM_NAME)."/_".CFG_SYSTEM_NAME."/modulo_ASLWRelatorio/aslExport.php" ,$strStream);

		echo($strStream);
	}
	//Para arquivos em outros formatos realizamos a leitura do arquivo utilizando um buffer
	//para garantir que arquivos grandes possam ser baixados. - By Vini 18.03.2013
	else{
        set_time_limit (0); 		
		if( file_exists($strFILE) ) 
		{ 
			//$sz = filesize($strFILE); 
			//if( $sz > 0 ) 
			//{ 
				if( $file = fopen($strFILE, 'r') ) 
				{ 
					header('Content-Disposition: attachment; filename="'.basename($strFILE).'"'); 
					//header("Content-Length: ".($sz)); 

					// Enquanto não chegar ao fim do arquivo 
					// e enquanto a conexão não for abortada 
					// e enquanto a conexão estiver com status normal 
					while(!feof($file) && !connection_aborted() && connection_status() == CONNECTION_NORMAL) 
					{ 
						$buffer = fread($file, 1024); // Coloca 1mb do arquivo no buffer 
						for($i=0; $i<strlen($buffer); $i++){ 
							print($buffer[$i]); 
							if( $i % 256 == 0 ) // A cada 256 bytes armazenados no buffer 
							{ 
								flush(); // Descarrega buffer para o cliente 

								 // Se a conexão foi abortada 
								 // ou se a conexão não estiver normal 
								if( connection_aborted() || connection_status() != CONNECTION_NORMAL) 
								{ 
									unset($buffer); // Libera buffer da memória 
									break 2; // Sai dos 2 loops 
								} 
							} 
						} 
						unset($buffer); // Libera buffer da memória 
					} 
					fclose($file); // Fecha o arquivo 
				}else{ 
					print("Não foi possível abrir o arquivo."); 
					die();
				} 
			//}else{ 
			//	die("Arquivo vazio!"); 
			//} 
		}else{ 
			print("Arquivo ".$strFILE." não existe!"); 
			die();
		} 				
	}
?>