<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class DashboardCard
{
    public string $backgroundColor;
    public string $iconBackgroundColor;
    public string $icon;
    public string $iconColor = 'text-white';
    public string $value;
    public string $valueColor = 'text-white';
    public string $label;
    public string $labelColor;
    public bool $small = false;
}
