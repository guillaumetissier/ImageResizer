<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 14:46
 */

namespace ImageResizer;


class JpegImageResizer extends AbstractImageResizer
{
    const DEFAULT_QUALITY = 75;

    /**
     * @param  string $filename
     * @throws ImageResizerException
     * @return resource image identifier
     */
    protected function getImageIdentifier($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg'])) {
            throw new ImageResizerException(ImageResizerException::WRONG_IMAGE_TYPE);
        }
        if (false === ($id = @imagecreatefromjpeg($filename))) {
            throw new ImageResizerException(ImageResizerException::INVALID_JPG_FILE);
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
        if (false === ($quality = $this->getOption(self::OPT_QUALITY))) {
            $quality = self::DEFAULT_QUALITY;
        }
        imagejpeg($dstId, $output, $quality);
    }
}