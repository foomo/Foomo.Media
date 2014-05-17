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
use Foomo\Media\Image\Adaptive\ClientInfo;
use Foomo\Media\Image\Adaptive\RuleSet;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author bostjan <bostjan.marusic@bestbytes.de>
 */
class Adaptive
{
	const COOKIE_NAME = 'foomoMediaClientInfo';

	/**
	 * @param string $filename
	 * @param RuleSet $rules
	 * @return ImageSpec
	 */
	public static function getImageSpec($filename, RuleSet $rules)
	{
		return $rules->getSpec(
			$filename,
			self::getClientInfo(
				isset($_COOKIE[self::COOKIE_NAME])?$_COOKIE[self::COOKIE_NAME]:''
			)
		);
	}
	public static function getClientInfo($cookieString = '')
	{
		$ret = new ClientInfo();
		if(!empty($cookieString)) {
			$parts = explode('@', trim($cookieString));
			$valid = false;
			if(count($parts) == 2) {
				$screenSizeParts = explode('x', $parts[0]);
				if(count($screenSizeParts) == 2) {
					$ret->pixelRatio = (float) $parts[1];
					$ret->screenWidth = (int) $screenSizeParts[0];
					$ret->screenHeight = (int) $screenSizeParts[1];
					$valid = true;
				}
			}
			if(!$valid) {
				trigger_error('invalid cookie value for adaptive client info', E_USER_WARNING);
			}
		}
		return $ret;
	}
	public static function getStreamingJSCookieSnippet()
	{
		return 'document.cookie=\'' . self::COOKIE_NAME . '=\' + screen.width + \'x\' + screen.height + \'@\' +(\'devicePixelRatio\' in window ?window.devicePixelRatio: \'1\') + \'; path=/\';';
	}
}