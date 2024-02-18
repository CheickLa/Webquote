<?php

namespace App\Service;

use Fernet\Fernet;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class FernetService
{
  private $fernet;

  public function __construct(ContainerBagInterface $params)
  {
    $this->fernet = new Fernet($params->get('FERNET_KEY'));
  }

  public function encode($data)
  {
    return $this->fernet->encode($data);
  }

  public function decode($data)
  {
    return $this->fernet->decode($data);
  }
}
