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
class Processor
{

	/**
	 * resize image
	 * @param string $filename
	 * @param string $destination
	 * @param string $size
	 * @format string jpeg|png...
	 * @return boolean $success
	 */
	public static function resizeImage($filename, $destination, $width, $height, $quality = '100', $format = 'jpeg', $convertColorspaceToRGB = false)
	{
		// create new Imagick object
		$img = new \Imagick();
		$img->readImage($filename);
		$img->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);

		if ($format == 'jpeg') {
			// set jpeg format
			$img->setImageFormat("jpeg");
			// Set to use jpeg compression
			$img->setImageCompression(\Imagick::COMPRESSION_JPEG);
			// Set compression level (1 lowest quality, 100 highest quality)
			$img->setImageCompressionQuality($quality);
		} else {
			$img->setImageFormat("png");
		}

		if ($convertColorspaceToRGB === true) {
			self::convertColorSpaceCYMKtoRGB($img);
		}

		// Strip out unneeded meta data
		$img->stripImage();
		// Writes resultant image to output directory
		$success = $img->writeImage($destination);
		// Destroys Imagick object, freeing allocated resources in the process
		$img->clear();
		$img->destroy();
		return $success;
	}

	/**
	 * get a thumbnail image for any media type
	 * @param string $filename
	 * @param string $destination
	 * @param string $size
	 * @param string $format format jpeg|png
	 * @return boolean
	 */
	public static function makeThumb($filename, $destination, $size, $format)
	{
		// create new Imagick object
		$img = new \Imagick();
		$img->readImage($filename);
	
		return self::makeThumbImage($img, $destination, $size, $format);
	}

	private static function makeThumbImage($img, $destination, $size, $format)
	{
		// Resizes to whichever is larger, width or height
		if ($img->getImageHeight() <= $img->getImageWidth()) {
			// Resize image using the lanczos resampling algorithm based on width
			$img->resizeImage($size, 0, \Imagick::FILTER_LANCZOS, 1);
		} else {
			// Resize image using the lanczos resampling algorithm based on height
			$img->resizeImage(0, $size, \Imagick::FILTER_LANCZOS, 1);
		}
		if ($format != 'png') {
			// set jpeg format
			$img->setImageFormat("jpeg");
			// Set to use jpeg compression
			$img->setImageCompression(\Imagick::COMPRESSION_JPEG);
			// Set compression level (1 lowest quality, 100 highest quality)
			$img->setImageCompressionQuality(100);
		} else {
			$img->setImageFormat("png");
		}

		//
		if ($img->getImageColorspace() == \Imagick::COLORSPACE_CMYK) {
			$img = self::convertColorSpaceCYMKtoRGB($img);
		}

		// Strip out unneeded meta data
		$img->stripImage();
		// Writes resultant image to output directory
		$success = $img->writeImage($destination);
		// Destroys Imagick object, freeing allocated resources in the process
		$img->clear();
		$img->destroy();
		return $success;
	}

	private static function convertColorSpaceCYMKtoRGB($img)
	{
		if ($img->getImageColorspace() == \Imagick::COLORSPACE_CMYK) {
			$pFile = __DIR__ . '/USWebUncoated.icc';
			$icc_CMYK = file_get_contents($pFile);
			$img->profileImage('icc', $icc_CMYK);
			$pFile = __DIR__ . '/AdobeRGB1998.icc';
			$icc_rgb = file_get_contents($pFile);
			$img->profileImage('icc', $icc_rgb);
			$img->setImageColorSpace(\Imagick::COLORSPACE_RGB);
		}
		return $img;
	}

}
