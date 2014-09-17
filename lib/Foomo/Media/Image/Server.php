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

use Foomo\Http\BrowserCache;
use Foomo\Media\Image\Adaptive\RuleSet;
use Foomo\Media\Module;
use Foomo\Utils;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Server
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string  $file
	 * @param string  $layout
	 * @param string  $type
	 * @param RuleSet $ruleSet
	 */
	public static function serve($file, $layout, $type, $ruleSet = null)
	{
		$config = Module::getImageServerConfig();

		if (is_null($ruleSet)) {
			$ruleSet = $config->getRuleSet();
		}

		$config->applyLayoutToRuleSet($ruleSet, $layout, $type);

		$spec = Adaptive::getImageSpec($file, $ruleSet);
		$hash = $spec->getHash();

		$cacheFilename = self::getCacheFilename($hash);

		// @todo locking anyone ?

		if (!file_exists($cacheFilename) || filemtime($cacheFilename) < filemtime($file)) {
			$success = Processor::resizeImageWithSpec($spec, $cacheFilename);
			if (!$success) {
				trigger_error('so wtf could I not resize that image ' . $file . ' with ' . var_export($spec, true), E_USER_ERROR);
			}
		}

		$mime = 'octet/stream';
		switch ($spec->format) {
			case Processor::FORMAT_GIF:
				$mime = 'image/gif';
				break;
			case Processor::FORMAT_JPEG:
				$mime = 'image/jpeg';
				break;
			case Processor::FORMAT_PNG:
				$mime = 'image/png';
				break;
		}

		BrowserCache::setResourceData($mime, $hash, filemtime($file), 7 * 24 * 3600);
		if (BrowserCache::tryBrowserCache()) {
			BrowserCache::sendNotModified();
		} else {
			BrowserCache::sendHeaders();
			Utils::streamFile($cacheFilename, null, $mime);
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $hash
	 * @return string
	 */
	private static function getCacheFilename($hash)
	{
		return Module::getCacheDir() . DIRECTORY_SEPARATOR . 'img-' . $hash;
	}
}