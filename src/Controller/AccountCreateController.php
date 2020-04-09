<?php

namespace App\Controller;
use App\Entity\Compte;

class AccountCreateController
{
    public function __construct()
    {

    }

    public function __invoke(Compte $data)
    {
        dd($data);
        return $data ;
    }
}
