<?php

namespace App\Console\Commands;

use App\Models\TranslationModel;
use Google\ApiCore\Call;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Nette\Utils\Callback;

class InsertTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "app:insert-translations";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $filePath = public_path('translations.json');
        // if (File::exists($filePath)) {
        //     TranslationModel::where(['language_code' => 'ar'])->delete();
        //     $jsonData = File::get($filePath);
        //     $translations = json_decode($jsonData, true);
        //     foreach ($translations as $translation) {
        //         TranslationModel::create($translation);
        //     }
        //     $this->info("Translation inserted");
        // } else {
        //     $this->info("File does not exists");
        // }
        $this->insertTranslation();
    }

    public function insertTranslation()
    {
        $translation_file = public_path('assets/json/translations.json');
        $translation_json = file_get_contents($translation_file);
        $translations = json_decode($translation_json, true);
        foreach ($translations as $index => $translation) {
            TranslationModel::updateOrCreate([
                'language_code' => $translation['language_code'],
                'key' => $translation['key']
            ], [
                'value' => $translation['value']
            ]);
            // TranslationModel::create($translation);
        }

        $this->info("Translation inserted $index");
    }
}
