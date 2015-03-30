<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptLatn;
use Fisharebest\Localization\Territory\Territory001;

/**
 * Class AbstractLanguage - Representation of a language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractLanguage {
	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new Territory001;
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptLatn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		throw new \DomainException('No plural rule defined for language ' . __CLASS__);
	}
}
