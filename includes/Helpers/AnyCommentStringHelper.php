<?php

namespace AnyComment\Helpers;

/**
 * Class AnyCommentStringHelper is helper to work with string length, etc.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Helpers
 */
class AnyCommentStringHelper
{
    /**
     * Get encoding.
     *
     * @param $encoding
     * @return string
     */
    private static function getEncoding($encoding)
    {
        if (null === $encoding) {
            return 'UTF-8';
        }
        $encoding = strtoupper($encoding);
        if ('8BIT' === $encoding || 'BINARY' === $encoding) {
            return 'CP850';
        }
        if ('UTF8' === $encoding) {
            return 'UTF-8';
        }
        return $encoding;
    }

    /**
     * Symphony polyfil taken from official GitHub repo.
     *
     * @link https://github.com/symfony/polyfill/blob/master/src/Mbstring/Mbstring.php#L477
     * @param string $s String/
     * @param null|string $encoding
     * @return int
     */
    public static function mb_strlen($s, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);

        if (function_exists('mb_strlen')) {
            return mb_strlen($s, $encoding);
        }

        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return \strlen($s);
        }
        return @iconv_strlen($s, $encoding);
    }
}
