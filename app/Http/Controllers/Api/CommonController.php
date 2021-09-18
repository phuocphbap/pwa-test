<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getAllCategories()
    {
        $cats = getAllCategoriesByLevels();

        return response()->json([
            'status' => true,
            'data' => $cats,
        ]);
    }

    public function getSubCategories(Request $request)
    {
        $subCats = getAllCategoriesByLevels($request->parent_id);

        return response()->json([
            'status' => true,
            'data' => $subCats,
        ]);
    }

    public function getAllRegions()
    {
        $regions = getRegions();

        return response()->json([
            'status' => true,
            'data' => $regions,
        ]);
    }

    public function getReferralBonus()
    {
        $referralBonus = getReferralBonus();

        return response()->json([
            'status' => true,
            'data' => $referralBonus,
        ]);
    }
}
