<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBhks - Representation of the Bhaiksuki script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptBhks extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Bhks';
    }

    public function number()
    {
        return '334';
    }

    public function unicodeName()
    {
        return 'Bhaiksuki';
    }
}
