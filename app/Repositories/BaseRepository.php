<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IBaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseRepository implements IBaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function all($with = []): array
    {
        $perPage = request()->get('per_page') ?? 15;
        $data = $this->model::with($with)
            ->paginate($perPage);
        return $this->formatPaginate($data);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findById($id, $with = [])
    {
        return $this->model::with($with)->findOrFail($id);
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function update($id, $data)
    {
        $dataModel = $this->findById($id);
        $dataModel->fill($data);
        $dataModel->save();
        return $dataModel->fresh();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function destroy($id)
    {
        $dataModel = $this->findById($id);
        $dataModel->delete();
        return $dataModel;
    }

    public function formatPaginate(LengthAwarePaginator $data): array
    {
        return [
            'data' => $data->items(),
            'links' => [
                'next' => $data->nextPageUrl(),
                'prev' => $data->previousPageUrl(),
            ],
            'current_page' => $data->currentPage(),
            'per_page' => intval($data->perPage()),
            'total' => $data->total(),
            'last_page' => $data->lastPage()
        ];
    }
}
