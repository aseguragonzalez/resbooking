<?php

namespace App\Domain\Reservations;

class BookingServices implements IBookingServices
{
    protected ?object $repository = null;
    protected ?object $aggregate = null;
    protected array $result = [];

    /**
     * Comprobación sobre la existencia de la reserva solicitada
     * @param \Booking $entity Referencia a la reserva a registrar
     * @return boolean Resultado de la comprobación. true si la reserva
     * ya está registrada. false en caso contrario
     */
    public function exist(?object $entity = null): bool
    {
        $filter = [ "Project" => $entity->Project, "Turn" => $entity->Turn,
                "Date" => $entity->Date, "Diners" => $entity->Diners,
                "Email" => "%" . $entity->Email . "%", "Phone" => "%" . $entity->Phone . "%",
                "Offer" => $entity->Offer, "Place" => $entity->Place ];
        $reservas = $this->repository->GetByFilter("Booking", $filter);
        return !empty($reservas);
    }

    /**
     * Obtiene una instancia para el registro de actividad
     * @param \Booking $entity Referencia a la reserva
     * @return \Log
     */
    public function getActivity(?object $entity = null): ?object
    {
        $info = [ "REQUEST" => $_REQUEST, "Entity" => $entity];
        $date = new \DateTime("NOW");
        $log = new \Log();
        $log->booking = $entity->id;
        $log->address = $_SERVER["REMOTE_ADDR"];
        $log->information = json_encode($info);
        $log->date = $date->format("Y-m-d");
        return $log;
    }

    /**
     * Proceso de validación de la entidad Reserva
     * @param \Booking $entity Referencia a los datos de reserva
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function validate(?object $entity = null): bool|array
    {
        $this->result = [];
        $this->validateClientName($entity->ClientName);
        $this->validateDate($entity->Date);
        $this->validateDiners($entity->Diners);
        $this->validateEmail($entity->Email);
        $this->validatePhone($entity->Phone);
        $this->validatePlace($entity->Place);
        $this->validateTurn($entity->Turn, $entity->Date);
        $this->validateOffer($entity->Offer, $entity->Turn, $entity->Date);
        return empty($this->result) ? true : $this->result;
    }

    /**
     * Proceso de validación del estado de la reserva
     * @param int $id Identidad del estado a validar
     * @return boolean Resultado de la validación del estado
     */
    public function validateState(int $id = 0): bool
    {
        // Referencia al estado de reserva
        $state = $this->getById($this->aggregate->states, $id);
        // Validación
        return ($state != null);
    }

    /**
     * Validación del nombre del cliente
     * @param string $name Nombre del cliente
     */
    private function validateClientName(string $name = ""): void
    {
        if (empty($name)) {
            $this->result[] = -1;
        } elseif (!is_string($name)) {
            $this->result[] = -2;
        } elseif (strlen($name) > 100) {
            $this->result[] = -3;
        }
    }

    /**
     * Proceso de validación de e-mail
     * @param string $email email del cliente
     */
    private function validateEmail(string $email = ""): void
    {
        if (empty($email)) {
            $this->result[] = -4;
        } elseif (strlen($email) > 100) {
            $this->result[] = -6;
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->result[] = -5;
        }
    }

    /**
     * Proceso de validación del número de teléfono
     * @param string $phone Teléfono del cliente
     */
    private function validatePhone($phone = ""): void
    {
        if (empty($phone)) {
            $this->result[] = -7;
        } elseif (!is_string($phone)) {
            $this->result[] = -8;
        } elseif (strlen($phone) > 15) {
            $this->result[] = -9;
        }
    }

    /**
     * Proceso de validación del número de comensales
     * @param int $diners Cantidad de comensales
     */
    private function validateDiners($diners = 0): void
    {
        if (empty($diners)) {
            $this->result[] = -10;
        } elseif (is_numeric($diners) === false) {
            $this->result[] = -11;
        } elseif ($diners > $this->aggregate->MaxDiners) {
            $this->result[] = -12;
        } elseif ($diners < $this->aggregate->MinDiners) {
            $this->result[] = -13;
        }
    }

    /**
     * Proceso de validación de la fecha de reserva
     * @param string $sDate Fecha de la validación
     */
    private function validateDate($sDate = "")
    {
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]"
                . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";

