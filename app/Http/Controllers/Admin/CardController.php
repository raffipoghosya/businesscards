<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessCard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Սա անհրաժեշտ է update-ի համար
use App\Http\Requests\Admin\StoreCardRequest; // Մեր վավերացման ֆայլը
use Illuminate\Support\Facades\Storage; // Սա անհրաժեշտ է ֆայլերի հետ աշխատելու համար

// --- ԱՎԵԼԱՑՐԵՔ ԱՅՍ 2 ՏՈՂԸ ---
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
// -----------------------------

class CardController extends Controller
{
    /**
     * Ցուցադրում է բոլոր քարտերի ցանկը (Dashboard)
     */
    public function index()
    {
        // Փոխում ենք all()-ը latest()-ով, որ նոր ստեղծածը առաջինը երևա
        $cards = BusinessCard::latest()->get();
        return view('admin.index', compact('cards'));
    }

    /**
     * Ցուցադրում է նոր քարտ ստեղծելու ֆորման
     */
    public function create()
    {
        $availableLinks = [
            ['key' => 'phone',     'label' => 'Հեռախոսահամար'],
            ['key' => 'sms',       'label' => 'SMS հեռախոսահամար'],
            ['key' => 'mail',      'label' => 'Էլ. փոստ (Email)'],
            ['key' => 'website',   'label' => 'Վեբ էջ'],
            ['key' => 'whatsapp',  'label' => 'WhatsApp'],
            ['key' => 'viber',     'label' => 'Viber'],
            ['key' => 'facebook',  'label' => 'Facebook'],
            ['key' => 'messenger', 'label' => 'Messenger'],
            ['key' => 'instagram', 'label' => 'Instagram'],
            ['key' => 'location',  'label' => 'Location (Google Maps)'],
        ];
        return view('admin.create', compact('availableLinks'));
    }


    /**
     * Պահպանում է նոր ստեղծված քարտը ՏԲ-ում
     */
    public function store(StoreCardRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validatedData['logo_path'] = $logoPath;
        }
        if ($request->hasFile('background_image')) {
            $bgPath = $request->file('background_image')->store('backgrounds', 'public');
            $validatedData['background_image_path'] = $bgPath;
        }

        $processedLinks = [];
        $availableLinks = [
            ['key' => 'phone',     'label' => 'Հեռախոսահամար'],
            ['key' => 'sms',       'label' => 'SMS հեռախոսահամար'],
            ['key' => 'mail',      'label' => 'Էլ. փոստ (Email)'],
            ['key' => 'website',   'label' => 'Վեբ էջ'],
            ['key' => 'whatsapp',  'label' => 'WhatsApp'],
            ['key' => 'viber',     'label' => 'Viber'],
            ['key' => 'facebook',  'label' => 'Facebook'],
            ['key' => 'messenger', 'label' => 'Messenger'],
            ['key' => 'instagram', 'label' => 'Instagram'],
            ['key' => 'location',  'label' => 'Location (Google Maps)'],
        ];

        $inputLinks = $validatedData['links'] ?? [];

        foreach ($availableLinks as $link) {
            $key = $link['key'];
            $isActive = isset($inputLinks[$key]['active']);
            $value = $inputLinks[$key]['value'] ?? null;
            if ($isActive && !empty($value)) {
                $processedLinks[] = [
                    'key' => $key,
                    'label' => $link['label'],
                    'value' => $value,
                    'active' => true 
                ];
            }
        }
        
        $validatedData['links'] = $processedLinks; 
        $validatedData['logo_bg_color'] = $validatedData['brand_color'];
        BusinessCard::create($validatedData);

        return redirect()->route('dashboard')->with('success', 'Քարտը հաջողությամբ ստեղծվեց։');
    }

    // --- ԱՎԵԼԱՑՐԵՔ ԱՅՍ ՆՈՐ ՖՈՒՆԿՑԻԱՆ ---
    /**
     * Գեներացնում և ներբեռնում է QR կոդը
     */
    public function downloadQr(BusinessCard $card)
    {
        // Ստեղծում ենք հղումը, օրինակ՝ http://localhost:8000/hakob
        $url = route('card.public.show', $card);

        // Գեներացնում ենք PNG ֆորմատի QR կոդ
        $qrCode = QrCode::format('png')->size(300)->margin(1)->generate($url);

        // Ստեղծում ենք ֆայլի անունը, օրինակ՝ hakob-qr-code.png
        $filename = $card->slug . '-qr-code.png';

        // Վերադարձնում ենք պատասխանը որպես ներբեռնվող ֆայլ
        return Response::make($qrCode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    // ------------------------------------


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}