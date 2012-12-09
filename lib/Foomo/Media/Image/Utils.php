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
 * @author frederik <frederik@bestbytes.de>
 */
class Utils
{

	const FORMAT_JPEG = 'JPEG';
	const FORMAT_GIF = 'GIF';
	const FORMAT_PNG = 'PNG';
	const FORMAT_TIF = 'TIF';

	/**
	 * get the width and height from a file
	 * 
	 * @param string $file
	 * @return int[] image sizes
	 */
	public static function getImageSize($file)
	{
		list($width, $height) = getimagesize($file);
		return array('0' => $width, '1' => $height, 'width' => $width, 'height' => $height);
	}

	/**
	 * compute a scaled size
	 * 
	 * @param integer $width
	 * @param integer $height
	 * @param integer $targetWidth
	 * @param integer $targetHeight 
	 * 
	 * @return array('width' => int, 'height' => int)
	 */
	public static function computeResampledSize($width, $height, $targetWidth = null, $targetHeight = null)
	{
		if($targetHeight === 0 || $targetWidth === 0) {
			trigger_error('must not resample to size 0', \E_USER_ERROR);
		}
		if (isset($targetWidth) && !isset($targetHeight)) {
			$scale = $targetWidth / $width;
			$targetHeight = ceil($scale * $height);
		} else if(!isset($targetWidth) && isset($targetHeight)) {
			$scale = $targetHeight / $height;
			$targetWidth = ceil($scale * $width);
		} else {
			if(is_null($targetWidth)) {
				$targetWidth = $width;
			}
			if(is_null($targetHeight)) {
				$targetHeight = $height;
			}
		}
		return array('0' => $targetWidth, '1' => $targetHeight, 'width' => $targetWidth, 'height' => $targetHeight);
	}
	
	/**
	 * map a filetype (JPEG, PNG, GIF) to its corresponding file extension
	 * 
	 * @param string $filetype
	 * @return string file extension (jpg, png, gif)
	 */
	public static function getFileExtensionByFileFormat($filetype)
	{
		switch ($filetype) {
			case self::FORMAT_JPEG:
				return 'jpg';
			case self::FORMAT_PNG:
				return 'png';
			case self::FORMAT_TIF:
				return 'tif';
			case self::FORMAT_GIF:
				return 'gif';
			default:
				return 'jpg';
		}
	}

	/**
	 * map a file extension to its corresponding filetype (JPEG, PNG, GIF)
	 * 
	 * @param string $fileExtension
	 * @return string
	 */
	public static function getFileFormatByFileExtension($fileExtension)
	{
		$fileExtension = \strtolower($fileExtension);
		switch ($fileExtension) {
			case 'jpg':
				return self::FORMAT_JPEG;
			case 'jpeg':
				return self::FORMAT_JPEG;
			case 'png':
				return self::FORMAT_PNG;
			case 'gif':
				return self::FORMAT_GIF;
			case 'tif':
				return self::FORMAT_TIF;
			case 'tiff':
				return self::FORMAT_TIF;
			default:
				return self::FORMAT_JPEG;
		}
	}
}
