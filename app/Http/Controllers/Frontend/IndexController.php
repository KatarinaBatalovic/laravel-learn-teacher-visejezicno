<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Language;

class IndexController extends Controller
{
    public function index($short_lang = NULL){
        if(isset($short_lang)){
           
            $language = Language::where('short_lang', $short_lang)->first();
            if(isset($language) && $language->ban == 0){
                $transTitle = $language->pages()->pluck('title'); //where not NULL!!
            $transTitle = $language->pages()->pluck('description');
            $transTitle = $language->pages()->pluck('content');
                return view('frontend.index.index-'.$short_lang, compact('transTitle'));
            }
                    
        }
        $language = Language::where('priority', 0)->first();   
        $transTitle = $language->pages()->pluck('title'); 
        return view('frontend.index.index', compact('transTitle')); 
        
    }
}
