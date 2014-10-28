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

namespace Foomo\Media;

use Foomo\Media\Image\Server\DomainConfig;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  bostjan <bostjan.marusic@bestbytes.de>
 */
class Module extends \Foomo\Modules\ModuleBase
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const NAME    = 'Foomo.Media';
	const VERSION = '0.3.4';

	//---------------------------------------------------------------------------------------------
	// ~ Overriden static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * Your module needs to be set up, before being used - this is the place to do it
	 */
	public static function initializeModule()
	{
	}

	/**
	 * Get a plain text description of what this module does
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return '
			Handles a variety of media types by interfacing with imagemagick, ffmpeg and the like.
			Using the Image Server can easily serve preconfigured sizes.
		';
	}

	/**
	 * get all the module resources
	 *
	 * @return \Foomo\Modules\Resource[]
	 */
	public static function getResources()
	{
		return array(
			\Foomo\Modules\Resource\Module::getResource('Foomo', '0.3.*'),
			# php modules
			\Foomo\Modules\Resource\PhpModule::getResource('imagick'),
			# cli commands
			\Foomo\Modules\Resource\CliCommand::getResource('gs'),
			\Foomo\Modules\Resource\CliCommand::getResource('convert'),
			\Foomo\Modules\Resource\CliCommand::getResource('ffmpeg'),
			\Foomo\Modules\Resource\CliCommand::getResource('pdfinfo'),
			# domain configs
			\Foomo\Modules\Resource\Config::getResource(self::NAME, 'Foomo.Media.Image.server'),
		);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return \Foomo\Media\Image\Server\DomainConfig
	 */
	public static function getImageServerConfig()
	{
		return self::getConfig(DomainConfig::NAME);
	}
}
