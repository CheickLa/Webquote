<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class SidebarItem
{
  public string $value;
  public string $icon;
  public string $route;
}
