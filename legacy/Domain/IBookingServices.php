<?php

namespace App\Domain\Reservations;

interface IBookingServices
{
    public function exist(object $entity = null): bool;

    public function getActivity(?object $entity = null): ?object;

    public function validate(?object $entity = null): bool|array;

    public function validateState(int $id = 0): bool;
}
