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
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'image1.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.jpg';

		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = 100, $format = 'jpeg', $convertColorspaceToRGB = false);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file doews not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());
		unlink($destinationFile);
	}

	public function testConvertFormat()
	{
		$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'image1.jpeg';
		$destinationFile = \Foomo\Config::getTempDir(\Foomo\Media\Module::NAME) . DIRECTORY_SEPARATOR . 'outputImage.png';

		$success = \Foomo\Media\Image\Processor::resizeImage($sourceFile, $destinationFile, $width = 100, $height = 100, $quality = 100, $format = 'png', $convertColorspaceToRGB = false);
		$this->assertTrue($success);

		$this->assertTrue(file_exists($destinationFile), 'destination file does not exist');

		$img = new \Imagick();
		$img->readImage($destinationFile);
		$this->assertEquals(100, $img->getimagewidth());
		$this->assertEquals(100, $img->getimageheight());
		
		$this->assertEquals('PNG', $img->getimageformat());
		unlink($destinationFile);
	}

}

