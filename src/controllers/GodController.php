<?php

namespace cool_name_for_your_group\hw4\controllers\GodController;

use cool_name_for_your_group\hw4\views\LandingView\LandingView as LandingView;
use cool_name_for_your_group\hw4\views\ShowChartData as ShowChartData;
use cool_name_for_your_group\hw4\views\ShowJasonData as ShowJasonData;
use cool_name_for_your_group\hw4\models\ChartData as ChartData;


class GodController
{

    function loadLandingPage()
    {   $data[titleFlag] = 0;
        $landingView = new LandingView();
        $landingView->render($data);

    }
    function submitChart($data){
        $flag = 0; // use to check if the data is perfect or not
        $title = $data[0];
        $Values = explode(PHP_EOL,$data[1]);
        $subValues = array();
        foreach($Values as $val){
            $subValues[] = explode ("," ,$val);
        }
        $correctArray = array(); //  array to hold correct values, if the array is incorrect
        $finalData = array(); // array to hold and convert to jason in case the array is correct

        foreach($subValues as $subVal){
            $temp = array(); // temp array created
            $temp[] = $subVal[0];
            for($index  = 1; $index< count($subVal);$index++){
                $subVal[$index] = preg_replace("/[\n\r]/","", $subVal[$index]); // removes the new line character from the end of Y values;
                $subVal[$index] = preg_replace("/ /","", $subVal[$index]); // removes the new line character from the end of Y values;
                if((!is_numeric($subVal[$index])) && ($subVal[$index]!=null) && ($subVal[$index] != "")){
                    echo "<br/> value: ".$subVal[$index]."<br/>";
                    $flag =1;
                    $temp[] = 23;
                }
                else{
                    $temp[] = $subVal[$index];
                }
            }
            $correctArray[] = $temp;
            unset($temp);
        }
        print_r($correctArray);
        echo "<br/>";
        print_r($subValues);
        echo "<br/>Flag: ".$flag. "<br/>";


        if($flag == 0){// setting the key for json key value pair
            foreach($subValues as $subVal){
                $storeTemp = $subVal[0];
                unset($subVal[0]);
                $subVal = preg_replace("/[\n\r]/","", $subVal);
                $finalData[$storeTemp] = array_values($subVal);

            }
            $insertData = new ChartData();
            $data[1] = json_encode($finalData);
            $data[] = md5($data[1]);

            $statusData = $insertData->writeChartData($data);
            if($statusData == 1){
                echo "Value inserted";
            }
            $url = "http://localhost/Hw4/index.php?c=chart&a=show&arg1=LineGraph&arg2=".$data[2];
			header("Location: ".$url);
			exit();
        }
        else{
            $data[flag] = 1;
            $data[1] = $Values;
            $data[correctValues] = $correctArray;
            $landingView = new LandingView();
            $landingView->render($data);

        }
    }
    function loadLineGraph($hashValue){
        $fetchData = new ChartData();
        $statusData = array();
        $statusData = $fetchData->fetchChartData($hashValue);
        $statusData[chartType] = "LineGraph";
        $statusData[hvalue] = $statusData[0];
        $showData = new ShowChartData();
        $showData->render($statusData);
    }
    function loadPointGraph($hashValue){
        $fetchData = new ChartData();
        $statusData = array();
        $statusData = $fetchData->fetchChartData($hashValue);
        $statusData[chartType] = "PointGraph";
        $statusData[hvalue] = $statusData[0];

        $showData = new ShowChartData();
        $showData->render($statusData);
    }
    function loadHistogram($hashValue){
        $fetchData = new ChartData();
        $statusData = array();
        $statusData = $fetchData->fetchChartData($hashValue);
        $statusData[chartType] = "Histogram";
        $statusData[hvalue] = $statusData[0];
        $showData = new ShowChartData();
        $showData->render($statusData);
    }
    function loadXML($hashValue){
    }
    function loadjson($hashValue){
        $fetchData = new ChartData();
        $statusData = array();
        $statusData = $fetchData->fetchChartData($hashValue);
        $statusData[2] = json_decode($statusData[2]);
        $data = json_encode(array("HashValue"=> $statusData[0],"title"=>$statusData[1], "Data"=>$statusData[2] ));
        $finaldata = array();
        $finaldata[] = $data;
        $finaldata[chartType] = "json" ;
        $finaldata[hvalue] = $statusData[0];
        $showData = new ShowJasonData();
        $showData->render($finaldata);
    }
    function loadjsonp($dataValue){
        $fetchData = new ChartData();
        $statusData = array();
        $statusData = $fetchData->fetchChartData($dataValue[0]);
        $statusData[2] = json_decode($statusData[2]);
        $data = json_encode(array("HashValue"=> $statusData[0],"title"=>$statusData[1], "Data"=>$statusData[2] ));
        $finaldata = array();
        $finaldata[] = $data;
        $finaldata[] = $dataValue[1];
        $finaldata[flag] =1;
        $finaldata[chartType] ="jsonp";
        $finaldata[hvalue] = $statusData[0];
        $showData = new ShowJasonData();
        $showData->render($finaldata);

    }

}
