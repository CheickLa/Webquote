<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TextField
{
    public string $label;
    public string $type = 'text';
    public string $name;
    public string $value = '';
    public string $autocomplete = 'on';
    public bool $required = true;
    public string $min;
    public string $id;
}
