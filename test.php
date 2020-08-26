/**
 https://api.stat.gov.pl/Home/TerytApi
*/
<?php
require 'TERYT_Webservices.php';
class test{
    private $servername = "localhost";
    private $username = "dbuser1";
    private $password = "1234";
    private $myDB   = "teryt";
    private $terytName = 'MaxAutoton';
    private $terytPassword = '4dRZ9Uhzn';
    private $con;
    private $webservice;

public function __construct(){
    $this->connectDB();
    //$this->webservice = new TERYT_Webservices('TestPubliczny', '1234abcd', 'test', true);
    $this->webservice = new TERYT_Webservices($this->$terytName, $teryt->$terytPassword, 'production', true);
    /*$districts=[];
    $municipals=[];
    foreach($this->checkRegions() as $r)
    {
        foreach ($this->checkDistrict($r['TerytId'],$r['Id']) as $d) {
            echo($d['TerytId']."<br>");
            $this->checkMunicipal($d['TerytId'],$d['Id']);
        }
    }*/
    print_r(array_flip(array_column($this->dbGetTerytId('TerytRegion'),'TerytId')));
}

public function connectDB(){
    try {
        $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->myDB", $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function checkRegions(){
    $result = $this->webservice->provinces();
    $regions = $this->dbGet('TerytRegion');

    foreach($result as $r){
        $found=false;
        foreach($regions as $region){
            if($region['TerytId']==$r->WOJ && $region['Name']==$r->NAZWA){
                $found = true;
                break 1;
            }
        }
        if(!$found)
            $this->dbInsert('TerytRegion',[
                'TerytId'=>$r->WOJ,
                'Name'=>$r->NAZWA
                ]);    
         
    }
    return $this->dbGet('TerytRegion');
}

public function checkDistrict($RegionTerytId,$Region_Id){
    $result = $this->webservice->districts($RegionTerytId)->PobierzListePowiatowResult->JednostkaTerytorialna;
    $districts = $this->dbGet('TerytDistrict',"TerytRegion='$Region_Id'");

    foreach($result as $r){
        $found=false;
        foreach($districts as $dist){
            if($dist['TerytId']==$r->WOJ.$r->POW && $dist['Name']==$r->NAZWA){
                $found = true;
                break 1;
            }
        }
        if(!$found)
            $this->dbInsert('TerytDistrict',[
                'TerytId'=>$r->WOJ.$r->POW,
                'Name'=>$r->NAZWA,
                'TerytRegion'=>$Region_Id
                ]);    
         
    }
    return $this->dbGet('TerytDistrict',"TerytRegion='$Region_Id'");
}

public function checkMunicipal($DistrictTerytId,$District_Id){
    try{
        $result = $this->webservice->communes($DistrictTerytId[0].$DistrictTerytId[1],$DistrictTerytId[2].$DistrictTerytId[3],true)->PobierzListeGminResult->JednostkaTerytorialna;
    } catch (SoapFault $exception) {
        var_dump($exception);
    }
    $this->console_log($result);
    $municipals = $this->dbGet('TerytMunicipal');

    if(!is_array($result))
        $this->console_log('Only one municipal ');
    else{
        foreach($result as $r){
            $found=false;
            foreach($municipals as $mun){
                if($mun['TerytId']==$r->WOJ.$r->POW.$r->GMI.$r->RODZ 
                    && $mun['Name']==$r->NAZWA
                    && $mun['Type']==$r->RODZ
                    ){
                        $found = true;
                        break 1;
                }
            }
            if(!$found)
                $this->dbInsert('TerytMunicipal',[
                    'TerytId'=>$r->WOJ.$r->POW.$r->GMI.$r->RODZ,
                    'Name'=>$r->NAZWA,
                    'TerytDistrict'=>$District_Id,
                    'Type'=>$r->RODZ
                    ]);    
            
        }
    }
    return $this->dbGet('TerytMunicipal');
}

public function dbGet($table_name,$where=null){
    $sql="SELECT * FROM $table_name ";
    if($where)
        $sql.="WHERE $where";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $p = $stmt->fetchAll();
    return($p);
}
public function dbGetTerytId($table_name,$where=null){
    $sql="SELECT TerytId FROM $table_name ";
    if($where)
        $sql.="WHERE $where";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $p = $stmt->fetchAll();
    return($p);
}

public function dbInsert($table_name, $values){
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $values['DateCreated']=date('Y-m=d');
    $values['Id']=uniqid();

    $sql = "INSERT INTO $table_name (".implode(',',array_keys($values)).") VALUES (:";
    $sql.=implode(',:',array_keys($values)).")";

    $stmt = $this->conn->prepare($sql);
    $ar=[];

    foreach($values as $key=>$v){
            
        $stmt->bindParam(":$key", $ar[$key]);
        
        $ar[$key]=$v; 
    }

    $stmt->execute();
}

public function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

/*
try {
        $result = $this->webservice->provinces();
        $this->console_log($result);
    } catch (SoapFault $exception) {
        var_dump($exception);
    }
*/
}

$new = new test();
