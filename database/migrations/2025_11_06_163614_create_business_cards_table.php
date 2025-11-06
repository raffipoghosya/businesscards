<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_cards', function (Blueprint $table) {
            $table->id();
            
            // Օրինակ՝ "Adidas" կամ "Flexitree", օգտագործվում է ցանկի մեջ
            $table->string('title'); 
            
            // Սա URL-ի մասն է (domain/adidas), պետք է լինի ունիկալ
            $table->string('slug')->unique(); 
            
            // Պատկերների հղումները
            $table->string('logo_path')->nullable();
            $table->string('background_image_path')->nullable();
            
            // Գույների կարգավորումներ
            $table->string('brand_color')->default('#FFFFFF'); // Իկոնների/կոճակների գույնը
            $table->string('logo_bg_color')->default('#000000'); // Լոգոյի ֆոնի գույնը
            $table->double('logo_bg_opacity', 3, 2)->default(1.0); // Թափանցիկություն (0.00-ից 1.00)
    
            // Բոլոր իկոնները և նրանց հղումները կպահենք այստեղ
            $table->json('links')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_cards');
    }
};
