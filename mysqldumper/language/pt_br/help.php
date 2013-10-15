<div id="content">
<h3>Sobre este Projeto </h3>
<SPAN id=BABID_Results>A idéia para este projeto é de Daniel Schlichtholz.</SPAN>
<p>Em 2004 ele criou um fórum chamado <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> e logo , programadores estavam escrevendo novos scripts, que complementavam os scripts de Daniel.<br>
Depois de um período curto de tempo um pequeno script de backup tinha se tornado um projeto robusto e avançado.
<p>Se você tiver alguma sugestão de aperfeiçoamento por favor visite o fórum do MySQLDumper: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.
<p>Nós esperamos que você tenha bons momentos com este projeto!<br>
<br>
<h4>Equipe do  MySQLDumper</h4>
<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>

<h3>Ajuda do MySQLDumper</h3>

<h4>Download</h4>
O script está disponível na página principal do  MySQLDumper.<br>
Recomendamos que você visite a página principal com frequência para obter as últimas informações, atualizações e ajuda.<br>
O endereço é
<a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>Requisitos de sistema obrigatórios </h4>
O script funciona em praticamente qualquer tipo de servidor (Windows, Linux, ...) <br>
e PHP >= Version 4.3.4 com GZip-Library, MySQL (>= 3.23), JavaScript (deve estar habilitado).

<a href="install.php?language=de" target="_top">
<h4>Instalação</h4>
</a>
A Instalação é muito fácil.
Descompacte o arquivo em qualquer pasta, com acesso permitido, no servidor<br>
(p.ex. no diretório raiz [Server rootdir/]MySQLDumper)<br>
altere o config.php para chmod 777 <br>
... e pronto!<br>
você pode executar o  MySQLDumper em seu navegador digitando"http://webserver/MySQLDumper"
para completar o setup, simplesmente siga as instruções.

<br>
<b>Nota:</b><br>
<i>Se o seu  webserver operar com a opção safemode=ON, o MySqlDump pode não conseguir criar os diretórios.<br>
você deverá fazê-lo, criando os diretórios você mesmo.<br>
O MySqlDump vai parar, neste caso, e dizer a você o quê fazer.<br>
Depois de criados os diretórios o  MySqlDump irá funcionar normalmente.</i><br>

<a name="perl"></a>
<h4>Guia para o script  Perl </h4>

Muitos tem um diretório  cgi-bin , no qual scripts  Perl podem ser executados. <br>
Normalmente isto é feito pelo navegador, i.é, http://www.domain.de/cgi-bin/ script disponível. <br>
<br>

Siga os seguintes passos, por favor.  <br>
<br>

1. Carregue no MySQLDumper a página Backup e clique em "Backup Perl"   <br>
2. Copie o path, antes da entrada em  crondump.pl para $absolute_path_of_configdir:    <br>
3. Abra o arquivoe "crondump.pl" em um editor <br>
4. Cole o path copiado como absolute_path_of_configdir (sem espaços em branco) <br>
5. Salve o crondump.pl <br>
6. Copie o crondump.pl, como perltest.pl e simpletest.pl para o diretório cgi-bin (use o modo ASCII do seu prog. de ftp!) <br>
7. Aplique chmod 755 nos scripts.  <br>
7b. Se o final do cgi for pedido, mude o final de todos os 3 arquivos pl - >cgi (renomear)<br>
8.  Carregue a página Configuração no  MySQLDumper<br>
9. Clique em Cronscript <br>
10. Altere a execução do path do para  /cgi-bin/<br>
10b. Se os scripts foram renomeados para *.cgi, altere a extensão do arquivo para cgii <br>
11. Salve a Configuração<br>
<br>

Pronto ! Os scripts estão disponíveis a partir da página "Backup" <br>
<br>

Quando você executar o Perl em qualquer lugar, somente os seguintes passos são necessários:  <br>
<br>

1. Carregue no MySQLDumper a página Backup.  <br>
2. Copie o path, antes da entrada em  crondump.pl para $absolute_path_of_configdir:    <br>
3. Abra o arquivo "crondump.pl" no seu editor<br>
4. Cole o path copiado como absolute_path_of_configdir (sem espaços em branco) <br>
5.  Salve o crondump.pl <br>

6. Aplique chmod 755 nestes scripts.  <br>
6b.Se o final do cgi for pedido, mude o final de todos os 3 arquivos pl - >;cgi (renomear)<br>
(ver 10b+11 acima) <br>
<br>


Usuários do windows devem alterar a primeira linha de todos os scripts Perl, para o path do Perl.<br>
<br>

Exemplo:  <br>

em lugar de:  #!/usr/bin/perl w <br>
agora #!C:\perl\bin\perl.exe w<br>

<h4>Operação</h4>
<ul>

