<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Kyc;
use App\Models\Document;
use Symfony\Component\HttpFoundation\Response;

class ApiKycController extends Controller
{
    public function getKyc() {
        abort_if(Gate::denies('kyc_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kyc = Auth::user()->kyc;
        $kyc->load(['documents']);
        return response()->json($kyc);
    }

    public function getKycByUser(User $user) {
        abort_if(Gate::denies('user_verify'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kyc = $user->kyc;
        $kyc->load(['documents']);
        return response()->json($kyc);
    }

    public function updateBvn(Request $request) {
        abort_if(Gate::denies('kyc_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user_id = Auth::id();
        $kyc = Kyc::where('user_id', $user_id)->first();
        if (!$kyc) {
            $kyc = new Kyc;
            $kyc->user_id = $user_id;
        }
        $kyc->bvn = $request->bvn;
        $kyc->birthday = $request->birthday;
        $kyc->status = 'pending';
        $kyc->save();

        return response()->json(['success' => true, 'kyc' => $kyc], 200);
    }

    public function reject(Kyc $kyc) {
        abort_if(Gate::denies('user_verify'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kyc->status = 'rejected';
        $kyc->save();

        return response()->json(['success' => true], 200);
    }

    public function approve(Kyc $kyc) {
        abort_if(Gate::denies('user_verify'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kyc->status = 'approved';
        $kyc->save();

        $user = User::find($kyc->user_id);
        $user->kyc_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        return response()->json(['success' => true], 200);
    }

    public function uploadDoc(Request $request)
    {
        abort_if(Gate::denies('kyc_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $upload_path = public_path().'/uploads/';
        $user_id = Auth::id();
        $kyc = Kyc::where('user_id', $user_id)->first();
        $kyc->status = 'pending';
        $kyc->save();

        $count = Document::where('kyc_id', $kyc->id)->count();
        if ($count >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'You can upload maximum 5 files.'
            ], 401);
        }

        if($request->file('file'))
        {
            $file = $request->file('file');
            $origin_name = $file->getClientOriginalName();
            $upload_name = time().'-'.$origin_name;
            $mime_type = $file->getClientMimeType();
            $size = $file->getSize();
            if (!file_exists($upload_path))
                mkdir($upload_path);
            $file->move($upload_path, $upload_name);
            $document = Document::create([
                'kyc_id' => $kyc->id,
                'mime_type' => $mime_type,
                'origin_name' => $origin_name,
                'upload_name' => $upload_name,
                'size' => $size
            ]);
        }

        return response()->json(['success' => true, 'document' => $document], 200);
    }

    public function destroyDoc(Document $document) {
        $upload_path = public_path().'/uploads/';

        if (file_exists($upload_path.$document->upload_name)) {
            unlink($upload_path.$document->upload_name);
        }

        $document->delete();

        return response()->json(['success' => true]);
    }
}
