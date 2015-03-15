<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKsf - Representation of the Bafia language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKsf extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ksf';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
