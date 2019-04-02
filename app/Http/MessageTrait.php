<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http;

use Nyholm\Psr7\UploadedFile;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Convert a Symfony request into a PSR-7 request.
 */
trait MessageTrait
{
    /**
     * @param string $version
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        $request = clone $this;
        if (method_exists($request, 'setProtocolVersion')) {
            $request->setProtocolVersion($version);
        } else {
            $request->server->set('SERVER_PROTOCOL', $version);
        }

        return $request;
    }

    /**
     * @return string[][]
     */
    public function getHeaders(): array
    {
        if (method_exists($this->headers, 'allPreserveCase')) {
            return $this->headers->allPreserveCase();
        }

        return $this->headers->all();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @param string $name
     *
     * @return string[]
     */
    public function getHeader($name): array
    {
        return $this->headers->get($name, null, false);
    }

    /**
     * @param string          $name
     * @param string|string[] $value
     *
     * @return static
     */
    public function withHeader($name, $value)
    {
        $request = clone $this;
        $request->headers->set($name, $value);

        return $request;
    }

    /**
     * @param string          $name
     * @param string|string[] $value
     *
     * @return static
     */
    public function withAddedHeader($name, $value): self
    {
        $request = clone $this;
        $request->headers->set($name, $value, false);

        return $request;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $request = clone $this;
        $request->headers->remove($name);

        return $request;
    }

    /**
     * @return StreamFactoryInterface
     */
    public function getBody(): StreamFactoryInterface
    {
        $stream_factory = app(StreamFactoryInterface::class);

        $stream = $stream_factory->create('php://memory', 'wb+');
        $stream->write($this->getContent());
        $stream->rewind();

        return $stream;
    }

    /**
     * @param StreamInterface $body
     *
     * @return static
     */
    public function withBody(StreamInterface $body)
    {
        $request = clone $this;
        if (method_exists($request, 'setContent')) {
            $request->setContent($body);
        } else {
            $request->content = $body;
        }

        return $request;
    }

    /**
     * @return string|UriInterface
     */
    public function getUri()
    {
        $uri = parent::getUri();

        $uri_factory = app(UriFactoryInterface::class);

        return $uri_factory->createUri($uri);
    }

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        $query = $this->getQueryString();

        return '/' . $this->path() . (!empty($query) ? '?' . $query : '');
    }

    /**
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern === '' ? '/' : $pattern;
    }

    /**
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        return self::create($requestTarget);
    }

    /**
     * @param $method
     *
     * @return static
     */
    public function withMethod($method)
    {
        $request = clone $this;
        $request->setMethod($method);

        return $request;
    }

    /**
     * @param UriInterface $uri
     * @param bool         $preserveHost
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = self::create($uri);
        if ($preserveHost) {
            $new->headers->set('HOST', $this->getHost());
        }

        return $new;
    }

    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->server->all();
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookies->all();
    }

    /**
     * @param array $cookies
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $new          = clone $this;
        $new->cookies = clone $this->cookies;
        $new->cookies->replace($cookies);

        return $new;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->query->all();
    }

    /**
     * @param array $query
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $new        = clone $this;
        $new->query = clone $this->query;
        $new->query->replace($query);

        return $new;
    }

    /**
     * @return UploadedFileInterface[]
     */
    public function getUploadedFiles()
    {
        $files = [];
        foreach ($this->files as $file) {
            $files[] = new UploadedFile(
                $file->getPath(),
                $file->getSize(),
                $file->getError(),
                $file->getClientOriginalName(),
                $file->getClientMimeType()
            );
        }

        return $files;
    }

    /**
     * @param UploadedFileInterface[] $uploadedFiles
     *
     * @return static
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $new        = clone $this;
        $new->files = clone $this->files;
        $files      = [];
        foreach ($uploadedFiles as $file) {
            /** @var UploadedFileInterface $file */
            $filename = tempnam(sys_get_temp_dir(), 'upload');
            $handle   = fopen($filename, 'wb');
            fwrite($handle, $file->getStream()->getContents());
            fclose($handle);
            $files[] = new UploadedFile(
                $filename,
                $file->getClientFilename(),
                $file->getClientMediaType(),
                $file->getSize(),
                $file->getError()
            );
        }
        $new->files->add($files);

        return $new;
    }

    /**
     * @return null|array|object
     */
    public function getParsedBody()
    {
        return $this->request->all();
    }

    /**
     * @param null|array|object $data
     *
     * @return static
     */
    public function withParsedBody($data)
    {
        $new          = clone $this;
        $new->request = clone $this->request;
        $new->request->replace($data);

        return $new;
    }

    /**
     * @return mixed[]
     */
    public function getAttributes()
    {
        return $this->attributes->all();
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $request             = clone $this;
        $request->attributes = clone $this->attributes;
        $request->attributes->set($name, $value);

        return $request;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function withoutAttribute($name)
    {
        $request             = clone $this;
        $request->attributes = clone $this->attributes;
        $request->attributes->remove($name);

        return $request;
    }
}
