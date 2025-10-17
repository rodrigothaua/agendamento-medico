<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    /**
     * GET /api/patients
     * Lista todos os pacientes, com opção de filtro por nome ou CPF.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Filtro por termo de busca (nome ou CPF)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'like', $searchTerm)
                  ->orWhere('cpf', 'like', $searchTerm);
        }

        // Ordena por nome
        $patients = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'message' => 'Lista de pacientes recuperada com sucesso.',
            'patients' => $patients
        ]);
    }

    /**
     * GET /api/patients/{id}
     * Recupera um paciente específico pelo ID.
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);

        return response()->json([
            'message' => 'Detalhes do paciente recuperados.',
            'patient' => $patient
        ]);
    }

    /**
     * PUT /api/patients/{id}
     * Atualiza os dados de um paciente existente.
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:patients,email,' . $patient->id, // Ignora o próprio ID
            'phone' => 'nullable|string|max:20',
            'cpf' => 'required|string|size:11|unique:patients,cpf,' . $patient->id, // Ignora o próprio ID
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Atualiza os dados
        $patient->update($request->all());

        return response()->json([
            'message' => 'Paciente atualizado com sucesso.',
            'patient' => $patient
        ]);
    }

    /**
     * DELETE /api/patients/{id}
     * Remove um paciente e, opcionalmente, seus agendamentos (cascata).
     * Nota: Em um sistema real, agendamentos confirmados e pagos devem ser tratados antes da exclusão.
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        // A exclusão de um paciente, em um sistema real, deve ser tratada com cuidado.
        // Assumindo que a regra de negócio permite exclusão:
        $patient->delete();

        return response()->json(['message' => 'Paciente excluído com sucesso.'], 200);
    }
}
