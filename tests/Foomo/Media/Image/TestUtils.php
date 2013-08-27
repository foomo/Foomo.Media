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
 * @author frederik <frederik@bestbytes.de>
 */
class TestUtils extends \PHPUnit_Framework_TestCase
{

	public function testPdfSize()
	{
		$pdf = __DIR__ . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'test.pdf';
		$size = Utils::getPdfSize($pdf);

		//mac preview document info
		$widthDocument = 21;
		$heightDocument = 29.71;

		//1 inch = 2.54 cm
		$inch = 2.54;
		$widthInch = $widthDocument / $inch;	//8,268
		$heightInch = $heightDocument / $inch;	//11,693

		$ppi = 72;
		$widthPx = floor($widthInch * $ppi);
		$heightPx = floor($heightInch * $ppi);

//		var_dump($widthPx, $heightPx);
//		var_dump($size['width'], $size['height']);

		$this->assertEquals($widthPx, $size['width'], 'pdf width is not as expected');
		$this->assertEquals($heightPx, $size['height'], 'pdf height is not as expected');
	}

}
