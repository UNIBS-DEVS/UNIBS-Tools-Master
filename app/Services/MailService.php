<?php

namespace App\Services;

use App\Models\ToolsMaster;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailService
{
    public function send(
        $to,
        $mailable = null,
        $subject = null,
        $html = null,
        $cc = [],
        $bcc = [],
        $attachments = []
    ) {
        try {

            $config = ToolsMaster::first();

            if (!$config) {
                throw new \Exception('Mail configuration not found.');
            }

            if ($config->email_auth_type === 'smtp') {

                Config::set('mail.default', 'smtp');

                Config::set('mail.mailers.smtp.transport', 'smtp');
                Config::set('mail.mailers.smtp.host', $config->smtp_host);
                Config::set('mail.mailers.smtp.port', $config->smtp_port);
                Config::set('mail.mailers.smtp.username', $config->support_user);
                Config::set('mail.mailers.smtp.password', $config->support_password);
                Config::set('mail.mailers.smtp.encryption', $config->smtp_auth);

                Config::set('mail.from.address', $config->support_user);
                Config::set('mail.from.name', config('app.name'));

                app('mail.manager')->forgetMailers();


                Mail::mailer('smtp')
                    ->to($to)
                    ->cc($cc)
                    ->bcc($bcc)
                    ->send($mailable);
            } elseif ($config->email_auth_type === 'graph_id') {


                $graph = new GraphMailService($config);


                if ($mailable) {

                    $html = $mailable->render();

                    if (method_exists($mailable, 'envelope')) {

                        $subject = $mailable->envelope()->subject;
                    } else {

                        $mailable->build();
                        $subject = $mailable->subject;
                    }
                }

                $graph->sendMail(
                    $to,
                    $subject,
                    $html,
                    $cc,
                    $bcc,
                    $attachments
                );
            } else {

                throw new \Exception('Invalid email authentication type.');
            }


            return true;
        } catch (Throwable $e) {

            Log::error('MailService Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}
