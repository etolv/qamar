<?php

namespace App\Http\Controllers;

use App\Enums\DeleteActionsEnum;
use App\Helpers\Helpers;
use App\Http\Requests\DeleteObjectRequest;
use App\Services\DeleteService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    function __construct(
        protected DeleteService $deleteService,
    ) {}

    public function __invoke(DeleteObjectRequest $request)
    {
        $object = Helpers::getModelObject($request->objectType, $request->objectId, withTrashed: $request->filled('withTrashed') ? $request->withTrashed : true);
        $flag = false;
        if ($request->actionType === DeleteActionsEnum::SOFT_DELETE()->value) {
            $flag = $object->delete();
        }
        if ($request->actionType === DeleteActionsEnum::RESTORE_DELETED()->value) {
            $flag =  $object->restore();
        }
        if ($request->actionType === DeleteActionsEnum::FORCE_DELETE()->value) {
            $flag =  $object->forceDelete();
            // $this->deleteService->delete(get_class($object),$request->objectId);
        }

        if ($flag == true) {
            return response()->success(_t('Done Successfully'));
        } else {
            return response()->error(_t('Resource Not Found'));
        }
    }
}
