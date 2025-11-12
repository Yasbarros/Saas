<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Budget;
use Carbon\Carbon;
use App\Models\CashAccount;
use Illuminate\Support\Facades\DB;
use App\Models\CurrentAccount;
use App\Models\Payment;
use App\Models\PaymentMethod;



class InvoicesController extends Controller
{
    public function index(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $query = Budget::where('patient_id', $id)
            ->where('status', 1)
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

        $perPage = $request->query('per_page', 20);
        $budgets = $query->paginate($perPage)->appends($request->query());

        $budgets->getCollection()->transform(function ($budget) {
            $budget->date = Carbon::parse($budget->date)->format('d/m/Y');
            return $budget;
        });

        $cashAccounts = CashAccount::where('status', 1)->get();

        return Inertia::render('patients/sessions/Invoices',[
            'patient' => $patient,
            'budgets' => $budgets,
            'cashAccounts' => $cashAccounts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCheckout(Request $request, $id)
    {

        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'cashier_session_id' => 'exists:cashier_sessions,id',
            'cash_id' => 'required|exists:cash_accounts,id',
            'methods' => 'required|array',
            'methods.*.method' => 'required|in:cash,credit_card,debit_card,pix,other',
            'methods.*.amount' => 'required|numeric|min:0',
            'methods.*.installments' => 'nullable|integer|min:1',
        ]);

        try{
            DB::beginTransaction();

            $payment = Payment::create([
                'budget_id' => $validated['budget_id'],
                'cashier_session_id' => $validated['cashier_session_id'],
            ]);

            foreach ($validated['methods'] as $methodData) {
                PaymentMethod::create([
                    'payment_id' => $payment->id,
                    'method' => $methodData['method'],
                    'amount' => $methodData['amount'],
                    'installments' => $methodData['installments'] ?? null,
                ]);
            }

            Budget::where('id', $validated['budget_id'])->update(['paid' => true]);
            CurrentAccount::create([
                'budget_id' => $validated['budget_id'],
                'cashier_session_id' => $validated['cashier_session_id'],
                'cash_id' => $validated['cash_id'],

            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocorreu um erro ao registrar o pagamento: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('patients.invoices', ['id' => $id])->with('success', 'Pagamento registrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    
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
        $budget->status = 0; // Inativo
        $budget->save();

        $patientId = $budget->patient_id;

        return redirect()->route('patients.invoices', ['id' => $patientId])->with('success', 'Pagamento cancelado com sucesso.');
    }

    // trocar nome de metodo para createCheckout
    public function checkout( string $id, Budget $budget)
    {
        $patient = Patient::findOrFail($id);

        $budget = Budget::with(['procedure:id,name', 'priceTable:id,name', 'patient:id,name'])
            ->findOrFail($budget->id);


        return Inertia::render('patients/sessions/CheckoutInvoices', [
            'budget' => $budget,
            'patient' => $patient,
        ]);
    }
}
