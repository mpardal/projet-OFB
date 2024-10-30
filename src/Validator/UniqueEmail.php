<?php
declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'Cette adresse e-mail est déjà utilisée.';
}