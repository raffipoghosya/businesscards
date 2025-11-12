<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\BusinessCard; 

if (!function_exists('generateVCard')) {
    function generateVCard(BusinessCard $card): string {
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";

        $vcard .= "FN:" . $card->title . "\r\n";
        $vcard .= "ORG:" . $card->title . "\r\n";

        if ($card->links) {
            foreach ($card->links as $link) {
                $value = $link['value'];

                switch ($link['key']) {
                    case 'phone':
                        $vcard .= "TEL;TYPE=WORK,VOICE:" . $value . "\r\n";
                        break;
                    case 'mail':
                        $vcard .= "EMAIL;TYPE=PREF,INTERNET:" . $value . "\r\n";
                        break;
                    case 'website':
                        $vcard .= "URL;TYPE=Website:" . $value . "\r\n";
                        break;
                    case 'location':
                        $vcard .= "ADR;TYPE=WORK:" . $value . "\r\n";
                        break;
                    default:
                        $safe_value = str_replace(array("\r", "\n", ","), array("", "", "\,"), $value);
                        $vcard .= "NOTE:{$link['label']}: {$safe_value}\r\n";
                        break;
                }
            }
        }

        if ($card->logo_path && Storage::disk('public')->exists($card->logo_path)) {
            $logo_data = base64_encode(Storage::disk('public')->get($card->logo_path));
            $logo_mime = Storage::disk('public')->mimeType($card->logo_path);

            $vcard .= "PHOTO;ENCODING=BASE64;TYPE=" . $logo_mime . ":" . $logo_data . "\r\n";
        }

        $vcard .= "END:VCARD\r\n";

        return 'data:text/vcard;charset=utf-8;base64,' . base64_encode($vcard);
    }
}