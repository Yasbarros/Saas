<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::select([
            'id',
            'name',
            'date_of_birth',
            'phone',
            'cpf',
            DB::raw("CASE sex
                WHEN 'F' THEN 'Feminino'
                WHEN 'M' THEN 'Masculino'
                WHEN 'O' THEN 'Outro'
                WHEN 'N' THEN 'Não informado'
                ELSE 'Desconhecido' END as sex")
        ]);


        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('cpf', 'like', '%' . $search . '%');
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

        $patients = $query->paginate($perPage)->appends($request->query());

        $patients->getCollection()->transform(function ($patient) {
            $patient->date_of_birth = Carbon::parse($patient->date_of_birth)->format('d/m/Y');
            return $patient;
        });

        return Inertia::render('patients/Index', [
            'patients' => $patients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('patients/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //TODO: Separar a validação em uma request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_guardian' => 'nullable|string|max:255',
            'social_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:patients,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'landline' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:14|unique:patients,cpf',
            'cpf_legal_guardian' => 'nullable|string|max:14',
            'rg' => 'nullable|string|max:20|unique:patients,rg',
            'sex' => 'required|string|max:1',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,bmp'
        ]);

        try {
            DB::beginTransaction();
    
            $address = Address::create([
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
            ]);
    
            $photoPath = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug($validated['name']) . '_' . time() . '.' . $extension;
    
                $photoPath = $file->storeAs('patients', $filename, 'public');
            }
    
            $patient = Patient::create([
                'name' => $validated['name'],
                'social_name' => $validated['social_name'] ?? null,
                'legal_guardian' => $validated['legal_guardian'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'notes' => $validated['notes'] ?? null,
                'landline' => $validated['landline'] ?? null,
                'cpf' => $validated['cpf'],
                'cpf_legal_guardian' => $validated['cpf_legal_guardian'] ?? null,
                'rg' => $validated['rg'],
                'sex' => $validated['sex'],
                'address_id' => $address->id,
                'clinic_id' => 1,
                'photo' => $photoPath
            ]);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            //TODO: Ver o que fazer com a exceção
            return redirect()->back()->with('error', 'Erro ao cadastrar paciente. Se o problema persistir, entre em contato com o suporte.');
        }

        return redirect()->route('patients.show', $patient->id)->with('success', 'Paciente cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::with('address')->findOrFail($id);

        return Inertia::render('patients/Show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $patient = Patient::findOrFail($id);
        $address = Address::find($patient->address_id);

        return Inertia::render('patients/Form', [
            'patient' => $patient,
            'address' => $address
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //TODO: Separar a validação em uma request
        $request->validate([
            'name' => 'required|string|max:255',
            'legal_guardian' => 'nullable|string|max:255',
            'social_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:patients,email,' . $id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:255',
            'landline' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:patients,cpf,' . $id,
            'cpf_legal_guardian' => 'nullable|string|max:14',
            'rg' => 'nullable|string|max:20|unique:patients,rg,' . $id,
            'sex' => 'required|string|max:1',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,bmp'
        ]);
    
        DB::beginTransaction();
    
        try {
            $patient = Patient::findOrFail($id);
    
            $patient->address->update($request->only([
                'street', 'number', 'complement', 'neighborhood', 'city', 'state', 'postal_code'
            ]));
    
            $data = $request->only([
                'name', 'social_name', 'legal_guardian', 'email', 'phone', 'date_of_birth',
                'notes', 'landline', 'cpf', 'cpf_legal_guardian', 'rg', 'sex'
            ]);
            
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                if ($patient->photo) {
                    Storage::disk('public')->delete($patient->getRawOriginal('photo'));
                }
            
                $file = $request->file('photo');
                $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('patients', $filename, 'public');
            
                $data['photo'] = $photoPath;
            }
            
            $patient->update($data);            
    
            DB::commit();
    
            return redirect()->route('patients.show', $patient->id)
                ->with('success', 'Paciente atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->back()->with('error', 'Erro ao atualizar paciente: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //TODO: confirmar se o endereço está sendo excluído junto
        $patients = Patient::findOrFail($id);
    
        if ($patients->photo) {
            Storage::delete($patients->photo);
        }

        $patients->delete();

        return redirect()->route('patients.index')->with('success', 'Paciente excluído com sucesso!');
    }

    public function birthdays()
    {
        $today = Carbon::now();
        $query = Patient::whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day);

        $search = request()->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
            $q->where('id', 'like', '%' . $search . '%')
            ->orWhere('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->query('per_page', 20);
        $patients = $query->paginate($perPage)->appends(request()->query());

        return Inertia::render('patients/BirthdayPeople', [
            'patients' => $patients
        ]);
    }
}
