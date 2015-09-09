<?php
/**
 * Image resizer base class
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */

namespace ImageResizer;


abstract class AbstractImageResizer
{
    /**
     * resize type
     */
    const PROPORTIONAL = 0;
    const FIXED        = 1;
    const FIXED_HEIGHT = 2;
    const FIXED_WIDTH  = 3;

    /**
     * dimension keys
     */
    const RATIO  = 0;
    const WIDTH  = 1;
    const HEIGHT = 2;

    /**
     * option keys
     */
    const OPT_INTERLACE = 0; /* true or false */
    const OPT_QUALITY   = 1; /* from 0 (worst) to 100 (best) */

    /**
     * @var int $type -- cf type constants above
     */
    private $type;

    /**
     * @var array $dimensions -- cf dimension keys above
     */
    protected $dimensions;

    /**
     * @var array $options -- cf option keys above
     */
    protected $options;

    /**
     * @param int    $type
     * @param array  $dimensions
     * @param array  $options
     * @throws ImageResizerException
     */
    public function __construct($type, array $dimensions, array $options = [])
    {
        $this->checkType($type);
        $this->type = $type;

        $this->checkDimensions($dimensions);
        $this->dimensions = $dimensions;
        $this->options    = $options;
    }

    /**
     * resize file $source and save the resized image into $destination
     *
     * @param string $source      file to resize
     * @param string $destination resized file
     * @throws ImageResizerException
     */
    public function resize($source, $destination)
    {
        list($srcWidth, $srcHeight) = $this->retrieveSrcDimensions($source);
        list($dstWidth, $dstHeight) = $this->calculateDstDimensions($srcWidth, $srcHeight);

        $srcId = $this->getImageIdentifier($source);
        $dstId = @imagescale($srcId, $dstWidth, $dstHeight, IMG_BILINEAR_FIXED);
        @imageinterlace($dstId, $this->getOption(self::OPT_INTERLACE));

        $this->save($dstId, $destination);
        @imagedestroy($dstId);
    }

    /**
     * get value of $option or false if not defined
     *
     * @param $option
     * @return bool
     */
    protected function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : false;
    }

    /**
     * check that $resizeType has a correct value
     *
     * @param integer $resizeType
     * @throws ImageResizerException
     * @return bool
     */
    private function checkType($resizeType)
    {
        $supportedTypes = [
            self::PROPORTIONAL,
            self::FIXED,
            self::FIXED_HEIGHT,
            self::FIXED_WIDTH
        ];
        if (!in_array($resizeType, $supportedTypes)) {
            throw new ImageResizerException(ImageResizerException::WRONG_RESIZE_TYPE);
        }
        return true;
    }

    /**
     * check that $dimensions contains correct pieces of information
     *
     * @param array $dimensions
     * @throws ImageResizerException
     * @return bool
     */
    private function checkDimensions(array $dimensions)
    {
        switch ($this->type) {
            case self::PROPORTIONAL:
                if (!isset($dimensions[self::RATIO])) {
                    throw new ImageResizerException(ImageResizerException::MISSING_RATIO);
                }
                if (!is_int($dimensions[self::RATIO])) {
                    throw new ImageResizerException(ImageResizerException::WRONG_VALUE_TYPE);
                }
                if (0 >= $dimensions[self::RATIO] || 100 <= $dimensions[self::RATIO]) {
                    throw new ImageResizerException(ImageResizerException::VALUE_OUT_OF_RANGE);
                }
                break;

            case self::FIXED_WIDTH:
                if (!isset($dimensions[self::WIDTH])) {
                    throw new ImageResizerException(ImageResizerException::MISSING_WIDTH);
                }
                if (!is_int($dimensions[self::WIDTH])) {
                    throw new ImageResizerException(ImageResizerException::WRONG_VALUE_TYPE);
                }
                break;

            case self::FIXED_HEIGHT:
                if (!isset($dimensions[self::HEIGHT])) {
                    throw new ImageResizerException(ImageResizerException::MISSING_HEIGHT);
                }
                if (!is_int($dimensions[self::HEIGHT])) {
                    throw new ImageResizerException(ImageResizerException::WRONG_VALUE_TYPE);
                }
                break;

            case self::FIXED:
                if (!isset($dimensions[self::WIDTH]) || !isset($dimensions[self::HEIGHT])) {
                    throw new ImageResizerException(ImageResizerException::MISSING_WIDTH_OR_HEIGHT);
                }
                if (!is_int($dimensions[self::WIDTH]) || !is_int($dimensions[self::HEIGHT])) {
                    throw new ImageResizerException(ImageResizerException::WRONG_VALUE_TYPE);
                }
                break;
        }
        return true;
    }

    /**
     * Retrieve the dimensions of image $source
     *
     * @param string $source iamge filename
     * @throws ImageResizerException
     * @return array
     */
    private function retrieveSrcDimensions($source)
    {
        if (!is_file($source)) {
            throw new ImageResizerException(ImageResizerException::FILE_DOES_NOT_EXIST);
        }

        $infos = @getimagesize($source);
        return array_slice($infos, 0, 2);
    }

    /**
     * Calculate the dimensions of the resized image
     *
     * @param integer $srcWidth  width of source image
     * @param integer $srcHeight height of source image
     * @return array
     */
    private function calculateDstDimensions($srcWidth, $srcHeight)
    {
        $dstWidth  = 0;
        $dstHeight = 0;

        switch ($this->type) {
            case self::PROPORTIONAL:
                $dstWidth  = round($srcWidth * $this->dimensions[self::RATIO] / 100);
                $dstHeight = round($srcHeight * $this->dimensions[self::RATIO] / 100);
                break;

            case self::FIXED;
                $dstWidth  = $this->dimensions[self::WIDTH];
                $dstHeight = $this->dimensions[self::HEIGHT];
                break;

            case self::FIXED_WIDTH:
                $dstWidth  = $this->dimensions[self::WIDTH];
                $dstHeight = round($dstWidth * $srcHeight / $srcWidth);
                break;

            case self::FIXED_HEIGHT:
                $dstHeight = $this->dimensions[self::HEIGHT];
                $dstWidth  = round($dstHeight * $srcWidth / $srcHeight);
                break;
        }
        return [$dstWidth, $dstHeight];
    }

    /**
     * retrieve an image resource id for $source
     *
     * @param string $source
     * @throws ImageResizerException
     * @return resource image identifier
     */
    abstract protected function getImageIdentifier($source);

    /**
     * save image identified by $dstId into file $destination
     *
     * @param resource $dstId       destination image resource id
     * @param string   $destination name of the destination file
     * @return bool
     */
    abstract protected function save($dstId, $destination);
}