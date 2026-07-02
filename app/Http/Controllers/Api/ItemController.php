<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('created_at', 'desc')->get();

        return response()->json(
            $items->map(fn ($item) => $this->transform($item)),
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'staf') {
            return response()->json(
                ['message' => 'Hanya Staf Gudang yang boleh mengubah data.'],
                403,
            );
        }

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|unique:items,code',
            'nama_barang' => 'required|string',
            'kategori' => 'required|string',
            'lokasi_rak' => 'required|string',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'limit_stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $imageUrl = null;
        if ($request->hasFile('foto')) {
            $imageUrl = $request->file('foto')->store('item_images', $this->disk());
        }

        $item = Item::create([
            'code' => $request->kode_barang,
            'name' => $request->nama_barang,
            'category' => $request->kategori,
            'rack_location' => $request->lokasi_rak,
            'description' => $request->deskripsi,
            'stock' => $request->stok,
            'stock_limit' => $request->limit_stok,
            'image_url' => $imageUrl,
        ]);

        return response()->json(
            [
                'message' => 'Barang berhasil ditambahkan.',
                'data' => $this->transform($item),
            ],
            201,
        );
    }

    public function update(Request $request, int $id)
    {
        if ($request->user()->role !== 'staf') {
            return response()->json(
                ['message' => 'Hanya Staf Gudang yang boleh mengubah data.'],
                403,
            );
        }

        $item = Item::find($id);
        if (! $item) {
            return response()->json(
                ['message' => 'Barang tidak ditemukan.'],
                404,
            );
        }

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|unique:items,code,'.$id,
            'nama_barang' => 'required|string',
            'kategori' => 'required|string',
            'lokasi_rak' => 'required|string',
            'deskripsi' => 'nullable|string',
            'limit_stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        if ($request->hasFile('foto')) {
            if ($item->image_url) {
                Storage::disk($this->disk())->delete($item->image_url);
            }
            $item->image_url = $request->file('foto')->store('item_images', $this->disk());
        }

        $item->update([
            'code' => $request->kode_barang,
            'name' => $request->nama_barang,
            'category' => $request->kategori,
            'rack_location' => $request->lokasi_rak,
            'description' => $request->deskripsi,
            'stock_limit' => $request->limit_stok,
        ]);

        return response()->json([
            'message' => 'Barang berhasil diperbarui.',
            'data' => $this->transform($item),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        if ($request->user()->role !== 'staf') {
            return response()->json(
                ['message' => 'Hanya Staf Gudang yang boleh mengubah data.'],
                403,
            );
        }

        $item = Item::find($id);
        if (! $item) {
            return response()->json(
                ['message' => 'Barang tidak ditemukan.'],
                404,
            );
        }

        if ($item->image_url) {
            Storage::disk($this->disk())->delete($item->image_url);
        }

        $item->delete();

        return response()->json(['message' => 'Barang berhasil dihapus.']);
    }

    private function transform(Item $item)
    {
        return [
            'id' => $item->id,
            'kode_barang' => $item->code,
            'nama_barang' => $item->name,
            'kategori' => $item->category,
            'lokasi_rak' => $item->rack_location,
            'deskripsi' => $item->description,
            'stok' => $item->stock,
            'limit_stok' => $item->stock_limit,
            'foto_url' => $item->image_url
                ? Storage::disk($this->disk())->url($item->image_url)
                : null,
        ];
    }

    private function disk(): string
    {
        return config('filesystems.default', 'local');
    }
}
