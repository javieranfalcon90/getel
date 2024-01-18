<?php

namespace App\Services;

use App\Entity\Riesgo\Analisis;
use App\Entity\Riesgo\NoConformidad;
use App\Entity\Riesgo\Seguimiento;
use App\Entity\Seguridad\Usuario;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\User;

class SecurityVoter extends Voter
{

    private $em;
    private $token;

    const OWN = 'OWN';

    public function __construct($token, $entityManager)
    {
        $this->token = $token;
        $this->em = $entityManager;
    }

    protected function supports($attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::OWN])) {
            return false;
        }
            return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool{

        $user = $token->getUser();

        /* El usuario tiene q estar logueado */
        if (!$user instanceof User && !$user instanceof Usuario) {
            return false;
        }

        if(in_array('ROLE_PROCESO', $user->getRoles())){
            /* En caso de que sea otro usuario se rectifica si tiene o no permisos */
            if ($subject->getProceso() == $user->getProceso()){
                return (bool) true;
            }else{
                return (bool) false;
            }
        }


        return (bool) true;

    }

}