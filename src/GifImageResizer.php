<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 14:49
 */

namespace ImageResizer;


class GifImageResizer extends AbstractImageResizer
{
    /**
     * @param string $filename
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($filename)
    {
        if ('gif' !== ($ext = pathinfo($filename, PATHINFO_EXTENSION))) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }
        if (false === ($id = @imagecreatefromgif($filename))) {
            throw new ImageResizerException(ImageResizerException::INVALID_GIF_FILE);
        }
        return $id;
    }

    /**
     * @param resource $dstId
     * @param string   $output
     *
     * @return bool
     */
    protected function save($dstId, $output)
    {
        return imagegif($dstId, $output);
    }
}