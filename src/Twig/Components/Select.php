<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Select 
{
    public string $label;
    public string $name;
    public string $id;
    public string $class = '';
}
