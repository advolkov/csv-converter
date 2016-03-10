# About
Simple php-based csv converter

# How to run program
To convert csv, for instance, to html, just execute the following command in your terminal:
>$ php convert_csv.php --input-csv=input.csv --output=output.html --format=html

Input csv should have headers on the first line and data on the others. Good example of input csv is:
>name,address,contact,phone
>Kremlin,"Russia, Moscow, Red Square, 1",Ivan Petrov,123-222-111


Supported formats:

- json
- xml
- html


The result could be grouped or sorted by header. To set grouping use option --group-by. To set sorting use option --sort-by.
For example:
>$ php convert_csv.php --input-csv=input.csv --output=output.html --format=html --group-by=\<header_name> --sort-by=\<header_name>

To see what's happening just add -v flag
>$ php convert_csv.php --input-csv=input.csv --output=output.html --format=html --group-by=\<header_name> --sort-by=\<header_name> -v

To get more information just execute
>$ php convert_csv.php -h

