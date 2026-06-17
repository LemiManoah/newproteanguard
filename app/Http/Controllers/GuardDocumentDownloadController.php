<?php

namespace App\Http\Controllers;

use App\Models\GuardDocument;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuardDocumentDownloadController extends Controller
{
    public function __invoke(GuardDocument $document, TenantContext $tenant, PermissionService $permissions): StreamedResponse
    {
        abort_unless($permissions->can($tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);
        abort_unless($document->businessId === $tenant->businessId(), Response::HTTP_NOT_FOUND);
        abort_unless($document->path !== null, Response::HTTP_NOT_FOUND);

        $disk = Storage::disk($document->disk);

        abort_unless($disk->exists($document->path), Response::HTTP_NOT_FOUND);

        return $disk->download($document->path, $document->original_name ?: $document->title ?: 'guard-document');
    }
}
