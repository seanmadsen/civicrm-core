<?php

/**
 * Class CRM_Utils_FileTest
 * @group headless
 */
class CRM_Utils_FileTest extends CiviUnitTestCase {

  /**
   * Test is child path.
   */
  public function testIsChildPath() {
    $testCases = [];
    $testCases[] = ['/ab/cd/ef', '/ab/cd', FALSE];
    $testCases[] = ['/ab/cd', '/ab/cd/ef', TRUE];
    $testCases[] = ['/ab/cde', '/ab/cd/ef', FALSE];
    $testCases[] = ['/ab/cde', '/ab/cd', FALSE];
    $testCases[] = ['/ab/cd', 'ab/cd/ef', FALSE];
    foreach ($testCases as $testCase) {
      $actual = CRM_Utils_File::isChildPath($testCase[0], $testCase[1], FALSE);
      $this->assertEquals($testCase[2], $actual, sprintf("parent=[%s] child=[%s] expected=[%s] actual=[%s]",
        $testCase[0], $testCase[1], $testCase[2], $actual
      ));
    }
  }
  public function testStripComment() {
    $strings = [
      "\nab\n-- cd\nef" => "\nab\nef",
      "ab\n-- cd\nef" => "ab\nef",
      "ab\n-- cd\nef\ngh" => "ab\nef\ngh",
      "ab\n--cd\nef" => "ab\nef",
      "ab\n--cd\nef\n" => "ab\nef\n",
      "ab\n#cd\nef\n" => "ab\nef\n",
      "ab\n--cd\nef" => "ab\nef",
      "ab\n#cd\nef" => "ab\nef",
      "ab\nfoo#cd\nef" => "ab\nfoo#cd\nef",
      "ab\r\n--cd\r\nef" => "ab\r\nef",
      "ab\r\n#cd\r\nef" => "ab\r\nef",
      "ab\r\nfoo#cd\r\nef" => "ab\r\nfoo#cd\r\nef",
    ];
    foreach ($strings as $string => $check) {
      $test = CRM_Utils_File::stripComments($string);
      $this->assertEquals($test,
          $check,
          sprintf("original=[%s]\nstripped=[%s]\nexpected=[%s]",
              json_encode($string),
              json_encode($test),
              json_encode($check)
           )
      );
    }
  }

  public function fileExtensions() {
    return [
      ['txt'],
      ['danger'],
    ];
  }

  /**
   * @dataProvider fileExtensions
   * @param string $ext
   */
  public function testDuplicate($ext) {
    $fileName = CRM_Utils_File::makeFileName('test' . rand(100, 999) . ".$ext");
    CRM_Utils_File::createFakeFile('/tmp', 'test file content', $fileName);
    $newFile = CRM_Utils_File::duplicate("/tmp/$fileName");
    $this->assertNotEquals("/tmp/$fileName", $newFile);
    $contents = file_get_contents($newFile);
    $this->assertEquals('test file content', $contents);
    unlink("/tmp/$fileName");
    unlink($newFile);
  }

}
