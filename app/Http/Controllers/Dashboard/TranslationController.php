<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alaaeta\Translation\Facades\Translation;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    public function __construct(protected TranslationService $translationService)
    {

        // $this->dashboardPaginate = $request->has('page_counter') ? ($request->page_counter == 0 ? $this->dashboardPaginate : $request->page_counter) : $this->dashboardPaginate;

        //create read update delete
        $this->middleware(['can:read_translation'])->only('index');
        $this->middleware(['can:update_translation'])->only('edit');
        $this->middleware(['can:read_translation'])->only('show');
        $this->middleware(['can:delete_translation'])->only('destroy');
    } //end of constructor


    public function index(Request $request)
    {
        // dd($request);

        $breadcrumbs = [
            ['link' => route('translation.index'), 'name' => _t('Translation')],
        ];

        if ($request->has('lang_name') || $request->has('lang_file_name')) {


            if ($request->lang_name == null) {
                $request->request->remove('lang_name');
            }

            if ($request->lang_file_name == null) {
                $request->request->remove('lang_file_name');
            }
        }

        $locales = config('translation')['locales'];
        // $translationTypes = Translation::getTypes();
        $translationTypes = ['en', 'ar'];

        request()->pageNumber = 50;

        $translations = Translation::getTranslations();

        return view('dashboard.translation.index', compact('locales', 'breadcrumbs', 'translationTypes', 'translations'));
    }

    public function translate_all(Request $request)
    {
        $translation = $this->translationService->translate_all();
        session()->flash('success', _t('The data updated successful'));
        return redirect()->back();
    }

    public function edit($id)
    {

        $breadcrumbs = [
            ['link' => route('translation.index'), 'name' => _t('dashboard.Translations')],
            ['name' => _t('dashboard.Edit')]
        ];

        $translations = $this->translationService->getTranslation($id);

        return view('dashboard.translation.edit', compact('translations', 'breadcrumbs'));
    }

    public function update($key, Request $request)
    {

        $result = $this->translationService->updateTranslation($key, $request);

        if ($result) {
            return redirect()->route('translation.index')->with('success', _t('message.The data updated successful'));
        }

        return redirect()->route('translation.index')->with('failed', _t('message.There is an error'));
    }


    public function destroy($id, Request $request)
    {

        $result = $this->translationService->deleteTranslation($id);

        if ($result) {
            return redirect()->route('translation.index')->with('success', _t('message.The data updated successful'));
        }
        return redirect()->route('translation.index')->with('failed', _t('message.There is an error'));
    }
}
