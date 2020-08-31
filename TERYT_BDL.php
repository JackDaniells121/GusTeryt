<?php
/**
 https://api.stat.gov.pl/Home/TerytApi
*/
class TerytBDL{
    private $client_key='xxx';

    public function __construct(){
      
    }

    public function requestBDL($level,$unit_lvl,$variable,$page_size,$page=0){
        
        $url = 'https://bdl.stat.gov.pl/api/v1/';
        switch ($level) {
            case 1:
                $url.='parent-id='.$variable;
                break;
            /*case '2':
                $url.='Variables?subject-id='.$variable;
                break;*/
            case 3:
                $url.='data/by-variable/'.$variable;
                break;
        }
        $url.="?format=json&year=2019&page-size=$page_size&unit-level=$unit_lvl&page=$page";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //'Authorization: Bearer '.$accessToken,
            'X-ClientId: '.$this->client_key
        ));
        $contents = curl_exec($ch);
        return json_decode($contents);
    }

    public function getPopulation($unit_lvl,$variable){
        $results = $this->requestBDL(3,$unit_lvl,$variable,100);
        $total = $results->totalRecords;
        if($total >100){
            for($i=1;($i*100)< $total+100;$i++){
               
                $results2 = $this->requestBDL(3,$unit_lvl,199202,100,$i);
                $results->results=array_merge($results->results,$results2->results);
            } 
        }
        
        return $results->results;
    }

    public function getPopulationRegions(){
        $arr=$this->getPopulation(2,199206);
        $arr2=[];
        foreach ($arr as $v) {
            $TerytId=substr($v->id,2,2);
            $arr2[$TerytId]=$v->values[0]->val;
        }
        return $arr2;
    }
    public function getPopulationDistricts(){
        $arr=$this->getPopulation(5,199206);                      //gminy bez miast na prawach powiatu
        $arr=array_merge($arr, $this->getPopulation(5,199207));   //+gminy/miasta na prawach powiatu
        $arr2=[];
        foreach ($arr as $v) {
            $TerytId=substr($v->id,2,2).substr($v->id,7,2);
            $arr2[$TerytId]=$v->values[0]->val;
        }
        return $arr2;
    }
    public function getPopulationMunicipals(){
        $arr=$this->getPopulation(6,199206);
        $arr=array_merge($arr, $this->getPopulation(6,199207));   //+gminy/miasta na prawach powiatu
        $arr2=[];
        foreach ($arr as $v) {
            $TerytId=substr($v->id,2,2).substr($v->id,7,5);
            $arr2[$TerytId]=$v->values[0]->val;
        }
        return $arr2;
    }
}