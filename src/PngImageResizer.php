<?php
/**
 * Image resizer for PNG image files
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */
namespace ImageResizer;

class PngImageResizer extends AbstractImageResizer
{
    const DEFAULT_QUALITY = 0; // Best quality

    /**
     * retrieve an image resource id for $source
     *
     * @param string $source
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($source)
    {
        if ('png' !== ($ext = pathinfo($source, PATHINFO_EXTENSION))) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }

        if (false === ($id = @imagecreatefrompng($source))) {
            throw new ImageResizerException(ImageResizerException::INVALID_PNG_FILE);
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
        } else {
            $quality = round((100 - $quality) / 10);
        }
        return @imagepng($dstId, $destination, $quality);
    }
}