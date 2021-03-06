<?php

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AntifloodValidator extends ConstraintValidator {

    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em) {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint) {

        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

//        $isFlood = $this->em
//                ->getRepository('OCPlatformBundle:Application')
//                ->isFlood($ip, 15) // Bien entendu, il faudrait écrire cette méthode isFlood, c'est pour l'exemple
//        ;
//
//        if ($isFlood) {
//            // C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message
//            $this->context->addViolation($constraint->message);
//        }
    }

}
