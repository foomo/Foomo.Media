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
 * @author bostjan <bostjan.marusic@bestbytes.de>
 */
class HiDPI extends AbstractRule
{
	protected $allowedPixelRatios;

	/**
	 * @param float[] $allowedPixelRatios what pixel ratios to clamp to, because there are too many android devices
	 *
	 * @return HiDPI
	 */
	public static function create(array $allowedPixelRatios = array(1.0, 2.0))
	{
		$ret = new self;
		$ret->allowedPixelRatios = $allowedPixelRatios;
		return $ret;
	}
	public function process(ClientInfo $info, ImageSpec $spec)
	{
		$pixelRatio = self::clampPixelRatio($info->pixelRatio, $this->allowedPixelRatios);
		$spec->width *= $pixelRatio;
		$spec->height *= $pixelRatio;
	}
	protected static function clampPixelRatio($pixelRatio, array $allowedPixelRatios)
	{
		$distance = 999999; // max
		$ratio = 1.0;
		foreach($allowedPixelRatios as $allowedPixelRatio) {
			$currentDistance = abs($allowedPixelRatio - $pixelRatio);
			if($currentDistance < $distance) {
				$ratio = $allowedPixelRatio;
			}
		}
		return $ratio;
	}
}