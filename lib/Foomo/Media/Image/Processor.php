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

	const FORMAT_JPEG = 'JPEG';
	const FORMAT_GIF = 'GIF';
	const FORMAT_PNG = 'PNG';
	const FORMAT_TIF = 'TIF';

	/**
	 * resize image
	 * @param string $filename
	 * @param string $destination
	 * @param string $size
	 * @param string $format one of self::FORMAT_
	 * @param boolean $keepAspectRatio
	 * @param booleasn $addBorder adds border. only effective if $keepAspectRatio is true
	 * @return boolean $success
	 */
	public static function resizeImage($filename, $destination, $width, $height, $quality = '100', $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false, $keepAspectRatio = false, $addBorder=false)
	{
		// create new Imagick object

		$img = new \Imagick();
		$img->readImage($filename);
		return self::resizeImg($img, $destination, $width, $height, $quality, $format, $convertColorspaceToRGB, $keepAspectRatio, $addBorder);
	}

	/**
	 * get a thumbnail image for any media type
	 * @param string $filename
	 * @param string $destination
	 * @param string $size resizes to whichever is larger, width or height
	 * @param string $format format jpeg|png
	 * @return boolean
	 */
	public static function makeThumb($filename, $destination, $size, $format, $convertColorspaceToRGB = true)
	{
		// create new Imagick object
		$img = new \Imagick();
		$extension = $ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($extension == 'pdf') {
			$img->readImage($filename . '[0]');
			//make sure we do not get inverted pdfs
			$img = $img->flattenImages();
		} else if ($extension == 'mp4') {
			$img->readImage($filename . '[50]');
		} else {
			$img->readImage($filename);
		}

		// Resizes to whichever is larger, width or height
		if ($img->getImageHeight() <= $img->getImageWidth()) {
			return self::resizeImg($img, $destination, $size, $height = 0, $quality = '100', $format, $convertColorspaceToRGB);
		} else {
			$img->resizeImage(0, $size, \Imagick::FILTER_LANCZOS, 1);
			return self::resizeImg($img, $destination, $width = 0, $size, $quality = '100', $format, $convertColorspaceToRGB);
		}
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

	private static function resizeImg($img, $destination, $width, $height, $quality = '100', $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB=false, $keepAspectRatio=false, $addBorder=false)
	{
		$img->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, $keepAspectRatio);
		
		if ($addBorder) {
		 $color=new \ImagickPixel();
		 $color->setColor("rgb(255,255,255)");
		 $img->borderImage($color,($width- $img->getimagewidth())/2,($height- $img->getimageheight())/2);
		 if ($img->getimagewidth() != $width || $img->getimageheight() != $height) {
			$img->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
		 }
		}
		if ($format == Processor::FORMAT_JPEG) {
			// set jpeg format
			$img->setImageFormat($format);
			// Set to use jpeg compression
			$img->setImageCompression(\Imagick::COMPRESSION_JPEG);
			// Set compression level (1 lowest quality, 100 highest quality)
			$img->setImageCompressionQuality($quality);
		} else {
			$img->setImageFormat($format);
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
	

	public static function getFileExtensionByFileFormat($filetype)
	{
		\trigger_error(__METHOD__, \E_DEPRECATED);
		return \Foomo\Media\Image\Utils::getFileExtensionByFileFormat($filetype);
	}

	public static function getFileFormatByFileExtension($fileExtension)
	{
		\trigger_error(__METHOD__, \E_DEPRECATED);
		return \Foomo\Media\Image\Utils::getFileFormatByFileExtension($fileExtension);
	}
}
