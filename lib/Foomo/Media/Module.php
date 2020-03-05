<?php

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
	const VERSION = '0.3.6';

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
