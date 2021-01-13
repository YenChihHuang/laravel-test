<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToDoListRequest;
use App\Http\Requests\ToDoListUpdateRequest;
use App\Http\Resources\ToDoListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ToDoListController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ToDoListResource::collection(auth()->user()->toDoLists->sortBy('deadline_at'));
    }

    /**
     * @param  ToDoListRequest  $request
     *
     * @return ToDoListResource
     */
    public function store(ToDoListRequest $request): ToDoListResource
    {
        $toDoList = auth()->user()->toDoLists()->create($request->all());

        return (new ToDoListResource($toDoList));
    }

    /**
     * @param $toDoListId
     *
     * @return ToDoListResource
     */
    public function show($toDoListId): ToDoListResource
    {
        $toDoList = auth()->user()->toDoLists()->firstWhere('id', $toDoListId);

        abort_if(empty($toDoList), Response::HTTP_NOT_FOUND);

        return new ToDoListResource($toDoList);
    }

    /**
     * @param  ToDoListUpdateRequest  $request
     * @param                         $toDoListId
     *
     * @return JsonResponse|object
     */
    public function update(ToDoListUpdateRequest $request, $toDoListId)
    {
        $toDoList = auth()->user()->toDoLists()->firstWhere('id', $toDoListId);

        abort_if(empty($toDoList), Response::HTTP_NOT_FOUND);

        $toDoList->update($request->all());

        return (new ToDoListResource($toDoList))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param $toDoListId
     *
     * @return JsonResponse
     */
    public function destroy($toDoListId): JsonResponse
    {
        $toDoList = auth()->user()->toDoLists()->firstWhere('id', $toDoListId);

        abort_if(empty($toDoList), Response::HTTP_NOT_FOUND);

        return response()->json([
            'message' => 'Deleted',
            'success' => true,
        ], Response::HTTP_NO_CONTENT);
    }
}
