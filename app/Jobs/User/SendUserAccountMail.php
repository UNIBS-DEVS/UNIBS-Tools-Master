<?php

namespace App\Jobs\User;

use App\Mail\UserAccountMail;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserAccountMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    protected ?string $plainPassword;

    protected bool $isUpdate;

    public function __construct(
        User $user,
        ?string $plainPassword = null,
        bool $isUpdate = false
    ) {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->isUpdate = $isUpdate;
    }

    public function handle(MailService $mailService): void
    {
        $html = view('emails.email', [
            'user' => $this->user,
            'plainPassword' => $this->plainPassword,
            'isUpdate' => $this->isUpdate,
        ])->render();

        $mailService->send(
            $this->user->email,
            new UserAccountMail(
                $this->user,
                $this->plainPassword,
                $this->isUpdate
            ),
            $this->isUpdate
                ? 'Your Account Has Been Updated'
                : 'Your Account Has Been Created',
            $html
        );
    }
}
