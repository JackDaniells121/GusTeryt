# :earth_africa: Poland Teryt API communication :house: :office: :hotel:
Main goal of this little project was to get current list of all Poland Cities.

This code is used to get API Teryt (https://api.stat.gov.pl/Home/TerytApi) data like Poland:
- Regions       (count = 16 - state mid 2020)
- Districts     (count = 380)
- Municipals    (count = 3798)
- Cities        (count = 80 k ?)

and store it into local database.

![Test Image 1](img/small_img1.png)

![Test Image 2](img/small_img2.png)

This code use class from https://github.com/gakowalski/teryt-webservices to connect to Teryt API
Thanks to @https://github.com/gakowalski :+1:
<br>
One more class was written to manage local database connection and store data from Teryt.

## :green_book: Use
Copy files, create database and run test.php 

# :zap: Installation

## 1.Database configuration :floppy_disk:
Create 5 tables in your database: 
- TerytRegion 
- TerytDistrict
- TerytMunicipal
- TerytCity
- TerytMunicipalType
- TerytCityType

To create use sql file create_tables.sql
In phpMyAdmin go to sql section and paste or use import function
*this will create new database name teryt

## 2.Set database connection in php :unlock:
Go to test.php file and set database connection credentials
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

*assume that You are on local system and have localdatabase mysql
*run script in Your browser=> localhost/GusTeryt/test.php

## Thanks To :pray:
- :watermelon: Jakub Ujwary 
- :green_apple: @https://github.com/gakowalski

## Limitations of use
You are free to use it. Enjoy
In code is sleep(1) function to prevent API request quota overlap, if You gain Your own API key you can experiment with disabling this limits

GUS Teryt Api has limitations:
Not registered users have lower request limits per minute (30?) per hour???, per day???
Registered users also have limits - per minute (60?)??? 

### How to get City X,Y? or population
Work in progress ...but You can use

- GUIK API to get coordinates of cities and then You must convert this coordinates using l4proj library.

- How to get Geometry of Regions, Districts, Municipals: https://capap.gugik.gov.pl/cat/org/gugik/dane/jednostki-administracyjne-f5cnk Download one file containing multiple geojson entries identified by TerytId

- How get Population ? You can use Another Teryt API BDL => https://api.stat.gov.pl/Home/BdlApi


## Minimal working example
Comment this lines in test.php

```php
    //$this->checkMunicipalTypes();
    //$this->checkCityTypes();
```

```php
    //$this->checkCity($m['TerytId'],$m['Id']);
```

![Test Image 3](img/teryt_example.png)

## Errors :bug:
It may take **hour** or more to complete execution of script. Im sorry but there could be errors on Your browser - due to API requests limit, like :

```
Notice: Undefined property: stdClass::$Miejscowosc in /Users/Apple/Documents/Vol/TerytUpdate/test.php on line 209
object(SoapFault)#84 (9) { ["message":protected]=> string(27) "Error Fetching http headers" ["string":"Exception":private]=> string(0) "" ["code":protected]=> int(0) ["file":protected]=> string(59) "/Users/Apple/Documents/Vol/TerytUpdate/TERYT_SoapClient.php" ["line":protected]=> int(33) ["trace":"Exception":private]=> array(6) { [0]=> array(6) { ["file"]=> string(59) "/Users/Apple/Documents/Vol/TerytUpdate/TERYT_SoapClient.php" ["line"]=> int(33) ["function"]=> string(11) "__doRequest" ["class"]=> string(10) "SoapClient" ["type"]=> string(2) "->" ["args"]=> array(5) { [0]=> string(1452) " MaxAutoton 4dRZ9Uhzn ZTgyYTg3MWNiZmFjM2ZkZTNmNjFjMDIxNDRjMjE3ZGM= 2020-08-27T16:42:46+00:00 http://tempuri.org/ITerytWs1/PobierzListeMiejscowosciWRodzajuGminy 02250342020-08-27 " [1]=> string(47) "https://uslugaterytws1.stat.gov.pl/TerytWs1.svc" [2]=> string(66) "http://tempuri.org/ITerytWs1/PobierzListeMiejscowosciWRodzajuGminy" [3]=> int(1) [4]=> int(0) } } [1]=> array(4) { ["function"]=> string(11) "__doRequest" ["class"]=> string(16) "TERYT_SoapClient" ["type"]=> string(2) "->" ["args"]=> array(5) { [0]=> string(1452) " MaxAutoton 4dRZ9Uhzn ZTgyYTg3MWNiZmFjM2ZkZTNmNjFjMDIxNDRjMjE3ZGM= 2020-08-27T16:42:46+00:00 http://tempuri.org/ITerytWs1/PobierzListeMiejscowosciWRodzajuGminy 02250342020-08-27 " [1]=> string(47) "https://uslugaterytws1.stat.gov.pl/TerytWs1.svc" [2]=> string(66) "http://tempuri.org/ITerytWs1/PobierzListeMiejscowosciWRodzajuGminy" [3]=> int(1) [4]=> int(0) } } [2]=> array(6) { ["file"]=> string(60) "/Users/Apple/Documents/Vol/TerytUpdate/TERYT_Webservices.php" ["line"]=> int(256) ["function"]=> string(6) "__call" ["class"]=> string(10) "SoapClient" ["type"]=> string(2) "->" ["args"]=> array(2) { [0]=> string(37) "PobierzListeMiejscowosciWRodzajuGminy" [1]=> array(1) { [0]=> array(5) { ["symbolWoj"]=> string(2) "02" ["symbolPow"]=> string(2) "25" ["symbolGmi"]=> string(2) "03" ["symbolRodz"]=> string(1) "4" ["DataStanu"]=> string(10) "2020-08-27" } } } } [3]=> array(6) { ["file"]=> string(47) "/Users/Apple/Documents/Vol/TerytUpdate/test.php" ["line"]=> int(208) ["function"]=> string(5) "towns" ["class"]=> string(17) "TERYT_Webservices" ["type"]=> string(2) "->" ["args"]=> array(4) { [0]=> string(2) "02" [1]=> string(2) "25" [2]=> string(2) "03" [3]=> string(1) "4" } } [4]=> array(6) { ["file"]=> string(47) "/Users/Apple/Documents/Vol/TerytUpdate/test.php" ["line"]=> int(43) ["function"]=> string(9) "checkCity" ["class"]=> string(4) "test" ["type"]=> string(2) "->" ["args"]=> array(2) { [0]=> string(7) "0225034" [1]=> string(13) "5f47b651c0960" } } [5]=> array(6) { ["file"]=> string(47) "/Users/Apple/Documents/Vol/TerytUpdate/test.php" ["line"]=> int(313) ["function"]=> string(11) "__construct" ["class"]=> string(4) "test" ["type"]=> string(2) "->" ["args"]=> array(0) { } } } ["previous":"Exception":private]=> NULL ["faultstring"]=> string(27) "Error Fetching http headers" ["faultcode"]=> string(4) "HTTP" }
Notice: Undefined property: stdClass::$Miejscowosc in /Users/Apple/Documents/Vol/TerytUpdate/test.php on line 209
```

If You have this type errors i recomend You to try more delay script or just run scipt next day