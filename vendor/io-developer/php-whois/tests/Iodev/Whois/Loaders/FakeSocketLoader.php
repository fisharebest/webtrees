<?php

namespace Iodev\Whois\Loaders;

use Iodev\Whois\Exceptions\ConnectionException;

class FakeSocketLoader extends SocketLoader
{
    public $text = "";
    public $failOnConnect = false;

    public function loadText($whoisHost, $query)
    {
        if ($this->failOnConnect) {
            throw new ConnectionException("Fake connection fault");
        }
        return $this->text;
    }
}
