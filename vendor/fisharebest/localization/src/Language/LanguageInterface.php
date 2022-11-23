<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleInterface;
use Fisharebest\Localization\Script\ScriptInterface;
use Fisharebest\Localization\Territory\TerritoryInterface;

/**
 * Interface LanguageInterface - Representation of a language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
interface LanguageInterface
{
    /**
     * The ISO639 code for this language.
     *
     * @return string
     */
    public function code();

    /**
     * The default territory where this language is spoken, which would
     * normally be omitted from an IETF language tag.
     * For example, we would normally omit the JP subtag from ja-JP.
     *
     * @return TerritoryInterface
     */
    public function defaultTerritory();

    /**
     * The default script used to write this language, which would
     * normally be omitted from an IETF language tag.
     * For example, we would normally omit the Latn subtag from en-Latn.
     *
     * @return ScriptInterface
     */
    public function defaultScript();

    /**
     * Which plural rule is used in this language?
     *
     * @return PluralRuleInterface
     */
    public function pluralRule();
}
