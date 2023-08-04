<x-filament::page>
    <div class="mx-auto max-w-xl w-full space-y-6">
        <h1 class="font-bold text-2xl text-center">Submission complete</h1>
        <div class="bg-white p-4 rounded-xl space-y-2 text-sm">
          <p>Your submission has been submitted, and a confirmation has been sent to your email for your records. After the editor completes their review of your submission, they will get in touch with you.</p>
          <p>For now you can review your submission <a class="text-primary-600 hover:text-primary-700 hover:underline" href="{{ route('filament.resources.submissions.view', $this->record) }}">here</a> or <a class="text-primary-600 hover:text-primary-700 hover:underline" href="{{ route('filament.resources.submissions.create') }}">create a new submission</a>.</p>
        </div>
    </div>
</x-filament::page>
