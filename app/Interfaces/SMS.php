<?php

namespace App\Interfaces;

interface SMS
{
    public function __construct(string|array $mobile , $message);
    public function send();
}
