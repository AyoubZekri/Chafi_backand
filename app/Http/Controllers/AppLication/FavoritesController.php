<?php

namespace App\Http\Controllers\AppLication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\TaxAndApp;
use App\Models\Different;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    public function show(Request $request)
    {
        // Expecting an array of favorites: [['item_id' => 1, 'item_type' => '8'], ...]
        $favorites = $request->input('favorites', []);
        
        $results = [];

        foreach ($favorites as $fav) {
            $itemId = $fav['item_id'];
            $itemType = $fav['item_type'];
            $data = null;

            // We identify the table/model based on itemType
            // Assuming 8 = Institution, 9 = TaxAndApp, 10 = Different, 11 = FAQ/Post (adjust as needed)
            // Or if item_type is the string name of the category
            
            if ($itemType == '8' || str_contains(strtolower($itemType), 'مؤسسات')) {
                $data = Institution::with('laws')->find($itemId);
            } elseif ($itemType == '9' || str_contains(strtolower($itemType), 'جبائية')) {
                $data = TaxAndApp::with('laws')->find($itemId);
            } elseif ($itemType == '10' || str_contains(strtolower($itemType), 'جزاءات')) {
                $data = Different::with('laws')->find($itemId);
            } else {
                // Try fetching from Posts (FAQs etc)
                $data = Post::find($itemId);
            }

            if ($data) {
                // Add the original itemType to the response so the frontend knows what it is
                $data->favorite_item_type = $itemType;
                $results[] = $data;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $results
        ]);
    }
    //
}
