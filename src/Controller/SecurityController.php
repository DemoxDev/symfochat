<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Message;
use App\Form\MessageType;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/registration', name: 'app_registration', stateless: true)]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIsConnected(false);
            $user->setLastConnected(new \DateTimeImmutable());
            // Save the user to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Redirect to a success page or login page
            return $this->redirectToRoute('app_login');
        }
        
        // Return response
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/messages', name: 'app_messages')]
    public function messages(Request $request): Response
    {
        // Créez un nouveau message
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associez le message à l'utilisateur actuel
            $message->setUser($this->getUser());
            $message->setCreatedAt(new \DateTimeImmutable());

            // Enregistrez le message dans la base de données
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

        // Récupérez tous les messages de la base de données
        $messages = $this->entityManager->getRepository(Message::class)->findAll();

        // Retournez la réponse
        return $this->render('security/messages.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
        ]);
    }
}
