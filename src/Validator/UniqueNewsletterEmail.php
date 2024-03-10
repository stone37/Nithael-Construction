<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueNewsletterEmail extends Constraint
{
    public string $message = 'Vous êtes déjà inscrit sur notre newsletter.';
}

