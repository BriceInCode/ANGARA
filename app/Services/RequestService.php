<?php

namespace App\Services;

use App\Models\Request;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RequestService
{
    public function createRequest(array $data)
    {
        DB::beginTransaction();
        try {
            $session = Session::findOrFail($data['session_id']);
            $requestNumber = $this->generateUniqueRequestNumber();
            $request = Request::create([
                'session_id' => $session->id,
                'request_number' => $requestNumber,
                'document_type' => $data['document_type'],
                'request_reason' => $data['request_reason'],
                'civil_center_reference' => $data['civil_center_reference'],
                'birth_act_number' => $data['birth_act_number'],
                'birth_act_creation_date' => $data['birth_act_creation_date'],
                'declaration_by' => $data['declaration_by'],
                'authorized_by' => $data['authorized_by'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'birth_place' => $data['birth_place'],
                'father_name' => $data['father_name'],
                'father_birth_date' => $data['father_birth_date'],
                'father_birth_place' => $data['father_birth_place'],
                'father_profession' => $data['father_profession'],
                'mother_name' => $data['mother_name'],
                'mother_birth_date' => $data['mother_birth_date'],
                'mother_birth_place' => $data['mother_birth_place'],
                'mother_profession' => $data['mother_profession'],
            ]);
            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur lors de la création de la demande : " . $e->getMessage());
        }
    }

    public function updateRequest($id, array $data)
    {
        DB::beginTransaction();
        try {
            $request = Request::findOrFail($id);
            $request->update([
                'document_type' => $data['document_type'],
                'request_reason' => $data['request_reason'],
                'civil_center_reference' => $data['civil_center_reference'],
                'birth_act_number' => $data['birth_act_number'],
                'birth_act_creation_date' => $data['birth_act_creation_date'],
                'declaration_by' => $data['declaration_by'],
                'authorized_by' => $data['authorized_by'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'birth_place' => $data['birth_place'],
                'father_name' => $data['father_name'],
                'father_birth_date' => $data['father_birth_date'],
                'father_birth_place' => $data['father_birth_place'],
                'father_profession' => $data['father_profession'],
                'mother_name' => $data['mother_name'],
                'mother_birth_date' => $data['mother_birth_date'],
                'mother_birth_place' => $data['mother_birth_place'],
                'mother_profession' => $data['mother_profession'],
            ]);
            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur lors de la mise à jour de la demande : " . $e->getMessage());
        }
    }

    public function getRequestById($id)
    {
        return Request::findOrFail($id);
    }

    public function getRequestBySessionId($sessionId)
    {
        return Request::where('session_id', $sessionId)->first();
    }

    public function getRequestByRequestNumber($requestNumber)
    {
        return Request::where('request_number', $requestNumber)->first();
    }

    public function listRequests()
    {
        return Request::all();
    }

    public function deleteRequest($id)
    {
        DB::beginTransaction();
        try {
            $request = Request::findOrFail($id);
            $request->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Erreur lors de la suppression de la demande : " . $e->getMessage());
        }
    }

    private function generateUniqueRequestNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "P0";
        $lastRequest = Request::where('request_number', 'like', "$prefix-$date%")
            ->orderByDesc('created_at')
            ->first();
        $counter = $lastRequest ? (int) substr($lastRequest->request_number, -5) + 1 : 1;
        $formattedCounter = str_pad($counter, 5, '0', STR_PAD_LEFT);
        $requestNumber = "{$prefix}-{$date}-{$formattedCounter}";

        if (Request::where('request_number', $requestNumber)->exists()) {
            return $this->generateUniqueRequestNumber();
        }

        return $requestNumber;
    }
}
