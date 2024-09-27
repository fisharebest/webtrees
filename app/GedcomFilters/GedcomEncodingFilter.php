<?php

/**
 * @copyright 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPLv3+
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\GedcomFilters;

use Fisharebest\Webtrees\Encodings\EncodingInterface;
use Fisharebest\Webtrees\Registry;
use php_user_filter;

use function stream_bucket_append;
use function stream_bucket_make_writeable;
use function stream_bucket_new;
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
     * @param int      $consumed Count of bytes processed
     * @param bool     $closing  Is the input about to end?
     *
     * @return int PSFS_PASS_ON / PSFS_FEED_ME / PSFS_ERR_FATAL
     */
    public function filter($in, $out, &$consumed, $closing): int
    {
        $return = PSFS_FEED_ME;

        // While input data is available, continue to read it.
        while ($bucket_in = stream_bucket_make_writeable($in)) {
            $this->data .= $bucket_in->data;
            $consumed   += $bucket_in->datalen;

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
        if ($closing && $this->data !== '') {
            $this->src_encoding ??= Registry::encodingFactory()->make('UTF-8');
            $data_out           = $this->dst_encoding->fromUtf8($this->src_encoding->toUtf8($this->data));
            $bucket_out         = stream_bucket_new($this->stream, $data_out);
            $return             = PSFS_PASS_ON;
            stream_bucket_append($out, $bucket_out);
        }

        return $return;
    }
}
