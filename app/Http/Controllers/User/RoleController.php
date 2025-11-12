<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Stancl\Tenancy\Facades\Tenancy;
// use App\Models\Tenant;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::select('id', 'name', 'guard_name');

        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('guard_name', 'like', '%' . $search . '%');
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
        $roles = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('users/roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Get modules with their permissions grouped by module
     */
    private function getModulesWithPermissions()
    {
        $permissions = Permission::select('id', 'name')->get();
        
        $moduleMapping = [
            'users' => ['view users', 'create users', 'edit users', 'delete users'],
            'roles' => ['view roles', 'create roles', 'edit roles', 'delete roles'],
            'patients' => ['view patients', 'create patients', 'edit patients', 'delete patients'],
        ];
        
        $modules = [];
        
        foreach ($moduleMapping as $moduleKey => $modulePermissions) {
            $moduleData = [
                'key' => $moduleKey,
                'name' => $moduleKey,
                'permissions' => []
            ];
            
            foreach ($permissions as $permission) {
                if (in_array($permission->name, $modulePermissions)) {
                    $moduleData['permissions'][] = [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }
            }
            
            if (!empty($moduleData['permissions'])) {
                $modules[] = $moduleData;
            }
        }
        
        return $modules;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = $this->getModulesWithPermissions();

        return Inertia::render('users/roles/Form', [
            'modules' => $modules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);
    
        try {
            DB::transaction(function () use ($validated) {
                $role = Role::create(['name' => $validated['name']]);

                if (!empty($validated['permissions'])) {
                    $role->syncPermissions($validated['permissions']);
                }
            });
        } catch (\Exception $e) {
        // TODO: Ver como tratar o erro
        //  Log::error('Erro ao criar função: ' . $e->getMessage(), [
            //  'trace'     => $e->getTraceAsString(),
            //  'tenant_id' => Tenancy::getTenant()->id ?? null,
            //  'user_id'   => auth()->id(),
        //  ]);
            
            return redirect()->back()->with('error', $e->getMessage());
        }
     
        return redirect()->route('roles.index')->with('success', 'Função criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with(['permissions' => function ($query) {
            $query->select('id', 'name');
        }])->findOrFail($id);

        return Inertia::render('users/roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::with(['permissions' => function ($query) {
            $query->select('id', 'name');
        }])->findOrFail($id);

        $modules = $this->getModulesWithPermissions();

        return Inertia::render('users/roles/Form', [
            'role' => $role,
            'modules' => $modules,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Não é possível editar a função "admin".');
        }

        try {
            DB::transaction(function () use ($validated, $role) {
                $role->name = $validated['name'];
                $role->save();

                if (!empty($validated['permissions'])) {
                    $role->syncPermissions($validated['permissions']);
                } else {
                    $role->syncPermissions([]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar a função: ' . $e->getMessage() . '. Se o problema persistir, entre em contato com o suporte.');
        }

        return redirect()->route('roles.show', $role->id)->with('success', 'Função atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Não é possível excluir a função "admin".');
        }

        try {
            DB::transaction(function () use ($role) {
                $role->delete();
            });
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', $e->getMessage());
        }

        return redirect()->route('roles.index')->with('success', 'Função excluída com sucesso!');
    }
}
