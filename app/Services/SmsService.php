<?php

namespace App\Services;

use App\Models\SentMessage;
use App\Models\SmsBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send(int $businessId, int $senderId, string $receiver, string $message, ?string $messageType = null): bool
    {
        $segments = $this->segments($message);

        if (! $this->reserveSegments($businessId, $segments)) {
            return false;
        }

        $response = $this->sendToProvider($receiver, $message);

        if (! $response) {
            $this->refundSegments($businessId, $segments);

            return false;
        }

        $this->recordSentMessage($businessId, $senderId, $receiver, $message, $messageType, $segments, $response);

        return true;
    }

    public function segments(string $message): int
    {
        return max(1, (int) ceil(strlen($message) / 160));
    }

    protected function reserveSegments(int $businessId, int $segments): bool
    {
        return DB::transaction(function () use ($businessId, $segments): bool {
            $balance = SmsBalance::query()
                ->where('businessId', $businessId)
                ->lockForUpdate()
                ->first();

            if (! $balance || $balance->qty < $segments) {
                return false;
            }

            $balance->decrement('qty', $segments);

            return true;
        });
    }

    protected function refundSegments(int $businessId, int $segments): void
    {
        SmsBalance::query()
            ->where('businessId', $businessId)
            ->increment('qty', $segments);
    }

    protected function recordSentMessage(
        int $businessId,
        int $senderId,
        string $receiver,
        string $message,
        ?string $messageType,
        int $segments,
        array $response
    ): void {
        $sentMessage = new SentMessage;
        $sentMessage->businessId = $businessId;
        $sentMessage->sender = $senderId;
        $sentMessage->receiver = $receiver;
        $sentMessage->message_type = $messageType;
        $sentMessage->message = $message;
        $sentMessage->size = $segments;
        $sentMessage->message_id = $response['message_id'] ?? null;
        $sentMessage->save();
    }

    protected function sendToProvider(string $receiver, string $message): ?array
    {
        $config = config('services.sms');

        if (blank($config['url']) || blank($config['api_id']) || blank($config['api_password']) || blank($config['sender_id'])) {
            return null;
        }

        $response = Http::timeout(15)->get($config['url'], [
            'api_id' => $config['api_id'],
            'api_password' => $config['api_password'],
            'sms_type' => 'P',
            'encoding' => 'T',
            'sender_id' => $config['sender_id'],
            'phonenumber' => $receiver,
            'textmessage' => $message,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json() ?: [];
    }
}
