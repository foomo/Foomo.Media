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
	const FORMAT_TIFF = 'TIFF';

	protected static $allowResizeAboveSource = true;

	/**
	 * set resize condition
	 * @param boolean $allow if false source size is max destination size
	 */
	public static function allowResizeAboveSource($allow)
	{
		self::$allowResizeAboveSource = $allow;
	}

	/**
	 * @return bool
	 */
	public static function getAllowResizeAboveSource()
	{
		return self::$allowResizeAboveSource;
	}

	/**
	 * @param string $filename
	 * @param string $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param string $destination
	 * @param string $backgroundColor
	 * @return bool success
	 */
	public static function cropImage($filename, $x, $y, $width, $height, $destination, $backgroundColor = null)
	{
		$img = self::readImage($filename, $backgroundColor);
		$success = $img->cropImage($width, $height, $x, $y);
		if (!$success) {
			return false;
		}
		// writes resultant image to output directory
		$success = $img->writeImage($destination);

		return $success;
	}

	/**
	 * @param ImageSpec $spec
	 * @param string $destination
	 *
	 * @return bool
	 */
	public static function resizeImageWithSpec($spec, $destination)
	{
		return self::resizeImage(
			$spec->filename,
			$destination,
			$spec->width,
			$spec->height,
			$spec->quality,
			$spec->format,
			$spec->convertColorspaceToRGB,
			$spec->keepAspectRatio,
			$spec->addBorder,
			array(),
			$spec->resolution,
			$spec->backgroundColor
		);
	}

	/**
	 * @param $filename
	 * @param $destination
	 * @param integer $width
	 * @param integer $height
	 * @param string $quality
	 * @param string $format one of self::FORMAT_
	 * @param bool $convertColorspaceToRGB
	 * @param bool $keepAspectRatio
	 * @param bool $addBorder adds border. only effective if $keepAspectRatio is true, if array it is assumed to contain rgb values of the border
	 * @param array $imageSharpenParams $imageSharpenParams['radius'], $imageSharpenParams['sigma'] are floats
	 * @param int $resolution
	 * @param string $backgroundColor #rgb or null
	 *
	 * @return bool
	 */
	public static function resizeImage($filename, $destination, $width, $height, $quality = '100', $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false, $keepAspectRatio = false, $addBorder = false, $imageSharpenParams = array(), $resolution = 72, $backgroundColor = null)
	{
		// create new Imagick object
		$img = self::readImage($filename, $backgroundColor);
		return self::resizeImg($img, $destination, $width, $height, $quality, $format, $convertColorspaceToRGB, $keepAspectRatio, $addBorder, $imageSharpenParams, $resolution);
	}

	/**
	 * get a thumbnail image for any media type
	 * @param string $filename
	 * @param string $destination
	 * @param string $size resizes to whichever is larger, width or height
	 * @param string $format format jpeg|png
	 *
	 * @return boolean
	 */

	/**
	 * get a thumbnail image for any media type
	 *
	 * @param string $filename
	 * @param string $destination
	 * @param integer $size resizes to whichever is larger, width or height
	 * @param string $format
	 * @param bool $convertColorspaceToRGB
	 *
	 * @return bool
	 */
	public static function makeThumb($filename, $destination, $size, $format, $convertColorspaceToRGB = true)
	{
		// create new Imagick object
		$img = self::readImage($filename);
		// Resizes to whichever is larger, width or height
		if ($img->getImageHeight() <= $img->getImageWidth()) {
			return self::resizeImg($img, $destination, $size, $height = 0, $quality = '100', $format, $convertColorspaceToRGB, $keepAspectRatio = false, $addBorder = false, $imageSharpenParams = array(), $resolution = 72);
		} else {
			$img->resizeImage(0, $size, \Imagick::FILTER_LANCZOS, 1);
			return self::resizeImg($img, $destination, $width = 0, $size, $quality = '100', $format, $convertColorspaceToRGB, $keepAspectRatio = false, $addBorder = false, $imageSharpenParams = array(), $resolution = 72);
		}
	}

	/**
	 * read image or get a image representation of doc
	 *
	 * @param string $filename
	 * @param string $backgroundColor #rgb  or null. if not null set color before loading
	 *
	 * @return \Imagick
	 */
	private static function readImage($filename, $backgroundColor = null)
	{

		$img = new \Imagick();
		if (!is_null($backgroundColor)) {
			$img->setbackgroundcolor(new \ImagickPixel($backgroundColor));
		}
		$extension = $ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($extension == 'pdf') {
			$img->readImage($filename . '[0]');
			//make sure we do not get inverted pdfs
			$img = $img->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
		} else if ($extension == 'mp4') {
			$img->readImage($filename . '[50]');
		} else {
			$img->readImage($filename);
		}
		// $img->setImageType(\Imagick::IMGTYPE_TRUECOLOR);
		$img = $img->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
		return $img;
	}

	protected static function convertColorSpaceCYMKtoRGB($img)
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

	/**
	 * @param \Imagick $img
	 * @param string $profileName
	 * @return mixed
	 */
	protected static function setDefaultIccProfile($img, $profileName = 'AdobeRGB1998')
	{
		//$pFile = __DIR__ . '/srgb.icc';
		$pFile = __DIR__ . '/' . $profileName . '.icc';
		$icc = file_get_contents($pFile);
		$img->profileImage('icc', $icc);
		$img->setImageColorSpace(\Imagick::COLORSPACE_SRGB);
		return $img;
	}


	/**
	 * @param $profile
	 * @param array $disallowed
	 * @return bool
	 */
	protected static function checkIccIsAllowed($profile, $disallowed = [])
	{
		if (!empty($disallowed)) {
			foreach ($disallowed as $item) {
				if (substr_count($profile, $item) > 0) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param \IMagick $img
	 * @param $destination
	 * @param $width
	 * @param $height
	 * @param string $quality
	 * @param string $format
	 * @param bool $convertColorspaceToRGB
	 * @param bool $keepAspectRatio
	 * @param bool $addBorder
	 * @param array $imageSharpenParams
	 * @param $resolution
	 * @return mixed
	 */
	private static function resizeImg($img, $destination, $width, $height, $quality = '100', $format = Processor::FORMAT_JPEG, $convertColorspaceToRGB = false, $keepAspectRatio = false, $addBorder = false, $imageSharpenParams = array(), $resolution)
	{
		if (self::$allowResizeAboveSource == false) {
			if ($width > $img->getImageWidth()) {
				$width = $img->getImageWidth();
			}
			if ($height > $img->getImageHeight()) {
				$height = $img->getImageHeight();
			}
		}

		$img->setResolution($resolution, $resolution);

		if ($format == Processor::FORMAT_GIF) {
			$img->coalesceImages();
		}

		if($keepAspectRatio) {
			$originalWidth = $img->getImageWidth();
			$originalHeight = $img->getImageHeight();
			if(is_null($width)) {
				$width = (int) $originalWidth * ($height / $originalHeight);
			} else if(is_null($height)) {
				$height = (int) $originalHeight * ($width / $originalWidth);
			}
		}

		$img->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, $keepAspectRatio);

		if ($addBorder === true || is_array($addBorder)) {
			$color = new \ImagickPixel();

			if (is_array($addBorder) && count($addBorder) == 3) {
				$colorStr = implode(',', $addBorder);
				$color->setColor("rgb(" . $colorStr . ")");
			} else { //addBorder === true
				if (in_array($format, array(Processor::FORMAT_TIFF, Processor::FORMAT_PNG, Processor::FORMAT_GIF))) {
					$color->setColor("transparent");
				} else {
					$color->setColor("rgb(255,255,255)");
				}
			}
			$img->borderImage($color, ($width - $img->getimagewidth()) / 2, ($height - $img->getimageheight()) / 2);
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

		if (isset($imageSharpenParams['radius']) && isset($imageSharpenParams['sigma'])) {
			$img->sharpenImage($imageSharpenParams['radius'], $imageSharpenParams['sigma']);
		}

		try {
			// strip out unneeded meta data
			// Get image profile
			$profile = $img->getImageProfile('icc');
			// strip out unneeded meta data
			$img->stripImage();
			// if profile not empty
			if (!empty($profile)) {
				$img->profileImage('icc', $profile);
			}
		} catch (\Exception $e) {
			//means there is no profile
		}

		// writes resultant image to output directory
		$success = $img->writeImage($destination);

		// destroys Imagick object, freeing allocated resources in the process
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
