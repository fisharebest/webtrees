<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSk - Representation of the Slovak language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sk';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySk;
	}
}
