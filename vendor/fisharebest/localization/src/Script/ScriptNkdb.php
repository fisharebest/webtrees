<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNkdb - Representation of the Naxi Dongba script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptNkdb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Nkdb';
    }

    public function number()
    {
        return '085';
    }
}
