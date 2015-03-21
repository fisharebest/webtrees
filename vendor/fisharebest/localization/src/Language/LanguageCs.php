<?php namespace Fisharebest\Localization;

/**
 * Class LanguageCs - Representation of the Czech language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCs extends Language {
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
