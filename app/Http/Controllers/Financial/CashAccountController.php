<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CashAccount;
use App\Models\User;
use App\Models\Budget;
use App\Models\Payment;
use App\Models\CashierSession;

class CashAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = CashAccount::with(['user:id,name'])
            ->select(['id', 'user_id', 'name', 'status', 'created_at', 'updated_at']);

        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%$search%"))
                ->orWhere('name', 'like', '%' . $search . '%');
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
            if ($col) $query->orderBy($col, $dir);
        }

        $perPage = $request->query('per_page', 20);
        $cashAccounts = $query->paginate($perPage)->appends($request->query());

        $cashAccounts->each(function ($cashAccount) {
            $totals = [
                'cash' => 0,
                'credit_card' => 0,
                'debit_card' => 0,
                'pix' => 0,
                'other' => 0,
            ];

            $cashAccount->cashierSessions()->with(['budgets.payments.paymentMethods'])->get()->each(function ($session) use (&$totals) {
                foreach ($session->budgets as $budget) {
                    if (!$budget->paid) continue;
                    foreach ($budget->payments as $payment) {
                        foreach ($payment->paymentMethods as $method) {
                            if (isset($totals[$method->method])) {
                                $totals[$method->method] += $method->amount;
                            }
                        }
                    }
                }
            });

            $cashAccount->total_cash = number_format($totals['cash'], 2, '.', '');
            $cashAccount->total_credit_card = number_format($totals['credit_card'], 2, '.', '');
            $cashAccount->total_debit_card = number_format($totals['debit_card'], 2, '.', '');
            $cashAccount->total_pix = number_format($totals['pix'], 2, '.', '');
            $cashAccount->total_other = number_format($totals['other'], 2, '.', '');
        });

        return Inertia::render('financial/Index', [
            'cashAccount' => $cashAccounts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('financial/Form', [
            'users' => User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id'
        ]);

        CashAccount::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('financial.index')->with('success', 'Caixa criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $cash = CashAccount::with(['user:id,name'])->findOrFail($id);

        $query = Budget::with(['procedure:id,name', 'patient:id,name', 'payments.paymentMethods'])
            ->where('paid', true)
            ->whereHas('payments', function ($q) use ($id) {
                $q->where('cash_id', $id);
            });

        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q3) use ($search) {
                        $q3->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('cashAccount', function ($q4) use ($search) {
                        $q4->where('name', 'like', '%' . $search . '%');
                    });
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

        $budgets->each(function ($budget) {
            $paymentDetails = [
                'cash' => 0,
                'credit_card' => 0,
                'debit_card' => 0,
                'pix' => 0,
                'other' => 0,
            ];
            foreach ($budget->payments as $payment) {
                foreach ($payment->paymentMethods as $method) {
                    if (isset($paymentDetails[$method->method])) {
                        $paymentDetails[$method->method] += $method->amount;
                    }
                }
            }

            foreach ($paymentDetails as $key => $value) {
                $paymentDetails[$key] = number_format((float) $value, 2, '.', '');
            }
            $budget->payment_details = $paymentDetails;
        });

        return Inertia::render('financial/Show', [
            'budgets' => $budgets,
            'cash' => $cash,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $cashAccount = CashAccount::with(['user:id,name'])
            ->findOrFail($id);

        $session = CashierSession::where('cash_account_id', $id)
            ->where('status', true)
            ->first();

        $users = User::all();

        return inertia('financial/Form', [
            'cashAccount' => $cashAccount,
            'users' => $users,
            'session' => $session,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $cashAccount = CashAccount::findOrFail($id);
        $cashAccount->user_id = $request->user_id;
        $cashAccount->name = $request->name;
        $cashAccount->save();

        $session = CashierSession::where('cash_account_id', $id)
            ->where('status', true)
            ->first();

        if ($session) {
            $session->initial_balance = $request->initial_balance;
            $session->save();
        }

        return redirect()
            ->route('financial.index')
            ->with('success', 'Caixa atualizado com sucesso!');
    }

    public function close(string $id)
    {
        $cash = CashAccount::with(['user:id,name'])
            ->findOrFail($id);

        $budgets = Budget::where('paid', 1)
            ->where('cash_id', $id)
            ->get();

        $totalBudgets = $budgets->sum('value');

        return Inertia::render('financial/Close', [
            'cash' => $cash,
            'totalBudgets' => $totalBudgets
        ]);
    }

    public function disable(string $id)
    {
        $cashierSession = CashierSession::where('cash_account_id', $id)
            ->where('status', 1)
            ->first();

        if ($cashierSession) {
            $cashierSession->status = false;
            $cashierSession->closed_at = now();
            $cashierSession->save();
        }

        $cash = CashAccount::findOrFail($id);
        $cash->status = false;
        $cash->save();

        return redirect()->route('financial.index')->with('success', 'Caixa fechado com sucesso!');
    }
}
