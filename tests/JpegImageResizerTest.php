<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 07/09/15
 * Time: 13:17
 */

namespace ImageResizer;

class JpegImageResizerTest extends \PHPUnit_Framework_TestCase
{
    const INPUT_IMAGE_JPG        = 'phpstorm.jpg';
    const INPUT_IMAGE_PNG        = 'phpstorm.png';
    const OUTPUT_IMAGE_JPG       = 'phpstormOut.jpg';
    const WRONG_INPUT_IMAGE_JPG  = 'phpstormWrong.jpg';
    const ABSENT_INPUT_IMAGE_JPG = 'phpstormAbsent.jpg';

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
     */
    public function testResize($resizeType, $dimensions, $options, $expectedDimensions)
    {
        $input  = $this->path . self::INPUT_IMAGE_JPG;
        $output = $this->path . self::OUTPUT_IMAGE_JPG;

        $resizer = new JpegImageResizer(
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
                JpegImageResizer::PROPORTIONAL,
                [JpegImageResizer::RATIO => 50],
                [JpegImageResizer::OPT_INTERLACE => true, JpegImageResizer::OPT_QUALITY => 25],
                [237, 153]
            ],
            [
                JpegImageResizer::FIXED,
                [JpegImageResizer::WIDTH => 50, JpegImageResizer::HEIGHT => 50],
                [JpegImageResizer::OPT_INTERLACE => true, JpegImageResizer::OPT_QUALITY => 50],
                [50, 50]
            ],
            [
                JpegImageResizer::FIXED_WIDTH,
                [JpegImageResizer::WIDTH => 360],
                [JpegImageResizer::OPT_INTERLACE => false, JpegImageResizer::OPT_QUALITY => 75],
                [360, 232]
            ],
            [
                JpegImageResizer::FIXED_HEIGHT,
                [JpegImageResizer::HEIGHT => 81],
                [],
                [126, 81]
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
        $output = $this->path . self::OUTPUT_IMAGE_JPG;

        try {
            $resizer = new JpegImageResizer(
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
                JpegImageResizer::PROPORTIONAL,
                [JpegImageResizer::RATIO => 50],
                [JpegImageResizer::OPT_INTERLACE => true],
                ImageResizerException::WRONG_IMAGE_TYPE
            ],
            [
                self::WRONG_INPUT_IMAGE_JPG,
                JpegImageResizer::PROPORTIONAL,
                [JpegImageResizer::RATIO => 50],
                [JpegImageResizer::OPT_INTERLACE => true],
                ImageResizerException::INVALID_JPG_FILE
            ],
            [
                self::ABSENT_INPUT_IMAGE_JPG,
                JpegImageResizer::PROPORTIONAL,
                [JpegImageResizer::RATIO => 50],
                [JpegImageResizer::OPT_INTERLACE => true],
                ImageResizerException::FILE_DOES_NOT_EXIST
            ]
        ];
    }
}