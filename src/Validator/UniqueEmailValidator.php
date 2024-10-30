<?php
declare(strict_types=1);

namespace App\Validator;

use App\Repository\AdminRepository;
use App\Repository\ExhibitorRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private AdminRepository $adminRepository,
                                private ExhibitorRepository $exhibitorRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Vérifie si l'email existe déjà dans la base de données
        $existingAdmin = $this->adminRepository->findOneBy(['email' => $value]);
        $existingExhibitor = $this->exhibitorRepository->findOneBy(['email' => $value]);

        if ($existingAdmin !== null || $existingExhibitor !== null) {
            // Ajoute une erreur si l'email existe déjà
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}