<?php

namespace App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseController extends Controller
{
  use ApiResponseTrait;

  protected $repository;
  protected string $storeRequestClass;
  protected string $updateRequestClass;
  protected string $resourceClass;
  protected ?string $collectionName = null;
  protected array $fileFields = [];
  protected string $uploadDisk = 'public';
  protected array $relations = [];

  public function __construct() {}

  protected function initService($repository, string $collectionName, array $fileFields = [], string $uploadDisk = 'public'): void
  {
    $this->repository = $repository;
    $this->collectionName = $collectionName;
    $this->fileFields = $fileFields;
    $this->uploadDisk = $uploadDisk;
  }

  // public function index(Request $request): JsonResponse
  // {
  //   try {
  //     $query = $this->repository->query();

  //     if ($search = $request->input('search')) {
  //       $query->where(function ($q) use ($search) {
  //         $table = $q->getModel()->getTable();
  //         $stringColumns = Schema::getColumnListing($table);
  //         $stringColumns = array_filter($stringColumns, function ($col) {
  //           return !in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at']);
  //         });
  //         foreach ($stringColumns as $column) {
  //           $q->orWhere($column, 'like', "%{$search}%");
  //         }
  //       });
  //     }

  //     $excluded = ['search', 'page', 'per_page'];
  //     foreach ($request->except($excluded) as $key => $value) {
  //       if ($value === null || $value === '') continue;
  //       if (Schema::hasColumn($query->getModel()->getTable(), $key)) {
  //         $query->where($key, $value);
  //       }
  //     }

  //     $perPage = $request->input('per_page', 10);
  //     $data = $query->latest()->paginate($perPage);

  //     if (class_exists($this->resourceClass)) {
  //       $data = $this->resourceClass::collection($data);
  //     }

  //     return $this->successResponsePaginate($data, "{$this->collectionName} list retrieved successfully");
  //   } catch (\Throwable $e) {
  //     Log::error("Error in {$this->collectionName} index: " . $e->getMessage());
  //     return $this->errorResponse("Failed to fetch data", 500);
  //   }
  // }

