<?php

namespace App\Interfaces;

interface TestInterface
{
    public function getAllTasks();

    public function create();

    public function find();

    public function update();

    public function delete();
}
