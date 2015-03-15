<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSv - Representation of the Swedish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSv extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sv';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySe;
	}
}
