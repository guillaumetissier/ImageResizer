<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 13:17
 */

namespace ImageResizer;


class ImageResizerException extends \Exception
{
    const FILE_DOES_NOT_EXIST     = 0;
    const WRONG_IMAGE_TYPE        = 1;
    const INVALID_GIF_FILE        = 2;
    const INVALID_JPG_FILE        = 3;
    const INVALID_PNG_FILE        = 4;
    const CANNOT_READ_IMAGE_INFO  = 5;
    const WRONG_RESIZE_TYPE       = 6;
    const WRONG_DIMENSION         = 7;
    const MISSING_RATIO           = 8;
    const MISSING_WIDTH           = 9;
    const MISSING_HEIGHT          = 10;
    const MISSING_WIDTH_OR_HEIGHT = 11;
    const WRONG_VALUE_TYPE        = 12;
    const VALUE_OUT_OF_RANGE      = 13;

    /**
     * construct a image resizer exception
     *
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct("Resizer Error", $code);
    }
}