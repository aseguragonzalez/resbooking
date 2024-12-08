<?php

/*
 * Copyright (C) 2015 manager
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ZapperController
 *
 * @author manager
 */
class ZapperController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    public function Index(){
        return "";
    }

    public function SetState(){
        try{
            $parameters = $this->getParameters();
            $dal = new \ZapperDAL();
            $result = $dal->SetZapperState($parameters["reference"],
                    $parameters["posreference"], 1);
            $resultDTO = [
                "Error" => ($result < 0) ,
                "Result" => $result ,
                "Message" => ""
            ];
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            $obj = $this->ProcessJSONError("SetState" , $e);
            return $this->ReturnJSON($obj);
        }
    }

    private function getParameters(){
        $parameters = ["reference" => "#", "posreference" => "#" ];
        if(isset($_REQUEST["reference"])
                && isset($_REQUEST["posreference"])){
            $parameters["reference"] = $_REQUEST["reference"];
            $parameters["posreference"] = $_REQUEST["posreference"];
        }
        else{
            $json = file_get_contents('php://input');
            $data = json_decode($json, false);
            if(isset($data->reference) && isset($data->posreference)){
                $parameters["reference"] = $data->reference;
                $parameters["posreference"] = $data->posreference;
            }
        }

        return $parameters;
    }

    public function RequiredPrePay(){
        try{
            $request = $_REQUEST;
            $idProject = isset($request["project"]) ? $request["project"] : 0;
            $idOffer = isset($request["offer"]) ? $request["offer"] : 0;
            $diners = isset($request["diners"]) ? $request["diners"] : 0;
            $date = isset($request["date"]) ? $request["date"] : "--";
            $dal = new \ZapperDAL();
            $result = $dal->RequiredPrePay($idProject, $idOffer, $diners, $date);
            $resultDTO = [
                "Amount" => $result,
                "Error" => FALSE ,
                "Result" => ($result !== FALSE),
                "Message" => ""
            ];
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            $obj = $this->ProcessJSONError("RequiredPrePay" , $e);
            return $this->ReturnJSON($obj);
        }
    }

}
