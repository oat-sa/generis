<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

require_once dirname(__FILE__).'/../../common/common.php';
require_once $GLOBALS['inc_path'].'/simpletest/autorun.php';

/**

/**
 * Test class for Expression.
*/

class HyperMask extends UnitTestCase {


	public function testHyperMask(){
    $checks = array();
    $checks[0][] = array(
      "hello, I'm speaking #insert test language#",
      "hello, I'm speaking german"
    );
    $checks[0][] = array(
      "I live in #insert country name# :-)",
      "I live in France :-)"
    );
    $checks[0][] = array(
      "My name is {NAME}, and yours?",
      "My name is Bond, and yours?"
    );
    $checks[0][] = array(
      "Welcome to {SURVEY INSTITUTE}, thanks to {SPONSOR}.",
      "Welcome to CRP Henri Tudor, thanks to the FNR."
    );

    $checks[1][] = array(
      "The sun #is/was# high",
      "The sun is high"
    );
    $checks[1][] = array(
      "My mother is keen on #insert country name#!",
      "My mother is keen on France!"
    );
    $checks[1][] = array(
      "I work at {SURVEY INSTITUTE}.",
      "I work at CRP Henri Tudor."
    );
    $checks[1][] = array(
      "I heiße {NAME}. Ich arbeite im {SURVEY INSTITUTE}. Ich liebe {SPONSOR}.",
      "I heiße Larry Wallis. Ich arbeite im CRP Henri Tudor. Ich liebe FNR."
    );
    $checks[1][] = array(
      "[IF BQ RESPONDENT IS NOT THE SAME AS SCREENER RESPONDENT:] Hello.",
      " Hello."
    );
    $checks[1][] = array(
      "[ALL RESPONDENTS:]",
      ""
    );


    foreach ($checks[1] as $check) {
      $toReplace = $check[0];
      $replaced = common_Utils::hyperMask($toReplace);
      $target = $check[1];
      echo "<p>$toReplace: <strong>$replaced</strong> ?= $target</p>";
      $this->assertTrue($replaced==$target);
	  }
  }
}
?>