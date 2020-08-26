# :earth_africa: Poland Teryt API communication :house: :office: :hotel:
This code is used to get API Teryt data and store it into local database.

This code use class from https://github.com/gakowalski/teryt-webservices to connect to Teryt API
Thanks to @https://github.com/gakowalski :+1:
<br>
One more class was written to manage local database connection and store data from Teryt.

## :green_book: Use
Copy files and run ...... to save files in your database

# :zap: Installation

## 1.Database configuration :floppy_disk:
Create 5 tables in your database: 
- TerytRegion 
- TerytDistrict
- TerytMunicipal
- TerytCity
~~- TerytMunicipalType~~
~~- TerytCityType~~

To create use sql file create_tables.sql
In phpMyAdmin go to sql section and paste or use import function
*this will create new database name teryt

## 2.Set database connection in php :unlock:
Go to ....php file and set database connection credentials
```php
    private $servername = "localhost";
    private $username = "dbuser1";
    private $password = "1234";
    private $myDB   = "teryt"; 
```

## 3.Set Teryt API key or set to public key :key:
```php
    private $terytName = 'xxx';
    private $terytPassword = '4ssssssn';
```
*OR uncomment this line 
```php
    $this->webservice = new TERYT_Webservices('TestPubliczny', '1234abcd', 'test', true);
```
*and comment/disable this
```php
    //$this->webservice = new TERYT_Webservices($this->$terytName, $teryt->$terytPassword, 'production', true);
```

## Ready to go :+1:
Just run main php file. Simple as that :smiley:

## Thanks To :pray:
- :watermelon: Jakub Ujwary 
- :green_apple: @https://github.com/gakowalski