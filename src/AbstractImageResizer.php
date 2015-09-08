<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 13:17
 */

namespace ImageResizer;


abstract class AbstractImageResizer
{
    const PROPORTIONAL = 0;
    const FIXED        = 1;
    const FIXED_HEIGHT = 2;
    const FIXED_WIDTH  = 3;

    const RATIO  = 0;
    const WIDTH  = 1;
    const HEIGHT = 2;

    const OPT_INTERLACE = 0; /* true or false */
    const OPT_BG_RED    = 1; /* from 0 to 255 */
    const OPT_BG_GREEN  = 2; /* from 0 to 255 */
    const OPT_BG_BLUE   = 3; /* from 0 to 255 */
    const OPT_BG_ALPHA  = 4; /* from 0 to 100 */
    const OPT_QUALITY   = 5; /* from 0 (worst) to 100 (best) */

    /**
     * @var int $type - proportional, fixed, fixed h or w
     */
    private $type;

    /**
     * @var array $dimensions
     */
    protected $dimensions;

    /**
     * @var array $dimensions
     */
    protected $options;


    /**
     * Loads image source and its properties to the instanciated object
     *
     * @param int    $type
     * @param array  $dimensions
     * @param array  $options
     *
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
     * @param string $input
     * @param string $output
     *
     * @throws ImageResizerException
     */
    public function resize($input, $output)
    {
        list($srcWidth, $srcHeight) = $this->retrieveSrcDimensions($input);
        list($dstWidth, $dstHeight) = $this->calculateDstDimensions($srcWidth, $srcHeight);

        $srcId = $this->getImageIdentifier($input);
        $dstId = @imagescale($srcId, $dstWidth, $dstHeight, IMG_BILINEAR_FIXED);
        @imageinterlace($dstId, $this->getOption(self::OPT_INTERLACE));

        $this->save($dstId, $output);
        @imagedestroy($dstId);
    }

    /**
     * @param $option
     *
     * @return bool
     */
    protected function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : false;
    }

    /**
     * @param integer $type
     *
     * @throws ImageResizerException
     *
     * @return bool
     */
    private function checkType($type)
    {
        $supportedTypes = [
            self::PROPORTIONAL,
            self::FIXED,
            self::FIXED_HEIGHT,
            self::FIXED_WIDTH
        ];
        if (!in_array($type, $supportedTypes)) {
            throw new ImageResizerException(ImageResizerException::WRONG_RESIZE_TYPE);
        }
        return true;
    }

    /**
     * @param array $dimensions
     *
     * @throws ImageResizerException
     *
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
     * @param $input
     *
     * @throws ImageResizerException
     *
     * @return array
     */
    private function retrieveSrcDimensions($input)
    {
        if (!is_file($input)) {
            throw new ImageResizerException(ImageResizerException::FILE_DOES_NOT_EXIST);
        }

        $infos = @getimagesize($input);
        return array_slice($infos, 0, 2);
    }

    /**
     * @param integer $srcWidth
     * @param integer $srcHeight
     *
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
     * @param string $input filename
     *
     * @return resource image identifier
     */
    abstract protected function getImageIdentifier($input);

    /**
     * @param $dstId
     * @param $output
     *
     * @return bool
     */
    abstract protected function save($dstId, $output);
}