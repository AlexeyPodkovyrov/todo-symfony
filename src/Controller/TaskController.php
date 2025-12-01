<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'task_list')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy([], ['id' => 'DESC']);
        
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/create', name: 'task_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            
            if (!empty($title)) {
                $task = new Task();
                $task->setTitle($title);
                $task->setCompleted(false);
                
                $entityManager->persist($task);
                $entityManager->flush();
                
                return $this->redirectToRoute('task_list');
            }
        }
        
        return $this->render('task/create.html.twig');
    }

    #[Route('/task/{id}/toggle', name: 'task_toggle')]
    public function toggle(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->setCompleted(!$task->isCompleted());
        $entityManager->flush();
        
        return $this->redirectToRoute('task_list');
    }
}