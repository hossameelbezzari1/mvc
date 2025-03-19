<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use App\Models\Visitor;
use App\Models\Form;
use function view;

class FormController
{
    public function submit(Request $request)
    {
        $ipAddress = $request->getClientIp();
        $formData = $request->request->all();

        $visitor = Visitor::findOrCreateByIp($ipAddress);
        $visitor->updateLastActivity();

        Form::create([
            'visitor_id' => $visitor->id,
            'form_data' => json_encode($formData),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->sendToTelegram($formData);

        return view('form', [
            'message' => 'Form submitted successfully!',
            'title' => 'Form Submission',
            'layout' => 'master'
        ]);
    }

    private function sendToTelegram($data)
    {
        $token = $_ENV['TELEGRAM_BOT_TOKEN'];
        $chatId = $_ENV['TELEGRAM_CHAT_ID'];
        $message = json_encode($data);

        $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=$message";
        file_get_contents($url);
    }
}