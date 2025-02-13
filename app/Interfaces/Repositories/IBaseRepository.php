<?php

namespace App\Interfaces\Repositories;

interface IBaseRepository
{
    public function all($with = []);

    public function findById($id, $with = []);

    public function store($data);

    public function update($id, $data);

    public function destroy($id);
}
