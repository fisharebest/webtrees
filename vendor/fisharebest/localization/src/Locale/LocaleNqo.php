<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNqo;

/**
 * Class LocaleNko - N'Ko
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNqo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ߒߞߏ';
	}

	public function language() {
		return new LanguageNqo;
	}
}
