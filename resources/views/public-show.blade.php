<?php
// ... (մնացած PHP կոդը)
use Illuminate\Support\Facades\Storage; // Ավելացնում ենք Storage-ի կանչը
use Illuminate\Support\Str;

// ԱՅՍ ԲԼՈԿՆ ԱՊԱՀՈՎՈՒՄ Է, ՈՐ ՖՈՒՆԿՑԻԱՆ ՄԻԱՅՆ ՄԵԿ ԱՆԳԱՄ ՍԱՀՄԱՆՎԻ
if (!function_exists('generateVCard')) {
    // Այս ֆունկցիան կօգտագործվի VCard-ը գեներացնելու համար
    // ՈՒՂՂՈՒՄ: Օգտագործում ենք \App\Models\BusinessCard ամբողջական անունը
    function generateVCard(\App\Models\BusinessCard $card): string { 
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        
        // Անունը (Title-ը օգտագործում ենք որպես Անուն/Ընկերության անուն)
        $vcard .= "FN:" . $card->title . "\r\n";
        $vcard .= "ORG:" . $card->title . "\r\n"; // Օրինակ՝ ընկերության անվանումը
        
        // Հավաքում ենք կոնտակտային տվյալները
        $phone = null;
        $email = null;
        $website = null;

        if ($card->links) {
            foreach ($card->links as $link) {
                $value = $link['value'];
                
                // Ֆիքսելով հեռախոսի և էլ. փոստի դաշտերը
                switch ($link['key']) {
                    case 'phone':
                        $vcard .= "TEL;TYPE=WORK,VOICE:" . $value . "\r\n";
                        $phone = $value;
                        break;
                    case 'mail':
                        $vcard .= "EMAIL;TYPE=PREF,INTERNET:" . $value . "\r\n";
                        $email = $value;
                        break;
                    case 'website':
                        $vcard .= "URL;TYPE=Website:" . $value . "\r\n";
                        $website = $value;
                        break;
                    case 'location':
                        $vcard .= "ADR;TYPE=WORK:" . $value . "\r\n";
                        break;
                    // Մնացածը կարող ենք ավելացնել NOTE դաշտում կամ URL-ով
                    default:
                        // Քանի որ VCard-ը սահմանափակ է, մյուսները ավելացնում ենք NOTE-ում
                        // Կոդավորում ենք արժեքը, որպեսզի այն ճիշտ ցուցադրվի 
                        $safe_value = str_replace(array("\r", "\n", ","), array("", "", "\,"), $value);
                        $vcard .= "NOTE:{$link['label']}: {$safe_value}\r\n"; 
                        break;
                }
            }
        }

        // Լոգոն կոդավորում ենք որպես նկար
        if ($card->logo_path && Storage::disk('public')->exists($card->logo_path)) {
            $logo_data = base64_encode(Storage::disk('public')->get($card->logo_path));
            $logo_mime = Storage::disk('public')->mimeType($card->logo_path);

            // Լոգոյի ավելացում VCard-ում (BASE64)
            // Պետք է ստուգել, թե արդյոք VCard-ը ճիշտ է ընդունում այս ֆորմատը
            $vcard .= "PHOTO;ENCODING=BASE64;TYPE=" . $logo_mime . ":" . $logo_data . "\r\n";
        }

        $vcard .= "END:VCARD\r\n";
        
        // Կոդավորում ենք URL-ի համար
        return 'data:text/vcard;charset=utf-8;base64,' . base64_encode($vcard);
    }
}
// ԱՅՍՏԵՂ ՓԱԿՎՈՒՄ Է if (!function_exists) ԲԼՈԿԸ

