<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Source
{
    public readonly static Source $facebook = new Source(1, 'facebook');
    public readonly static Source $instagram = new Source(2, 'instagram');
    public readonly static Source $twitter = new Source(3, 'twitter');
    public readonly static Source $whatsapp = new Source(8, 'whatsapp');
    public readonly static Source $telegram = new Source(9, 'telegram');
    public readonly static Source $email = new Source(10, 'email');
    public readonly static Source $phone = new Source(12, 'phone');
    public readonly static Source $website = new Source(13, 'website');
    public readonly static Source $other = new Source(14, 'other');

    private function __construct(
        public readonly int $id,
        public readonly string $name,
    ) { }
}
