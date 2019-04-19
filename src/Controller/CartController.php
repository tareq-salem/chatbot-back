<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**

     * @Route("/", name="cart_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Security $security, CartRepository $cartRepository): JsonResponse
    {
        $cartInfo = [];
        $user = $userRepository->findOneByUserName($security->getUser()->getUsername());
        $carts =$user->getCarts();

        foreach ($carts as $cart){
            $cart = [ 'name' => $cart->getProduct()->getName(),
                'price' => $cart->getProduct()->getPrice(),
                'qauntity' => $cart->getQuantity(),
                ];
            array_push($cartInfo , $cart);
        }

        $data = $this->get('serializer')->serialize($cartInfo, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/show", name="cart_show", methods={"GET"})
     */
    public function show(UserRepository $userRepository, Security $security, CartRepository $cartRepository,Request $request): Response
    {
        $cart = [];
        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneByUserName($security->getUser()->getUsername());
        $carts =$user->getCarts();
        $researchedCart = $cartRepository->find($data);

        foreach ($carts as $cart){
            if($cart === $researchedCart)
            {
                $cart = [ 'name' => $cart->getProduct()->getName(),
                          'price' => $cart->getProduct()->getPrice(),
                          'qauntity' => $cart->getQuantity(),
                        ];

                $data = $this->get('serializer')->serialize($cart, 'json');
                return new JsonResponse($data, 200, [], true);
            }else
            {
                return new Response('error');
            }

        }
        $data = $this->get('serializer')->serialize($cart, 'json');
        return new JsonResponse($data, 200, [], true);

    }
  
    /**
     * @Route("/add", name="cart_add", methods={"GET","POST"})
     */
    public function add(ProductRepository $productRepository, UserRepository $userRepository, Security $security, CartRepository $cartRepository,Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        $product_id = $data['product_id'];
        $quantity = $data['quantity'];
        $user_id = $data['user_id'];
        $product = $productRepository->find($product_id);
        $user = $userRepository->findOneByUserName($security->getUser()->getUsername());
        $currentUser_id = $user->getId();

        if($user_id == $currentUser_id)
        {
            $cart = new Cart();
            $cart->setProduct($product);
            $cart->setQuantity($quantity);
            $cart->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cart);
            $entityManager->flush();

            $maCart = 
            [
                'nomProduct' =>$cart->getProduct()->getName(),
                'quantity' =>$cart->getQuantity()
            ];

            $data = $this->get('serializer')->serialize($maCart, 'json');
            return new JsonResponse($data, 200, [], true);

        }else
        {
            return new Response('error');
        }


    }

    /**
     * @Route("/{id}", name="cart_delete", methods={"DELETE"})
     */
    public function delete( Cart $cart, UserRepository $userRepository, Security $security ): Response
    {
        $user = $userRepository->findOneByUserName($security->getUser()->getUsername());
        $currentUser_id = $user->getId();

        if ($currentUser_id == $cart->getUser()->getId() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cart);
            $entityManager->flush();
        }else{

            return new Response("Vous n'avez pas l'autorisation.");

        }
        return new Response("le panier " . $cart->getId() . " à été supprimé");
    }
}
