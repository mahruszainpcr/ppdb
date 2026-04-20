<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WilayahController extends Controller
{
    public function options(Request $request)
    {
        $validated = $request->validate([
            'level' => ['required', Rule::in(['province', 'regency', 'district', 'village'])],
            'parent_code' => ['nullable', 'string', 'max:20'],
        ]);

        $level = $validated['level'];
        $parentCode = $validated['parent_code'] ?? null;

        $dotCountByLevel = [
            'province' => 0,
            'regency' => 1,
            'district' => 2,
            'village' => 3,
        ];

        $requiredParentDotCount = [
            'regency' => 0,
            'district' => 1,
            'village' => 2,
        ];

        if (isset($requiredParentDotCount[$level])) {
            if (!$parentCode) {
                return response()->json([]);
            }

            if (substr_count($parentCode, '.') !== $requiredParentDotCount[$level]) {
                return response()->json([]);
            }
        }

        $query = Wilayah::query()
            ->select(['kode as code', 'nama as name'])
            ->whereRaw("(LENGTH(kode) - LENGTH(REPLACE(kode, '.', ''))) = ?", [$dotCountByLevel[$level]])
            ->orderBy('kode');

        if ($parentCode) {
            $query->where('kode', 'like', $parentCode . '.%');
        }

        return response()->json($query->get());
    }
}
