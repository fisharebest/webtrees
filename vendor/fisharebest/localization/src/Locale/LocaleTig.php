<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTig;

/**
 * Class LocaleTig - Tigre
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTig extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ትግራይት';
	}

	public function language() {
		return new LanguageTig;
	}
}
