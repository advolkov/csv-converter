<?php
/**
 * @author Anton Volkov <advolkov1@gmail.com>
 */

require_once "src/CsvConverter.php";

$Converter = new ConvertCsv\CsvConverter();
$Converter->convertCsv();
