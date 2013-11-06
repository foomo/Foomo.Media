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
 * @author jan <jan@bestbytes.de>
 */
class ImageSpec
{
	public static function create($filename)
	{
		$ret = new self;
		$ret->filename = $filename;
		return $ret;
	}
	public $filename;
	public $width = 0;
	public $height = 0;
	public $quality = '100';
	public $format = Processor::FORMAT_JPEG;
	public $convertColorspaceToRGB = false;
	public $keepAspectRatio = false; // true is breaking it
	public $addBorder = false;
	public $imageSharpenParams = array();
	public $resolution = 72;
	/**
	 * @return string
	 */
	public function getHash()
	{
		return sha1(serialize($this));
	}
}
