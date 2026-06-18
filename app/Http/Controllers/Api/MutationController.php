<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MutationController extends Controller
{
    public function index()
    {
        $mutations = Mutation::with(["item", "user"])
            ->orderBy("created_at", "desc")
            ->get();

        return response()->json(
            $mutations->map(fn($m) => $this->transform($m)),
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== "staf") {
            return response()->json(
                [
                    "message" =>
                        "Hanya Staf Gudang yang boleh menginput mutasi barang.",
                ],
                403,
            );
        }

        $validator = Validator::make($request->all(), [
            "item_id" => "required|integer|exists:items,id",
            "type" => "required|string|in:IN,OUT",
            "quantity" => "required|integer|min:1",
            "note" => "nullable|string",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "message" => "Validasi gagal.",
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        return DB::transaction(function () use ($request) {
            $item = Item::lockForUpdate()->find($request->item_id);

            if ($request->type === "OUT" && $item->stock < $request->quantity) {
                return response()->json(
                    [
                        "message" =>
                            "Transaksi ditolak. Stok barang saat ini tidak mencukupi.",
                        "errors" => [
                            "quantity" => [
                                "Stok saat ini hanya tersedia " .
                                $item->stock .
                                " unit.",
                            ],
                        ],
                    ],
                    400,
                );
            }

            if ($request->type === "IN") {
                $item->stock += $request->quantity;
            } else {
                $item->stock -= $request->quantity;
            }
            $item->save();

            $mutation = Mutation::create([
                "item_id" => $request->item_id,
                "user_id" => $request->user()->id,
                "type" => $request->type,
                "quantity" => $request->quantity,
                "note" => $request->note,
            ]);

            return response()->json(
                [
                    "message" =>
                        "Transaksi mutasi berhasil dicatat dan stok telah diperbarui.",
                    "data" => $this->transform(
                        $mutation->load(["item", "user"]),
                    ),
                ],
                201,
            );
        });
    }

    private function transform(Mutation $mutation)
    {
        return [
            "id" => $mutation->id,
            "item_id" => $mutation->item_id,
            "kode_barang" => $mutation->item->code ?? "-",
            "nama_barang" => $mutation->item->name ?? "Barang Terhapus",
            "staf_gudang" => $mutation->user->name ?? "-",
            "jenis_mutasi" => $mutation->type,
            "jumlah" => $mutation->quantity,
            "keterangan" => $mutation->note,
            "tanggal_input" => $mutation->created_at->toIso8601String(),
        ];
    }
}
