<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

namespace Utils;

class FileOperations
{
    const READ_MODE = "r";
    const WRITE_MODE = "w";
    const DEFAULT_READ_TIMEOUT = 30;

    /**
     * @param string $file_path
     * @param string $data
     */
    public static function writeDataToFile($file_path, $data)
    {
        $handle = fopen($file_path, self::WRITE_MODE);
        $result = fwrite($handle, $data);
        if (!$result) {
            die("Unable to write to file $file_path");
        }
        fclose($handle);
    }

    /**
     * @param string $file_name
     * @param int $timeout
     * @return array
     */
    public static function readFile($file_name, $timeout = self::DEFAULT_READ_TIMEOUT)
    {
        $res = [];
        $handle = fopen($file_name, self::READ_MODE);
        $start_time = time();
        while (!feof($handle)) {
            if (time() - $start_time > $timeout) {
                die("Timeout $timeout seconds has been reached while reading from $file_name");
            }
            $str = fgets($handle);
            if (empty($str)) continue;
            $res[] = $str;
        }
        fclose($handle);

        return $res;
    }
}
