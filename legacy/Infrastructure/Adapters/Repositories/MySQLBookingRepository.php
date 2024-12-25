<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Repositories;

use App\Domain\Booking\BookingRepository;

final class MySQLBookingRepository extends BaseRepository implements BookingRepository
{
    /**
     * Referencia a la clase base
     * @var \BookingRepository
     */
    private static $_reference = null;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0)
    {
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingRepository
     */
    public static function GetInstance($project = 0, $service = 0)
    {
        if (BookingRepository::$_reference == null) {
            BookingRepository::$_reference =
                    new \BookingRepository($project, $service);
        }
        return BookingRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseAggregate
     */
    public function GetAggregate($project = 0, $service = 0)
    {
        // Instanciar agregado
        $agg = new \BookingAggregate($project, $service);
        // Información del proyecto
        $agg->Project = $this->Read("Project", $project);
        // Tablas maestras
        $agg->States = $this->Get("State");
        $agg->Turns = $this->Get("Turn");
        $agg->Slots = $this->Get("Slot");
        $agg->BookingSources = $this->Get("BookingSource");
        // Información filtrada por proyecto
        $filter = [ "Project" => $project ];

        $configs = $this->GetByFilter("ConfigurationService", $filter);
        $agg->Configuration = (empty($configs))
                ? new \ConfigurationService() : $configs[0];

        $agg->Places = $this->GetByFilter("Place", $filter);
        $agg->Blocks = $this->GetByFilter("Block", $filter);
        $agg->Configurations = $this->GetByFilter("Configuration", $filter);
        $agg->Offers = $this->GetByFilter("Offer", $filter);
        $agg->OffersEvents = $this->GetByFilter("OfferEvent", $filter);
        foreach ($agg->Offers as $offer) {
            $filtroOferta = ["Offer" => $offer->Id];
            $offer->Config =  $this->GetByFilter("OfferConfig", $filtroOferta);
        }

        $agg->OffersShare = $this->GetByFilter("OfferShareDTO", $filter);

        $agg->TurnsShare = $this->GetByFilter("TurnShareDTO", $filter);

        $agg->SetAggregate();

        return $agg;
    }

    /**
     * Obtiene la referencia a la entidad cliente de la reserva
     * @param \Booking $entity Referencia a la reserva actual
     * @param boolean $advertising Flag para indicar si el cliente quiere publicidad
     * @return int Identidad del cliente
     */
    public function GetClient($entity = null, $advertising = false)
    {
        $filter = ["Project" => $entity->Project ];
        // Buscar el registro de cliente
        if (empty($entity->Email)) {
            $filter["Phone"] = "%$entity->Phone%";
        } else {
            $filter["Email"] = "%$entity->Email%";
        }
        $clients = $this->Dao->GetByFilter("Client", $filter);
        $client = (empty($clients)) ? null : $clients[0];
        // Crear el registro si no existe
        if ($client == null) {
            $client = new \Client();
            $client->Project = $entity->Project;
            $client->Name = $entity->ClientName;
            $client->Email = $entity->Email;
            $client->Phone = $entity->Phone;
            $client->Advertising = $advertising;
            $client->Id = $this->Dao->Create($client);
        } else {
            if ($client->Advertising == false) {
                $client->Advertising = $advertising;
                $this->Dao->Update($client);
            }
        }
        return $client->Id;
    }

    /**
     * Genera el registro de notificación de una reserva
     * @param \Booking $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($entity = null, $subject = "")
    {
        if ($entity != null) {
            $bookingDTO = $this->Read("BookingNotificationDTO", $entity->Id);
            if ($bookingDTO != null) {
                $date = new \DateTime($bookingDTO->Date);
                $bookingDTO->ClientName = $bookingDTO->Name;
                $bookingDTO->Date = strftime("%A %d de %B del %Y", $date->getTimestamp());
                $bookingDTO->Turn = $bookingDTO->Start;
                $bookingDTO->Offer = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Title : "Sin oferta";
                $bookingDTO->OfferTerms = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Terms : "";
                $bookingDTO->OfferDesc = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Description : "";
                return $this->RegisterNotification($bookingDTO, $subject);
            }
        }
        return false;
    }

    /**
     * Crea el registro de la notificación con la información de
     * la reserva y la tipología indicada.
     * @param \BookingNotificationDTO $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    private function RegisterNotification($entity = null, $subject = "")
    {
        if ($entity != null && is_object($entity)) {
            $date = new \DateTime("NOW");
            $dto = new \Notification();
            $dto->Project = $this->IdProject;
            $dto->Service = $this->IdService;
            $dto->To = $entity->Email;
            $dto->Subject = $subject;
            $dto->Date = $date->format("y-m-d h:i:s");

            $entity->Ticket = $this->GetTicket($dto->To, $entity);
            $dto->Content = json_encode($entity);
            $this->Dao->Create($dto);

            $dto->To = "";
            $entity->Ticket = $this->GetTicket("", $entity);
            $dto->Content = json_encode($entity);
            $this->Dao->Create($dto);
            return true;
        }
        return false;
    }

    /**
     * Obtiene un ticket de validación para las notificaciones
     * @param string $user Destinatario de Ticket
     * @param \BookingNotificationDTO $dto Referencia a la reserva
     * @return string Ticket generado
     */
    private function GetTicket($user = "", $dto = "")
    {
        // Establecer el destinatario de la notificación
        if ($user == "") {
            $user = "admin";
        }
        // Array de parámetros del ticket
        $arr = ["User" => $user, "Project" => $dto->Project, "Id" => $dto->Id ];
        // Serialización de la información del ticket
        $text = json_encode($arr);
        // cifrado del ti
        return $this->fnEncrypt($text, "resbooking2015");
    }

    /**
     * Método para cifrar el texto pasado como argumento con la clave especificada
     * @param string $sValue Texto plano
     * @param string $sSecretKey clave de cifrado
     * @return string
     */
    private function fnEncrypt($sValue, $sSecretKey)
    {
        return base64_encode($sValue);
    }
}
