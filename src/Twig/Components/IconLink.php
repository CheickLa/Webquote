<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class IconLink 
{
  public string $href;
  public string $id; 
  public string $class = '';
}
