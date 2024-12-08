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

class ZapperConfiguration{
    public $ProjectId = 0;
    public $MerchantId = 0;
    public $SiteId = 0;
    public $TableName = "";
    public $WaiterName = "";
    public $CurrencyISO = "";
    public $MerchantName = "";
}

class ZapperOfferConfiguration {
    public $Id = 0;
    public $ProjectId = 0;
    public $OfferId = 0;
    public $Date = "";
    public $Diners = 0;
    public $AmountByDiner = 0;
}

class ZapperBooking{
    public $Id = 0;
    public $BookingId = 0;
    public $Reference = "";
    public $PosReference = "";
    public $Amount = 0;
    public $Image = "";
    public $State = 0;
}

class ZapperDAL{

    private $_log = null;

    private $_dao = null;

    private $_proxy = null;

    public function __construct() {
        $connectionString = ConfigurationManager::GetKey( "connectionString" );
        $oConnString = ConfigurationManager::GetConnectionStr($connectionString);
        $injector = Injector::GetInstance();
        $this->_log = $injector->Resolve( "ILogManager" );
        $this->_dao = $injector->Resolve( "IDataAccessObject" );
        $this->_dao->Configure($oConnString);
        $this->_proxy = ZapperClientWebService::GetInstance();
    }

    public function RequiredPrePay($idProject = 0, $idOffer = -1, $diners = -1, $date = null){
        $filter = ["ProjectId" => $idProject ];
        $lst = $this->_dao->GetByFilter("ZapperOfferConfiguration", $filter);
        if(count($lst) > 0){
            $items = array_filter($lst, function ($item)
                    use($diners, $idOffer, $date) {
                return (($item->Diners > 0 && $item->Diners <= $diners)
                       || ($item->OfferId > 0 && $item->OfferId == $idOffer)
                       || ($item->Date!= NULL && $item->Date == $date));
            });
            return count($items) > 0 ? current($items) : FALSE;
        }
        return FALSE;
    }

    public function SetZapperState($reference = "#", $posReference = "#", $state = -1){
        $filter = ["Reference" => $reference, "PosReference" => $posReference];
        $lst = $this->_dao->GetByFilter("ZapperBooking", $filter);
        if(isset($lst) && count($lst) > 0){
            $item = $lst[0];
            $item->State = $state;
            $this->_dao->Update($item);
            return TRUE;
        }
        return FALSE;
    }

    public function RegisterZapperBooking($booking = NULL){
        if($booking != NULL) {
            $filter = ["ProjectId" => $booking->Project ];
            $lst = $this->_dao->GetByFilter("ZapperOfferConfiguration", $filter);
            $configs = $this->_dao->GetByFilter("ZapperConfiguration", $filter);
            if (count($configs) == 0){
                return NULL;
            }
            $config = $configs[0];
            if(count($lst) > 0){
                $zConfig = $lst[0];
                $item = new \ZapperBooking();
                $item->Reference = $this->getReference();
                $item->PosReference = $this->getPosReference();
                $item->BookingId = $booking->Id;
                $item->Amount = $booking->Diners * $zConfig->AmountByDiner;
                $item->State = 0;
                $item->Id = $this->_dao->Create($item);
                $item->Image = $this->GetZapperQR($item, $config);
                $item->Image = $item->Reference ."-". $item->PosReference . ".png";
                $this->_dao->Update($item);
                $booking->Amount = $item->Amount;
                $booking->QrContent = $item->Reference ."-". $item->PosReference . ".png";
                return $booking;
            }
        }
        return $booking;
    }

    public function GetZapperQR($dto = null, $config = null){
        $data = $this->_proxy->GetQR($dto, $config);
        if ($data->Result === TRUE){
            return $data->Image;
        }
        return "";
    }

    private function getReference($maxlength = 30){
        mt_srand((double)microtime()*10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12);
        if(strlen($uuid) > $maxlength){
            $uuid = substr($uuid, 0, $maxlength);
        }
        return $uuid;
    }

    private function getPosReference($length = 16){
        $clientIp = $this->getClientIp();
        $uuid = $this->getReference(32);
        $hash = strtoupper(hash("SHA512", $clientIp.$uuid));
        $hlen = strlen($hash);
        if($hlen > $length){
            $start = rand(0, ($hlen - $length));
            $hash= substr($hash, $start, $length);
        }
        return $hash;
    }

    private function getClientIp() {
        $data = $_SERVER;
        $ipaddress = $this->getReference();
        if (isset($data['HTTP_CLIENT_IP'])) {
            $ipaddress = $data['HTTP_CLIENT_IP'];
        } else if (isset($data['HTTP_X_FORWARDED_FOR'])){
            $ipaddress = $data['HTTP_X_FORWARDED_FOR'];
        }
        else if (isset($data['HTTP_X_FORWARDED'])){
            $ipaddress = $data['HTTP_X_FORWARDED'];
        }
        else if (isset($data['HTTP_FORWARDED_FOR'])){
            $ipaddress = $data['HTTP_FORWARDED_FOR'];
        }
        else if (isset($data['HTTP_FORWARDED'])){
            $ipaddress = $data['HTTP_FORWARDED'];
        }
        else if (isset($data['REMOTE_ADDR'])){
            $ipaddress = $data['REMOTE_ADDR'];
        }
    }
}

class ZapperClientWebServiceResult{
    public $Result = FALSE;
    public $ErrorInfo = [];
    public $Image = "";
}

class ZapperClientWebService{

    private static $_singleton = null;
    private $_url = "";
    private $_path = "";
    private $_fileNameFormat = "{REFERENCE}-{posREFERENCE}.png";

    private function __construct() {
        $this->_url = ConfigurationManager::GetKey("qr-url");
        $this->_path = ConfigurationManager::GetKey("qr-path");
    }

    public function GetQR($dto = null, $config = null){
        $error = TRUE;
        $resultDto = new \ZapperClientWebServiceResult();
        $params = $this->getParams($dto, $config);
        if(($body = $this->getRequestBody($params)) !== FALSE){
            $this->saveContent($dto, $body);
            $error = ($resultDto->Image = base64_encode($body)) === FALSE;
						$resultDto->Result = !$error;
        }
        if($error){
            $resultDto->ErrorInfo = error_get_last();
        }

        return $resultDto;
    }

    private function getParams($dto = null, $config = null){
        return [
            "merchantId" => $config->MerchantId,
            "siteId" => $config->SiteId,
            "amount" => $dto->Amount,
            "reference" => $dto->Reference,
            "posReference" => $dto->PosReference,
            "tableName" => $config->TableName,
            "waiterName" => $config->WaiterName,
            "currencyISO" => $config->CurrencyISO,
            "merchantName" => $config->MerchantName,
        ];
    }

    private function getRequestBody($params = null){
        $body = NULL;
        if(($ch = curl_init($this->_url)) !== FALSE){
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            if(($response = curl_exec($ch)) !== FALSE){
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $body = substr($response, $header_size);
            }
            curl_close($ch);
        }
        return ($body == NULL) ? FALSE : $body;
    }

    private function saveContent($dto = null, $content = ""){
        $keys = [ "{REFERENCE}", "{posREFERENCE}" ];
        $values = [ $dto->Reference, $dto->PosReference ];
        $path = $this->_path . str_replace($keys, $values, $this->_fileNameFormat);
        return file_put_contents($path, $content);
    }

    public static function GetInstance(){
        if(ZapperClientWebService::$_singleton == null){
            ZapperClientWebService::$_singleton = new ZapperClientWebService();
        }
        return ZapperClientWebService::$_singleton;
    }
}