  public function index(Request $request): JsonResponse
  {
    try {
      $query = $this->repository->query();
      $model = $query->getModel();
      $table = $model->getTable();

      if (!empty($this->relations)) {
        $query->with($this->relations);
      }

      /*
        -----------------------
        Search
        -----------------------
        */
      if ($search = $request->input('search')) {
        $columns = array_diff(
          Schema::getColumnListing($table),
          ['id', 'created_at', 'updated_at', 'deleted_at']
        );

        $query->where(function ($q) use ($columns, $search) {
          foreach ($columns as $col) {
            $q->orWhere($col, 'like', "%$search%");
          }
        });
      }

      /*
        -----------------------
        Dynamic Filters
        -----------------------
        */
      $excluded = ['search', 'page', 'per_page', 'limit', 'offset', 'sort', 'order'];

      foreach ($request->except($excluded) as $key => $value) {

        if ($value === null || $value === '') continue;

        // ✅ column filter
        if (Schema::hasColumn($table, $key)) {
          $query->where($key, $value);
          continue;
        }

        // ✅ relation_id filter
        if (str_ends_with($key, '_id')) {
          $relation = str_replace('_id', '', $key);

          if (
            in_array($relation, $this->relations) &&
            method_exists($model, $relation)
          ) {
            $query->whereHas(
              $relation,
              fn($q) =>
              $q->where('id', $value)
            );
          }

          continue;
        }

        // ✅ relation.column filter
        if (str_contains($key, '.')) {
          [$relation, $col] = explode('.', $key);

          if (
            in_array($relation, $this->relations) &&
            method_exists($model, $relation)
          ) {
            $query->whereHas(
              $relation,
              fn($q) =>
              $q->where($col, $value)
            );
          }
        }
      }

      /*
        -----------------------
        Sorting
        -----------------------
        */
      $sort = $request->input('sort', 'created_at');
      $order = $request->input('order', 'desc');

      if (Schema::hasColumn($table, $sort)) {
        $query->orderBy($sort, $order);
      }

      /*
        -----------------------
        Pagination (always paginator)
        -----------------------
        */
      if ($request->has('limit')) {

        $limit = (int)$request->limit;
        $offset = (int)($request->offset ?? 0);
        $page = floor($offset / $limit) + 1;

        $data = $query->paginate($limit, ['*'], 'page', $page);
      } else {

        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage);
      }

      if (class_exists($this->resourceClass)) {
        $data = $this->resourceClass::collection($data);
      }

      return $this->successResponsePaginate(
        $data,
        "{$this->collectionName} list retrieved successfully"
      );
    } catch (\Throwable $e) {
      Log::error("Error in {$this->collectionName} index: " . $e->getMessage());
      return $this->errorResponse("Failed to fetch data", 500);
    }
  }





  public function show(int $id): JsonResponse
  {
    $record = $this->repository->find($id);

    if (!$record) {
      return $this->errorResponse("Record not found", 404);
    }

    return $this->successResponse(new $this->resourceClass($record), 'Record retrieved successfully');
  }

  public function store(Request $request): JsonResponse
  {
    $validated = app($this->storeRequestClass)->validated();

    try {
      DB::beginTransaction();

      $validated = $this->handleFileUploads($request, $validated);
      $record = $this->repository->create($validated);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error("Error creating {$this->collectionName}: " . $e->getMessage());

      return $this->errorResponse(
        "Failed to create {$this->collectionName}: " . $e->getMessage(),
        500
      );
    }

    return $this->successResponse(new $this->resourceClass($record), 'Record created successfully', 201);
  }

  public function update(Request $request, int $id): JsonResponse
  {
    $validated = app($this->updateRequestClass)->validated();

    $record = $this->repository->find($id);
    if (!$record) {
      return $this->errorResponse("Record not found", 404);
    }

    try {
      DB::beginTransaction();

      $validated = $this->handleFileUploads($request, $validated, $record);
      $record = $this->repository->update($id, $validated);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error("Error updating {$this->collectionName}: " . $e->getMessage());
      return $this->errorResponse("Failed to update record", 500);
    }

    return $this->successResponse(new $this->resourceClass($record), 'Record updated successfully');
  }

  // public function destroy($id): JsonResponse
  // {
  //   try {
  //     DB::beginTransaction();

  //     $deletedCount = $this->repository->delete($id);

  //     DB::commit();
  //   } catch (\Throwable $e) {
  //     DB::rollBack();
  //     Log::error("Error deleting {$this->collectionName}: " . $e->getMessage());
  //     return $this->errorResponse("Failed to delete record(s)", 500);
  //   }

  //   return $this->successResponse(null, "$deletedCount record(s) deleted successfully");
  // }
  public function destroy(Request $request): JsonResponse
  {
    try {
      DB::beginTransaction();

      $ids = $request->query('ids', null);
      if ($ids) {
        $idsArray = is_array($ids) ? $ids : explode(',', $ids);
      } else {
        return $this->errorResponse("No IDs provided", 400);
      }

      $deletedCount = $this->repository->deleteMultiple($idsArray);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error("Error deleting {$this->collectionName}: " . $e->getMessage());
      return $this->errorResponse("Can't delete record(s)", 500);
    }

    return $this->successResponse(
      null,
      "$deletedCount record(s) deleted successfully"
    );
  }


  protected function handleFileUploads(Request $request, array $validated, $existingRecord = null): array
  {
    if (empty($this->fileFields)) return $validated;

    foreach ($this->fileFields as $field) {
      if ($request->hasFile($field)) {
        try {
          $file = $request->file($field);
          $filename = time() . '_' . $file->getClientOriginalName();
          $path = $file->storeAs("uploads/{$this->collectionName}", $filename, $this->uploadDisk);

          if ($existingRecord && !empty($existingRecord->$field)) {
            Storage::disk($this->uploadDisk)
              ->delete('uploads/' . $this->collectionName . '/' . basename($existingRecord->$field));
          }

          /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
          // $disk = Storage::disk($this->uploadDisk);
          $validated[$field] = "https://astar.zayamrock.com/storage/app/public/" . $path;
        } catch (\Throwable $e) {
          Log::error("File upload failed for field [{$field}] in {$this->collectionName}: " . $e->getMessage());
        }
      }
    }

    return $validated;
  }
}
