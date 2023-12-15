<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class IconButton 
{
  public string $id; 
  public string $class = '';
}
