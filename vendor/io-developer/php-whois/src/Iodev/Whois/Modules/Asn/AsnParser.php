<?php

namespace Iodev\Whois\Modules\Asn;

use Iodev\Whois\Helpers\ParserHelper;

class AsnParser
{
    /**
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param string $className
     * @return self
     */
    public static function createByClass($className)
    {
        return new $className();
    }

    /**
     * @param string $content
     * @return array
     */
    public static function parseBlocks($content)
    {
        return array_map([__CLASS__, 'parseBlock'], preg_split('~(\r\n|\r|\n){2,}~ui', $content));
    }

    /**
     * @param string $block
     * @return array
     */
    public static function parseBlock($block)
    {
        $dict = [];
        foreach (ParserHelper::splitLines($block) as $line) {
            $kv = explode(':', $line, 2);
            if (count($kv) == 2) {
                list($k, $v) = $kv;
                $k = trim($k);
                $v = trim($v);
                $dict[$k] = empty($dict[$k]) ? $v : "{$dict[$k]}\n{$v}";
            }
        }
        return $dict;
    }

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param AsnResponse $response
     * @return AsnInfo|null
     */
    public function parseResponse(AsnResponse $response)
    {
        $routes = [];
        foreach (self::parseBlocks($response->getText()) as $block) {
            if (count($block) > 1) {
                $routes[] = new AsnRouteInfo($block);
            }
        }
        if (count($routes) == 0) {
            return null;
        }
        return new AsnInfo($response, $response->getAsn(), $routes);
    }
}
