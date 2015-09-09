<?php
/**
 * PhpUnit tests for class PngImageResizer
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */

namespace ImageResizer;

class PngImageResizerTest extends \PHPUnit_Framework_TestCase
{
    const INPUT_IMAGE_PNG        = 'phpstorm.png';
    const INPUT_IMAGE_GIF        = 'phpstorm.gif';
    const OUTPUT_IMAGE_PNG       = 'phpstormOut.png';
    const WRONG_INPUT_IMAGE_PNG  = 'phpstormWrong.png';
    const ABSENT_INPUT_IMAGE_PNG = 'phpstormAbsent.png';

    /**
     * @var string
     */
    private $path;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->path = __DIR__ . '/images/';
    }

    /**
     * test the function resize
     *
     * @dataProvider getDataToResize
     *
     * @param integer $resizeType
     * @param array   $dimensions
     * @param array   $options
     * @param array   $expectedDimensions
     *
     */
    public function testResize($resizeType, $dimensions, $options, $expectedDimensions)
    {
        $input  = $this->path . self::INPUT_IMAGE_PNG;
        $output = $this->path . self::OUTPUT_IMAGE_PNG;

        $resizer = new PngImageResizer(
            $resizeType,
            $dimensions,
            $options
        );
        $resizer->resize($input, $output);

        $this->assertTrue(is_file($output));

        $outputSize = getimagesize($output);
        $this->assertEquals($expectedDimensions[0], $outputSize[0]);
        $this->assertEquals($expectedDimensions[1], $outputSize[1]);

        unlink($output);
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
                PngImageResizer::PROPORTIONAL,
                [PngImageResizer::RATIO => 50],
                [PngImageResizer::OPT_INTERLACE => true, PngImageResizer::OPT_QUALITY => 80],
                [200, 200]
            ],
            [
                PngImageResizer::FIXED,
                [PngImageResizer::WIDTH => 50, PngImageResizer::HEIGHT => 50],
                [PngImageResizer::OPT_INTERLACE => true],
                [50, 50]
            ],
            [
                PngImageResizer::FIXED_WIDTH,
                [PngImageResizer::WIDTH => 100],
                [PngImageResizer::OPT_INTERLACE => true, PngImageResizer::OPT_QUALITY => 60],
                [100, 100]
            ],
            [
                PngImageResizer::FIXED_HEIGHT,
                [PngImageResizer::HEIGHT => 150],
                [PngImageResizer::OPT_INTERLACE => true, PngImageResizer::OPT_QUALITY => false],
                [150, 150]
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
     * @param array   $options
     * @param integer $expectedExceptionCode
     *
     */
    public function testResizeKo($input, $resizeType, $dimensions, $options, $expectedExceptionCode)
    {
        $input  = $this->path . $input;
        $output = $this->path . self::OUTPUT_IMAGE_PNG;

        try {
            $resizer = new PngImageResizer(
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
                self::INPUT_IMAGE_GIF,
                PngImageResizer::PROPORTIONAL,
                [PngImageResizer::RATIO => 50],
                [PngImageResizer::OPT_INTERLACE => true, PngImageResizer::OPT_QUALITY => 80],
                ImageResizerException::WRONG_IMAGE_TYPE
            ],
            [
                self::WRONG_INPUT_IMAGE_PNG,
                PngImageResizer::PROPORTIONAL,
                [PngImageResizer::RATIO => 50],
                [PngImageResizer::OPT_INTERLACE => true, PngImageResizer::OPT_QUALITY => 60],
                ImageResizerException::INVALID_PNG_FILE
            ],
            [
                self::ABSENT_INPUT_IMAGE_PNG,
                PngImageResizer::PROPORTIONAL,
                [PngImageResizer::RATIO => 50],
                [PngImageResizer::OPT_INTERLACE => true],
                ImageResizerException::FILE_DOES_NOT_EXIST
            ]
        ];
    }
}