<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramLoginController extends Controller
{
    public function callback(Request $request)
    {
        $data = $request->all();

        if (! $this->checkSignature($data)) {
            abort(403, 'Invalid Telegram signature');
        }

        if ((time() - (int) ($data['auth_date'] ?? 0)) > 86400) {
            abort(403, 'Login expired');
        }

        $user = User::updateOrCreate(
            ['telegram_id' => $data['id']],
            [
                'name'               => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                'telegram_username'  => $data['username'] ?? null,
                'telegram_photo_url' => $data['photo_url'] ?? null,
                'email'              => $data['id'] . '@telegram.local',
            ]
        );

        Auth::login($user, true);

        return redirect('/admin');
    }

    private function checkSignature(array $data): bool
    {
        $hash = $data['hash'] ?? '';
        unset($data['hash']);

        ksort($data);
        $checkString = collect($data)
            ->map(fn ($v, $k) => "$k=$v")
            ->implode("\n");

        $secretKey = hash('sha256', config('services.telegram.bot_token'), true);
        $computed  = hash_hmac('sha256', $checkString, $secretKey);

        return hash_equals($computed, $hash);
    }
}