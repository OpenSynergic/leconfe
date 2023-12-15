<?php

namespace App\Panel\Livewire\Submissions;

use App\Facades\Payment as PaymentFacade;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Payment extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, InteractWithTenant;

    public Submission $submission;

    public function render()
    {
        return view('panel.livewire.submissions.payment', [
            'reviewStageOpen' => StageManager::peerReview()->isStageOpen(),
        ]);
    }

    public function form(Form $form)
    {
        // return PaymentFacade::getPaymentForm($form);
        return $form->schema([

        ]);
    }
}
