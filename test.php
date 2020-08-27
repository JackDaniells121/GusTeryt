
<?php
/**
 https://api.stat.gov.pl/Home/TerytApi
*/
require 'TERYT_Webservices.php';
class test{
    private $servername = "localhost";
    private $username = "dbuser1";
    private $password = "1234";
    private $myDB   = "teryt";
    private $terytName = 'xxx';
    private $terytPassword = 'xxx';
    private $con;
    private $webservice;
    private $cities;

public function __construct(){
    $this->connectDB();
    $this->webservice = new TERYT_Webservices('TestPubliczny', '1234abcd', 'test', true);
    //$this->webservice = new TERYT_Webservices($this->terytName, $this->terytPassword, 'production', true);
    $this->cities = array_flip(array_column($this->dbGetTerytId('TerytCity'),'TerytId'));
    $this->checkMunicipalTypes();
    $this->checkCityTypes();
    $districts=[];
    $municipals=[];
    $step=0;

    foreach($this->checkRegions() as $r)
    {
        sleep(1);
        $progress = round(($step++ / count($this->checkRegions()))*100);
        $this->console_log('Current progress '.$progress.' %');
        
        foreach ($this->checkDistrict($r['TerytId'],$r['Id']) as $d) {  
            sleep(1);
            echo($d['TerytId']."<br>"); 

            foreach ($this->checkMunicipal($d['TerytId'],$d['Id']) as $m) {
                sleep(1);
                echo($m['TerytId']."<br>");  
                
                //Not add cities if it is part of city/ district of city
                if(!in_array($m['Type'],['8','9']))
                    $this->checkCity($m['TerytId'],$m['Id']);
            }
        }
        return;
    }
    //print_r(array_flip(array_column($this->dbGetTerytId('TerytRegion'),'TerytId')));
    //$this->console_log($this->webservice->division_types());
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
public function checkMunicipalTypes(){
    $result = $this->webservice->division_types();
    $types = $this->dbGet('TerytMunicipalTypes');
    $types_found =[];
    foreach ($result as $key => $r) {
        $found=false;
        foreach ($types as $type) {
            if($type['Type']==$key && $type['Name']==$r){
                $found=true;
                $types_found[]=$type['Id'];
                break 1;
            }
        }
        if(!$found)
            $this->dbInsert('TerytMunicipalTypes',[
                'Type'=>$key,
                'Name'=>$r
                ]); 
    }
    $types_in_db = array_values(array_column($types,'Id'));
    $to_delete = array_diff($types_in_db,$types_found);
    $this->dbDelete('TerytMunicipalTypes', $to_delete);
}
public function checkCityTypes(){
    $result = $this->webservice->town_types();
    $types = $this->dbGet('TerytCityTypes');
    $types_found =[];
    foreach ($result as $r) {
        $found=false;
        foreach ($types as $type) {
            if($type['Type']==$r->Symbol && $type['Name']==$r->Nazwa){
                $found=true;
                $types_found[]=$type['Id'];
                break 1;
            }
        }
        if(!$found)
            $this->dbInsert('TerytCityTypes',[
                'Type'=>$r->Symbol,
                'Name'=>$r->Nazwa
                ]); 
    }
    $types_in_db = array_values(array_column($types,'Id'));
    $to_delete = array_diff($types_in_db,$types_found);
    $this->dbDelete('TerytCityTypes', $to_delete);
}
public function checkRegions(){
    $result = $this->webservice->provinces();
    $regions = $this->dbGet('TerytRegion');
    $regions_found =[];
    
    foreach($result as $r){
        $found=false;
        foreach($regions as $region){
            if($region['TerytId']==$r->WOJ && $region['Name']==$r->NAZWA){
                $found = true;
                $regions_found[]=$region['Id'];
                break 1;
            }
        }
        if(!$found)
            $this->dbInsert('TerytRegion',[
                'TerytId'=>$r->WOJ,
                'Name'=>$r->NAZWA
                ]);    
         
    }
    $regions_in_db = array_values(array_column($regions,'Id'));
    $to_delete = array_diff($regions_in_db,$regions_found);
    $this->dbDelete('TerytRegion', $to_delete);

    return $this->dbGet('TerytRegion');
}
public function checkDistrict($RegionTerytId,$Region_Id){
    $result = $this->webservice->districts($RegionTerytId)->PobierzListePowiatowResult->JednostkaTerytorialna;
    $districts = $this->dbGet('TerytDistrict',"TerytRegion='$Region_Id'");
    $districts_found =[];

    foreach($result as $r){
        $found=false;
        foreach($districts as $dist){
            if($dist['TerytId']==$r->WOJ.$r->POW && $dist['Name']==$r->NAZWA){
                $found = true;
                $districts_found[]=$dist['Id'];
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
    $districts_in_db = array_values(array_column($districts,'Id'));
    $to_delete = array_diff($districts_in_db,$districts_found);
    $this->dbDelete('TerytDistrict', $to_delete);

    return $this->dbGet('TerytDistrict',"TerytRegion='$Region_Id'");
}
public function checkMunicipal($DistrictTerytId,$District_Id){
    try{
        $result = $this->webservice->communes($DistrictTerytId[0].$DistrictTerytId[1],$DistrictTerytId[2].$DistrictTerytId[3],true)->PobierzListeGminResult->JednostkaTerytorialna;
    } catch (SoapFault $exception) {
        var_dump($exception);
    }
    //$this->console_log($result);
    $municipals = $this->dbGet('TerytMunicipal',"TerytDistrict='$District_Id'");
    $municipals_found =[];

    if(!is_array($result)){
        $result2[]=$result;
        $result=$result2;
    }
        
    foreach($result as $r){
        $found=false;
        foreach($municipals as $mun){
            if($mun['TerytId']==$r->WOJ.$r->POW.$r->GMI.$r->RODZ 
                && $mun['Name']==$r->NAZWA
                && $mun['Type']==$r->RODZ
                ){
                    $found = true;
                    $municipals_found[]=$mun['Id'];
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
    $municipals_in_db = array_values(array_column($municipals,'Id'));
    $to_delete = array_diff($municipals_in_db,$municipals_found);
    $this->dbDelete('TerytRegion', $to_delete);
    return $this->dbGet('TerytMunicipal',"TerytDistrict='$District_Id'");
}
public function checkCity($MunicipalTerytId,$Municipal_Id){
    try{
        $result = $this->webservice->towns($MunicipalTerytId[0].$MunicipalTerytId[1],
                                            $MunicipalTerytId[2].$MunicipalTerytId[3],
                                            $MunicipalTerytId[4].$MunicipalTerytId[5],
                                            $MunicipalTerytId[6]
                                            )->PobierzListeMiejscowosciWRodzajuGminyResult->Miejscowosc;
    } catch (SoapFault $exception) {
        var_dump($exception);
    }
    
    //$this->console_log($result);
    if(!empty($result))
    {
        if(!is_array($result)){
            $result2[]=$result;
            $result=$result2;
        }
        foreach ($result as $r) {
            
            $found=false;
            if(!empty($this->cities[$r->Symbol])){
                //$this->console_log('City exists');
            }else{
                $this->dbInsert('TerytCity',[
                    'TerytId'=>$r->Symbol,
                    'Name'=>$r->Nazwa,
                    'TerytMunicipal'=>$Municipal_Id,
                    ]);   
            }
        }
    }
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
    //$this->console_log(['Insert prepared',$sql,$table_name]);
    $stmt->execute();
}
public function dbDelete($table_name, $id){
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "DELETE FROM $table_name WHERE Id='";
    
    if(is_array($id))
        $sql.=implode("' OR Id='",$id)."'";    
    else
        $sql.=$id."'";

    //$this->console_log(['Delete prepared',$sql]);
    $stmt = $this->conn->prepare($sql);
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
