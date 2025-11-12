<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Password;
use App\Notifications\NewUserSetPassword;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'is_active', 'phone')->with('roles:name');
    
        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
    
        $filters = json_decode($request->query('filters', '{}'), true);
        foreach ($filters as $field => $filter) {
            if (!isset($filter['filter'])) continue;
            $query->where($field, 'like', '%' . $filter['filter'] . '%');
        }
    
        $sorts = json_decode($request->query('sort', '[]'), true);
        foreach ($sorts as $sort) {
            $col = $sort['colId'] ?? null;
            $dir = $sort['sort'] ?? 'asc';
            if ($col) {
                $query->orderBy($col, $dir);
            }
        }
    
        $perPage = $request->query('per_page', 20);
    
        $users = $query->paginate($perPage)->appends($request->query());
    
        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select('id', 'name')->get();

        return Inertia::render('users/Form', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //TODO: Criar validação separada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'phone' => 'nullable|string|min:11|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt(Str::random(16)),
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        $token = Password::createToken($user);
    
        $user->notify(new NewUserSetPassword($token));

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('roles:id,name')->select('id', 'name', 'email', 'phone', 'is_active', 'email_verified_at', 'created_at', 'updated_at')->findOrFail($id);

        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles:id,name')->select('id', 'name', 'email', 'phone')->findOrFail($id);
        $roles = Role::select('id', 'name')->get();

        return Inertia::render('users/Form', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //TODO: Criar validação separada
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'phone' => 'nullable|string|min:11|max:20',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('users.show', $user->id)->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        if (!$user->delete()) {
            return redirect()->route('users.index')->with('error', 'Erro ao excluir o usuário.');
        }

        //TODO: Rever esse return / onConfirm no front (ta dando render duas vezes)
        return redirect()->back()->with('success', 'Usuário excluído com sucesso.');
    }

    public function deactivate(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode desativar seu próprio usuário.');
        }
        $user->is_active = false;
        
        if (!$user->save()) {
            return redirect()->route('users.index')->with('error', 'Erro ao desativar o usuário.');
        }

        return redirect()->back()->with('success', 'Usuário desativado com sucesso.');
    }

    public function activate(string $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        
        if (!$user->save()) {
            return redirect()->route('users.index')->with('error', 'Erro ao ativar o usuário.');
        }

        return redirect()->back()->with('success', 'Usuário ativado com sucesso.');
    }
}
