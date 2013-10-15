#!/usr/bin/perl -w
#
# ERROR / FEHLER !!
# +++++++++++++++++
# If you can read this line Perl is not executed.
# Ask your hoster how to activate Perl.
#
# Wenn Du diese Zeile hier siehst, dann wird Perl nicht ausgefuehrt.
# Frage Deinen Hoster, ob und wie Du Perl aktivieren kannst.
#
# Sample Apache-Config:
# <Directory /usr/local/apache2/htdocs/mysqldumper/msd_cron>
#    Options ExecCGI
#    AddHandler cgi-script cgi pl
# </Directory>
# 
# This file is part of MySQLDumper released under the GNU/GPL 2 license
# http://www.mysqldumper.net 
# @package 			MySQLDumper
# @version 			$Rev: 1351 $
# @author 			$Author: jtietz $
# @lastmodified 	$Date: 2011-01-16 20:55:42 +0100 (So, 16. Jan 2011) $
# @filesource 		$URL: https://mysqldumper.svn.sourceforge.net/svnroot/mysqldumper/branches/msd1.24.3/msd_cron/simpletest.pl $

use strict;
use CGI::Carp qw(warningsToBrowser fatalsToBrowser);  
warningsToBrowser(1);

print "Content-type: text/html\n\n";
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html><head><title>MySQLDumper - simple Perl test</title>\n";
print '<style type="text/css">body { padding-left:18px; font-family:Verdana,Helvetica,Sans-Serif;}</style>';
print "\n</head><body>\n";
print "<p>If you see this perl works fine on your system !<br><br>";
print "Wenn Du das siehst, funktioniert Perl auf Deinem System !</p>";
print "</body></html>\n";