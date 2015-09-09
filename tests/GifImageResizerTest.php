<?php
/**
 * PhpUnit tests for class GifImageResizer
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */

namespace ImageResizer;

class GifImageResizerTest extends \PHPUnit_Framework_TestCase
{
    const INPUT_IMAGE_GIF        = 'phpstorm.gif';
    const INPUT_IMAGE_PNG        = 'phpstorm.png';
    const OUTPUT_IMAGE_GIF       = 'phpstormOut.gif';
    const WRONG_INPUT_IMAGE_GIF  = 'phpstormWrong.gif';
    const ABSENT_INPUT_IMAGE_GIF = 'phpstormAbsent.gif';
    const WRONG_RESIZE_TYPE      = 10;

    /**
     * @var string
     */
    private $path;

    /**
     * set up test environmemt
     */
    public function setUp()
    {
        $this->path = __DIR__ . '/images/';
    }

    /**
     * clear up test environment
     */
    public function tearDown()
    {
        $output = $this->path . self::OUTPUT_IMAGE_GIF;
        if (file_exists($output)) {
            unlink($output);
        }
    }

    /**
     * test the function resize
     *
     * @dataProvider getDataToResize
     *
     * @param $resizeType
     * @param $dimensions
     * @param $expectedDimensions
     *
     */
    public function testResize($resizeType, $dimensions, $options, $expectedDimensions)
    {
        $input  = $this->path . self::INPUT_IMAGE_GIF;
        $output = $this->path . self::OUTPUT_IMAGE_GIF;

        $resizer = new GifImageResizer(
            $resizeType,
            $dimensions,
            $options
        );
        $resizer->resize($input, $output);

        $this->assertTrue(is_file($output));

        $outputSize = getimagesize($output);
        $this->assertEquals($expectedDimensions[0], $outputSize[0]);
        $this->assertEquals($expectedDimensions[1], $outputSize[1]);
    }

    /**
     * test the locate function with degree, second, minute unit
     *
     * @return array
     */
    public function getDataToResize()
    {
        return [
            [
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 50],
                [GifImageResizer::OPT_INTERLACE => true],
                [360, 81]
            ],
            [
                GifImageResizer::FIXED,
                [GifImageResizer::WIDTH => 50, GifImageResizer::HEIGHT => 50],
                [GifImageResizer::OPT_INTERLACE => false],
                [50, 50]
            ],
            [
                GifImageResizer::FIXED_WIDTH,
                [GifImageResizer::WIDTH => 180],
                [GifImageResizer::OPT_INTERLACE => true],
                [180, 41]
            ],
            [
                GifImageResizer::FIXED_HEIGHT,
                [GifImageResizer::HEIGHT => 20],
                [],
                [89, 20]
            ]
        ];
    }

    /**
     * test the function resize
     *
     * @dataProvider getDataToResizeKo
     *
     * @param string  $input
     * @param integer $resizeType
     * @param array   $dimensions
     * @param integer $expectedExceptionCode
     *
     */
    public function testResizeKo($input, $resizeType, $dimensions, $options, $expectedExceptionCode)
    {
        $input  = $this->path . $input;
        $output = $this->path . self::OUTPUT_IMAGE_GIF;

        try {
            $resizer = new GifImageResizer(
                $resizeType,
                $dimensions,
                $options
            );
            $resizer->resize($input, $output);
        } catch (ImageResizerException $ex) {
            $this->assertEquals($expectedExceptionCode, $ex->getCode());
        }
    }

    /**
     * test the locate function with degree, second, minute unit
     *
     * @return array
     */
    public function getDataToResizeKo()
    {
        return [
            [
                self::INPUT_IMAGE_PNG,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 50],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_IMAGE_TYPE
            ],
            [
                self::WRONG_INPUT_IMAGE_GIF,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 50],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::INVALID_GIF_FILE
            ],
            [
                self::ABSENT_INPUT_IMAGE_GIF,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 50],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::FILE_DOES_NOT_EXIST
            ],
            [
                self::INPUT_IMAGE_GIF,
                self::WRONG_RESIZE_TYPE,
                [GifImageResizer::WIDTH => 50, GifImageResizer::HEIGHT => 50],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_RESIZE_TYPE
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::WIDTH => 360],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::MISSING_RATIO
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 'a'],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_VALUE_TYPE
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::PROPORTIONAL,
                [GifImageResizer::RATIO => 120],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::VALUE_OUT_OF_RANGE
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED,
                [GifImageResizer::WIDTH => 100],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::MISSING_WIDTH_OR_HEIGHT
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED,
                [GifImageResizer::WIDTH => 100, GifImageResizer::HEIGHT => 'a'],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_VALUE_TYPE
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED_WIDTH,
                [],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::MISSING_WIDTH
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED_WIDTH,
                [GifImageResizer::WIDTH => 'a'],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_VALUE_TYPE
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED_HEIGHT,
                [],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::MISSING_HEIGHT
            ],
            [
                self::INPUT_IMAGE_GIF,
                GifImageResizer::FIXED_HEIGHT,
                [GifImageResizer::HEIGHT => 'a'],
                [GifImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_VALUE_TYPE
            ]
        ];
    }
}