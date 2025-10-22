<?php

namespace App\Http\Controllers;
use App\Traits\HttpResponses;
use App\Traits\HandlesExceptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\TestInterface;
use App\Repositories\TaskRepository;

class TestController extends Controller
{
    use HttpResponses, HandlesExceptions;

    /* protected TaskRepository $taskRepository;
    public function __construct(TaskRepository $taskRepository){
        $this->taskRepository = $taskRepository;
    } */

    protected TestInterface $taskInterface;
    public function __construct(TestInterface $taskInterface)
    {
        $this->taskInterface = $taskInterface;
    }

    public function index(Request $request)
    {
        //return $this->taskRepository->getAllTasks();
        return $this->taskInterface->getAllTasks();
    }
}
