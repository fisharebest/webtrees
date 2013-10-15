<?php
$lang['L_DUMP_HEADLINE']="Criar backup...";
$lang['L_GZIP_COMPRESSION']="Compressão gzip";
$lang['L_SAVING_TABLE']="Salvando a tabela ";
$lang['L_OF']="of";
$lang['L_ACTUAL_TABLE']="Tabela atual";
$lang['L_PROGRESS_TABLE']="Progresso da tabela";
$lang['L_PROGRESS_OVER_ALL']="Progresso do todo";
$lang['L_ENTRY']="Entrada";
$lang['L_DONE']="Pronto!";
$lang['L_DUMP_SUCCESSFUL']=" foi criado com sucesso.";
$lang['L_UPTO']="até";
$lang['L_EMAIL_WAS_SEND']="Um email foi enviado com sucesso para ";
$lang['L_BACK_TO_CONTROL']="Continuar";
$lang['L_BACK_TO_OVERVIEW']="Visão geral do banco de dados";
$lang['L_DUMP_FILENAME']="Arquivo de backup: ";
$lang['L_WITHPRAEFIX']="com o prefixo";
$lang['L_DUMP_NOTABLES']="Nenhuma tabela foi encontrada no banco de dados `<b>%s</b>` ";
$lang['L_DUMP_ENDERGEBNIS']="O arquivo contém <b>%s</b> tabela(s) com <b>%s</b> registro(s).<br>";
$lang['L_MAILERROR']="O envio do email falhou!";
$lang['L_EMAILBODY_ATTACH']="O anexo contém o backup do seu banco de dados MySQL.<br>Backup do banco de dados `%s`
<br><br>O seguinte arquivo foi criado:<br><br>%s <br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_NOATTACH']="Um backup Multi-parte foi criad.<br>Os arquivos não estão anexados a este email!<br>Backup do banco de dados `%s`
<br><br>Os seguintes arquivos foram criados:<br><br>%s
<br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_MP_ATTACH']="Um backup Multi-parte foi criado.<br>Os arquivos de backup estão anexados em emails separados.<br>Backup do banco de dados `%s`
<br><br>Os seguintes arquivos foram criados:<br><br>%s <br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_FOOTER']="`<br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_TOOBIG']="O arquivo de backup excedeu o tamanho máximo de %s e não foi anexado a este email.<br>Backup do banco de dados `%s`
<br><br>O seguinte arquivo foi criado:<br><br>%s
<br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAILBODY_NOATTACH']="Os arquivos não estão anexados a este email!<br>Backup do banco de dados `%s`
<br><br>O seguinte arquivo foi criado:<br><br>%s
<br><br>Atenciosamente<br><br>MySQLDumper<br>";
$lang['L_EMAIL_ONLY_ATTACHMENT']=" ... somente anexos.";
$lang['L_TABLESELECTION']="Seleção de tabela";
$lang['L_SELECTALL']="Selecionar tudo";
$lang['L_DESELECTALL']="Desselecionar tudo";
$lang['L_STARTDUMP']="Iniciar backup";
$lang['L_LASTBUFROM']="última atualização de";
$lang['L_NOT_SUPPORTED']="Este backup não suporta esta função.";
$lang['L_MULTIDUMP']="Multidump: Backup do(s) <b>%d</b> banco(s) de dados pronto.";
$lang['L_FILESENDFTP']="enviando o arquivo via FTP... favor ter paciente. ";
$lang['L_FTPCONNERROR']="Conexão de FTP não estabelecida! Conexão com ";
$lang['L_FTPCONNERROR1']=" com o usuário ";
$lang['L_FTPCONNERROR2']=" impossível";
$lang['L_FTPCONNERROR3']="Envio por FTP falhou! ";
$lang['L_FTPCONNECTED1']="Conectado com ";
$lang['L_FTPCONNECTED2']=" em ";
$lang['L_FTPCONNECTED3']=" trasnferido com sucesso";
$lang['L_NR_TABLES_SELECTED']="- com %s tabelas selecionadas";
$lang['L_NR_TABLES_OPTIMIZED']="<span class=\"small\">%s tabelas foram otimizadas.</span>";
$lang['L_DUMP_ERRORS']="<p class=\"error\">%s erros ocorreram: <a href=\"log.php?r=3\">verdere</a></p>";
$lang['L_FATAL_ERROR_DUMP']="Fatal error: the CREATE-Statement of table '%s' in database '%s' couldn't be read!";


?>