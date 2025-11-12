<?php

namespace App\Http\Controllers\Financial;

use Illuminate\Http\Request;
use App\Models\CashierSession;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Carbon\Carbon;


class CurrentAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = CashierSession::with(['user:id,name', 'cashAccount:id,name'])
            ->select([
                'id',
                'user_id_opened',
                'cash_account_id',
                'opened_at',
                'closed_at',
                'initial_balance',
                'final_balance',
                'status',
                'created_at',
                'updated_at'
            ]);

        

        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('userOpened', function ($q3) use ($search) {
                        $q3->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('cashAccount', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!empty($startDate)) {
            $query->where('opened_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if (!empty($endDate)) {
            $query->where('opened_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $perPage = $request->query('per_page', 20);
        $cashierSessions = $query->paginate($perPage)->appends($request->query());

        $cashierSessions->getCollection()->transform(function ($session) {
            $session->created_at_formatted = Carbon::parse($session->opened_at)->format('d/m/Y H:i');

            $totalPayments = Payment::where('cashier_session_id', $session->id)
                ->join('payment_methods', 'payments.id', '=', 'payment_methods.payment_id')
                ->selectRaw('SUM(CASE WHEN payment_methods.method = "cash" THEN payment_methods.amount ELSE 0 END) as total_cash')
                ->selectRaw('SUM(CASE WHEN payment_methods.method = "credit_card" THEN payment_methods.amount ELSE 0 END) as total_credit_card')
                ->selectRaw('SUM(CASE WHEN payment_methods.method = "debit_card" THEN payment_methods.amount ELSE 0 END) as total_debit_card')
                ->selectRaw('SUM(CASE WHEN payment_methods.method = "pix" THEN payment_methods.amount ELSE 0 END) as total_pix')
                ->selectRaw('SUM(CASE WHEN payment_methods.method = "other" THEN payment_methods.amount ELSE 0 END) as total_other')
                ->first();

            $session->total_cash_entries = (float)($totalPayments->total_cash ?? 0);
            $session->total_credit_card_entries = (float)($totalPayments->total_credit_card ?? 0);
            $session->total_debit_card_entries = (float)($totalPayments->total_debit_card ?? 0);
            $session->total_pix_entries = (float)($totalPayments->total_pix ?? 0);
            $session->total_other_entries = (float)($totalPayments->total_other ?? 0);

            $session->total_entries = $session->total_cash_entries +
                                      $session->total_credit_card_entries +
                                      $session->total_debit_card_entries +
                                      $session->total_pix_entries +
                                      $session->total_other_entries;

            // TODO: Implementar lógica para saídas quando houver
            $session->total_exits = 0.00; 

            $session->final_balance_calculated = (float)$session->initial_balance + $session->total_entries - $session->total_exits;

            return $session;
        });

        return Inertia::render('financial/CurrentAccount', [
            'cashierSessions' => $cashierSessions,
            'summary' => $this->getSummary($request)
        ]);
    }

    private function getSummary(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = CashierSession::query();

        if (!empty($startDate)) {
            $query->where('opened_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if (!empty($endDate)) {
            $query->where('opened_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $cashierSessions = $query->get();

        $totalInitialBalance = $cashierSessions->sum('initial_balance');
        $totalInflows = 0;
        $totalOutflows = 0;

        foreach ($cashierSessions as $cashierSession) {
            $totalPayments = Payment::where('cashier_session_id', $cashierSession->id)
                ->join('payment_methods', 'payments.id', '=', 'payment_methods.payment_id')
                ->selectRaw('SUM(payment_methods.amount) as total_amount')
                ->first();
            $totalInflows += (float)($totalPayments->total_amount ?? 0);
        }

        $finalBalance = $totalInitialBalance + $totalInflows - $totalOutflows;

        return [
            'total_initial_balance' => number_format($totalInitialBalance, 2, '.', ''),
            'total_inflows' => number_format($totalInflows, 2, '.', ''),
            'total_outflows' => number_format($totalOutflows, 2, '.', ''),
            'final_balance' => number_format($finalBalance, 2, '.', ''),
        ];
    }
}

