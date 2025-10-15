<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\TranslationModel;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\File;

class ExtractTranslations extends Command
{
    protected $signature = 'extract:translations';
    protected $description = 'Extract all phrases inside _t("") for translation';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $translations = [];
        $i = 0;
        $config = config('permission_seeder.role_structure');
        $mapPermission = collect(config('permission_seeder.permissions_map'));
        foreach ($config as $key => $modules) {
            foreach ($modules as $module => $value) {
                $translations[$i]['key'] = $module;
                $translations[$i]['value'] = $module;
                $translations[$i]['language_code'] = 'ar';
                $i++;
                foreach (explode(',', $value) as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);
                    $value = $permissionValue . '_' . $module;
                    $translations[$i]['key'] = $value;
                    $translations[$i]['value'] = $value;
                    $translations[$i]['language_code'] = 'ar';
                    $i++;
                }
            }
        }
        foreach (Setting::get() as $setting) {
            $translations[$i]['key'] = $setting->key;
            $translations[$i]['value'] = $setting->key;
            $translations[$i]['language_code'] = 'ar';
            $i++;
            if ($setting->description) {
                $translations[$i]['key'] = $setting->description;
                $translations[$i]['value'] = $setting->description;
                $translations[$i]['language_code'] = 'ar';
                $i++;
            }
        }
        $directories = [
            resource_path('views'),
            resource_path('views/dashboard'),
            app_path('Http/Controllers/Dashboard'),
        ];

        $pattern = '/_t\([\'"]([^\'"]+)[\'"]\)/';

        $phrases = [];

        foreach ($directories as $directory) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regex as $file) {
                $fileContent = file_get_contents($file[0]);

                preg_match_all($pattern, $fileContent, $matches);

                if (!empty($matches[1])) {
                    $phrases = array_merge($phrases, $matches[1]);
                }
            }
        }

        $phrases = array_unique($phrases);

        foreach ($phrases as $phrase) {
            $translations[$i]['key'] = $phrase;
            $translations[$i]['value'] = $phrase;
            $translations[$i]['language_code'] = 'ar';
            $i++;
            // $this->info($phrase);
            // $this->translate($phrase);
        }
        $jsonData = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filePath = public_path('new_translations.json');
        File::put($filePath, $jsonData);

        // $this->info("Total phrases found: " . count($phrases));
    }

    public function translate($phrase)
    {
        $translatedText = '';
        try {
            $translator = new GoogleTranslate();
            $translator->setSource('en');
            $translator->setTarget('ar');
            $translatedText = $translator->translate($phrase);
            if ($translatedText) {
                if (!TranslationModel::where(['language_code' => 'ar', 'key' => $phrase])->exists()) {
                    TranslationModel::updateOrCreate(
                        ['language_code' => 'ar', 'key' => $phrase],
                        ['value' => $translatedText]
                    );
                    $echoed = $phrase . " : " . $translatedText . "\n";
                    echo $echoed;
                }
            }
        } catch (LargeTextException | RateLimitException | TranslationRequestException $ex) {
            echo "error: " . $ex->getMessage() . "\n";
            return false;
        }
    }
}
