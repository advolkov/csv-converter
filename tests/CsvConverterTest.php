<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

require_once __DIR__ . "/CsvConverterTestCase.php";
require_once __DIR__ . "/../src/CsvConverter.php";

class CsvConverterTest extends CsvConverterTestCase
{
    /**
     * @var \ConvertCsv\CsvConverter
     */
    private $Converter;

    public function setUp()
    {
        $this->Converter = new \ConvertCsv\CsvConverter();
    }

    public function providerForTestCheckCsvData()
    {
        return [
            [
                [["name" => "correct_name", "url" => "http://google.com", "rate" => 5]],
                "positive"
            ],
            [
                [
                    ["name" => "correct_name", "url" => "http://goo gle.com", "rate" => 5],
                    ["name" => "correct_name", "url" => "http://google.com", "rate" => 5],
                ],
                "negative"
            ],
            [
                [["name" => "correct_name", "url" => "http://google.com", "rate" => "asdsq"]],
                "negative"
            ],
            [
                [["name" => "Ё_bad_ö_name", "url" => "http://google.com", "rate" => 5]],
                "negative"
            ],
        ];
    }

    /**
     * @param array $array
     * @param string$type
     * @dataProvider providerForTestCheckCsvData
     */
    public function testCheckCsvData($array, $type)
    {
        $mock = $this->getMock('\ConvertCsv\CsvDataValidator');
        if ($type == "positive") {
            $mock = $this->getMock('\ConvertCsv\CsvDataValidator');
            $mock
                ->method('checkUrl')
                ->with($array[0]["url"])
                ->will($this->returnValue(true));

            $check_csv_data = self::getPrivateMethod("\\ConvertCsv\\CsvConverter", 'checkCsvData');
            $checked_data = $check_csv_data->invokeArgs($this->Converter, [$array]);
            $this->assertEquals($array, $checked_data);
        }
        if ($type == "negative") {
            $mock
                ->method('checkUrl')
                ->with($array[0]["url"])
                ->will($this->returnValue(false));

            $check_csv_data = self::getPrivateMethod("\\ConvertCsv\\CsvConverter", 'checkCsvData');
            $checked_data = $check_csv_data->invokeArgs($this->Converter, [$array]);
            $this->assertEquals(count($array) - 1, count($checked_data));
            $this->assertNotEquals($array, $checked_data);
        }
    }
}
