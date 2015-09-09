# ImageResizer

A PHP class that enables you to resize and scale images.

## Installation

Add in your composer.json the following code:

          "require-dev": {
              "guillaumetissier/ImageResizer": $VERSION
          }

## Usage

### Examples 

In order to resize proportionally (50%) a gif image $source and save the result into $destination  

        use ImageResizer\GifImageResizer as Resizer;

        $resizeType = Resizer::PROPORTIONAL;
        $dimensions = [Resizer::RATIO => 50 ]; // 50 %
        $options    = [Resizer::INTERLACE => true ];
        $resizer    = new Resizer($resizeType, $dimensions, $options);
        $resizer->resize($source, $destination);
        
In order to resize a jpeg image $source with a fixed width and save the result into $destination  

        use ImageResizer\JpegImageResizer as Resizer;

        $resizeType = Resizer::FIXED_WIDTH;
        $dimensions = [Resizer::WIDTH => 100 ]; // 100 px
        $options    = [Resizer::INTERLACE => true, Resizer::QUALITY => 75];
        $resizer = new Resizer($resizeType, $dimensions, $options);
        $resizer->resize($source, $destination);
        
In order to resize a png image $source with fixed dimensions and save the result into $destination  

        use ImageResizer\PngImageResizer as Resizer;

        $resizeType = Resizer::FIXED;
        $dimensions = [Resizer::WIDTH => 100, Resizer::HEIGHT => 150]; // 100 px x 150px
        $options    = [Resizer::INTERLACE => true, Resizer::QUALITY => 80];
        $resizer = new Resizer($resizeType, $dimensions, $options);
        $resizer->resize($source, $destination);
