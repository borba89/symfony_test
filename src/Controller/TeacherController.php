<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 09.08.22
 * Time: 17:04
 */

namespace App\Controller;


use App\Entity\Discipline;
use App\Entity\Teacher;
use App\Repository\DisciplineRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class TeacherController extends AbstractController
{

    /**
     * @Route("teacher/list", name="teachers")
     */

    public function index(TeacherRepository $repository)
    {
        $teachers = $repository->findAll();

        return $this->render('teacher/list.html.twig', ['teachers' => $teachers]);

    }

    /**
     * @Route("teacher/create", name="teacher_create")
     */
    public function create(Request $request, EntityManagerInterface $em, DisciplineRepository $disciplineRepository)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 20, 'maxMessage' => 'Maximal length {{ limit }}'])
            ])
            ->add('surname', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 20, 'maxMessage' => 'Maximal length {{ limit }}'])
            ])
            ->add('email', EmailType::class, [
                'constraints' => new Email(['message' => 'Incorrect email'])
            ])
            ->add('avatar', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload mimeType jpeg or png',
                    ])
                ],

            ])
            ->add('discipline', EntityType::class, [
                'class' => Discipline::class,
                'choice_label' => 'title',
                'choice_value' => function ($discipline) {
                    return $discipline ? $discipline->getId() : '';
                },
                'multiple' => true,

            ])

            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $teacher = new Teacher();

            $teacher->setName($data['name']);
            $teacher->setSurname($data['surname']);
            $teacher->setEmail($data['email']);
            $image = $request->files->get('form')['avatar'];

            $extensionImage = $image->guessExtension();
            $someNewImagename = md5(rand(1, 99999)) . '.' . $extensionImage;

            $imageDir = 'images/';
            $image->move($imageDir, $someNewImagename);

            $teacher->setAvatar($imageDir . $someNewImagename);
            foreach ($data['discipline'] as $item)
            {
                $teacher->addDiscipline($item);
            }


            $em->persist($teacher);
            $em->flush();

            return $this->redirectToRoute('teachers');
        }

        return $this->render('teacher/create.html.twig', ['form' => $form->createView()]);

    }


    /**
     * @Route("teacher/update/{id}", name="teacher_update")
     */
    public function update($id, Request $request, EntityManagerInterface $em, TeacherRepository $repository)
    {
        $teacher = $repository->find($id);

        $oldImage = $teacher->getAvatar();

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 20, 'maxMessage' => 'Maximal length {{ limit }}']),
                'data' => $teacher->getName(),
            ])
            ->add('surname', TextType::class, [
                'constraints' => new Length(['min' => 3, 'minMessage' => 'Minimal length {{ limit }}', 'max' => 20, 'maxMessage' => 'Maximal length {{ limit }}']),
                'data' => $teacher->getSurname(),
            ])
            ->add('email', EmailType::class, [
                'constraints' => new Email(['message' => 'Incorrect email']),
                'data' => $teacher->getEmail(),
            ])
            ->add('avatar', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload mimeType jpeg or png',
                    ])
                ],
            ])
            ->add('discipline', EntityType::class, [
                'class' => Discipline::class,
                'choice_label' => 'title',
                'choice_value' => function ($discipline) {
                    return $discipline ? $discipline->getId() : '';
                },
                'multiple' => true,
            ])

            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $teacher->setName($data['name']);
            $teacher->setSurname($data['surname']);
            $teacher->setEmail($data['email']);
            $image = $request->files->get('form')['avatar'];
            if($image != '') {
                $extensionImage = $image->guessExtension();

                $someNewImagename = md5(rand(1, 99999)) . '.' . $extensionImage;

                $imageDir = 'images/';
                $image->move($imageDir, $someNewImagename);

                $teacher->setAvatar($imageDir . $someNewImagename);
            }else{
                $teacher->setAvatar($oldImage);
            }
            foreach ($data['discipline'] as $item)
            {
                $teacher->addDiscipline($item);
            }

            $em->flush();

            return $this->redirectToRoute('teachers');
        }

        return $this->render('teacher/update.html.twig', ['form' => $form->createView()]);

    }


    /**
     * @Route("teacher/delete/{id}", name="teacher_delete")
     */
    public function delete($id, EntityManagerInterface $em, TeacherRepository $repository)
    {
        $teacher = $repository->find($id);

        foreach ($teacher->getDisciplines() as $item)
        {
            $teacher->removeDiscipline($item);
            $em->flush();
        }

        $em->remove($teacher);
        $em->flush();

        return $this->redirectToRoute('teachers');

    }


}