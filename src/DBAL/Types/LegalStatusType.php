<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class LegalStatusType extends AbstractEnumType
{
    public final const EI = 'EI';
    public final const EURL = 'EURL';
    public final const SARL = 'SARL';
    public final const SA = 'SA';
    public final const SAS = 'SAS';

    protected static array $choices = [
        self::EI => 'EI',
        self::EURL => 'EURL',
        self::SARL => 'SARL',
        self::SA => 'SA',
        self::SAS => 'SAS',
    ];
}
