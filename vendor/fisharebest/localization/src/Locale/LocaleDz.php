<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDz;

/**
 * Class LocaleDz - Dzongkha
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDz extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'རྫོང་ཁ';
	}

	public function language() {
		return new LanguageDz;
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
