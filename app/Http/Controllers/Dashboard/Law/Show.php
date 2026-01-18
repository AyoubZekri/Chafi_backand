<?php

namespace App\Http\Controllers\Dashboard\Law;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Law;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function show()
    {
        try {
            $data = Law::orderBy('index', 'asc')->get(); // ترتيب تصاعدي حسب index_link
            return Respons::success(
                 $data
            );

        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }

}
