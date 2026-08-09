<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionEnquiry;
use App\Models\GeneralEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'admissionEnquiries' => AdmissionEnquiry::query()->latest()->paginate(15, ['*'], 'admissions')->withQueryString(),
            'generalEnquiries' => GeneralEnquiry::query()->latest()->paginate(15, ['*'], 'general')->withQueryString(),
        ]);
    }

    public function updateAdmission(Request $request, AdmissionEnquiry $enquiry): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,follow-up required,visit scheduled,application started,admitted,not interested,closed'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $notes = $enquiry->internal_notes ?? [];
        if (! empty($data['internal_notes'])) {
            $notes[] = ['body' => $data['internal_notes'], 'at' => now()->toDateTimeString()];
        }

        $enquiry->update(['status' => $data['status'], 'internal_notes' => $notes]);

        return response()->json(['message' => 'Admission enquiry updated.']);
    }

    public function updateGeneral(Request $request, GeneralEnquiry $enquiry): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,replied,follow-up required,closed,spam'],
        ]);

        $enquiry->update($data);

        return response()->json(['message' => 'General enquiry updated.']);
    }
}
