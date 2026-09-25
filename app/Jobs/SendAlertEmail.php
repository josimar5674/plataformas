<?php

namespace App\Jobs;

use App\Models\AlertRecipient;
use App\Services\GoogleGmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendAlertEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $recipientId
    ) {
    }

    public function handle(GoogleGmailService $gmail): void
    {
        $recipient = AlertRecipient::with('alert')
            ->find($this->recipientId);

        if (!$recipient) {
            return;
        }

        $alerta = $recipient->alert;

        if (!$alerta) {
            return;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | ASUNTO DEL CORREO
            |--------------------------------------------------------------------------
            */

            $asunto = $alerta->asunto;

            /*
            |--------------------------------------------------------------------------
            | REFERENCIA ADICIONAL
            |--------------------------------------------------------------------------
            */

    if (!empty($alerta->metadata)) {

    $metadata = is_string($alerta->metadata)
        ? json_decode($alerta->metadata, true)
        : $alerta->metadata;

    if (
        is_array($metadata)
        && !empty($metadata['referencia'])
        && isset($metadata['valor'])
        && $metadata['valor'] !== ''
    ) {

        $valorReferencia = $metadata['valor'];

        $asunto =
            $asunto
            . ' | '
            . $valorReferencia;
    }
}


            /*
            |--------------------------------------------------------------------------
            | ENVIAR CORREO
            |--------------------------------------------------------------------------
            */

            $gmail->send(
                $recipient->email,
                $asunto,
                $alerta->mensaje
            );

        } catch (\Throwable $e) {

            Log::error(
                'Error enviando alerta por Gmail',
                [
                    'alert_recipient_id' =>
                        $recipient->id,

                    'email' =>
                        $recipient->email,

                    'alert_id' =>
                        $alerta->id,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            throw $e;
        }
    }
}