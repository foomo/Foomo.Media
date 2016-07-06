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

namespace Foomo\Media\Image\Server;

use Foomo\Config\AbstractConfig;
use Foomo\Media\Image\Adaptive\RuleSet;
use Foomo\Media\Image\Processor;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class DomainConfig extends AbstractConfig
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Media.Image.server';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Grid size definitions for breakpoints
	 *
	 * @var array[int][string]int
	 */
	public $grid = array(
		0    => array(
			'screen' => 768,
			'full'   => 768,
			'medium' => 384,
			'small'  => 192,
		),
		768  => array(
			'screen' => 992,
			'full'   => 750,
			'medium' => 375,
			'small'  => 188,
		),
		992  => array(
			'screen' => 1200,
			'full'   => 970,
			'medium' => 485,
			'small'  => 243,
		),
		1200 => array(
			'screen' => 1600,
			'full'   => 1170,
			'medium' => 585,
			'small'  => 293,
		),
	);
	/**
	 * Layout definitions
	 *
	 * @var array[string][int][string][string]
	 */
	public $layouts = array(
		'my-app' => array(
			0 => array(
				'foo' => 'screen',
				'bar' => 'medium'
			),
			768 => array(
				'foo' => 'full',
				'bar' => 'small'
			),
			992 => array(
				'foo' => 'screen',
				'bar' => 'medium'
			),
			1200 => array(
				'foo' => 'screen',
				'bar' => 'full'
			)
		)
	);
	/**
	 * List of rules
	 *
	 * @var array
	 */
	public $rules = array(
		array(
			'class'  => 'Foomo\\Media\\Image\\Adaptive\\Rules\\HiDPI',
			'params' => array(
				array('1.0', '2.0')
			)
		)
	);

	/**
	 * jpeg image quality
	 *
	 * @var string
	 */
	public $quality = '100';

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return RuleSet
	 */
	public function getRuleSet()
	{
		$ruleSet = new RuleSet();
		foreach ($this->rules as $rule) {
			$ruleSet->addRule(call_user_func_array(array($rule['class'], 'create'), $rule['params']));
		}
		return $ruleSet;
	}

	/**
	 * @param RuleSet $ruleSet
	 * @param string  $layout
	 * @param string  $type
	 * @throws \Exception
	 */
	public function applyLayoutToRuleSet(RuleSet $ruleSet, $layout, $type)
	{
		if (!isset($this->layouts[$layout])) {
			throw new \Exception("File layout '$layout' not found");
		}
		$found = false;
		$layoutType = $this->layouts[$layout];
		$layoutRules = array();
		foreach (array_keys($this->grid) as $key => $breakpoint) {
			//var_dump($breakpoint, $layout, $type, $layoutType, $layoutType[$breakpoint][$type]);exit;
			if (isset($layoutType[$breakpoint]) && isset($layoutType[$breakpoint][$type])  && isset($this->grid[$breakpoint][$layoutType[$breakpoint][$type]])) {
				$layoutRules[] = \Foomo\Media\Image\Adaptive\Rules\BreakPoint::create(
					$this->grid[$breakpoint][$layoutType[$breakpoint][$type]],
					$breakpoint
				);
				$found = true;
			}
		}
		if(!empty($layoutRules)) {
			$ruleSet->rules = array_merge($layoutRules, $ruleSet->rules);
		}
		if (!$found) {
			throw new \Exception("File type '$type' not found in layout '$layout'!");
		}
	}
}
