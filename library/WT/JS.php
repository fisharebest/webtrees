<?php
// Support for JavaScript.  Allow scripts to buffer JS, and send it
// at the end of the document.  Inline JS can be given a priority
// which controls the order in which it is sent.
//
// High priority scripts include those that change the presentation,
// such as jQuery tabs and accorions.
//
// Low priority scripts include things like GoogleAnalytics, which
// should run after all other scripts.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_JS {
	// Inline javascript is wrapped in these tags
	const START="\n<script type=\"text/javascript\">\n//<![CDATA[\n";
	const END  ="\n//]]>\n</script>\n";

	// Inline javascript can be given a priority
	const PRIORITY_HIGH   = 0;
	const PRIORITY_NORMAL = 1;
	const PRIORITY_LOW    = 2;

	private static $inline_scripts=array(
		self::PRIORITY_HIGH  =>array(),
		self::PRIORITY_NORMAL=>array(),
		self::PRIORITY_LOW   =>array(),
	);
	private static $external_scripts=array();

	public static function addExternal($script_name) {
		self::$external_scripts[]=$script_name;
	}

	public static function addInline($script, $priority=self::PRIORITY_NORMAL) {
		if (WT_DEBUG) {
			$backtrace=debug_backtrace();
			$script='/* '.$backtrace[0]['file'].':'.$backtrace[0]['line'].' */'.PHP_EOL.$script;
		}
		self::$inline_scripts[$priority][]=$script;
	}

	public static function render() {
		// Load external libraries first
		$html=PHP_EOL;
		foreach (array_unique(self::$external_scripts) as $script_name) {
			$html.='<script type="text/javascript" src="'.htmlspecialchars($script_name).'?v='.rawurlencode(WT_VERSION_TEXT).'"></script>'.PHP_EOL;
		}
		// Process the scripts, in priority order
		$html.=self::START;
		foreach (self::$inline_scripts as $scripts) {
			foreach ($scripts as $script) {
				$html.=$script.PHP_EOL;
			}
		}
		$html.=self::END;

		return $html;
	}
}
