<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatboxController extends AbstractController
{
    #[Route('/chatbox', name: 'app_chatbox')]
    public function index(): Response
    {
        $message = ["message1", "message2", "message3", "message4", "message5", "message6",];
        return $this->render('chatbox/index.html.twig', [
            'message' => $message,
        ]);
    }
}
