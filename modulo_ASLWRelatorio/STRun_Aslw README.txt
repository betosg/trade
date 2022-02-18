----------------------------------------------------------------------------------------
Sobre Relatórios Estáticos:
----------------------------------------------------------------------------------------
Relatório eSTáticos devem ter essa nomeclatura: STRun_[nome do relatório].php
- Devem ser colocado no relatório diretametne como o executor do mesmo
- Devem ser contruídos a partir da aslRun_Default.php mantendo funcionalidades de exportação,
  controle de sessao (se tem user logado), gravação de log, etc...
  (basta manter os includes especiais estrutura geral da aslRun_Default.php)

A necessidade de criação deles vem do formato de impressão que seja necessário. Lembrando 
que classificamso nossos relatórios, ou melhor dizendo, nossas impressões da segunte forma: 

- LISTAGENS...: grade com listagem dos dados em si - ASL com parâmetros, modificadores
			    cabeçalho e rodapé, consegue atender bem.

- RELATPTIOS..: a saída/impressão peculiaridades, ou agrupadores que não podem ser 
				atendidos da forma listagem. 

- DOUMENTOS...: contratos, certidões, certificados e impressões altamente específica
				onde o layout requer um maior controle visual.
----------------------------------------------------------------------------------------
* Atenção para a padronização do nome dos "runners" estáticos de relatórios 

  STRun_[nome do relatório].php

----------------------------------------------------------------------------------------
