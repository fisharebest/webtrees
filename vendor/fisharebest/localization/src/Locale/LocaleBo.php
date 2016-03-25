<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBo;

/**
 * Class LocaleBo - Tibetan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'བོད་སྐད་';
	}

	public function language() {
		return new LanguageBo;
	}
}
