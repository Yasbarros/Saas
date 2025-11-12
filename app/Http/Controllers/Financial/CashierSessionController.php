<?php

namespace App\Http\Controllers\Financial;

use Illuminate\Http\Request;
use App\Models\CashierSession;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\CashAccount;
use App\Models\User;

class CashierSessionController extends Controller
{
    public function open(string $id)
    {
        $cashAccount = CashAccount::with(['user:id,name'])
            ->find($id);
        $users = User::all();

        return Inertia::render('financial/Open', [
            'cashAccount' => $cashAccount,
            'users' => $users,
        ]);
    }

    public function storeOpen(Request $request, $id)
    {
        $request->validate([
            'initial_balance' => 'required|numeric|min:0',
            'cash_account_id' => 'required|exists:cash_accounts,id',
        ]);

        CashierSession::create([
            'initial_balance' => $request->initial_balance,
            'user_id_opened' => auth()->id(),
            'opened_at'=>now(),
            'cash_account_id'=>$request->cash_account_id,
            'status'=>true
        ]);

        CashAccount::where('id', $id)->update(['status' => true]);

        return redirect()->route('financial.index')->with('success', 'Caixa aberto como sucesso!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'initial_balance' => 'required|numeric|min:0',
            'user_id_opened' => 'required|exists:users,id',

        ]);

        $cashierSession = CashierSession::findOrFail($id);
        $cashierSession->initial_balance = $request->initial_balance;
        $cashierSession->user_id_opened = $request->user_id_opened;
        $cashierSession->save();

        return redirect()->route('financial.index')->with('success', 'Caixa atualizado com sucesso!');
    }

    public function closeSession(Request $request, $id)
    {
        $request->validate([
            'final_balance' => 'required|numeric|min:0',
            'user_id_closed' => 'required|exists:users,id',
        ]);
        
        $cashierSession = CashierSession::findOrFail($id);
        $cashierSession->final_balance = $request->final_balance;
        $cashierSession->user_id_closed = $request->user_id_closed;
        $cashierSession->status = false;
        $cashierSession->closed_at = now();
        $cashierSession->save();

        return redirect()->route('financial.index')->with('success', 'Caixa fechado com sucesso!');
    }
}
