<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Cache;

class SystemDataController extends Controller
{
    public function getSystemData()
    {
        $data = Cache::remember('business_settings', 86400, function () {
            // Get all routes
            // $routes = collect(Route::getRoutes())->filter(function ($route) {
            //     // Exclude API routes and include only GET routes and include only index routes
            //     $routeName = $route->getName() ?? '';
            //     return strpos($route->getPrefix(), 'api') === false
            //         && in_array('GET', $route->methods())
            //         && strpos($routeName, 'index') !== false;;
            // })->map(function ($route) {
            //     // Get the route name and replace dots with underscores
            //     $routeName = $route->getName() ?? '';
            //     $slug = str_replace('.', ' ', $routeName);

            //     return [
            //         'url' => $route->uri(),
            //         'name' => $slug,
            //         'icon' => 'menu-icon tf-icons ti ti-settings', // Example icon, adjust as needed
            //         'slug' => $slug,
            //         'permission' => 'read_' . $slug
            //     ];
            // })->values();

            // Get all users
            $users = User::all()->map(function ($user) {
                return [
                    'name' => $user->name,
                    'subtitle' => $user->user_type,
                    'src' => $user->getFirstMedia('profile') ? $user->getFirstMedia('profile')->getUrl() : asset('assets/img/illustrations/NoImage.png'),
                    'url' => Route::has("$user->user_type.edit") ? route("$user->user_type.edit", $user->type_id) : '#',
                ];
            });

            // Get all files from the media table
            $files = Media::all()->map(function ($media) {
                return [
                    'name' => $media->name,
                    'subtitle' => $media->getCustomProperty('subtitle', ''), // Assuming you store custom properties
                    'src' => $media->getFullUrl(), // Adjust based on your storage setup
                    'meta' => round($media->size / 1024) . 'kb', // Convert size to kb
                    'url' => $media->getFullUrl()
                ];
            });
            $menu = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')), true)['menu'];
            function transformMenuArray($menuArray)
            {
                $transformedArray = [];

                foreach ($menuArray as $menuItem) {
                    if (!isset($menuItem['menuHeader'])) {
                        $slugParts = $menuItem['slug'];
                        if (!is_array($menuItem['slug']))
                            $slugParts = explode('.', $menuItem['slug']);
                        $lastPart = end($slugParts);
                        $permission = 'read_' . $lastPart;

                        $transformedArray[] = [
                            'url' => isset($menuItem['url']) ? $menuItem['url'] : route('home'),
                            'name' => $menuItem['name'],
                            'icon' => $menuItem['icon'],
                            'slug' => $menuItem['slug'],
                            'permission' => $permission
                        ];
                    }
                }

                return $transformedArray;
            }

            $transformedMenu = transformMenuArray($menu);

            return response()->json([
                'pages' => $transformedMenu,
                // 'pages' => $routes,
                'files' => $files,
                'members' => $users
            ]);
        });
        return $data;
    }
}
