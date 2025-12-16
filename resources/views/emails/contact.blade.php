<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.contact_us') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #0db777;">
            <h1 style="color: #0db777; margin: 0;">electropalestine</h1>
            <p style="color: #666; margin: 10px 0 0 0;">{{ __('common.contact_us') }}</p>
        </div>

        <div style="margin-bottom: 20px;">
            <h2 style="color: #0db777; font-size: 20px; margin-bottom: 15px;">{{ __('common.contact_us') }}</h2>
            <p style="color: #333; font-size: 16px;">
                {{ __('common.new_contact_message') ?? 'New contact message from website' }}
            </p>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #0db777; margin-top: 0;">{{ __('common.message_details') ?? 'Message Details' }}</h3>
            <p><strong>{{ __('common.name') }}:</strong> {{ $name }}</p>
            <p><strong>{{ __('common.email') }}:</strong> {{ $email }}</p>
            <p><strong>{{ __('common.message_label') }}:</strong></p>
            <div style="background: #fff; padding: 15px; border-radius: 5px; border-left: 4px solid #0db777; margin-top: 10px;">
                <p style="margin: 0; white-space: pre-wrap;">{{ $contactMessage }}</p>
            </div>
            <p style="margin-top: 15px; color: #666; font-size: 12px;">
                {{ __('common.sent_at') ?? 'Sent at' }}: {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #0db777; text-align: center; color: #666; font-size: 12px;">
            <p>{{ __('common.thanks_for_contacting') ?? 'Thank you for contacting electropalestine' }}</p>
        </div>
    </div>
</body>
</html>
