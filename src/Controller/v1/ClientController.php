<?php

namespace App\Controller\v1;

use App\Service\v1\ClientService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/v1/client')]
class ClientController extends AbstractController
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    #[Route('/{slug}/user/all', name: 'client_show_user_all', methods: ['GET'])]
    public function showAllUsers(string $slug): JsonResponse
    {
        $showAll = $this->clientService->showAllUsers($slug, $this->getUser());

        return new JsonResponse($showAll, $showAll['status']);
    }

    #[Route('/{slug}/user/{id}', name: 'client_show_user_detail', methods: ['GET'])]
    public function showUserDetail(string $slug, int $id): JsonResponse
    {
        $showDetail = $this->clientService->showDetailUser($slug, $id, $this->getUser());

        return new JsonResponse($showDetail, $showDetail['status']);
    }
}
