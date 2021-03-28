<?php

namespace Iodev\Whois\Modules\Tld;

use Iodev\Whois\DataObject;

/**
 * @property string $query
 * @property string $text
 * @property string $host
 * @property string $domain
 */
class TldResponse extends DataObject
{
    use TldResponseDeprected;

    /** @var string */
    protected $dataDefault = [
        'query' => '',
        'text' => '',
        'host' => '',
        'domain' => '',
    ];
}
