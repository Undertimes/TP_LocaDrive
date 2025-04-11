<?php

namespace App\Controller;

use App\Application\CreateUserUseCase;
use App\Application\GetUserByIdUseCase;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $createUserUseCase;
    private $getUserByIdUseCase;

    public function __construct(
        CreateUserUseCase $createUserUseCase,
        GetUserByIdUseCase $getUserByIdUseCase
    ) {
        $this->createUserUseCase = $createUserUseCase;
        $this->getUserByIdUseCase = $getUserByIdUseCase;
    }

    #[Route("/user/create", name: "user_create", methods: ["POST"])]
    public function createUser(Request $request): Response
    {
        $mail = $request->request->get("mail");
        $password = $request->request->get("password");
        $firstName = $request->request->get("firstName");
        $lastName = $request->request->get("lastName");
        $licenseDate = new DateTime($request->request->get("licenseDate"));

        try {
            $user = $this->createUserUseCase->execute($mail, $password, $firstName, $lastName, $licenseDate);
            return new Response($user->serializeToXml());
        } catch (Exception $exception) {
            return new Response($exception->getMessage());
        }
    }

    #[Route('/user/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(int $id): Response
    {

        try {
            $user = $this->getUserByIdUseCase->execute($id);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response($user->serializeToXml());
    }
}
