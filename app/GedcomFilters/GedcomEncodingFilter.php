<?php

/**
 * @copyright 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPLv3+
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\GedcomFilters;

use Fisharebest\Webtrees\Encodings\EncodingInterface;
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF16LE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Registry;
use php_user_filter;

use function str_starts_with;
use function stream_bucket_append;
use function stream_bucket_make_writeable;
use function stream_bucket_new;
use function strlen;
use function substr;

use const PSFS_FEED_ME;
use const PSFS_PASS_ON;

/**
 * Filter a GEDCOM data stream, converting to UTF-8.
 *
 * These properties are created after the class is instantiated.
 *
 * @property string               $filtername
 * @property array<string,string> $params
 * @property resource             $stream
 */
class GedcomEncodingFilter extends php_user_filter
{
    private string $data = '';

    private bool $bom_checked = false;

    private EncodingInterface|null $src_encoding = null;

    private EncodingInterface|null $dst_encoding = null;

    /**
     * Initialization
     */
    public function onCreate(): bool
    {
        parent::onCreate();

        $src_encoding = $this->params['src_encoding'] ?? '';
        $dst_encoding = $this->params['dst_encoding'] ?? 'UTF-8';

        if ($src_encoding !== '') {
            $this->src_encoding = Registry::encodingFactory()->make($src_encoding);
        }

        $this->dst_encoding = Registry::encodingFactory()->make($dst_encoding);

        return true;
    }

    /**
     * Filter some data.
     *
     * @param resource $in       Read from this input stream
     * @param resource $out      Write to this output stream
     * @param int      $consumed Count of bytes processed (initially null)
     * @param bool     $closing  Is the input about to end?
     */
    public function filter($in, $out, &$consumed, bool $closing): int
    {
        $return = PSFS_FEED_ME;

        while (is_object($bucket_in = stream_bucket_make_writeable($in))) {
            // https://www.php.net/manual/en/php-user-filter.filter.php says you should
            // increment $consumed by the number of processed bytes.
            // However, PHP ignores this parameter. https://3v4l.org/p9d8S
            // Also, PHP doesn't care if it overflows to a float - although phpstan does.
            // @phpstan-ignore parameterByRef.type
            $consumed += $bucket_in->datalen;

            $this->data .= $bucket_in->data;

            // Buffer data until we have enough to check for a byte-order-mark.
            // Byte-order-marks override any configured or detected encoding.
            if (!$this->bom_checked) {
                if (strlen($this->data) >= 3) {
                    $this->bom_checked = true;
                    $this->detectByteOrderMark();
                } else {
                    continue;
                }
            }

            $this->src_encoding ??= Registry::encodingFactory()->detect($this->data);

            if ($this->src_encoding instanceof EncodingInterface) {
                $bytes      = $this->src_encoding->convertibleBytes($this->data);
                $data_in    = substr($this->data, 0, $bytes);
                $data_out   = $this->dst_encoding->fromUtf8($this->src_encoding->toUtf8($data_in));
                $bucket_out = stream_bucket_new($this->stream, $data_out);
                $this->data = substr($this->data, $bytes);
                $return     = PSFS_PASS_ON;
                stream_bucket_append($out, $bucket_out);
            }
        }

        // Process the final record.
        if ($closing) {
            if (!$this->bom_checked) {
                $this->detectByteOrderMark();
            }

            $this->src_encoding ??= Registry::encodingFactory()->make('UTF-8');
            $data_out           = $this->dst_encoding->fromUtf8($this->src_encoding->toUtf8($this->data));
            $bucket_out         = stream_bucket_new($this->stream, $data_out);
            $return             = PSFS_PASS_ON;
            stream_bucket_append($out, $bucket_out);
        }

        return $return;
    }

    /**
     * Detect a byte-order-mark and use it to set the source encoding.
     * The BOM is stripped from the data so it does not appear in the output.
     */
    private function detectByteOrderMark(): void
    {
        $bom_encodings = [
            UTF8::BYTE_ORDER_MARK    => UTF8::NAME,
            UTF16BE::BYTE_ORDER_MARK => UTF16BE::NAME,
            UTF16LE::BYTE_ORDER_MARK => UTF16LE::NAME,
        ];

        foreach ($bom_encodings as $bom => $encoding) {
            if (str_starts_with($this->data, $bom)) {
                $this->src_encoding = Registry::encodingFactory()->make($encoding);
                $this->data         = substr($this->data, strlen($bom));

                return;
            }
        }
    }
}
