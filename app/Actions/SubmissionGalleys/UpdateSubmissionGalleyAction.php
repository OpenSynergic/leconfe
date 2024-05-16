<?php

namespace App\Actions\SubmissionGalleys;

use App\Constants\SubmissionFileCategory;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionGalley;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSubmissionGalleyAction
{
    use AsAction;

    public function handle(SubmissionGalley $submissionGalley, array $data): SubmissionGalley
    {
        try {
            DB::beginTransaction();

            if (! $data['is_remote_url']) {
                $data['remote_url'] = null;
            }

            $submissionGalley->update($data);
            $submissionGalley->refresh();
            
            if ($media = data_get($data, 'media')) {
                $submissionFile = $submissionGalley->file;
                $submissionFile->refresh();

                $fileMedia = $submissionFile->media;
                if ($fileName = data_get($media, 'name')) {
                    $fileMedia->update([
                        'file_name' => $fileName.'.'.$fileMedia->extension,
                        'name' => $fileName,
                    ]);
                }

                $submissionFile->update([
                    'submission_file_type_id' => $media['type'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $submissionGalley;
    }
}
