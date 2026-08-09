<?php

namespace App\Http\Controllers\Public;

use App\Models\AdmissionEnquiry;
use App\Models\GeneralEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnquiryController
{
    public function admission(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:120'],
            'guardian_name' => ['required', 'string', 'max:120'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'desired_program' => ['required', 'string', 'max:80'],
            'current_grade' => ['nullable', 'string', 'max:80'],
            'preferred_contact_method' => ['required', 'in:phone,email'],
            'message' => ['nullable', 'string', 'max:2000'],
            'privacy_consent' => ['accepted'],
        ]);

        AdmissionEnquiry::create($data + ['status' => 'new']);

        if (! $request->expectsJson()) {
            return back()->with('success', 'Your admission enquiry has been received. The school office will contact you soon.');
        }

        return response()->json(['message' => 'Your admission enquiry has been received. The school office will contact you soon.'], 201);
    }

    public function general(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:3000'],
            'privacy_consent' => ['accepted'],
        ]);

        GeneralEnquiry::create($data + ['status' => 'new']);

        if (! $request->expectsJson()) {
            return back()->with('success', 'Your enquiry has been sent to the school office.');
        }

        return response()->json(['message' => 'Your enquiry has been sent to the school office.'], 201);
    }
}
