<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 09.08.22
 * Time: 15:26
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('home/homepage.html.twig', []);
    }
}