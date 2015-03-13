<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLo - Representation of the Lao language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lo';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptLaoo;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLa;
	}
}
