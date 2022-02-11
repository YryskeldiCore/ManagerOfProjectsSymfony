<?php
declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{

    /**
     * @Route("/connect/facebook", name="connect_facebook_start")
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    public function connect(ClientRegistry $clientRegistry):Response
    {
        return $clientRegistry->getClient('facebook_main')->redirect(['public_profile']);
    }

    /**
     * @Route("/connect/facebook/check", name="connect_facebook_check")
     * @return Response
     */
    public function check():Response
    {
        return $this->redirectToRoute('home');
    }
}