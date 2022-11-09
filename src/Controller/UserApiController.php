<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends AbstractController
{
    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signup(Request $request, SerializerInterface $serializer, DocumentManager $dm): JsonResponse
    {
        try {
            //Get the request body
            $userJson = $request->getContent();
            $user = $serializer->deserialize($userJson, User::class, 'json');

            //Check if the user is already exists
            $repository = $dm->getRepository(User::class);
            $existingUser = $repository->findBy(['email' => $user->getEmail()]);
            dd($existingUser);

            if ($existingUser) {
                return $this->json(['message' => 'User alredy exists! Please try to login'], 400);
            }

            //Signup the User
            $dm->persist($user);
            $dm->flush();

            return $this->json($user, 201);
        } catch (\Error $err) {
            return $this->json(['message' => $err->getMessage()], 500);
        }
    }

    #[Route('/api/signin', name: 'signin', methods: ['POST'])]
    public function login(Request $request, SerializerInterface $serializer, DocumentManager $dm)
    {
        try {
            //Get the request body
            $userJson = $request->getContent();
            $user = $serializer->deserialize($userJson, User::class, 'json');

            //Check if the user exists
            $repository = $dm->getRepository(User::class);
            $existingUser = $repository->findBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                return $this->json(['message' => "User doesn't exist! Please verify your Email Adress or try to Signup"], 400);
            }

            //Check if the passwords do match
            $inputPassword = $user->getPassword();

            // if ($inputPassword !== $existingUser->getPassword()) {
            //     return $this->json(['message' => 'Wrong password'], 400);
            // }
        } catch (\Error $err) {
            return $this->json(['message' => $err->getMessage()], 500);
        }
    }
}
