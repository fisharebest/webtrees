<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule8;
use Fisharebest\Localization\Territory\TerritoryCz;

/**
 * Class LanguageCs - Representation of the Czech language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCs extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'cs';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCz;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule8;
	}
}
