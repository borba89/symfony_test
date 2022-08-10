<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 09.08.22
 * Time: 17:12
 */

namespace App\Controller;


use App\Entity\Discipline;
use App\Repository\DisciplineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class DisciplineController extends AbstractController
{
    /**
     * @Route("discipline/list", name="discipline")
     */

    public function index(DisciplineRepository $repository)
    {
        $disciplines = $repository->findAll();

        return $this->render('discipline/list.html.twig', ['disciplines' => $disciplines]);

    }

    /**
     * @Route("discipline/create", name="discipline_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 50, 'maxMessage' => 'Maximal length {{ limit }}'])
            ])
            ->add('datastartAt', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ])
            ->add('dataendAt', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $discipline = new Discipline();

            $discipline->setTitle($data['title']);
            $discipline->setDatastartAt($data['datastartAt']);
            $discipline->setDataendAt($data['dataendAt']);

            $em->persist($discipline);
            $em->flush();

            return $this->redirectToRoute('discipline');
        }

        return $this->render('discipline/create.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("discipline/update/{id}", name="discipline_update")
     */
    public function update($id, Request $request, EntityManagerInterface $em, DisciplineRepository $repository)
    {
        $discipline = $repository->find($id);
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 50, 'maxMessage' => 'Maximal length {{ limit }}']),
                'data' => $discipline->getTitle(),
            ])
            ->add('datastartAt', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'data' => $discipline->getDatastartAt(),
            ])
            ->add('dataendAt', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'data' => $discipline->getDatastartAt(),
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $discipline->setTitle($data['title']);
            $discipline->setDatastartAt($data['datastartAt']);
            $discipline->setDataendAt($data['dataendAt']);

            $em->persist($discipline);
            $em->flush();

            return $this->redirectToRoute('discipline');
        }

        return $this->render('discipline/update.html.twig', ['form' => $form->createView(), 'discipline' => $discipline]);

    }

    /**
     * @Route("discipline/delete/{id}", name="discipline_delete")
     */
    public function delete($id, EntityManagerInterface $em, DisciplineRepository $repository)
    {
        $discipline = $repository->find($id);

        $em->remove($discipline);
        $em->flush();

        return $this->redirectToRoute('discipline');

    }

}
