<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer les 4 types d\'emails de test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Envoi des emails de test...');

        // 1. Email de candidature soumise
        $submitData = [
            'nom' => 'Martin',
            'prenom' => 'Léa',
            'email' => 'florentboudz@gmail.com'
        ];

        Mail::send('emails.candidate_submitted', $submitData, function($message) use ($submitData) {
            $message->to($submitData['email'])
                    ->subject('🎉 Candidature reçue - Miss ESGIS 2025');
        });

        $this->line('✅ Email 1/4 envoyé : Candidature soumise');

        // 2. Email de candidature approuvée
        $approveData = [
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'florentboudz@gmail.com',
            'numero' => 'MISS001',
            'telephone' => '+229 97 12 34 56',
            'date_naissance' => '15/03/2002'
        ];

        Mail::send('emails.candidature_approuve', $approveData, function($message) use ($approveData) {
            $message->to($approveData['email'])
                    ->subject('✨ Félicitations ! Votre candidature Miss ESGIS 2025 est approuvée !');
        });

        $this->line('✅ Email 2/4 envoyé : Candidature approuvée');

        // 3. Email de candidature rejetée
        $rejectData = [
            'nom' => 'Bernard',
            'prenom' => 'Sophie',
            'email' => 'florentboudz@gmail.com',
            'raison' => 'Après étude approfondie de votre dossier, nous regrettons de vous informer que votre candidature ne répond pas à tous les critères requis pour participer à cette édition.'
        ];

        Mail::send('emails.candidature_rejete', $rejectData, function($message) use ($rejectData) {
            $message->to($rejectData['email'])
                    ->subject('📋 Réponse à votre candidature Miss ESGIS 2025');
        });

        $this->line('✅ Email 3/4 envoyé : Candidature rejetée');

        // 4. Email de compte restreint
        $restrictData = [
            'nom' => 'Laurent',
            'prenom' => 'Emma',
            'email' => 'florentboudz@gmail.com'
        ];

        Mail::send('emails.compte_restreint', $restrictData, function($message) use ($restrictData) {
            $message->to($restrictData['email'])
                    ->subject('⚠️ Accès temporairement restreint - Miss ESGIS 2025');
        });

        $this->line('✅ Email 4/4 envoyé : Compte restreint');

        $this->newLine();
        $this->info('🎉 Tous les emails de test ont été envoyés à florentboudz@gmail.com');
        $this->comment('Vérifiez votre boîte de réception et vos spams !');
    }
}
