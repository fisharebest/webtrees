<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNus - Representation of the Nuer language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNus extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nus';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySd;
	}
}
