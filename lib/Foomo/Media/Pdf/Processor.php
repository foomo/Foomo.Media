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
namespace Foomo\Media\Pdf;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author bostjan <bostjan.marusic@bestbytes.de>
 */
class Processor
{
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
		$img->readImage($filename . '[0]');
		$img = $img->flattenImages();
		
		return \Foomo\Media\Image\Processor::makeThumbImage($img, $destination, $size, $format);
	}	
}