// Գեներացնում ենք VCard-ի հղումը
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
        /* Հիմնական ոճավորում՝ Figma-ին համապատասխան */
        body {
            /* Կիրառում ենք վերբեռնված ֆոնի նկարը */
            background-image: linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3)), url('{{ $card->background_image_path ? Storage::url($card->background_image_path) : '' }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* Այս հատկությունը ապահովում է, որ ֆոնի նկարը ֆիքսված մնա էջը թերթելիս */
            background-attachment: fixed; 
            background-color: #1a1a1a; 
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            /* !!! ՆՈՐ. Ավելացրել ենք ներքևի լրացուցիչ տարածք ֆիքսված կոճակի համար */
            padding-bottom: 100px; 
        }
        
        /* Ստեղծում է RGBA գույնը՝ ադմինից եկած HEX-ից և OPACITY-ից */
        @php
            list($r, $g, $b) = sscanf($card->brand_color, "#%02x%02x%02x");
            $logo_bg_rgba = "rgba($r, $g, $b, " . $card->logo_bg_opacity . ")";
            $brand_color = $card->brand_color;
        @endphp

        /* Custom scrollbar to match the dark theme */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; }
        
        /* Ֆիքսում ենք կլորավուն ֆոնի դիրքը և ձևը */
        .header-arc {
            height: 300px; /* Ֆիքսված բարձրություն */
            border-bottom-left-radius: 50%;
            border-bottom-right-radius: 50%;
            width: 100%;
            max-width: 400px; /* Համապատասխանեցնում է հիմնական բլոկին */
        }

        /* Ֆիքսում ենք լոգոյի բլոկի դիրքը */
        .logo-block {
            padding-top: 8vh; /* Բարձրացված լոգո */
        }
        
        /* Ոճավորում նկարի ֆիդբեքի դաշտերի համար */
        .feedback-input {
            background-color: #2c2c2c; /* Մուգ մոխրագույն ֆոն */
            color: white; /* Սպիտակ տեքստ */
            border: none; /* Առանց եզրագծի */
            padding: 1rem;
            width: 100%;
            border-radius: 0.5rem;
            outline: none;
        }
        
        /* Placeholder-ի գույնը փոխելու համար */
        .feedback-input::placeholder {
            color: #b3b3b3; /* Բաց մոխրագույն placeholder */
        }
        
        /* !!! ՁԵՐ SVG ԻԿՈՆՆԵՐԻ ՀԱՄԱՐ ՈՃԱՎՈՐՈՒՄ */
        .icon-img {
            width: 32px; 
            height: 32px;
            /* Կիրառում ենք ֆիլտր, որպեսզի սև SVG-ները դառնան սպիտակ, անկախ Brand Color-ից: */
            filter: brightness(0) invert(1); 
        }
        
        /* !!! ՆՈՐ. ՖԻՔՍՎԱԾ ԿՈՃԱԿԻ ԿՈՆՏԵՅՆԵՐ */
        .fixed-contact-button {
            position: fixed;
            bottom: 0; 
            left: 50%; /* Տեղափոխում ենք կենտրոն */
            transform: translateX(-50%); /* Հետ ենք քաշում կիսով չափ՝ ճիշտ կենտրոնացման համար */
            z-index: 50; /* Ապահովում ենք, որ լինի բոլորի վերևում */
            width: 100%;
            max-width: 400px; /* Համապատասխանեցնում ենք հիմնական բովանդակության լայնությանը */
            padding: 1rem;
        }

        /* ՖԻՔՍՎԱԾ ԿՈՃԱԿԻ ԴԻԶԱՅՆ՝ ՆԿԱՐԻՆ ՀԱՄԱՊԱՏԱՍԽԱՆ */
        .contact-btn {
            /* ՓՈՓՈԽՈՒԹՅՈՒՆ 1: Օգտագործում ենք Brand Color-ը background-ի համար */
            background-color: {{ $brand_color }}; 
            color: white;
            padding: 1rem 1.5rem; /* Լայն ներքին լցոնում */
            border-radius: 9999px; /* Իդեալական կլորացված եզրեր */
            font-weight: 700; /* Կիսաթավ տառատեսակ */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4); /* Մուգ ստվեր */
            transition: transform 0.2s, box-shadow 0.2s;
        }

        /* Փոքրիկ իկոնկաների չափի ճշգրտում (եթե անհրաժեշտ է) */
        .share-icon-img {
            width: 28px;
            height: 28px;
            /* Քանի որ ֆոնը մուգ մոխրագույն է (#2c2c2c), իկոնկաները պետք է լինեն սպիտակ */
            filter: brightness(0) invert(1);
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
                            $iconPath = 'icons/'; // Ձեր SVG-ների հիմնական ճանապարհը
                            
                            // ՖԻՔՍՎԱԾ ԼԵՅԲԼՆԵՐ ԵՎ SVG ԻԿՈՆՆԵՐ
                            switch ($link['key']) {
                                case 'phone':     
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'telephone.svg') . '" alt="Phone Icon" class="icon-img">'; 
                                    $href = 'tel:' . $link['value']; 
                                    $label = 'Phone'; 
                                    break;
                                case 'sms':       
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'sms.svg') . '" alt="SMS Icon" class="icon-img">';
                                    $href = 'sms:' . $link['value']; 
                                    $label = 'SMS'; 
                                    break;
                                case 'mail':      
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'mail.svg') . '" alt="Mail Icon" class="icon-img">'; 
                                    $href = 'mailto:' . $link['value']; 
                                    $label = 'Mail'; 
                                    break;
                                case 'website':   
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'web.svg') . '" alt="Website Icon" class="icon-img">'; 
                                    $label = 'Website'; 
                                    break; 
                                case 'whatsapp':  
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'whatsapp.svg') . '" alt="WhatsApp Icon" class="icon-img">'; 
                                    $href = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $link['value']); 
                                    $label = 'WhatsApp'; 
                                    break;
                                case 'viber':     
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'viber.svg') . '" alt="Viber Icon" class="icon-img">'; 
                                    $href = 'viber://chat?number=' . preg_replace('/[^0-9]/', '', $link['value']); 
                                    $label = 'Viber'; 
                                    break;
                                case 'facebook':  
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'facebook.svg') . '" alt="Facebook Icon" class="icon-img">'; 
                                    $label = 'Facebook'; 
                                    break;
                                case 'messenger': 
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'massenger.svg') . '" alt="Messenger Icon" class="icon-img">'; 
                                    $label = 'Messenger'; 
                                    break;
                                case 'instagram': 
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'instagram.svg') . '" alt="Instagram Icon" class="icon-img">'; 
                                    $label = 'Instagram'; 
                                    break;
                                case 'location':  
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'location.svg') . '" alt="Location Icon" class="icon-img">'; 
                                    $label = 'Location'; 
                                    break; 
                                default: 
                                    // Ոչ ստանդարտ դաշտերի համար, օգտագործում ենք դեֆոլտ իկոնկա
                                    $iconContent = '<img src="' . Storage::url($iconPath . 'default-link.svg') . '" alt="Link Icon" class="icon-img">'; 
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
                <!-- Փոփոխություն: Օգտագործում ենք Ձեր SVG ֆայլերը Share կոճակների համար -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ Storage::url('icons/facebook.svg') }}" alt="Facebook Share Icon" class="share-icon-img">
                </a>
                <a href="https://wa.me/?text=Check%20out%20my%20digital%20card:%20{{ url()->current() }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ Storage::url('icons/whatsapp.svg') }}" alt="WhatsApp Share Icon" class="share-icon-img">
                </a>
                <a href="https://www.instagram.com/share?url={{ urlencode(url()->current()) }}" target="_blank" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ Storage::url('icons/instagram.svg') }}" alt="Instagram Share Icon" class="share-icon-img">
                </a>
                <a href="sms:?body=Check%20out%20my%20digital%20card:%20{{ url()->current() }}" class="w-14 h-14 bg-[#2c2c2c] rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ Storage::url('icons/sms.svg') }}" alt="SMS Share Icon" class="share-icon-img">
                </a>
            </div>
            
        </div>
    </div>
    
    <!-- ՖԻՔՍՎԱԾ ԿՈՃԱԿԻ ԲԼՈԿ (ԹԱՐՄԱՑՎԱԾ ԻԿՈՆԿԱՅՈՎ ԵՎ ԴԻԶԱՅՆՈՎ) -->
    <div class="fixed-contact-button text-center">
        <!-- Կոճակի href-ը հիմա ունի VCard-ի տվյալներ -->
        <!-- ՓՈՓՈԽՈՒԹՅՈՒՆ 2: Կիրառել Brand Color-ը background-ի համար -->
        <a href="{{ $vcard_link }}" download="{{ $card->slug }}.vcf" 
           class="inline-flex items-center justify-center w-full max-w-[280px] contact-btn transition-transform duration-200 hover:scale-[1.02] active:scale-[0.98]">
            <!-- Տեղադրում ենք Ձեր SVG իկոնկան -->
            <img src="{{ Storage::url('icons/add-user.svg') }}" alt="Add User Icon" class="icon-img mr-3"> 
            <span class="text-lg uppercase tracking-wide">
                Add me to the contact list
            </span>
        </a>
    </div>
    <!-- ՎԵՐՋ. ՖԻՔՍՎԱԾ ԿՈՃԱԿԻ ԲԼՈԿ -->

</body>
</html>