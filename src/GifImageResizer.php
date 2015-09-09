<?php
/**
 * Image resizer for GIF image files
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */

namespace ImageResizer;

class GifImageResizer extends AbstractImageResizer
{
    /**
     * retrieve an image resource id for $source
     *
     * @param string $source
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($source)
    {
        if ('gif' !== ($ext = pathinfo($source, PATHINFO_EXTENSION))) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }
        if (false === ($id = @imagecreatefromgif($source))) {
            throw new ImageResizerException(ImageResizerException::INVALID_GIF_FILE);
        }
        return $id;
    }

    /**
     * save image identified by $dstId into file $destination
     *
     * @param resource $dstId       destination image resource id
     * @param string   $destination name of the destination file
     * @return bool
     */
    protected function save($dstId, $destination)
    {
        return imagegif($dstId, $destination);
    }
}