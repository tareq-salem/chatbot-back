<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="booking_index", methods={"GET"})
     */
    public function index(Security $security,BookingRepository $bookingRepository, UserRepository $userRepository): JsonResponse
    {
        $bookingInfo = [];
        $user = $userRepository->findOneByUserName($security->getUser()->getUsername());
        $bookings =$user->getBookings();
        foreach ($bookings as $list){
            $list = [ 'name' => $list->getUser()->getUsername(),
                      'status' => $list->getIsValidate(),
                      'isTerminated' => $list->getIsTerminated()];
            array_push($bookingInfo , $list);
        }


        $data = $this->get('serializer')->serialize($bookingInfo, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/new", name="booking_new", methods={"GET","POST"})
     */
    public function new(BookingRepository $bookingRepository, Request $request, Security $security, CartRepository $cartRepository, UserRepository $userRepository): Response
    {
        $currentUser = $security->getUser();

        $data = json_decode($request->getContent(), true);

        $isValidate = $data['isValidate'];
        $isTerminated = $data['isTerminated'];
        $cart_id = $data['cart_id'];
        $user_id = $data['user_id'];
        $user = $userRepository->find($user_id);
        $cartToBeBooked = $cartRepository->find($cart_id);
        
        if($currentUser === $user)
        {



            $cartsOfCurrentUser = $user->getCarts();
            foreach ($cartsOfCurrentUser as $cart)
            {
                $cartInBookings = $cartToBeBooked->getBooking();
                if ($cart == $cartToBeBooked && $cartInBookings == null )
                {
                        $theBooking = new Booking();
                        $theBooking->setIsValidate($isValidate);
                        $theBooking->setIsTerminated($isTerminated);
                        $theBooking->setUser($user);
                        $theBooking->addCart($cart);
                        $entityManager = $this->getDoctrine()->getManager();

                        $entityManager->persist($theBooking);
                        $entityManager->flush();

                        return new Response('added');
                }else
                {
                    return new Response('error');
                }

            }


        }else
        {
            return new Response('error');
        }

    }

    /**
     * @Route("/{id}", name="booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="booking_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index', [
                'id' => $booking->getId(),
            ]);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index');
    }
}
