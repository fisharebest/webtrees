<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule8;
use Fisharebest\Localization\Territory\TerritorySk;

/**
 * Class LanguageSk - Representation of the Slovak language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSk extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'sk';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySk;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule8;
	}
}
