<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Serializer;
use Error;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request,  UserPasswordEncoderInterface $encoder): Response
    {
       
       
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password =  $data['password'];
        $firstname =  $data['firstname'];;
        $lastname =  $data['lastname'];
        $address =  $data['address'];
        $apiToken = $data['apiToken'];
        
        $user = new User();
        
        
            $user->setEmail($email);
            $encoded  = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setAddress($address);
            $user->setRoles($user->getRoles());
            $user->setApiToken($apiToken);
            $em->persist($user);
            $em->flush();

      
        $data = $this->get('serializer')->serialize($user, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Security $security, UserRepository $userRepository, $id, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $security->getUser();

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password =  $data['password'];
        $firstname =  $data['firstname'];;
        $lastname =  $data['lastname'];
        $address =  $data['address'];
        $apiToken = $data['apiToken'];
        $userData = $userRepository->find($id);

         if ($userData === $user) {
             $userData->setEmail($email);
             $encoded  = $encoder->encodePassword($userData, $password);
             $userData->setPassword($encoded);
             $userData->setFirstname($firstname);
             $userData->setLastname($lastname);
             $userData->setAddress($address);
             $userData->setRoles($user->getRoles());
             $userData->setApiToken($apiToken);
             $em->persist($userData);
             $em->flush();

         }

        $data = $this->get('serializer')->serialize($userData , 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Security $security, User $user): Response
    {   
        $userConnecte = $security->getUser();
          
        if ($userConnecte  === $user) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }else{

            return new Response("Vous n'avez pas l'autorisation.");

        }
        

        return new Response("l'utilisateur " . $user->getUsername() .  " à été supprimé");
    }
}
