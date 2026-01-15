<?php
use Illuminate\Support\Facades\Storage;

$getTranslation = function($field, $lang = 'en') {
    if (is_array($field)) {
        return $field[$lang] ?? $field['en'] ?? '';
    }
    return $field;
};

$titleEn = $getTranslation($card->title, 'en');
$subtitleEn = $getTranslation($card->subtitle, 'en');

$jsData = [
    'titles' => is_array($card->title) ? $card->title : ['en' => $card->title, 'ru' => $card->title, 'hy' => $card->title],
    'subtitles' => is_array($card->subtitle) ? $card->subtitle : ['en' => '', 'ru' => '', 'hy' => ''],
];

$vcard_link = generateVCard($card);
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link href="https://fonts.cdnfonts.com/css/gunterz" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/mardoto" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Վերնագրի մեջ | նշանը փոխարինում ենք բացատով, որ tab-ի վրա սիրուն երևա --}}
    <title>{{ str_replace('|', ' ', $titleEn) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @php
            // Հիմնական գույների հաշվարկ
            list($r, $g, $b) = sscanf($card->brand_color, "#%02x%02x%02x");
            $logo_bg_rgba = "rgba($r, $g, $b, " . ($card->logo_bg_opacity ?? 1.0) . ")";
            $brand_color = $card->brand_color;

            $iconColorHex = $card->icon_bg_color ?? '#ffffff'; 
            $iconOpacity = $card->icon_bg_opacity ?? 1.0;

            list($ir, $ig, $ib) = sscanf($iconColorHex, "#%02x%02x%02x");
            $icon_bg_rgba = "rgba($ir, $ig, $ib, " . $iconOpacity . ")";

            $bgOverlayHex = $card->bg_overlay_color ?? '#151212'; 
            $bgOverlayOpacity = $card->bg_overlay_opacity ?? 0.3; 

            list($br, $bg, $bb) = sscanf($bgOverlayHex, "#%02x%02x%02x");
            $bg_overlay_rgba = "rgba($br, $bg, $bb, " . $bgOverlayOpacity . ")";
            
            // ՆՈՐ ՀԱՇՎԱՐԿ: «Պահպանել կոնտակտը» կոճակի գույն և թափանցիկություն
            $contactColorHex = $card->contact_btn_color ?? $card->brand_color; 
            $contactOpacity = ($card->contact_btn_opacity ?? 100) / 100;

            list($cr, $cg, $cb) = sscanf($contactColorHex, "#%02x%02x%02x");
            $contact_btn_rgba = "rgba($cr, $cg, $cb, " . $contactOpacity . ")";
            
            // Share buttons գույնի հաշվարկ
            $shareColorHex = $card->share_btn_bg_color ?? '#ffffff';
            $shareOpacity = ($card->share_btn_bg_opacity ?? 100) / 100;
            list($sr, $sg, $sb) = sscanf($shareColorHex, "#%02x%02x%02x");
            $share_bg_rgba = "rgba($sr, $sg, $sb, " . $shareOpacity . ")";
        @endphp

        body {
            background-image: linear-gradient({{ $bg_overlay_rgba }}, {{ $bg_overlay_rgba }}), url('{{ $card->background_image_path ? Storage::url($card->background_image_path) : '' }}');
            background-color: #1a1a1a;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Mardoto', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-bottom: 85px; 
        }

        .contact-btn {
            background-color: {{ $contact_btn_rgba }}; 
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 9999px;
            font-weight: 700;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); border-radius: 4px; }

        /* Դիրքը բարձրացնելու համար նվազեցնում ենք padding-ը */

        .icon-img {
            width: 36px;
            height: 36px;
            filter: brightness(0) invert(1);
        }
        
        .share-icon-img {
    /* Փոխարինում ենք այս կոդով՝ Tailwind-ի դասերին գերակայելու համար */
    width: 24px !important; 
    height: 24px !important; 
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
        
        /* Փոքրացնում ենք gap-ը */
        .lang-switcher-container {
            display: flex;
            align-items: center;
            gap: 8px; /* Փոքրացված է 12px-ից */
        }

        .lang-btn {
            background: transparent;
            border: none;
            padding: 0;
            font-family: 'Mardoto', sans-serif;
            font-weight: 600;
            /* Փոքրացնում ենք font-size-ը */
            font-size: 15px; /* Փոքրացված է 15px-ից */ 
            text-transform: uppercase;
            color: #7a7a7a;
            cursor: pointer;
            position: relative;
            transition: color 0.3s ease;
            letter-spacing: 0.5px;
        }

        .lang-btn:hover {
            color: #bfbfbf;
        }

        .lang-btn.active {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 700;
        }

        .lang-btn.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 100%;
            height: 2px;
            background-color: #ffffff;
        }
        
        /* ՖԻՔՍՎԱԾ ԲԱԺՆԻ ՈՃԵՐԸ */
        .fixed-header-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            width: 100%;
            max-width: 400px; 
            margin: 0 auto;
            pointer-events: none; 
        }
        .header-content {
            position: relative;
            pointer-events: all; 
            padding-top: 24px;
            padding-bottom: 24px; 
        }
        
        /* Դատարկ տարածքի պահպանումը header-ի տակ */
        .content-spacer {
            height: 70px; 
            width: 100%;
        }
    </style>
    <style>
        @font-face {
            font-family: 'Gunterz';
            src: url('/fonts/Gunterz-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gunterz';
            src: url('/fonts/Gunterz-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
    </style>
</head>
<body>

    <div class="relative w-full max-w-md mx-auto">
        {{-- Մեծ Կապույտ Ֆոնի Բլոկ --}}
        <div class="absolute top-0 left-0 right-0 z-0 w-full h-[390px] shadow-2xl"
             style="background-color: {{ $logo_bg_rgba }};border-bottom-left-radius: 40%; border-bottom-right-radius: 40%;">
        </div>

        {{-- ********************************************************** --}}
        {{-- ՖԻՔՍՎԱԾ ՀԵԴԵՐԻ ԿՈՆՏԵՅՆԵՐ --}}
        {{-- ********************************************************** --}}
        <div class="fixed-header-container">
            <div class="header-content flex justify-between px-6">
                
                {{-- Ձախ: Լոգոն --}}
                <div class="z-50">
                    <img src="{{ asset('iconsvg/logo.png') }}" alt="Brand Logo" class="h-12 w-auto opacity-90 drop-shadow-md">
                </div>

                {{-- Աջ: Լեզվի Ընտրիչ --}}
                <div class="z-50 lang-switcher-container">
                    <button onclick="switchLanguage('hy')" id="btn-hy" class="lang-btn active">ՀԱՅ</button>
                    <button onclick="switchLanguage('ru')" id="btn-ru" class="lang-btn">РУ</button>
                    <button onclick="switchLanguage('en')" id="btn-en" class="lang-btn">EN</button>
                </div>
            </div>
        </div>
        {{-- ********************************************************** --}}

        {{-- ԲՈՎԱՆԴԱԿՈՒԹՅԱՆ ԲԼՈԿՆԵՐ --}}
        
        {{-- Ավելացնում ենք դատարկ տարածք, որպեսզի սքրոլի ժամանակ բովանդակությունը չծածկվի ֆիքսված հեդերով --}}
        <div class="content-spacer"></div>

        <div class="relative z-10 flex flex-col items-center logo-block">
        <div class="logo-background w-48 h-48 rounded-full flex items-center justify-center shadow-lg">
    @if ($card->logo_path)
        <img src="{{ Storage::url($card->logo_path) }}" alt="Logo" class="w-full h-full object-contain rounded-full">
    @else
        {{-- Այստեղ նույնպես թույլ ենք տալիս տողադարձ --}}
        <h1 id="logo-text" class="text-3xl font-bold text-center text-gray-800 px-4 p-2">{!! str_replace('|', '<br>', e($titleEn)) !!}</h1>
    @endif
</div>

            <div class="text-center mt-6 w-full">
            {{-- Title: Փոխարինում ենք | նշանը <br>-ով --}}
            <h1 id="display-title"
    class="text-4xl tracking-wide drop-shadow-lg font-bold"
    style="color: {{ $card->title_color ?? '#ffffff' }}; 
           font-weight: 600; 
           display: block; 
           max-width: 100%; 
           line-height: 1.2;
           font-family: sans-serif;"> 
    {!! str_replace('|', '<br>', e($titleEn)) !!}
</h1>

                {{-- Subtitle: Փոխարինում ենք | նշանը <br>-ով --}}
                <p id="display-subtitle"
                   class="text-[18px] font-medium tracking-wide px-6 mx-auto" {{-- Ավելացվեց mx-auto --}}
                   style="color: #ffffff; opacity: 0.8; font-family: 'Mardoto', sans-serif;
                          letter-spacing: 0px; margin-top: 0.09rem; 
                          max-width: 29ch; {{-- Պահպանում ենք max-width-ը --}}
                          display: -webkit-box;
                          -webkit-box-orient: vertical;
                          overflow: hidden;
                          text-overflow: ellipsis;
                          min-height: 2.8em;
                          line-height: 1.4;">
                    {!! str_replace('|', '<br>', e($subtitleEn)) !!}
                </p>
            </div>
        </div>

        <div class="relative z-10 w-full px-6 pt-16 mb-12">

        <div class="w-full grid grid-cols-4 gap-4">
    @if ($card->links)
        @foreach ($card->links as $link)
            @php
                $iconContent = '';
                $href = $link['value']; // Լռելյայն արժեքը
                $label = $link['label'];
                $iconPath = 'iconsvg/';

                // Մաքրում ենք հեռախոսահամարները ոչ թվային նշաններից
                $cleanValue = preg_replace('/[^0-9]/', '', $link['value']);

                switch ($link['key']) {
                    // Ուղղակի զանգ և նամակ (URI Schemes են)
                    case 'phone': $iconContent = '<img src="' . asset($iconPath . 'telephone.svg') . '" class="icon-img">'; $href = 'tel:' . $cleanValue; break;
                    case 'sms': $iconContent = '<img src="' . asset($iconPath . 'sms.svg') . '" class="icon-img">'; $href = 'sms:' . $cleanValue; break;
                    case 'mail': $iconContent = '<img src="' . asset($iconPath . 'mail.svg') . '" class="icon-img">'; $href = 'mailto:' . $link['value']; break;

                    // Հավելվածների համար (URI Schemes)
                    case 'whatsapp': $iconContent = '<img src="' . asset($iconPath . 'whatsapp.svg') . '" class="icon-img">'; $href = 'https://wa.me/' . $cleanValue; break; // wa.me-ը լավագույն տարբերակն է
                    case 'viber': $iconContent = '<img src="' . asset($iconPath . 'viber.svg') . '" class="icon-img">'; $href = 'viber://chat?number=' . $cleanValue; break;

                    // Սոցիալական մեդիա (Փորձում ենք օգտագործել App Schemes)
                    case 'facebook':
    $iconContent = '<img src="' . asset($iconPath . 'facebook.svg') . '" class="icon-img">';
    
    $val = $link['value'];
    // Ստուգում ենք՝ եթե արդեն հղում է (http...), թողնում ենք նույնը
    if (str_starts_with($val, 'http')) {
        $href = $val;
    } else {
        // Եթե միայն username է, սարքում ենք https հղում
        $username = basename(rtrim($val, '/')); 
        $href = 'https://www.facebook.com/' . $username;
    }
    break;
                    case 'messenger': 
                        $iconContent = '<img src="' . asset($iconPath . 'massenger.svg') . '" class="icon-img">'; 
                        $href = 'fb-messenger://user-thread/' . basename(rtrim($link['value'], '/'));
                        break;
                    case 'instagram': 
                        $iconContent = '<img src="' . asset($iconPath . 'instagram.svg') . '" class="icon-img">'; 
                        $username = str_replace('@', '', basename(rtrim($link['value'], '/')));
                        $href = 'instagram://user?username=' . $username; 
                        break;
                    case 'youtube': 
                        $iconContent = '<img src="' . asset($iconPath . 'youtube.svg') . '" class="icon-img">'; 
                        $href = 'vnd.youtube://' . $link['value']; 
                        break;
                    case 'telegram': 
                        $iconContent = '<img src="' . asset($iconPath . 'telegram.svg') . '" class="icon-img">'; 
                        $val = $link['value']; 
                        if (!str_starts_with($val, 'http')) { 
                            $val = 'https://t.me/' . str_replace('@', '', $val); 
                        } 
                        $href = $val;
                        break;
                    case 'tiktok': 
    $iconContent = '<img src="' . asset($iconPath . 'tiktok.svg') . '" class="icon-img">'; 
    
    $val = $link['value'];
    // Ստուգում ենք՝ եթե արդեն հղում է (http...), թողնում ենք նույնը
    if (str_starts_with($val, 'http')) {
        $href = $val;
    } else {
        // Եթե միայն username է գրված, սարքում ենք ճիշտ հղում
        // Մաքրում ենք ավելորդ @ նշանը, եթե կա, և նորից ավելացնում (որ կրկնակի չլինի)
        $username = str_replace('@', '', basename(rtrim($val, '/')));
        $href = 'https://www.tiktok.com/@' . $username;
    }
    break;
                        
                    // Մյուսները
                    case 'website': $iconContent = '<img src="' . asset($iconPath . 'web.svg') . '" class="icon-img">'; break;
                    case 'location': $iconContent = '<img src="' . asset($iconPath . 'location.svg') . '" class="icon-img">'; break;
                    default: $iconContent = '<img src="' . asset($iconPath . 'default-link.svg') . '" class="icon-img">';
                }
            @endphp

            <a href="{{ $href }}" target="_blank" class="flex flex-col items-center text-decoration-none group">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mt-px-1 transition-transform duration-200 shadow-xl hover:scale-110"
                     style="background-color: {{ $icon_bg_rgba }};">
                    {!! $iconContent !!}
                </div>

                <span class="text-xs  mt-0 text-center truncate w-full" style="color: #ffffff; line-height: 2.9;letter-spacing: 1.9px;">
                    {{ $label }}
                </span>
            </a>
        @endforeach
    @endif
</div>
<div class="my-12 border-t border-gray-700/50"></div>
<h2 id="share-text" 
    class="text-xl font-bold text-gray-300 text-center mb-2 tracking-tight opacity-90">
    SHARE MY CARD
</h2>
<div class="flex justify-center gap-4 pb-4">
    <div id="copy-message" class="absolute bg-green-500 text-white px-3 py-1 rounded-lg transition duration-300 opacity-0 transform translate-y-10">
        Հղումը պատճենված է!
    </div>
    
    <a href="javascript:void(0);" onclick="handleShare('facebook', '{{ url()->current() }}');"
       class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform duration-200 shadow-xl hover:scale-110" style="background-color: {{ $share_bg_rgba }};"> 
       <img src="{{ asset('iconsvg/facebook.svg') }}" alt="Facebook" style="width: 28px; height: 28px; filter: brightness(0) invert(1);"> </a>

    <a href="https://wa.me/?text=Check%20out%20my%20digital%20card:%20{{ url()->current() }}" target="_blank"
       class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform duration-200 shadow-xl hover:scale-110" style="background-color: {{ $share_bg_rgba }};"> 
       <img src="{{ asset('iconsvg/whatsapp.svg') }}" alt="WhatsApp" style="width: 28px; height: 28px; filter: brightness(0) invert(1);"> </a>

    <a href="javascript:void(0);" onclick="handleShare('instagram', '{{ url()->current() }}');"
       class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform duration-200 shadow-xl hover:scale-110" style="background-color: {{ $share_bg_rgba }};"> 
       <img src="{{ asset('iconsvg/instagram.svg') }}" alt="Instagram" style="width: 28px; height: 28px; filter: brightness(0) invert(1);"> </a>

    <a href="sms:?body=Check%20out%20my%20digital%20card:%20{{ url()->current() }}"
       class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform duration-200 shadow-xl hover:scale-110" style="background-color: {{ $share_bg_rgba }};"> 
       <img src="{{ asset('iconsvg/sms.svg') }}" alt="SMS" style="width: 28px; height: 28px; filter: brightness(0) invert(1);"></a>
</div>

    <div class="fixed-contact-button text-center">
        <a href="{{ $vcard_link }}" download="{{ $card->slug }}.vcf"
           class="inline-flex items-center justify-center w-full max-w-[280px] contact-btn transition-transform duration-200 hover:scale-[1.02] active:scale-[0.98]">
            <img src="{{ asset('iconsvg/add-user.svg') }}" alt="Add User" class="icon-img mr-3 w-5 h-5" style="filter: brightness(0) invert(1);">
            <span id="save-contact-text" class="text-xs font-bold uppercase tracking-widest">
                ADD TO CONTACT LIST
            </span>
        </a>
    </div>
    <script>
        const cardData = {
            titles: @json($jsData['titles']),
            subtitles: @json($jsData['subtitles'])
        };

        const uiTranslations = {
            shareText: {
                en: 'SHARE MY CARD',
                ru: 'ПОДЕЛИТЬСЯ МОЕЙ КАРТОЙ',
                hy: 'ԿԻՍՎԵԼ ԻՄ ՔԱՐՏՈՎ'
            },
            saveContactText: {
                en: 'ADD TO CONTACT LIST',
                ru: 'ДОБАВИТЬ В СПИСОК КОНТАКТОВ',
                hy: 'ԱՎԵԼԱՑՆԵԼ ԿՈՆՏԱԿՏՆԵՐԻ ՑԱՆԿՈՒՄ'
            }
        };

        // Օգնական ֆունկցիա՝ անվտանգ HTML ֆորմատավորման համար
        function formatText(text) {
            if (!text) return '';
            // Պաշտպանում ենք XSS-ից (escapes HTML)
            let safeText = text.replace(/&/g, "&amp;")
                               .replace(/</g, "&lt;")
                               .replace(/>/g, "&gt;")
                               .replace(/"/g, "&quot;")
                               .replace(/'/g, "&#039;");
            // Փոխարինում ենք | նշանը <br>-ով
            return safeText.replace(/\|/g, '<br>');
        }

        function switchLanguage(lang) {
            const titleRaw = cardData.titles[lang] || cardData.titles['en'] || '';
            const subtitleRaw = cardData.subtitles[lang] || cardData.subtitles['en'] || '';

            // Ձևափոխում ենք տեքստը (escape + replace |)
            const title = formatText(titleRaw);
            const subtitle = formatText(subtitleRaw);

            const titleEl = document.getElementById('display-title');
            const subtitleEl = document.getElementById('display-subtitle');
            const logoTextEl = document.getElementById('logo-text');

            // Օգտագործում ենք innerHTML, որպեսզի <br>-ը աշխատի
            if(titleEl) {
                titleEl.innerHTML = title;
                
                // Տառաչափի կառավարում՝ հիմնված լեզվի վրա
                if (lang === 'en') {
                    // Անգլերենի համար ավելի մեծ տառաչափ
                    titleEl.classList.remove('text-xl');
                    titleEl.classList.add('text-xl');
                } else {
                    // Մյուս լեզուների համար ստանդարտ տառաչափ
                    titleEl.classList.remove('text-2xl');
                    titleEl.classList.add('text-xl');
                }
            }
            if(subtitleEl) subtitleEl.innerHTML = subtitle;
            if(logoTextEl) logoTextEl.innerHTML = title;


            const shareTextEl = document.getElementById('share-text');
            const saveContactTextEl = document.getElementById('save-contact-text');

            if(shareTextEl) {
                shareTextEl.innerText = uiTranslations.shareText[lang] || uiTranslations.shareText['en'];
            }
            if(saveContactTextEl) {
                saveContactTextEl.innerText = uiTranslations.saveContactText[lang] || uiTranslations.saveContactText['en'];
            }

            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            const activeBtn = document.getElementById('btn-' + lang);
            if(activeBtn) {
                activeBtn.classList.add('active');
            }
        }
        
        // Դեֆոլթ լեզուն հայերեն դարձնելու համար
        document.addEventListener('DOMContentLoaded', function() {
            switchLanguage('hy'); 
        });
    </script>
    <script>
    // Ֆունկցիա՝ հղումը պատճենելու և ծանուցում ցուցադրելու համար
    function copyLinkAndNotify(link) {
        navigator.clipboard.writeText(link).then(function() {
            var message = document.getElementById('copy-message');
            
            // Ցուցադրել հաղորդագրությունը (Tailwind-ի դասեր)
            message.classList.remove('opacity-0', 'translate-y-10');
            message.classList.add('opacity-100', 'translate-y-0');
            
            // Թաքցնել հաղորդագրությունը 2 վայրկյան հետո
            setTimeout(function() {
                message.classList.remove('opacity-100', 'translate-y-0');
                message.classList.add('opacity-0', 'translate-y-10');
            }, 2000);
            
        }).catch(err => {
            console.error('Հղումը չհաջողվեց պատճենել:', err);
        });
    }

    // Ֆունկցիա՝ հավելվածը բացելու կամ հղումը պատճենելու համար
    function handleShare(platform, link) {
        
        let appScheme = '';
        let webShareUrl = ''; 

        if (platform === 'facebook') {
            webShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(link);
            appScheme = 'fb://';

        } else if (platform === 'instagram') {
            webShareUrl = 'https://www.instagram.com/';
            appScheme = 'instagram://';
        }

        if (appScheme) {
            window.location.href = appScheme;
            
            setTimeout(function() {
                if (document.hasFocus()) {
                    copyLinkAndNotify(link);
                }
            }, 1500);

        } else {
            copyLinkAndNotify(link);
        }
    }
</script>
</body>
</html>