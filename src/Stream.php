<?php

namespace Usa\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * @var $stream
     */
    protected $stream;

    protected $resource;


    /**
     * Stream constructor.
     * @param $stream
     * @param $mode
     */
    public function __construct($stream, $mode)
    {
        $this->stream = $stream;

        if (is_resource($stream)) {
            $this->resource = $stream;
        } elseif (is_string($stream)) {
            $this->resource = fopen($stream, $mode);
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {

        if (!$this->isReadable()) {
            return '';
        }

        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return;
        }

    }


    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (!$this->resource) {
            return;
        }

        $resource = $this->detach();

        fclose($resource);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (!$this->resource) {
            return null;
        }

        $meta = fstat($this->resource);

        return $meta['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->resource) {
            // throw exception
        }

        $result = ftell($this->resource);

        if (!is_int($result)) {
            // throw exception
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if (!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return $meta['seekable'];
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->resource) {
            // throw exception
        }

        if (!$this->isSeekable()) {
            // throw exception
        }

        $result = fseek($this->resource, $offset, $whence);

        if ($result !== 0) {
            // throw exception
        }

    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        $mode = $meta['mode'];

        return is_writable($meta['uri']);

    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {

        if (!$this->resource) {
            // throw
        }

        if (!$this->isWritable()) {
            // throw
        }

        $result = fwrite($this->resource, $string);

        if ($result === false) {
            // throw
        }

        return $result;

    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        $mode = $meta['mode'];

        return (strstr($mode, 'r') || strstr($mode, '+'));
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        // TODO: Implement read() method.
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            return '';
        }

        $result = stream_get_contents($this->resource);


        if (false === $result) {
            // throw exception
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return stream_get_meta_data($this->resource);
        }

        $metaData = stream_get_meta_data($this->resource);

        if (!array_key_exists($key, $metaData)) {
            return null;
        }

        return $metaData[$key];
    }

}