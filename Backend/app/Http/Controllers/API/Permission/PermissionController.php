<?php

namespace App\Http\Controllers\API\Permission;

use App\Http\Controllers\API\Post\BaseController;
use App\Http\Resources\PermissionCollection;
use App\Models\Permission;
use App\Http\Resources\Permission as PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends BaseController
{
    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 15);
        return (new PermissionCollection(Permission::paginate($per_page)))->additional([
            'success' => true,
            'message' => 'Successfully retrieved permissions'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:255|unique:permissions'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['errors' => $validator->errors()]);
        }

        $input['user_id'] = auth()->user()->id;

        $permission = Permission::create($input);
        return $this->sendResponse(new PermissionResource($permission), 'Permission created successfully');
    }

    public function show($id) {
        $permission = Permission::find($id);

        if (is_null($permission)) {
            return $this->sendError('Permission does not exists.');
        }

        return $this->sendResponse(new PermissionResource($permission), 'Permission retrieved successfully.');
    }

    public function update(Request $request, Permission $permission) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:255|unique:permissions,name,' . $permission->name
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['errors' => $validator->errors()]);
        }

        $permission->name = $input['name'];

        $permission->save();

        return $this->sendResponse(new PermissionResource($permission), 'Permission updated successfully.');
    }

    public function destroy(Permission $permission) {
        $permission->delete();
        return $this->sendResponse([], 'Permission soft-deleted successfully.');
    }
}