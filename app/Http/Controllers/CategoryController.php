<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Listar todas las categorías con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $itemsPerPage = $request->input('itemsPerPage', 5); // Número de elementos por página

        $query = DB::table("categories as c")->select(
            "c.id",
            "c.name",
            "c.type"
        );

        if ($search) {
            $query->where('c.name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($itemsPerPage);

        return response()->json(['message' => 'Lista de categorías', 'categories' => $categories], 200);
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'type' => 'required|in:income,expense',
        ]);

        try {
            $category = Category::create($request->all());
            return response()->json(['message' => 'Categoría creada exitosamente', 'category' => $category], 201);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al crear la categoría', 'error' => $e->errorInfo], 400);
        }
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json(['message' => 'Categoría encontrada', 'category' => $category], 200);
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'type' => 'required|in:income,expense',
        ]);

        try {
            $category->update($request->all());
            return response()->json(['message' => 'Categoría actualizada exitosamente', 'category' => $category], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al actualizar la categoría', 'error' => $e->errorInfo], 400);
        }
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        try {
            $category->delete();
            return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al eliminar la categoría', 'error' => $e->errorInfo], 400);
        }
    }
}
