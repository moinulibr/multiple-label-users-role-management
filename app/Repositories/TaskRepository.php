<?php

namespace App\Repositories;

use App\Interfaces\TestInterface;

class TaskRepository implements TestInterface
{

    public function getAllTasks()
    {
        return "from repository";
    }

    public function create() {}

    public function find() {}

    public function update() {}

    public function delete() {}
}
