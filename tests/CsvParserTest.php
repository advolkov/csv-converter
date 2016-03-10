<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

require_once __DIR__ . "/CsvConverterTestCase.php";

class CsvParserTest extends CsvConverterTestCase
{
    /**
     * @var \Utils\CsvParser
     */
    private $Parser;

    public function setUp()
    {
        $this->Parser = new \Utils\CsvParser();
    }

    public function providerForTestGroupBy()
    {
        return [
            [
                ["name", "url", "test"],
                ["correct_name", "http://google.com", "test"],
                "test"
            ],
        ];
    }

    /**
     * @param array $keys
     * @param array $data
     * @param string $group_by
     * @dataProvider providerForTestGroupBy
     */
    public function testGroupBy($keys, $data, $group_by)
    {
        $group = self::getPrivateMethod("\\Utils\\CsvParser", 'groupBy');
        $grouped_data = $group->invokeArgs($this->Parser, [$keys, $data, $group_by]);
        $this->assertEquals(array_combine($keys, $data), $grouped_data);
    }

    public function providerForTestSortBy()
    {
        return [
            [
                [
                    ["name" => "Nikolay Urtov", "url" => "http://google2.com", "rate" => 3],
                    ["name" => "Ivan Vasiliev Asfe", "url" => "http://google3.com", "rate" => 1],
                    ["name" => "Petr Cattaneov", "url" => "http://google1.com", "rate" => 5],
                    ["name" => "Ivan Vasiliev", "url" => "http://google2.com", "rate" => 1],
                ],
                "rate"
            ],
            [
                [
                    ["name" => "Zack Urt", "url" => "http://google2.com", "rate" => 3],
                    ["name" => "Ivan Vasiliev Asfe", "url" => "http://google3.com", "rate" => 4],
                    ["name" => "Petr Cattaneov", "url" => "http://google1.com", "rate" => 5],
                    ["name" => "Ivan Vasiliev", "url" => "http://google2.com", "rate" => 1],
                ],
                "name"
            ],

        ];
    }

    /**
     * @param $csv_array
     * @param $sort_by
     * @dataProvider providerForTestSortBy
     */
    public function testSortBy($csv_array, $sort_by)
    {
        $expected_array = [];
        $sort = self::getPrivateMethod("\\Utils\\CsvParser", 'sortBy');
        $sorted_array = $sort->invokeArgs($this->Parser, [$csv_array, $sort_by]);
        if ($sort_by == "rate") {
            $expected_array = [
                ["name" => "Ivan Vasiliev Asfe", "url" => "http://google3.com", "rate" => 1],
                ["name" => "Ivan Vasiliev", "url" => "http://google2.com", "rate" => 1],
                ["name" => "Zack Urt", "url" => "http://google2.com", "rate" => 3],
                ["name" => "Petr Cattaneov", "url" => "http://google1.com", "rate" => 5],
            ];
        }
        if ($sort_by == "name") {
            $expected_array = [
                ["name" => "Ivan Vasiliev", "url" => "http://google2.com", "rate" => 1],
                ["name" => "Ivan Vasiliev Asfe", "url" => "http://google3.com", "rate" => 4],
                ["name" => "Petr Cattaneov", "url" => "http://google1.com", "rate" => 5],
                ["name" => "Zack Urt", "url" => "http://google2.com", "rate" => 3],
            ];
        }
        $this->assertEquals($expected_array, $sorted_array);
    }
}
