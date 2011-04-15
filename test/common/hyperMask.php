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