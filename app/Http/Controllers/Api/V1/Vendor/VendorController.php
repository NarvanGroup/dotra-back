<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\VendorResource;
use App\Traits\ResponderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    use ResponderTrait;

    public function getProfile(Request $request): JsonResponse
    {
        return $this->response(VendorResource::make($request->user()), 'Profile fetched successfully');
    }

    public function storeProfile(Request $request): JsonResponse
    {
        return $this->response(VendorResource::make($request->user()), 'Profile fetched successfully');
    }

    public function notifications(Request $request): JsonResponse
    {
        return $this->response(NotificationResource::collection($request->user()->notifications));
    }

    public function unreadNotifications(Request $request): JsonResponse
    {
        return $this->response(NotificationResource::collection($request->user()->unreadNotifications));
    }

    public function markAsReadNotification(Request $request, string $notificationId): JsonResponse
    {
        $request->user()->notifications()->findOrFail($notificationId)->update(['read_at' => now()]);
        return $this->responseSuccessful('عملیات با موفقیت انجام شد');
    }

    public function markAsUnreadNotification(Request $request, string $notificationId): JsonResponse
    {
        $request->user()->notifications()->findOrFail($notificationId)->update(['read_at' => null]);
        return $this->responseSuccessful('عملیات با موفقیت انجام شد');
    }

    public function markAsReadAllNotifications(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->responseSuccessful('عملیات با موفقیت انجام شد');
    }

    public function markAsUnreadAllNotifications(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsUnread();
        return $this->responseSuccessful('عملیات با موفقیت انجام شد');
    }


}
