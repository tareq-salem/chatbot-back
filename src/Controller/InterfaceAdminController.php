<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InterfaceAdminController extends AbstractController
{
    /**
     * @Route("/admin/interface", name="interface_admin")
     */
    public function index()
    {
        return $this->render('interface_admin/index.html.twig', [
            'controller_name' => 'InterfaceAdminController',
        ]);
    }
}
