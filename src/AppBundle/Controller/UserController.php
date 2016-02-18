<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Route("/profile")
 * @Security("has_role('ROLE_USER')")
 *
 * @package AppBundle\Controller
 */
class UserController extends Controller
{
    /**
     * Finds and displays a User entity.
     *
     * @param User $user
     *
     * @Route("/{id}", name="profile_show", options={"expose"=true})
     * @Method("GET")
     * @ParamConverter("user", class="AppBundle:User")
     *
     * @return Response
     */
    public function showAction(User $user)
    {
        return $this->render('user/profile.html.twig', array(
            'user' => $user,
        ));
    }
}
