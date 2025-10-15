<?php

namespace App\Services;

use Alaaeta\Translation\Facades\Translation;
use App\Models\TranslationModel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;

/**
 * Class TranslationService.
 */
class TranslationService
{
    public function updateTranslation($key, $request)
    {
        DB::beginTransaction();

        foreach (config('translation')['locales']  as $code => $locale) {
            Translation::updateOrCreateTranslation([
                'language_code' => $code,
                'key' => $key
            ], [
                'value' => $request->get($code)['title']
            ]);
        }

        DB::commit();

        Artisan::call('view:clear');

        return true;
    }

    public function translate_all()
    {
        $phrases = TranslationModel::get();
        // $phrases = Translation::getTranslations();
        $translatedText = '';
        foreach ($phrases as $phrase) {
            try {
                $translator = new GoogleTranslate();
                $translator->setSource('en');
                $translator->setTarget('ar');
                $translatedText = $translator->translate($phrase->key);
                TranslationModel::updateOrCreate(
                    ['language_code' => 'ar', 'key' => $phrase->key],
                    ['value' => $translatedText]
                );
                $echoed = $phrase->key . " : " . $translatedText . "\n";
                // echo $echoed;
            } catch (LargeTextException | RateLimitException | TranslationRequestException $ex) {
                // echo "error: " . $ex->getMessage() . "\n";
                continue;
            }
        }
    }

    public function getTranslation($id)
    {

        $translation = TranslationModel::query()->findOrFail($id);

        $translations = TranslationModel::query()->where('key', $translation->key)->get();

        return $translations;
    }

    public function deleteTranslation($id)
    {

        DB::beginTransaction();

        $data = TranslationModel::query()->findOrFail($id);

        $data->delete();

        DB::commit();

        Artisan::call('cache:clear');

        return true;
    }
}
