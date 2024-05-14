<?php

namespace App\Actions\SubmissionGalleys;

use App\Models\SubmissionGalley;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMediaSubmissionGalleyFileAction
{
    use AsAction;

    public function handle(SubmissionGalley $submissionGalley, $fileUpload)
    {
        try {
            DB::beginTransaction();

            $media = $submissionGalley->media()->where('uuid', reset($fileUpload))->first();

            if ($media) {
                $submissionGalley?->file?->update([
                    'media_id' => $media->id,
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
