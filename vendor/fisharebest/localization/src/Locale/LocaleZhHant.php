<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptHant;

/**
 * Class LocaleZhHant - Traditional Chinese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHant extends LocaleZh {
	public function endonym() {
		return '繁體中文';
	}

	protected function minimumGroupingDigits() {
		return 3;
	}

	public function script() {
		return new ScriptHant;
	}
}
