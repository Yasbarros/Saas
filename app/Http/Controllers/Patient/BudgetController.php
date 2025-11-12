<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\PriceTable;
use App\Models\Procedure;
use App\Models\CashAccount;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CashierSession;


class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {   
        // Ajustar filtros de pesquisa, paginação e ordenação
        $patient = Patient::findOrFail($id);
        $query = Budget::where('patient_id', $id)
            ->where('paid', false)
            ->with(['procedure:id,name', 'priceTable:id,name', 'cashAccount:id,name']);


        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                ->orWhereHas('cashAccount', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('procedure', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('priceTable', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('value', 'like', '%' . $search . '%');
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

        // Paginação
        $perPage = $request->query('per_page', 20);
        $budgets = $query->paginate($perPage)->appends($request->query());

        $budgets->getCollection()->transform(function ($budget) {
            $budget->date = Carbon::parse($budget->date)->format('d/m/Y');
            return $budget;
        });

        $cashAccounts = CashAccount::where('status', 1)->get();

        return Inertia::render('patients/sessions/budgets/Index',[
            'patient' => $patient,
            'budgets' => $budgets,
            'cashAccounts' => $cashAccounts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $budget = Budget::where('patient_id', $id)
            ->where('status', 0)
            ->with(['procedure:id,name', 'priceTable:id,name', 'cashAccount:id,name'])
            ->get();
        $patient = Patient::findOrFail($id);
        $priceTables = PriceTable::all();
        $procedures = Procedure::all();
        $cashAccounts = CashAccount::with(['user:id,name'])
            ->where('status', 1)
            ->select('id', 'user_id', 'name')
            ->get();
        return Inertia::render('patients/sessions/budgets/Form', [
            'priceTables' => $priceTables,
            'procedures' => $procedures,
            'cashAccounts' => $cashAccounts,
            'patient' => $patient,
            'budgets' => $budget,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'value' => 'required|numeric',
            'date' => 'nullable|date',
            'unit_value' => 'required|numeric',
            'procedure_id' => 'required|exists:procedures,id',
            'procedure_dentist' => 'nullable|string|max:255',
            'registration_dentist' => 'nullable|string|max:255',
            'guide' => 'nullable|string|max:255',
            'teeth_region' => 'nullable|string|max:255',
            'dentition' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cash_id' => 'required|exists:cash_accounts,id',
            'price_table_id' => 'required|exists:price_tables,id',
            'cas'
        ]);

        $cashier_session_id = CashierSession::where('cash_account_id', $validated['cash_id'])
            ->where('status', 1)
            ->value('id');

        if (!$cashier_session_id) {
            return redirect()->back()->with('error', 'Sessão de caixa não está aberta.');
        }

        try{
            DB::beginTransaction();
            $budget = Budget::create([
                'value' => $validated['value'],
                'unit_value' => $validated['unit_value'],
                'date' => $validated['date'] ?? now(),
                'notes' => $validated['notes'] ?? null,
                'status' => 0,
                'guide' => $validated['guide'] ?? null,
                'teeth_region' => $validated['teeth_region'] ?? null,
                'dentition' => $validated['dentition'] ?? null,
                'cash_id' => $validated['cash_id'],
                'cashier_session_id' => $cashier_session_id,
                'registration_dentist' => $validated['registration_dentist'] ?? null,
                'procedure_id' => $validated['procedure_id'],
                'procedure_dentist' => $validated['procedure_dentist'] ?? null,
                'execute_all_procedures' => false,
                'patient_id' => $id,
                'price_table_id' => $validated['price_table_id'],
            ]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao cadastrar orçamento. Se o problema persistir, entre em contato com o suporte.');
        }

        return redirect()->route('patients.budgets.form', ['id' => $id] )->with('success', 'Orçamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::findOrFail($id);
        $patientId = $budget->patient_id;

        try{
            DB::beginTransaction();
            $budget->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao excluir orçamento. Se o problema persistir, entre em contato com o suporte.');
        }

        return redirect()->route('patients.budgets', ['id' => $patientId] )->with('success', 'Orçamento excluído com sucesso.');
    }

    public function approve (Request $request, $id){
        $validated = $request->validate([
            'budgets' => 'required|array',
            'budgets.*' => 'exists:budgets,id',
        ]);

        try{
            DB::beginTransaction();
            Budget::whereIn('id', $validated['budgets'])
                ->where('patient_id', $id)
                ->update(['status' => 1]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
    
            return redirect()->back()->with('error', 'Erro ao aprovar orçamento. Se o problema persistir, entre em contato com o suporte.');
        }

        return redirect()->route('patients.budgets', ['id' => $id] )->with('success', 'Orçamento aprovado com sucesso.');
    }
}
