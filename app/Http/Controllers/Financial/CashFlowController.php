<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CashAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Models\CashierSession;


class CashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = CashierSession::with(['user:id,name', 'cashAccount:id,name'])
        ->select([
            'id',
            'cash_account_id',
            'initial_balance',
            'final_balance',
            'user_id_opened',
            'user_id_closed',
            'status',
            'closed_at',
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
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        
        if (!empty($endDate)) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
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
        $cashierSession = $query->paginate($perPage)->appends($request->query());

        $cashierSession->getCollection()->transform(function ($cashierSession) {
            $cashierSession->created_at_formatted = Carbon::parse($cashierSession->created_at)->format('d/m/Y');

            $totalEntries = $cashierSession->budgets()
                ->where('paid', 1)
                ->sum('value');

            $cashierSession->total_entries = number_format($totalEntries, 2, '.', '');
            $cashierSession->total_exits = '0.00';
            $cashierSession->final_balance = number_format($cashierSession->initial_balance + $totalEntries, 2, '.', '');

            return $cashierSession;
        });

        return Inertia::render('financial/CashFlow', [
            'cashierSession' => $cashierSession
        ]);
    }
}
