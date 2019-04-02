<?php
declare(strict_types=1);
namespace Narrowspark\HttpEmitter;

use Psr\Http\Message\ResponseInterface;

final class Util
{
    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Inject the Content-Length header if is not already present.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function injectContentLength(ResponseInterface $response): ResponseInterface
    {
        // PSR-7 indicates int OR null for the stream size; for null values,
        // we will not auto-inject the Content-Length.
        if (! $response->hasHeader('Content-Length') &&
            $response->getBody()->getSize() !== null
        ) {
            $response = $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
        }

        return $response;
    }

    /**
     * Cleans or flushes output buffers up to target level.
     *
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     *
     * @param int  $maxBufferLevel The target output buffering level
     * @param bool $flush          Whether to flush or clean the buffers
     *
     * @return void
     */
    public static function closeOutputBuffers(int $maxBufferLevel, bool $flush): void
    {
        $status = \ob_get_status(true);
        $level  = \count($status);
        $flags  = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $maxBufferLevel && (bool) ($s = $status[$level]) && ($s['del'] ?? ! isset($s['flags']) || $flags === ($s['flags'] & $flags))) {
            if ($flush) {
                \ob_end_flush();
            } else {
                \ob_end_clean();
            }
        }
    }
}
