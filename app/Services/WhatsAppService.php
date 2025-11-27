<?php

namespace App\Services;

class WhatsAppService
{

 private string $baileysUrl;

    public function __construct()
    {
        $this->baileysUrl = config('whatsapp.baileys_url', 'http://localhost:3000');
    }

    public function sendJobNotifications(JobAlert $alert, Collection $jobs): bool
    {
        if($jobs->isEmpty()){
            return true;
        }

        $message = $this->buildConsolidatedMessage($alert, $jobs);

        $result = $this->sendMessage($alert->user_phone, $message);

        if($result){
            $alert->markJobsAsNotified($jobs->pluck('id')->toArray());

            Log::info("WhatsApp notification sent", [
                'alert_id' => $alert->id,
                'phone' => $alert->user_phone,
                'jobs_count' => $jobs->count(),
            ]); 
        }
        return $result;
    }

    private function buildConsolidatedMessage(JobAlert $alert, Collection $jobs): string
    {
        $message = "New job alert for keyword: {$alert->keyword}\n";
        $message .= "Hello {$alert->user_name}\n";
        $message .= "We found {$jobs->count()} new jobs for your alert\n";
        
        foreach($jobs as $index => $job){
            $message .= $this->formatJobForMessage($job, $index + 1);
            $message .= "\n";

            if($index < $jobs->count() - 1){
                $message .= "\n";
            }
        }

        $message .= "\n";
        $message .= "Good Luck!";

        return $message;
    }

    private function formatJobForMessage($job, int $number): string
    {
        $message = "{$number}. {$job->title}\n";
        $message .= "{$job->company}\n";
        $message .= "{$job->location}\n";

        if($job->job_type){
            $message .= "Job Type: {$job->job_type}\n";
        }

        if($job->salary){
            $message .= "Salary: {$job->salary}\n";
        }

        if($job->deadline){
            $message .= "Deadline: {$job->deadline->format('d/m/Y')}\n";
        }

        $message .= "{$job->url}\n";
        
        return $message;
    }

    private function sendMessage(string $phone, string $message): bool
    {
        try {
            // Format phone number for Baileys
            $phone = $this->formatPhoneNumber($phone);
            
            $response = Http::timeout(30)->post("{$this->baileysUrl}/send-message", [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Baileys WhatsApp error", [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp send error", [
                'error' => $e->getMessage(),
                'phone' => $phone,
            ]);
            return false;
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (!str_starts_with($phone, '212')) {
            $phone = ltrim($phone, '0');
            $phone = '212' . $phone;
        }
        
        return $phone;
    }

    public function testConnection(string $phone): bool
    {
        $testMessage = "Test de connexion WhatsApp (Baileys FREE) - Moroccan Job Scraper";
        return $this->sendMessage($phone, $testMessage);
    }
}
