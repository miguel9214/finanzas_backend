<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Listar todas las transacciones con su categoría asociada.
     */
    public function index()
    {
        return Transaction::with('category')->get();
    }

    /**
     * Guardar una nueva transacción.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'amount'        => 'required|numeric',
            'description'   => 'nullable|string',
            'date'          => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Guardar la imagen si se proporciona
        if ($request->hasFile('receipt_image')) {
            $imagePath = $request->file('receipt_image')->store('receipts', 'public');
            $data['receipt_image'] = $imagePath;
        }

        $transaction = Transaction::create($data);
        return response()->json($transaction, 201);
    }

    /**
     * Mostrar una transacción específica.
     */
    public function show($id)
    {
        $transaction = Transaction::with('category')->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transacción no encontrada'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Actualizar una transacción existente.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transacción no encontrada'], 404);
        }

        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'amount'        => 'required|numeric',
            'description'   => 'nullable|string',
            'date'          => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Si hay una nueva imagen, eliminar la anterior y guardar la nueva
        if ($request->hasFile('receipt_image')) {
            // Eliminar imagen anterior si existe
            if ($transaction->receipt_image) {
                Storage::disk('public')->delete($transaction->receipt_image);
            }

            // Guardar nueva imagen
            $imagePath = $request->file('receipt_image')->store('receipts', 'public');
            $data['receipt_image'] = $imagePath;
        }

        $transaction->update($data);
        return response()->json($transaction);
    }

    /**
     * Eliminar una transacción.
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transacción no encontrada'], 404);
        }

        // Eliminar la imagen asociada si existe
        if ($transaction->receipt_image) {
            Storage::disk('public')->delete($transaction->receipt_image);
        }

        $transaction->delete();
        return response()->json(['message' => 'Transacción eliminada correctamente']);
    }
}
