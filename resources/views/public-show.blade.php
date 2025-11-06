<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        /* Հիմնական ոճավորում՝ Figma-ին համապատասխան */
        body {
            /* Կիրառում ենք վերբեռնված ֆոնի նկարը */
            /* Storage::url()-ը ստեղծում է ճիշտ հղումը (օրինակ՝ /storage/backgrounds/image.jpg) */
            background-image: url('{{ $card->background_image_path ? Storage::url($card->background_image_path) : '' }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* Թող ամբողջ էկրանը լինի մուգ ֆոնով, եթե նկար չկա */
            background-color: #1a1a1a; 
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 5vh; /* Մի փոքր վերևից բաց թողնենք */
        }
        .card-container {
            width: 100%;
            max-width: 400px; /* Սահմանափակում ենք լայնությունը՝ բջջայինի տեսք տալու համար */
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        /* * Սա rgba գույնը սարքում է hex-ից և թափանցիկությունից
         * Օրինակ՝ #FF0000 և 0.5 -> rgba(255, 0, 0, 0.5)
         */
        @php
            list($r, $g, $b) = sscanf($card->logo_bg_color, "#%02x%02x%02x");
            $logo_bg_rgba = "rgba($r, $g, $b, " . $card->logo_bg_opacity . ")";
        @endphp
    </style>
</head>
<body>

    <div class="card-container">
        
        <div class="logo-background w-48 h-48 rounded-full flex items-center justify-center mb-6 shadow-lg" 
             style="background-color: {{ $logo_bg_rgba }};">
            
            @if ($card->logo_path)
                <img src="{{ Storage::url($card->logo_path) }}" alt="{{ $card->title }} Logo" class="w-40 h-40 object-contain rounded-full">
            @else
                <h1 class="text-3xl font-bold text-center px-4">{{ $card->title }}</h1>
            @endif
        </div>

        <div class="w-full grid grid-cols-4 gap-4 mb-8">
            
            @if ($card->links) @foreach ($card->links as $link)
                    @php
                        // Սահմանում ենք ճիշտ իկոնը և հղումը՝ ըստ 'key'-ի
                        $iconClass = '';
                        $href = $link['value']; // Հղումը (եթե հատուկ ձևաչափ չի պահանջվում)
                        $label = $link['label']; // Օգտագործում ենք ՏԲ-ից եկած լեյբլը

                        switch ($link['key']) {
                            case 'phone':     $iconClass = 'fa-solid fa-phone'; $href = 'tel:' . $link['value']; $label = 'Phone'; break;
                            case 'sms':       $iconClass = 'fa-solid fa-message'; $href = 'sms:' . $link['value']; $label = 'SMS'; break;
                            case 'mail':      $iconClass = 'fa-solid fa-envelope'; $href = 'mailto:' . $link['value']; $label = 'Mail'; break;
                            case 'website':   $iconClass = 'fa-solid fa-globe'; $label = 'Website'; break;
                            case 'whatsapp':  $iconClass = 'fa-brands fa-whatsapp'; $href = 'https://wa.me/' . $link['value']; $label = 'WhatsApp'; break;
                            case 'viber':     $iconClass = 'fa-brands fa-viber'; $href = 'viber://chat?number=' . $link['value']; $label = 'Viber'; break;
                            case 'facebook':  $iconClass = 'fa-brands fa-facebook-f'; $label = 'Facebook'; break;
                            case 'messenger': $iconClass = 'fa-brands fa-facebook-messenger'; $label = 'Messenger'; break;
                            case 'instagram': $iconClass = 'fa-brands fa-instagram'; $label = 'Instagram'; break;
                            case 'location':  $iconClass = 'fa-solid fa-location-dot'; $label = 'Location'; break;
                            // Այստեղ կարող ենք ավելացնել մնացածը, եթե ցանկը փոխվի
                        }
                    @endphp

                    <a href="{{ $href }}" target="_blank" class="flex flex-col items-center text-decoration-none group">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-2 transition-transform duration-200 group-hover:scale-110" 
                             style="background-color: {{ $card->brand_color }};">
                            <i class="{{ $iconClass }} text-white text-3xl"></i>
                        </div>
                        <span class="text-sm font-medium" style="color: {{ $card->brand_color }};">
                            {{ $label }}
                        </span>
                    </a>
                @endforeach
            @endif
            
        </div>

        <a href="#" class="w-full max-w-xs py-3 px-6 rounded-full text-center text-white font-bold uppercase shadow-lg transition-transform duration-200 hover:scale-105"
           style="background-color: {{ $card->brand_color }};">
            Add me to the contact
        </a>

    </div>

</body>
</html>