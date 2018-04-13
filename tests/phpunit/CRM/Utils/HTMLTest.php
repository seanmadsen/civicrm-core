<?php
/*
+--------------------------------------------------------------------+
| CiviCRM version 4.7                                                |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2018                                |
+--------------------------------------------------------------------+
| This file is a part of CiviCRM.                                    |
|                                                                    |
| CiviCRM is free software; you can copy, modify, and distribute it  |
| under the terms of the GNU Affero General Public License           |
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
 */

/**
 * Tests for parsing translatable strings in HTML content.
 * @group headless
 */
class CRM_Utils_HTMLTest extends CiviUnitTestCase {
  /**
   * @return array
   */
  public function translateExamples() {
    $cases = [];
    $cases[] = [
      '',
      [],
    ];
    $cases[] = [// missing ts
      '<div>Hello world</div>',
      [],
    ];
    $cases[] = [// text, no arg
      '<div>{{ts("Hello world")}}</div>',
      ['Hello world'],
    ];
    $cases[] = [// text, no arg, alternate text
      '<div>{{ts("Good morning, Dave")}}</div>',
      ['Good morning, Dave'],
    ];
    $cases[] = [// text, with arg
      '<div>{{ts("Hello world", {1: "whiz"})}}</div>',
      ['Hello world'],
    ];
    $cases[] = [// text, not really ts(), no arg
      '<div>{{clients("Hello world")}}</div>',
      [],
    ];
    $cases[] = [// text, not really ts(), with arg
      '<div>{{clients("Hello world", {1: "whiz"})}}</div>',
      [],
    ];
    $cases[] = [// two strings, duplicate
      '<div>{{ts("Hello world")}}</div> <p>{{ts("Hello world")}}</p>',
      ['Hello world'],
    ];
    $cases[] = [// two strings, addition
      '<div>{{ts("Hello world") + "-" + ts("How do you do?")}}</p>',
      ['Hello world', 'How do you do?'],
    ];
    $cases[] = [// two strings, separate calls
      '<div>{{ts("Hello world")}}</div> <p>{{ts("How do you do?")}}</p>',
      ['Hello world', 'How do you do?'],
    ];
    $cases[] = [// single quoted
      '<div>{{ts(\'Hello world\')}}</div>',
      ['Hello world'],
    ];
    $cases[] = [// unclear string
      '<div>{{ts(message)}}</div>',
      [],
    ];
    $cases[] = [// ts() within a string
      '<div>{{ts("Does the ts(\'example\') notation work?")}}</div>',
      ['Does the ts(\'example\') notation work?'],
    ];
    $cases[] = [// attribute, no arg
      '<div crm-title="ts("Hello world")"></div>',
      ['Hello world'],
    ];
    $cases[] = [// attribute, with arg
      '<div crm-title="ts("Hello world", {1: "whiz"})"></div>',
      ['Hello world'],
    ];
    $cases[] = [// attribute, two strings, with arg
      '<div crm-title="ts("Hello world", {1: "whiz"}) + ts("How do you do, %1?", {2: "funky"})"></div>',
      ['Hello world', 'How do you do, %1?'],
    ];
    $cases[] = [// trick question! Not used on Smarty templates.
      '<div>{ts}Hello world{/ts}</div>',
      [],
    ];

    return $cases;
  }

  /**
   * @param string $html
   *   Example HTML input.
   * @param array $expectedStrings
   *   List of expected strings.
   * @dataProvider translateExamples
   */
  public function testParseStrings($html, $expectedStrings) {
    // Magic! The JS parser works with HTML!
    $actualStrings = CRM_Utils_JS::parseStrings($html);
    sort($expectedStrings);
    sort($actualStrings);
    $this->assertEquals($expectedStrings, $actualStrings);
  }

}
