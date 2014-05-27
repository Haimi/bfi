<?php

namespace BFI\Email;

/**
 * Class BodyPart
 * @package BFI\Email
 */
class BodyPart
{
    /**
     * Content type constants
     */
    const CONTENT_TYPE_PLAIN = 'text/plain';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_ATTACHMENT = 'application/octet-stream';

    /**
     * Charset constants
     */
    const CHARSET_ISO = 'ISO-8859-1';
    const CHARSET_UTF8 = 'UTF-8';

    /**
     * Encoding constants
     */
    const ENCODING_BASE = '7bit';
    const ENCODING_UTF8 = '8bit';
    const ENCODING_ATTACHMENT = 'base64';

    /**
     * Body content
     * @var string
     */
    protected $_content = '';

    /**
     * @var string
     */
    protected $_contentType;

    /**
     * @var string
     */
    protected $_charset;

    /**
     * @var string
     */
    protected $_encoding = self::ENCODING_BASE;

    /**
     * @var array
     */
    protected $_additionalHeaders = array();

    /**
     * @var string
     */
    protected $_boundary;

    /**
     * C'tor
     * @param string $content
     * @param string $boundary
     * @param string $contentType
     * @param string $charset
     * @throws Exception
     */
    public function __construct($content, $boundary, $contentType = self::CONTENT_TYPE_PLAIN, $charset = self::ENCODING_UTF8)
    {
        $this->_boundary = $boundary;
        $this->_charset = $charset;
        $this->_contentType = $contentType;
        if ($contentType === self::CONTENT_TYPE_ATTACHMENT) {
            $this->_encoding = self::ENCODING_ATTACHMENT;
            $name = basename($content);
            if (! is_file($content)) {
                throw new Exception('File not found: ' . $name);
            }
            $content = chunk_split(base64_encode(file_get_contents($content)));
            $this->_additionalHeaders['Content-Type'] = $this->_contentType . '; name=' . $name;
            $this->_additionalHeaders['Content-Description'] = $name;
            $this->_additionalHeaders['Content-Disposition'] = 'attachment; filename="' . $name . '"';
        } else {
            $this->_additionalHeaders['Content-Type'] = $this->_contentType . '; charset=' . $this->_charset;
        }
        $this->_content = $content;
    }

    /**
     * Assemble mail part to string
     * @return string
     */
    public function assemble()
    {
        $out = "\r\n\r\n--" . $this->_boundary . "\r\n";
        foreach ($this->_additionalHeaders as $header => $content) {
            $out .= $header . ': ' . $content . "\r\n";
        }
        $out .= 'Content-Transfer-Encoding: ' . $this->_encoding . "\r\n\r\n";
        $out .= $this->_content;
        $out .= "\r\n\r\n";
        return $out;
    }
}