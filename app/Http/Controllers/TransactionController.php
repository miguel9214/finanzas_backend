<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DB::table("transactions as t")
            ->join("categories as c", "t.category_id", "=", "c.id")
            ->select("t.id", "t.description", "t.amount", "t.date", "t.receipt_image", "c.name as category_name");

        if ($search) {
            $query->where("t.description", "like", "%" . $search . "%");
        }

        $transactionList = $query->paginate(5);

        return response()->json(['message' => 'Transaction list', 'transactions' => $transactionList], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->all();

            if ($request->hasFile('receipt_image')) {
                $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            }

            $transaction = Transaction::create($data);
            return response()->json(['message' => 'Transaction created successfully', 'transaction' => $transaction], 201);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error creating transaction', 'error' => $e->errorInfo], 400);
        }
    }

    public function show($id)
    {
        $transaction = DB::table("transactions as t")
            ->join("categories as c", "t.category_id", "=", "c.id")
            ->select("t.id", "t.description", "t.amount", "t.date", "t.receipt_image", "c.name as category_name")
            ->where("t.id", $id)
            ->first();

        if ($transaction) {
            return response()->json(['message' => 'Transaction found', 'transaction' => $transaction]);
        } else {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $transaction = Transaction::find($id);
            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $data = $request->all();

            if ($request->hasFile('receipt_image')) {
                if ($transaction->receipt_image) {
                    Storage::disk('public')->delete($transaction->receipt_image);
                }
                $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            }

            $transaction->update($data);
            return response()->json(['message' => 'Transaction updated successfully', 'transaction' => $transaction], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error updating transaction', 'error' => $e->errorInfo], 400);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->receipt_image) {
            Storage::disk('public')->delete($transaction->receipt_image);
        }

        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully'], 200);
    }
}