<h6>Menu</h6>
Na caixa de seleção acima você deve escolher o banco de dados.<br>
Todas as ações serão para este banco de dados.

<h6>Inicial</h6>
Aqui você irá obter informação sobre o sistema, os números da versão e detalhes
sobre as configurações do banco de dados.<br>
Se você clicar em um banco de dados na caixa de seleção, irá ver a lista de tabelas
com os registros de gravação, tamanho e a data da última atualização.
<h6>Configuração</h6>
Aqui você pode editar sua Configuração, salvá-la ou carregar as configurações
padrão.
<ul>
	<li><a name="conf1"></a><strong>Bancos de Dados Configurados:</strong> lista dos bancos
	  de dados. O bd ativo está em negrito.</li>
	<li><a name="conf2"></a><strong>Prefixo da Tabela:</strong> você pode escolher um prefixo
	  para cada bd separadamente. O prefixo pode operar como um filtro, o que permite que você escolha tabelas em um backup, que comecem com esse prefixo (p.ex. todas as tabelas que comecem com "phpBB_").
	  Se você não for usar isto deixe este campo em branco.</li>
	<li><a name="conf3"></a><strong>GZip-Compression:</strong> Aqui você pode ativar a compressão. é recomendável trabalhar com compressão por conta do tamanho dos arquivos: espaço em disco sempre é algo precioso, não é?</li>
	<li><a name="conf19"></a><strong>Contagem de Registros para Backup:</strong> Número de registros lidos simultaneamente durante o processo de backup, antes do registro fazer nova chamada. Em servidores lentos você deve reduzir este parâmetro para prevenir timeouts.</li>
	<li><a name="conf20"></a><strong>Contagem de Registros para restaurar:</strong>  Número de registros lidos simultaneamente durante o processo de restauração, antes do registro fazer nova chamada.Em servidores lentos você deve reduzir este parâmetro para prevenir timeouts</li>
	<li><a name="conf4"></a><strong>diretório para arquivos de backup:</strong> escolha seu diretório para os arquivos de backup. Se você quiser criar um, o script irá criar um para você. você pode usar paths relativos ou absolutos.</li>
	<li><a name="conf5"></a><strong>Enviar arquivo de backup como e-mail:</strong> Quando esta opção está ativa, o script irá encaminhar automaticamente o arquivo de backup como anexo do e-mail (cuidado!, você deve usar a opção de comprimir os arquivos porquê os arquivos de backup podem ficar muito grandes para serem encaminhados por eamail!</li>
	<li><a name="conf6"></a><strong>endereço de e-mail:</strong> endereço do e-mail do destinatário </li>
	<li><a name="conf7"></a><strong>Assunto do E-mail:</strong> Assunto do e-mail </li>
	<li><a name="conf13"></a><strong>Transferência por FTP: </strong>Quando esta opção estiver ativa, o script irá, automaticamente, enviar o arquivo de backup via FTP.</li>
	<li><a name="conf14"></a><strong>Servidor de FTP: </strong>endereço do servidor de FTP
	  (p.ex. ftp.mybackups.de)</li>
	<li><a name="conf15"></a><strong>Porta do Servidor de FTP: </strong>a Porta do Servidor de FTP (normalmente 21)</li>
	<li><a name="conf16"></a><strong>Usuário do FTP: </strong>nome de Usuário da conta de FTP</li>
	<li><a name="conf17"></a><strong>Senha do FTP: </strong>senha da conta de FTP</li>
	<li><a name="conf18"></a><strong>FTP - diretório para upload: </strong>diretório/pasta para salvar os arquivos de backup (tem de ter permissão para upload!)</li>

	<li><a name="conf8"></a><strong>Apagar backups automaticamente:</strong> Quando você ativar esta opção os arquivos de backup serão apagados automaticamente segundo as configurações a seguir.</li>
	<li><a name="conf10"></a><strong>Apagar por Número de arquivos:</strong> Um valor > 0
	  apagará todos os que excederem o valor determinado</li>
	<li><a name="conf11"></a><strong>Idioma:</strong> escolha a linguagem para a interface.</li>
</ul>

<h6>Gerenciamento</h6>
Todas as ações estão listadas aqui.<br>
Você poderá ver todos os arquivos do diretório de backup .Para realizar as ações
de "Restore" e "Backup" você deve selecionar um arquivo primeiro.
<UL>
	<li><strong>Restaurar:</strong> você restaura o banco de dados com os registros
	  do arquivo de backup selecionado.</li>
	<li><strong>Apagar:</strong> você pode apagar o arquivo de backup selecionado.</li>
	<li><strong>Iniciar novo Dump:</strong> aqui você inicia um novo backup (dump)
	  com seus parâmetros anteriormente configurados.</li>
</UL>

<h6>Log</h6>
você pode ler os registros de Log e apagá-los.
<h6>Créditos/Ajuda</h6>
Esta página.
</ul>