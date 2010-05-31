<?php
/**
 * Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id$
 */

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'yahrzeit':
	$title=i18n::translate('Yahrzeiten block');
	$text=i18n::translate('This block shows you Yahrzeiten that are coming up in the near future.<br /><br />Yahrzeiten (singular: Yahrzeit) are anniversaries of a person\'s death.  These anniversaries are observed in the Jewish tradition; they are no longer in common use in other traditions.  «Yahrzeit» can also be spelled «Jahrzeit» or «Yartzeit».<br /><br />The Administrator determines how far ahead the block will look.  You can further refine the block\'s display of upcoming Yahrzeiten through configuration options.');
	break;
}
