<?php

namespace App\Repositories;


class ResourceRepository implements ResourceInterface
{
    private $model;

    /**
     * ResourceRepository constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getPaginated(int $nbr_per_page)
    {
        return $this->model->paginate($nbr_per_page);
    }

    public function store(array $validated)
    {
        return $this->model->create($validated);
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $validated)
    {
        $this->getById($id)->update($validated);
    }

    public function destroy(int $id)
    {
        $this->getById($id)->delete();
    }
}
