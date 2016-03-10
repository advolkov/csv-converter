<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

namespace Utils;

class CsvParser
{
    const DEFAULT_DELIMITER = ",";

    /**
     * This method reads csv-file and parses it into an array
     * The first line in csv-file will be parsed as headers and used as keys in result array
     * It also groups result array
     *
     * @param string $file_name
     * @param string $group_by
     * @param string $delimiter
     * @return array
     */
    public static function parseCsvWithHeadersFromFile($file_name, $group_by, $sort_by, $delimiter = self::DEFAULT_DELIMITER)
    {
        $csv_data = \Utils\FileOperations::readFile($file_name);
        $csv_array = self::parseAndGroupCsv($csv_data, $group_by, $delimiter);
        $csv_array = self::sortBy($csv_array, $sort_by);

        return $csv_array;
    }

    /**
     * @param array $csv_data
     * @param string $group_by
     * @param string $delimiter
     * @return array
     */
    private static function parseAndGroupCsv($csv_data, $group_by, $delimiter)
    {
        $csv_array = [];
        $keys = explode(self::DEFAULT_DELIMITER, rtrim(array_shift($csv_data)));
        for ($i = 0; $i < count($csv_data); $i++) {
            $data = str_getcsv($csv_data[$i], $delimiter);
            $csv_array[] = self::groupBy($keys, $data, $group_by);
        }

        return $csv_array;
    }

    /**
     * @param array $keys
     * @param array $data
     * @param string $group_by
     * @return array
     */
    private static function groupBy($keys, $data, $group_by)
    {
        $group_by_index = 0;
        foreach ($keys as $index => $key) {
            if ($key == $group_by) {
                unset($keys[$index]);
                array_unshift($keys, $key);
                $group_by_index = $index;
            }
        }
        foreach ($data as $index => $item) {
            if ($index == $group_by_index) {
                unset($data[$index]);
                array_unshift($data, $item);
            }
        }

        return array_combine($keys, $data);
    }

    /**
     * Sort csv_array with header by header
     *
     * @param array $csv_array
     * @param string $sort_by
     * @return array
     */
    private static function sortBy($csv_array, $sort_by)
    {
        $tmp_array = [];
        $result_array = [];

        foreach ($csv_array as $index => $item) {
            $tmp_array[$index] = $item[$sort_by];
        }
        sort($tmp_array);
        $i = 0;
        while ($i < count($tmp_array)) {
            foreach ($csv_array as $index => $item) {
                if ($tmp_array[$i] === $item[$sort_by]) {
                    $result_array[$i] = $item;
                    $i++;
                }
            }
        }

        return $result_array;
    }
}
