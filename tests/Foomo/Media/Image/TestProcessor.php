<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Media\Image;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author bostjan <bostjan.marusic@bestbytes.de>
 */
class TestProcessor extends \PHPUnit_Framework_TestCase
{

	public function testImageResize()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rgb.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';
		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = 100, $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false,false,false);

		$this->assertTrue($success);
		$this->assertTrue(file_exists($destinationFile), 'destination file doews not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());
		
		unlink($destinationFile);
	}

	public function testImageResizeLimited()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rgb.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';

		\Foomo\Media\Image\Processor::allowResizeAboveSource(false);
		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 1000, $height = 1000, $quality = 100, $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false,false,false);

		$this->assertTrue($success);
		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);


		$this->assertEquals(375, $img->getimagewidth());
		$this->assertEquals(525, $img->getimageheight());

		unlink($destinationFile);
	}

	public function testConvertFormat()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rgb.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.png';

		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = 100, $format = Processor::FORMAT_PNG, $convertColorspaceToRGB = false);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());

		$this->assertEquals(Processor::FORMAT_PNG, $img->getimageformat());
		unlink($destinationFile);
	}

	public function testCMYKtoRGBConversion()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'cmyk.jpg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';

		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = 100, $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = true);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());

		$this->assertTrue(($img->getImageColorspace() == \Imagick::COLORSPACE_RGB), 'color space is not cymk');

		$this->assertEquals(Processor::FORMAT_JPEG, $img->getimageformat());
		unlink($destinationFile);
	}

	public function testSetQuality()
	{
		$qualitySetting = 22;
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rgb.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';

		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = $qualitySetting, $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = true);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());

		$this->assertEquals($qualitySetting, $img->getimagecompressionquality(), 'quality does not match');

		$this->assertEquals(Processor::FORMAT_JPEG, $img->getimageformat());
		unlink($destinationFile);
	}
	
	
	public function testMakeThumb()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rgb.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.git';

		$success = \Foomo\Media\Image\Processor::makeThumb($sourceFile, $destinationFile, $size = 200, $format = Processor::FORMAT_GIF);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(142, $img->getimagewidth());
		$this->assertEquals(200, $img->getimageheight());

		$this->assertEquals(Processor::FORMAT_GIF, $img->getimageformat());
		unlink($destinationFile);
	}
	
	
	public function testSharpen()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'large.jpg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';
		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 644, $height = 1088, $quality = 100, $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false,false,false, array('radius'=>2,'sigma' => 1));

		$this->assertTrue($success);
		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(644, $img->getimagewidth());
		$this->assertEquals(1088, $img->getimageheight());
		
		unlink($destinationFile);
		//var_dump($destinationFile);
	}
	
	
	public function testPNGBorder()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'large.jpg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.png';
		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 1000, $height = 600, $quality = 100, $format = Processor::FORMAT_PNG, $convertColorspaceToRGB = false,true,true, array('radius'=>2,'sigma' => 1));

		$this->assertTrue($success);
		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(1000, $img->getimagewidth());
		$this->assertEquals(600, $img->getimageheight());
		$borderColor = $img->getimagebordercolor();
		$colorArray = $borderColor->getcolor();
		$this->assertEquals(1,$colorArray['a'], 'alpha not 1 on border');
		//unlink($destinationFile);
	}

	public function testResolution()
	{
		foreach(array(72,300) as $dpi) {
			$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'resolution' . DIRECTORY_SEPARATOR . 'ski_300dpi.jpg';
			$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'ski_'.$dpi.'dpi.png';
			list($width, $height) = Utils::getImageSize($sourceFile);
			var_dump($destinationFile);
			$success = \Foomo\Media\Image\Processor::resizeImage(
				$sourceFile,
				$destinationFile,
				$width,
				$height,
				$quality = 100,
				$format = Processor::FORMAT_JPEG,
				$convertColorspaceToRGB = false,
				$keepAspectRatio = false,
				$addBorder = false,
				$imageSharpenParams = array(),
				$dpi
			);

			$this->assertTrue($success);
			$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');


			$im = new \Imagick($destinationFile);
			$resolution = $im->getImageResolution();
			$this->assertEquals($dpi, $resolution['x'], 'x-resolution does not match ' . $dpi . ' dpi');
			$this->assertEquals($dpi, $resolution['y'], 'y-resolution does not match ' . $dpi . ' dpi');
			var_dump($resolution);

			//unlink($destinationFile);
		}

	}

}

