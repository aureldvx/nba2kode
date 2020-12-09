<?php

namespace App\Utils;

class Response
{
    public function redirectToRoute(string $path): void
    {
        header("Location: $path", null, 302);
        die();
    }
}
