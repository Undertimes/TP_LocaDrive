<?php

namespace App\Controller;

use App\Application\CreateUserUseCase;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class UserController extends AbstractController
{

    private $createUserUseCase;

    public function __construct(CreateUserUseCase $createUserUseCase)
    {
        $this->createUserUseCase = $createUserUseCase;
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
            $this->createUserUseCase->execute($mail, $password, $firstName, $lastName, $licenseDate);
        } catch (\Exception $exception) {
            return new Response($exception->getMessage());
        }

        $encoder = new XmlEncoder();

        return new Response($encoder->encode(['mail' => (string)$mail, 'password' => (string)$password, 'firstName' => (string)$firstName, 'lastName' => (string)$lastName, 'licenseDate' => $licenseDate->format(DateTimeInterface::ATOM)], 'xml'));
    }
}
