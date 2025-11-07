<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessCard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Հիմա սա մեզ պետք է
use App\Http\Requests\Admin\StoreCardRequest; 
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

class CardController extends Controller
{
    // Սահմանում ենք մեր հղումների ցանկը մեկ տեղում, որպեսզի կրկնօրինակում չլինի
    private $availableLinks = [
        'phone'     => 'Phone',
        'sms'       => 'SMS',
        'mail'      => 'Mail',
        'website'   => 'Website',
        'whatsapp'  => 'WhatsApp',
        'viber'     => 'Viber',
        'facebook'  => 'Facebook',
        'messenger' => 'Messenger',
        'instagram' => 'Instagram',
        'location'  => 'Location',
    ];

    /**
     * Ցուցադրում է բոլոր քարտերի ցանկը (Dashboard)
     */
    public function index()
    {
        $cards = BusinessCard::latest()->get();
        return view('admin.index', compact('cards'));
    }

    /**
     * Ցուցադրում է նոր քարտ ստեղծելու ֆորման
     */
    public function create()
    {
        // Փոխանցում ենք հղումների ցանկը view-ին
        return view('admin.create', ['availableLinks' => $this->availableLinks]);
    }


    /**
     * Պահպանում է նոր ստեղծված քարտը ՏԲ-ում
     */
    public function store(StoreCardRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('logo')) {
            $validatedData['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        if ($request->hasFile('background_image')) {
            $validatedData['background_image_path'] = $request->file('background_image')->store('backgrounds', 'public');
        }

        // Մշակում ենք հղումները
        $validatedData['links'] = $this->processLinks($validatedData['links'] ?? []);
        
        // Սահմանում ենք լոգոյի գույնը
        $validatedData['logo_bg_color'] = $validatedData['brand_color'];
        
        BusinessCard::create($validatedData);

        return redirect()->route('dashboard')->with('success', 'Քարտը հաջողությամբ ստեղծվեց։');
    }

    /**
     * Ցուցադրում է խմբագրման ֆորման
     */
    public function edit(BusinessCard $card) // Route Model Binding
    {
        // $card->links-ը արդեն իսկ զանգված է (array), շնորհիվ մեր Model-ի $casts-ի
        // Բայց այն պարունակում է միայն ակտիվները։ Մենք պետք է այն համեմատենք $availableLinks-ի հետ։
        // Մեր edit.blade.php-ն արդեն իսկ անում է այս տրամաբանությունը, 
        // այնպես որ մենք ուղղակի փոխանցում ենք երկուսն էլ։
        
        return view('admin.edit', [
            'card' => $card,
            'availableLinks' => $this->availableLinks
        ]);
    }

    /**
     * Թարմացնում է քարտը ՏԲ-ում
     */
    public function update(StoreCardRequest $request, BusinessCard $card)
    {
        $validatedData = $request->validated();

        // Ֆայլի թարմացում (ստուգում ենք՝ արդյոք նոր ֆայլ է վերբեռնվել)
        if ($request->hasFile('logo')) {
            // Ջնջում ենք հին ֆայլը, եթե այն գոյություն ունի
            if ($card->logo_path) {
                Storage::disk('public')->delete($card->logo_path);
            }
            $validatedData['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($card->background_image_path) {
                Storage::disk('public')->delete($card->background_image_path);
            }
            $validatedData['background_image_path'] = $request->file('background_image')->store('backgrounds', 'public');
        }

        // Մշակում ենք հղումները
        $validatedData['links'] = $this->processLinks($validatedData['links'] ?? []);
        
        // Սահմանում ենք լոգոյի գույնը
        $validatedData['logo_bg_color'] = $validatedData['brand_color'];

        // Թարմացնում ենք մոդելը
        $card->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'Քարտը հաջողությամբ թարմացվեց։');
    }


    /**
     * Գեներացնում և ներբեռնում է QR կոդը (SVG)
     */
    public function downloadQr(BusinessCard $card)
    {
        $url = route('card.public.show', $card);
        $qrCode = QrCode::format('svg')->size(300)->margin(1)->generate($url);
        $filename = $card->slug . '-qr-code.svg';

        return Response::make($qrCode, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Օգնող ֆունկցիա՝ հղումները մշակելու համար
     */
    private function processLinks(array $inputLinks): array
    {
        $processedLinks = [];
        foreach ($this->availableLinks as $key => $label) {
            $isActive = isset($inputLinks[$key]['active']);
            $value = $inputLinks[$key]['value'] ?? null;

            if ($isActive && !empty($value)) {
                $processedLinks[] = [
                    'key' => $key,
                    'label' => $label, // Օգտագործում ենք մեր հիմնական լեյբլը
                    'value' => $value,
                    'active' => true 
                ];
            }
        }
        return $processedLinks;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessCard $card) // Route Model Binding
    {
        $cardTitle = $card->title; // Պահպանում ենք անվանումը հաղորդագրության համար

        // 1. Ջնջում ենք լոգոն, եթե այն գոյություն ունի
        if ($card->logo_path) {
            Storage::disk('public')->delete($card->logo_path);
        }

        // 2. Ջնջում ենք ֆոնի նկարը, եթե այն գոյություն ունի
        if ($card->background_image_path) {
            Storage::disk('public')->delete($card->background_image_path);
        }
        
        // 3. Ջնջում ենք գրառումը ՏԲ-ից
        $card->delete();

        // 4. Վերադարձնում ենք հաջողության հաղորդագրություն
        return redirect()->route('dashboard')->with('success', "«{$cardTitle}» քարտը հաջողությամբ ջնջվեց։");
    }
}