<?php

namespace Iodev\Whois\Modules\Tld;

use Iodev\Whois\Config;

trait TldParserDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @param string $type
     * @return TldParser
     */
    public static function create($type = null)
    {
        $type = $type ? $type : self::AUTO;
        $d = [
            self::AUTO => __NAMESPACE__.'\Parsers\AutoParser',
            self::COMMON => __NAMESPACE__.'\Parsers\CommonParser',
            self::COMMON_FLAT => __NAMESPACE__.'\Parsers\CommonParser',
            self::BLOCK => __NAMESPACE__.'\Parsers\BlockParser',
            self::INDENT => __NAMESPACE__.'\Parsers\IndentParser',
            self::INDENT_AUTOFIX => __NAMESPACE__.'\Parsers\IndentParser',
        ];
        return self::createByClass($d[$type], $type);
    }

    /**
     * @deprecated will be removed in v4.2
     * @param string $className
     * @param string $configType
     * @return TldParser
     */
    public static function createByClass($className, $configType = null)
    {
        $configType = empty($configType) ? self::AUTO : $configType;
        /* @var $p TldParser */
        $p = new $className();
        $p->setConfig(self::getConfigByType($configType));
        return $p;
    }

    /**
     * @deprecated will be removed in v4.2
     * @param string $type
     * @return array
     */
    public static function getConfigByType($type)
    {
        if ($type == self::COMMON_FLAT) {
            $type = self::COMMON;
            $extra = ['isFlat' => true];
        }
        if ($type == self::INDENT_AUTOFIX) {
            $type = self::INDENT;
            $extra = ['isAutofix' => true];
        }
        $config = Config::load("module.tld.parser.$type");
        return empty($extra) ? $config : array_merge($config, $extra);
    }
}
