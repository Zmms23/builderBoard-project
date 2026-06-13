<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ProjectStatus;
use App\Mail\ClientProofUploadMail;
use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProofUpload;
use App\Settings\CompanySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientProofUploadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_receives_email_when_visible_proof_is_uploaded(): void
    {
        Mail::fake();

        ['company' => $company, 'order' => $order, 'project' => $project] = $this->proofContext();

        $proofUpload = ProofUpload::query()->create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'order_id' => $order->id,
            'title' => 'Bathroom renovation completed',
            'photo_path' => 'proof-uploads/bathroom.png',
            'comment' => 'Bathroom renovation is completed.',
            'is_client_visible' => true,
        ]);

        Mail::assertSent(
            ClientProofUploadMail::class,
            fn (ClientProofUploadMail $mail): bool => $mail->hasTo('client@example.com')
                && $mail->proofUpload->is($proofUpload),
        );
    }

    public function test_hidden_proof_does_not_email_client(): void
    {
        Mail::fake();

        ['company' => $company, 'order' => $order, 'project' => $project] = $this->proofContext();

        ProofUpload::query()->create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'order_id' => $order->id,
            'title' => 'Internal work note',
            'photo_path' => 'proof-uploads/internal.png',
            'is_client_visible' => false,
        ]);

        Mail::assertNothingSent();
    }

    public function test_company_can_disable_client_email_notifications(): void
    {
        Mail::fake();

        $settings = app(CompanySettings::class);
        $settings->client_email_notifications_enabled = false;
        $settings->save();

        ['company' => $company, 'order' => $order, 'project' => $project] = $this->proofContext();

        ProofUpload::query()->create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'order_id' => $order->id,
            'title' => 'Wall painting completed',
            'photo_path' => 'proof-uploads/wall.png',
            'is_client_visible' => true,
        ]);

        Mail::assertNothingSent();
    }

    /**
     * @return array{company: Company, client: Client, project: Project, order: Order}
     */
    private function proofContext(): array
    {
        $company = Company::factory()->create();

        $client = Client::query()->create([
            'company_id' => $company->id,
            'name' => 'Client Name',
            'email' => 'client@example.com',
        ]);

        $project = Project::query()->create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'title' => 'Apartment renovation',
            'status' => ProjectStatus::Active,
            'progress' => 50,
            'budget_amount' => 0,
        ]);

        $order = Order::query()->create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'client_id' => $client->id,
            'number' => 'ORD-TEST',
            'title' => 'Bathroom renovation',
            'status' => OrderStatus::Approved,
            'progress' => 100,
            'estimated_price_amount' => 0,
        ]);

        return [
            'client' => $client,
            'company' => $company,
            'order' => $order,
            'project' => $project,
        ];
    }
}
