<?php

namespace App\Console\Commands;

use App\Models\TranslationModel;
use Illuminate\Console\Command;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Alaaeta\Translation\Facades\Translation;

class TranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phrases = TranslationModel::get();
        // $phrases = Translation::getTranslations();
        echo ($phrases->count() . "\n");
        $translatedText = '';
        foreach ($phrases as $phrase) {
            try {
                $translator = new GoogleTranslate();
                $translator->setSource('en');
                $translator->setTarget('ar');
                $translatedText = $translator->translate($phrase->key);
                if ($translatedText)
                    TranslationModel::updateOrCreate(
                        ['language_code' => 'ar', 'key' => $phrase->key],
                        ['value' => $translatedText]
                    );
                $echoed = $phrase->key . " : " . $translatedText . "\n";
                echo $echoed;
            } catch (LargeTextException | RateLimitException | TranslationRequestException $ex) {
                echo "error: " . $ex->getMessage() . "\n";
                continue;
            }
        }
    }
}
