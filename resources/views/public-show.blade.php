<?php
$vcard_link = generateVCard($card);
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @php
            list($r, $g, $b) = sscanf($card->brand_color, "#%02x%02x%02x");
            $logo_bg_rgba = "rgba($r, $g, $b, " . $card->logo_bg_opacity . ")";
            $brand_color = $card->brand_color;
        @endphp

        body {
            background-image: linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3)), url('{{ $card->background_image_path ? Storage::url($card->background_image_path) : '' }}');
            background-color: #1a1a1a;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-bottom: 100px;
        }

        .contact-btn {
            background-color: {{ $brand_color }};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 9999px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .share-icon-img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; }

        .logo-block { padding-top: 8vh; }

        .feedback-input {
            background-color: #2c2c2c;
            color: white;
            border: none;
            padding: 1rem;
            width: 100%;
            border-radius: 0.5rem;
            outline: none;
        }
        .feedback-input::placeholder { color: #b3b3b3; }
        .icon-img {
            width: 32px;
            height: 32px;
            filter: brightness(0) invert(1);
        }
        .fixed-contact-button {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }
    </style>
</head>
<body>

    <div class="relative w-full max-w-md mx-auto pb-20">
        <div class="absolute top-0 left-0 right-0 z-0 w-full h-[360px] shadow-2xl"
             style="background-color: {{ $logo_bg_rgba }};border-bottom-left-radius: 40%; border-bottom-right-radius: 40%;">
        </div>

        <div class="relative z-10 flex flex-col items-center logo-block">
            <div class="logo-background w-48 h-48 rounded-full flex items-center justify-center shadow-lg bg-white p-2">

                @if ($card->logo_path)
                    <img src="{{ Storage::url($card->logo_path) }}" alt="{{ $card->title }} Logo" class="w-full h-full object-contain rounded-full">
                @else
                    <h1 class="text-3xl font-bold text-center text-gray-800 px-4">{{ $card->title }}</h1>
                @endif
            </div>

            <h2 class="mt-4 text-xl font-bold text-white tracking-widest">{{ $card->title }}</h2>
        </div>

        <div class="relative z-10 w-full px-6 pt-28 mb-12">

            <div class="w-full grid grid-cols-4 gap-4">

                @if ($card->links)
                    @foreach ($card->links as $link)
                        @php
                            $iconContent = '';
                            $href = $link['value'];
                            $label = $link['label'];
                            $iconPath = 'icons/';

                            switch ($link['key']) {
                                case 'phone':
                                    $iconContent = '<img src="' . asset($iconPath . 'telephone.svg') . '" alt="Phone Icon" class="icon-img">';
                                    $href = 'tel:' . $link['value'];
                                    $label = 'Phone';
                                    break;
                                case 'sms':
                                    $iconContent = '<img src="' . asset($iconPath . 'sms.svg') . '" alt="SMS Icon" class="icon-img">';
                                    $href = 'sms:' . $link['value'];
                                    $label = 'SMS';
                                    break;
                                case 'mail':
                                    $iconContent = '<img src="' . asset($iconPath . 'mail.svg') . '" alt="Mail Icon" class="icon-img">';
                                    $href = 'mailto:' . $link['value'];
                                    $label = 'Mail';
                                    break;
                                case 'website':
                                    $iconContent = '<img src="' . asset($iconPath . 'web.svg') . '" alt="Website Icon" class="icon-img">';
                                    $label = 'Website';
                                    break;
                                case 'whatsapp':
                                    $iconContent = '<img src="' . asset($iconPath . 'whatsapp.svg') . '" alt="WhatsApp Icon" class="icon-img">';
                                    $href = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $link['value']);
                                    $label = 'WhatsApp';
                                    break;
                                case 'viber':
                                    $iconContent = '<img src="' . asset($iconPath . 'viber.svg') . '" alt="Viber Icon" class="icon-img">';
                                    $href = 'viber://chat?number=' . preg_replace('/[^0-9]/', '', $link['value']);
                                    $label = 'Viber';
                                    break;
                                case 'facebook':
                                    $iconContent = '<img src="' . asset($iconPath . 'facebook.svg') . '" alt="Facebook Icon" class="icon-img">';
                                    $label = 'Facebook';
                                    break;
                                case 'messenger':
                                    $iconContent = '<img src="' . asset($iconPath . 'massenger.svg') . '" alt="Messenger Icon" class="icon-img">';
                                    $label = 'Messenger';
                                    break;
                                case 'instagram':
                                    $iconContent = '<img src="' . asset($iconPath . 'instagram.svg') . '" alt="Instagram Icon" class="icon-img">';
                                    $label = 'Instagram';
                                    break;
                                case 'location':
                                    $iconContent = '<img src="' . asset($iconPath . 'location.svg') . '" alt="Location Icon" class="icon-img">';
                                    $label = 'Location';
                                    break;
                                default:
                                    $iconContent = '<img src="' . asset($iconPath . 'default-link.svg') . '" alt="Link Icon" class="icon-img">';
                                    $label = $link['label'] ?? 'Link';
                            }
                        @endphp

                        <a href="{{ $href }}" target="_blank" class="flex flex-col items-center text-decoration-none group">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-2 transition-transform duration-200 shadow-xl"
                                 style="background-color: {{ $brand_color }};">
                                {!! $iconContent !!}
                            </div>
                            <span class="text-sm font-bold mt-1" style="color: {{ $brand_color }};">
                                {{ $label }}
                            </span>
                        </a>
                    @endforeach
                @endif
            </div>

        </div>


            <div class="my-10 border-t border-gray-700"></div>

            <h2 class="text-2xl font-bold text-white text-center mb-6 tracking-widest">SHARE MY CARD</h2>

            <div class="flex justify-center space-x-4 pb-12">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ asset('icons/facebook.svg') }}" alt="Facebook Share Icon" class="share-icon-img">
                </a>
                <a href="https://wa.me/?text=Check%20out%20my%20digital%20card:%20{{ url()->current() }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ asset('icons/whatsapp.svg') }}" alt="WhatsApp Share Icon" class="share-icon-img">
                </a>
                <a href="https://www.instagram.com/share?url={{ urlencode(url()->current()) }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ asset('icons/instagram.svg') }}" alt="Instagram Share Icon" class="share-icon-img">
                </a>
                <a href="sms:?body=Check%20out%20my%20digital%20card:%20{{ url()->current() }}" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ asset('icons/sms.svg') }}" alt="SMS Share Icon" class="share-icon-img">
                </a>
            </div>

        </div>
    </div>

    <div class="fixed-contact-button text-center">
        <a href="{{ $vcard_link }}" download="{{ $card->slug }}.vcf"
           class="inline-flex items-center justify-center w-full max-w-[280px] contact-btn transition-transform duration-200 hover:scale-[1.02] active:scale-[0.98]">
            <img src="{{ asset('icons/add-user.svg') }}" alt="Add User Icon" class="icon-img mr-3">
            <span class="text-lg uppercase tracking-wide">
                Add me to the contact list
            </span>
        </a>
    </div>
</body>
</html>