<x-mail::message>
# {{ __('mail.client_proof_upload.greeting', ['client' => $client?->name]) }}

{{ __('mail.client_proof_upload.intro', ['company' => $company?->name ?? config('app.name')]) }}

<x-mail::panel>
**{{ __('mail.client_proof_upload.project') }}:** {{ $project?->title ?? '-' }}

**{{ __('mail.client_proof_upload.order') }}:** {{ $order?->title ?? '-' }}

**{{ __('mail.client_proof_upload.update') }}:** {{ $proofUpload->title }}
</x-mail::panel>

@if (filled($proofUpload->comment))
{{ $proofUpload->comment }}
@endif

@if ($photoUrl)
<x-mail::button :url="$photoUrl">
{{ __('mail.client_proof_upload.view_photo') }}
</x-mail::button>
@endif

{{ __('mail.client_proof_upload.footer') }}

{{ $company?->name ?? config('app.name') }}
</x-mail::message>
