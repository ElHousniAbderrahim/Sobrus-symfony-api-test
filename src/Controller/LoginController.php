<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // The JWT is automatically generated, just return a success response.
        return new JsonResponse(['status' => 'success'], 200);
    }
}
