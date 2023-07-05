<?php

declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\User\UserFetcher;
use App\Security\UserIdentity;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        /**
         * @var UserIdentity $currUser
         */
        $currUser = $this->getUser();

        try {
            $user = $this->users->findDetail($currUser->getId());
        } catch (Exception|\Doctrine\DBAL\Exception $e) {
        }

        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}
