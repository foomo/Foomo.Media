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

namespace Foomo\Media\Image\Adaptive;
use Foomo\Media\Image\Adaptive\Rules\AbstractRule;
use Foomo\Media\Image\Adaptive\Rules\HiDPI;
use Foomo\Media\Image\ImageSpec;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author bostjan <bostjan.marusic@bestbytes.de>
 */
class RuleSet
{
	public $rules = array();

	public static function create()
	{
		return new self;
	}

	/**
	 * @param AbstractRule $rule
	 * @return $this
	 */
	public function addRule(AbstractRule $rule)
	{
		$this->rules[] = $rule;
		return $this;
	}

	/**
	 * @param AbstractRule[] $rules
	 * @return $this
	 */
	public function addRules(array $rules)
	{
		$this->rules = array_merge($this->rules, $rules);
		return $this;
	}
	/**
	 * @param float[] $allowedPixelRatios which pixel ratios are we clamping to
	 *
	 * @return $this
	 */
	public function hiDPI(array $allowedPixelRatios = array(1.0, 2.0))
	{
		return $this->addRule(HiDPI::create($allowedPixelRatios));
	}

	/**
	 * @param $width
	 * @param $screenWidth
	 *
	 * @return $this
	 */
	public function scaleToWidthAtScreenWidth($width, $screenWidth)
	{
		return $this->addRule(Rules\BreakPoint::create($width, $screenWidth));
	}

	/**
	 * @param string $filename
	 * @param ClientInfo $info
	 *
	 * @return ImageSpec
	 */
	public function getSpec($filename, ClientInfo $info)
	{
		$spec = ImageSpec::create($filename);
		foreach($this->rules as $rule) {
			/* @var $rule AbstractRule */
			$rule->process($info, $spec);
		}
		return $spec;
	}
}