<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/{id}", name="cart_show", methods={"GET"})
     */
    public function show(Request $request):Response
    {
        
        $em = $this->getDoctrine()->getManager();
        $array = [];

        $id = $request->get('id');

        $cart = $em->getRepository(Cart::class)->find($id);
        

         if(!$cart) {
             throw $this->createNotFoundException(
                 'No cart found for id '.$id
             );
         }
         $array = [
            "cart_id" => $cart->getId(),
            "product_id" => $cart->getProduct()->getId(),
            "user_id" => $cart->getBooking()->getUser()->getId()
         ];
        // Serialize your object in Json
        $serializer = $this->get('serializer');
        $data = $serializer->serialize($array, 'json',[
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new JsonResponse($data ,200,[],true);
    }

    // /**
    //  * @Route("/{id}/edit", name="cart_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, Cart $cart): Response
    // {
    //     $form = $this->createForm(CartType::class, $cart);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('cart_index', [
    //             'id' => $cart->getId(),
    //         ]);
    //     }

    //     return $this->render('cart/edit.html.twig', [
    //         'cart' => $cart,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="cart_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Cart $cart): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
    }
}
