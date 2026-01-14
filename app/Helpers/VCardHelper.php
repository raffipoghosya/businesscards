<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\BusinessCard;

if (!function_exists('generateVCard')) {
    function generateVCard(BusinessCard $card): string {
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        
        // Ֆայլի անունը
        $slug = Str::slug($card->slug ?? 'card');
        $fileName = "vcards/{$slug}.vcf";

        $displayTitle = $card->title;
        if (is_array($card->title)) {
            $displayTitle = isset($card->title['en']) ? $card->title['en'] : (reset($card->title) ?: 'Digital Card');
        }

        $vcard .= "FN:" . $displayTitle . "\r\n";
        $vcard .= "ORG:" . $displayTitle . "\r\n";

        if ($card->links) {
            $links = is_array($card->links) ? $card->links : json_decode($card->links, true);

            if (is_array($links)) {
                foreach ($links as $link) {
                    if (!is_array($link)) continue;

                    $value = isset($link['value']) ? $link['value'] : '';
                    $key = isset($link['key']) ? $link['key'] : '';

                    switch ($key) {
                        case 'phone':
                            $vcard .= "TEL;TYPE=WORK,VOICE:" . $value . "\r\n";
                            break;
                        case 'sms':
                            $vcard .= "TEL;TYPE=MSG:" . $value . "\r\n";
                            break;
                        case 'mail':
                            $vcard .= "EMAIL;TYPE=PREF,INTERNET:" . $value . "\r\n";
                            break;
                        case 'website':
                            $vcard .= "URL;TYPE=Website:" . $value . "\r\n";
                            break;
                        case 'location':
                            $nameParts = explode(' ', $displayTitle);
                            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                            $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
                            
                            $vcard .= "N:{$lastName};{$firstName};;;\r\n"; 
                            $vcard .= "ADR;TYPE=WORK:;;" . $value . "\r\n";
                            break;
                        case 'whatsapp':
                            $vcard .= "X-SOCIALPROFILE;type=whatsapp:" . $value . "\r\n";
                            break;
                        case 'facebook':
                            $vcard .= "X-SOCIALPROFILE;type=facebook:" . $value . "\r\n";
                            break;
                        case 'instagram':
                            $vcard .= "X-SOCIALPROFILE;type=instagram:" . $value . "\r\n";
                            break;
                        case 'youtube':
                            $vcard .= "X-SOCIALPROFILE;type=youtube:" . $value . "\r\n";
                            break;
                        case 'telegram':
                            $vcard .= "X-SOCIALPROFILE;type=telegram:" . $value . "\r\n";
                            break;
                        case 'tiktok':
                            $vcard .= "X-SOCIALPROFILE;type=tiktok:" . $value . "\r\n";
                            break;                        
                        default:
                            if (!empty($value)) {
                                $vcard .= "URL;TYPE=" . ucfirst($key) . ":" . $value . "\r\n";
                            }
                            break;
                    }
                }
            }
        }

        if ($card->logo_path && Storage::disk('public')->exists($card->logo_path)) {
            // Ստանում ենք նկարի ֆայլը
            $imageContent = Storage::disk('public')->get($card->logo_path);
            
            // Վերածում ենք Base64 կոդի, որպեսզի ներդրվի ֆայլի մեջ
            $base64 = base64_encode($imageContent);
            
            // Որոշում ենք ֆայլի տիպը (JPEG, PNG և այլն)
            $ext = strtoupper(pathinfo($card->logo_path, PATHINFO_EXTENSION));
            if ($ext === 'JPG') $ext = 'JPEG';
            
            // Ավելացնում ենք VCard-ի մեջ
            $vcard .= "PHOTO;ENCODING=b;TYPE={$ext}:{$base64}\r\n";
        }

        $vcard .= "END:VCARD\r\n";

        Storage::disk('public')->put($fileName, $vcard);

        return Storage::url($fileName);
    }
}