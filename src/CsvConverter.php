<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

namespace ConvertCsv;

require_once __DIR__ . "/../Utils/CsvParser.php";
require_once __DIR__ . "/../Utils/FileOperations.php";

class CsvConverter
{
    const
        OPT_HELP = "--help",
        OPT_H = "-h",
        OPT_V = "-v",
        OPT_VERBOSE = "--verbose",
        OPT_INPUT_FILE = "--input-csv",
        OPT_OUTPUT_FILE = "--output",
        OPT_FORMAT = "--format",
        OPT_SORTING = "--sorting",
        OPT_GROUP_BY = "--group-by",
        OPT_SORT_BY = "--sort-by";
    const WRITE_MODE = "w";
    const
        FORMAT_HTML = "html",
        FORMAT_JSON = "json",
        FORMAT_XML = "xml";
    const
        DEFAULT_GROUP_BY = "name",
        DEFAULT_SORT_BY = "name";

    private
        $input_file,
        $output_file,
        $format,
        $verbose,
        $group_by = self::DEFAULT_GROUP_BY,
        $sort_by = self::DEFAULT_SORT_BY;

    private $supported_formats = [
        self::FORMAT_HTML,
        self::FORMAT_JSON,
        self::FORMAT_XML,
    ];

    public function __construct()
    {
        if ($this->getHelp()) {
            $this->showHelp();
            exit(0);
        }
        $this->verbose = $this->getVerbose();
        $this->format = $this->getFormat();
        $this->output_file = $this->getOutputFile();
        $this->input_file = $this->getInputFile();
        $this->group_by = $this->getGroupBy();
        $this->sort_by = $this->getSortBy();
    }

    /**
     * This method parses csv from file, checks parsed data,
     * converts to one of available format according to data passed through command line
     * and writes converted data into output-file
     */
    public function convertCsv()
    {
        $result = "";

        if (!empty($this->input_file) && !empty($this->format) && !empty($this->output_file)) {
            $this->log("Starting...");
            $csv_array = \Utils\CsvParser::parseCsvWithHeadersFromFile($this->input_file, $this->group_by, $this->sort_by);
            $this->log("Csv data parsed...");

            if ($this->format == self::FORMAT_HTML) {
                $result = $this->csvArrayToHtml($csv_array);
            }
            if ($this->format == self::FORMAT_JSON) {
                $result = $this->csvArrayToJson($csv_array);
            }
            if ($this->format == self::FORMAT_XML) {
                $result = $this->csvArrayToXML($csv_array);
            }
            $this->log("Csv data converted...");

            \Utils\FileOperations::writeDataToFile($this->output_file, $result);
            $this->log("Done");
        }

        if (empty($this->input_file)) {
            echo("Please enter input csv file path using option [--input-csv=].\n");
        }
        if (empty($this->output_file)) {
            echo("Please enter output file path using option [--output=].\n");
        }
        if (empty($this->format)) {
            echo(
                "Please enter format using option [--format=]. Available formats: "
                . implode(", ", $this->supported_formats)
                . "\n"
            );
        }
    }

    /**
     * @param array $csv_array
     * @return string
     */
    private function csvArrayToJson($csv_array)
    {
        return json_encode($csv_array);
    }

    private function csvArrayToXML($csv_array)
    {
        $xml = new \SimpleXMLElement('<csv_data/>');
        for ($i = 0; $i < count($csv_array); $i++) {
            $hotel = $xml->addChild("csv_elem");
            foreach ($csv_array[$i] as $header => $csv_item) {
                $hotel->addChild($header, $csv_item);
            }
        }
        return $xml->asXML();
    }

    /**
     * @param array $csv_array
     * @return string
     */
    private function csvArrayToHtml($csv_array)
    {
        $html = '<head><meta charset=\'utf-8\'></head>';
        $html .= '<table>';
        $html .= '<tr>';
        $headers = array_keys($csv_array[0]);
        foreach($headers as $header){
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr>';

        foreach($csv_array as $key => $csv_item){
            $html .= '<tr>';
            foreach($csv_item as $header => $value){
                $html .= '<td>' . $value . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * @param string $msg
     */
    private function log($msg)
    {
        date_default_timezone_set('UTC');
        if ($this->verbose) {
            echo date("M d H:i:s." . str_pad(microtime() * 1e6, 6, "0", STR_PAD_LEFT)) . " [LOG] $msg\n";
        }
    }

    private function getFormat()
    {
        $format = $this->getCommandLineArgs(self::OPT_FORMAT);
        if (in_array(strtolower($format), $this->supported_formats)) {
            return $format;
        }
    }

    private function getOutputFile()
    {
        $output_file = $this->getCommandLineArgs(self::OPT_OUTPUT_FILE);
        if (!empty($output_file)) return $output_file;
    }

    private function getInputFile()
    {
        $input_file = $this->getCommandLineArgs(self::OPT_INPUT_FILE);
        if (!empty($input_file)) return $input_file;
    }

    private function getGroupBy()
    {
        $group_by = $this->getCommandLineArgs(self::OPT_GROUP_BY);
        if (!empty($group_by)) return $group_by;
    }

    private function getSortBy()
    {
        $sort_by = $this->getCommandLineArgs(self::OPT_SORT_BY);
        if (!empty($sort_by)) return $sort_by;
    }

    private function getVerbose()
    {
        $v = $this->getCommandLineArgs(self::OPT_V);
        $verbose = $this->getCommandLineArgs(self::OPT_VERBOSE);
        if (!empty($verbose)|| !empty($v)) return true;

        return false;
    }

    private function getHelp()
    {
        $help = $this->getCommandLineArgs(self::OPT_HELP);
        $h = $this->getCommandLineArgs(self::OPT_H);
        if (!empty($help) || !empty($h)) {
            return true;
        }

        return false;
    }

    private function showHelp()
    {
        echo(
            "This tool converts csv files to " . implode(", ", $this->supported_formats) . " format.\n"
                . "\nUsage: php convert_csv.php --input-csv=<path/to/input.csv> --output=<path/to/output> --format=<format> [other options]\n"
                . "\nOptions:\n"
                . self::OPT_INPUT_FILE . " [path/to/input_file.csv] set path to input csv-file\n"
                . self::OPT_FORMAT . " [" . implode(", ", $this->supported_formats) . "] set output format\n"
                . self::OPT_OUTPUT_FILE . " [path/to/output.format] set path to result file\n"
                . self::OPT_GROUP_BY . " [csv header name [default: name]] group elements by csv header\n"
                . self::OPT_SORT_BY . " [csv header name [default: name]] group elements by csv header\n"
                . self::OPT_VERBOSE . ", " . self::OPT_V . " [default: false] increase verbosity\n"
                . self::OPT_HELP . ", " . self::OPT_H . " show this help\n"
        );
    }

    /**
     * This method returns arg or its value if it is passed through command line
     *
     * @param string $arg_name
     * @return null|string
     */
    private function getCommandLineArgs($arg_name)
    {
        foreach ($_SERVER['argv'] as $arg) {
            if (strpos($arg, "$arg_name=") === 0) {
                return substr($arg, strlen($arg_name) + 1);
            }
            if (strpos($arg, "$arg_name") === 0) {
                return $arg;
            }
        }

        return null;
    }
}
