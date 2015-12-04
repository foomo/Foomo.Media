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

namespace Foomo\Media\Image\Adaptive\Rules;
use Foomo\Media\Image\Adaptive\ClientInfo;
use Foomo\Media\Image\ImageSpec;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class Size extends AbstractRule
{
	protected $width;
	protected $height;

	public static function create($width, $height)
	{
		$ret = new self;
		$ret->width = $width;
		$ret->height = $height;
		return $ret;
	}

	public static function createPreserveWidth($height)
	{
		return static::create(null, $height);
	}
	public static function createPreserveHeight($width)
	{
		return static::create($width, null);

	}
	public function process(ClientInfo $info, ImageSpec $spec)
	{
		$spec->width = $this->width;
		$spec->height = $this->height;
		$spec->keepAspectRatio = is_null($this->width) || is_null($this->height);
	}
}