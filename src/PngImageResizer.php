<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 14:51
 */

namespace ImageResizer;

class PngImageResizer extends AbstractImageResizer
{
    const DEFAULT_QUALITY = 0; // Best quality

    /**
     * @param  string $filename
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($filename)
    {
        if ('png' !== ($ext = pathinfo($filename, PATHINFO_EXTENSION))) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }

        if (false === ($id = @imagecreatefrompng($filename))) {
            throw new ImageResizerException(ImageResizerException::INVALID_PNG_FILE);
        }
        return $id;
    }

    /**
     * @param resource $dstId
     * @param string   $output
     * @return bool
     */
    protected function save($dstId, $output)
    {
        if (false === ($quality = $this->getOption(self::OPT_QUALITY))) {
            $quality = self::DEFAULT_QUALITY;
        } else {
            $quality = round((100 - $quality) / 10);
        }
        return @imagepng($dstId, $output, $quality);
    }
}