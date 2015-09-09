<?php
/**
 * Image resizer for JPEG image files
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */


namespace ImageResizer;


class JpegImageResizer extends AbstractImageResizer
{
    const DEFAULT_QUALITY = 75;

    /**
     * retrieve an image resource id for $source
     *
     * @param string $source
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($source)
    {
        $ext = pathinfo($source, PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg'])) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }
        if (false === ($id = @imagecreatefromjpeg($source))) {
            throw new ImageResizerException(ImageResizerException::INVALID_JPG_FILE);
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
        if (false === ($quality = $this->getOption(self::OPT_QUALITY))) {
            $quality = self::DEFAULT_QUALITY;
        }
        imagejpeg($dstId, $destination, $quality);
    }
}