        if (empty($sDate)) {
            $this->result[] = -14;
        } elseif (preg_match($regex, $sDate) != 1) {
            $this->result[] = -15;
        } else {
            try {
                $date = new \DateTime($sDate);
                $yesterday = new \DateTime("YESTERDAY");
                if ($date <= $yesterday) {
                    $this->result[] = -16;
                }
            } catch (Exception $e) {
                $this->result[] = -15;
            }
        }
    }

    /**
     * Validación del Espacio. Comprueba que el espacio está
     * asociado al proyecto actual
     * @param int $place Identidad del Espacio|Lugar
     */
    private function validatePlace($place = 0)
    {
        if (empty($place)) {
            $this->result[] = -17;
        } elseif (!is_numeric($place)) {
            $this->result[] = -18;
        } else {
            $filter = ["Project" =>
                $this->aggregate->IdProject,"Id" => $place ];
            $places = $this->getListByFilter(
                $this->aggregate->Places,
                $filter
            );
            if (empty($places)) {
                $this->result[] = -19;
            }
        }
    }

    /**
     * Validación del Turno. Comprueba que el turno está asociado
     * al proyecto actual para la fecha dada(date)
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     */
    private function validateTurn($turn = 0, $sDate = "")
    {
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]" . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";
        if (empty($turn)) {
            $this->result[] = -20;
        } elseif (!is_numeric($turn)) {
            $this->result[] = -21;
        } elseif (empty($sDate)) {
            $this->result[] = -14;
        } elseif (preg_match($regex, $sDate) != 1) {
            $this->result[] = -15;
        } elseif ($this->TurnIsBlock($turn, $sDate)) {
            $this->result[] = -22;
        } elseif ($this->TurnIsOpen($turn, $sDate) || $this->TurnIsConfig($turn, $sDate)) {
            if (!$this->TurnIsAlive($turn, $sDate)) {
                $this->result[] = -27;
            } elseif (!$this->validateTurnShare($turn, $sDate)) {
                $this->result[] = -28;
            }
        } else {
            $this->result[] = -23;
        }
    }

    /**
     * Filtro para validar los turnos activos por la hora de reserva
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean resultado de la validación
     */
    private function turnIsAlive($id = 0, $sDate = "")
    {
        // Comprobar si es necesario validar el turno
        $date = new \DateTime($sDate);
        $current = new \DateTime("NOW");
        $validar = (intval($date->format("d")) == intval($current->format("d")))
                && (intval($date->format("m")) == intval($current->format("m")))
                && (intval($date->format("Y")) == intval($current->format("Y")));
        if ($validar == false) {
            return true;
        }
        // Proceso de validación del turno
        $turn = $this->getById($this->aggregate->Turns, $id);
        if ($turn != null && $turn instanceof \Turn) {
            $start = substr($turn->Start, 0, 5);
            $startParts = explode(":", $start);
            $H = intval($current->format("H"));
            $h = intval($startParts[0]);
            if ($H < $h) {
                return true;
            } elseif ($H == $h) {
                $M = intval($current->format("i")) + 20;
                $m = intval($startParts[1]);
                return $M < $m;
            }
        }
        return false;
    }

    /**
     * Comprueba si el turno está bloqueado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function turnIsBlock($turn = 0, $sDate = "")
    {
        $blocksFilter = ["Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 0];
        $blocks = $this->getListByFilter(
            $this->aggregate->Blocks,
            $blocksFilter
        );
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está "abierto" en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function turnIsOpen($turn = 0, $sDate = "")
    {
        $blocksFilter = [ "Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 1];
        $blocks = $this->getListByFilter(
            $this->aggregate->Blocks,
            $blocksFilter
        );
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está configurado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsConfig($turn = 0, $sDate = "")
    {
        $date = new \DateTime($sDate);
        $dayOfWeek = $date->format("N");
        $filter = ["Project" => $this->IdProject,
            "Day" => $dayOfWeek, "Turn" => $turn ];
        $configs = $this->getListByFilter(
            $this->aggregate->Configurations,
            $filter
        );
        return !empty($configs);
    }

    /**
     * Proceso para validar la cuota del turno
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la validación
     */
    private function validateTurnShare($id = 0, $sDate = "")
    {
        $filter = [ "Project" => $this->IdProject,
            "Turn" => $id, "Date" => $sDate ];
        $shares = $this->getListByFilter($this->aggregate->TurnsShare, $filter);
        $filterShares = array_filter($shares, function ($item) {
            return $item->DinersFree <= 0;
        });
        return empty($filterShares);
    }

    /**
     * Proceso de validación de la oferta seleccionada
     * @param int $offer Identidad de la oferta seleccionada
     * @param int $turn Identidad del turno seleccionado
     * @param string $sDate Fecha de la reserva
     */
    private function validateOffer($offer = 0, $turn = 0, $sDate = "")
    {
        if ($offer > 0) {
            $off = $this->getById($this->aggregate->Offers, $offer);
            if ($off == null) {
                $this->result[] = -24;
            }
            // Comprobamos si la oferta está abierta
            elseif ($this->OfferIsOpen($offer, $turn, $sDate) == true) {
                return;
            }
            // Comprobamos si la oferta está cerrada
            elseif ($this->OfferIsClose($offer, $turn, $sDate) == true) {
                $this->result[] = -26;
            } elseif (!$this->validateOfferDates($off, $sDate)) {
                $this->result[] = -25;
            } elseif (!$this->validateOfferConfig($off, $turn, $sDate)) {
                $this->result[] = -26;
            } elseif (!$this->validateOfferShare($off, $turn, $sDate)) {
                $this->result[] = -29;
            }
        }
    }

    /**
     * Proceso de validación de oferta de configuración
     * @param \Offer $offer Referencia a la oferta seleccionada
     * @param int $idturn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean
     */
    private function validateOfferConfig($offer = null, $idturn = 0, $sDate = "")
    {
        if ($offer != null) {
            $date = new \DateTime($sDate);
            $dayOfWeek = $date->format("N");
            $filter = [ "Turn" => $idturn, "Day" => $dayOfWeek ];
            $configs = json_decode($offer->Config);
            if ($configs == false) {
                $configs = [];
            }
            return !empty($this->getListByFilter($configs, $filter));
        }
        return false;
    }

    /**
     * Validación de las fechas de la oferta
     * @param \Offer $offer Referencia al objeto oferta
     * @param string $sDate Referencia a la fecha
     * @return boolean Resultado de la comprobación
     */
    private function validateOfferDates($offer = null, $sDate = "")
    {
        // Instanciar fecha
        $date = new \DateTime($sDate);

        $start = (isset($offer->Start)
                && $offer->Start != ""
                && $offer->Start != "0000-00-00 00:00:00")
                ? new DateTime($offer->Start) : null;

        $end = (isset($offer->End)
                && $offer->End != ""
                && $offer->End != "0000-00-00 00:00:00")
                ? new DateTime($offer->End) : null;

        $cmp_ok_1 = ($start == null
                || ($start != null && $date >= $start));

        $cmp_ok_2 = ($end == null
                || ($end != null && $date <= $end));

        return ($cmp_ok_1 && $cmp_ok_2);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Abierta"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsOpen($id = 0, $turn = 0, $sDate = "")
    {
        $filter = ["Project" => $this->IdProject, "Offer" => $id,
                "Turn" => $turn, "Date" => $sDate, "State" => 1];
        $events = $this->getListByFilter(
            $this->aggregate->AvailableOffersEvents,
            $filter
        );
        return !empty($events);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Cerrada"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsClose($id = 0, $turn = 0, $sDate = "")
    {
        $filter = ["Project" => $this->IdProject, "Offer" => $id,
                 "Turn" => $turn, "Date" => $sDate, "State" => 0];
        $events = $this->getListByFilter(
            $this->aggregate->AvailableOffersEvents,
            $filter
        );
        return !empty($events);
    }

    /**
     * Proceso de validación del cupo de oferta
     * @param int $id Identidad de la oferta
     * @param int $idTurn Identidad del turno
     * @param string $sDate Fecha de reserva
     * @return boolean Resultado de la validación
     */
    private function validateOfferShare($id = 0, $idTurn = 0, $sDate = "")
    {
        $turn = $this->getById($this->aggregate->Turns, $idTurn);
        $filterShares = [];
        if ($turn != null) {
            $filter = [ "Project" => $this->IdProject, "Offer" => $id,
                "Slot" => $turn->Slot, "Date" => $sDate ];
            $shares = $this->getListByFilter($this->aggregate->OffersShare, $filter);
            $filterShares = array_filter($shares, function ($item) {
                return $item->DinersFree <= 0;
            });
        }
        return empty($filterShares);
    }
}
