<?php

namespace Iodev\Whois\Loaders;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Helpers\TextHelper;

class CurlLoader implements ILoader
{
    public function __construct($timeout = 60)
    {
        $this->setTimeout($timeout);
        $this->options = [];
    }

    /** @var int */
    private $timeout;

    /** @var array */
    private $options;

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setTimeout($seconds)
    {
        $this->timeout = max(0, (int)$seconds);
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $opts
     * @return $this
     */
    public function setOptions(array $opts)
    {
        $this->options = $opts;
        return $this;
    }

    /**
     * @param array $opts
     * @return $this
     */
    public function replaceOptions(array $opts)
    {
        $this->options = array_replace($this->options, $opts);
        return $this;
    }

    /**
     * @param string $whoisHost
     * @param string $query
     * @return string
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadText($whoisHost, $query)
    {
        if (!gethostbynamel($whoisHost)) {
            throw new ConnectionException("Host is unreachable: $whoisHost");
        }
        $input = fopen('php://temp','r+');
        if (!$input) {
            throw new ConnectionException('Query stream not created');
        }
        fwrite($input, $query);
        rewind($input);

        $curl = curl_init();
        if (!$curl) {
            throw new ConnectionException('Curl not created');
        }
        curl_setopt_array($curl, array_replace($this->options, [
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROTOCOLS => CURLPROTO_TELNET,
            CURLOPT_URL => "telnet://$whoisHost:43",
            CURLOPT_INFILE => $input,
        ]));

        $result = curl_exec($curl);
        $errstr = curl_error($curl);
        $errno = curl_errno($curl);
        curl_close($curl);
        fclose($input);

        if ($result === false) {
            throw new ConnectionException($errstr, $errno);
        }
        return $this->validateResponse(TextHelper::toUtf8($result));
    }

    /**
     * @param string $text
     * @return mixed
     * @throws WhoisException
     */
    private function validateResponse($text)
    {
        if (preg_match('~^WHOIS\s+.*?LIMIT\s+EXCEEDED~ui', $text, $m)) {
            throw new WhoisException($m[0]);
        }
        return $text;
    }
}
