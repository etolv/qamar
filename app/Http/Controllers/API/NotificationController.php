<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexNotificationApiRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function __construct(private NotificationService $notificationService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notifications = $this->notificationService->all(user_id: auth()->id());
        $data = NotificationResource::collection($notifications);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
