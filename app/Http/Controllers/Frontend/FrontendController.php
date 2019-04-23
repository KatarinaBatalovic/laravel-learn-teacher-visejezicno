<?php

namespace App\Http\Controllers\Frontend;
use App\Model\Language;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Page;

class FrontendController extends Controller
{
    public function page(Page $page, $short_lang=NULL){
        if(isset($short_lang)){
           
            $language = Language::where('short_lang', $short_lang)->first();
            if(isset($language) && $language->ban == 0){
                $transTitle = $language->pages()->pluck('title'); //where not NULL!!   ->where('page_id', $page->id)->get(); $title=$transTitle->pivot->title;
         //  slicno se ispise i description  $transTitle = $language->pages()->pluck('description');
        //  content   $transTitle = $language->pages()->pluck('content');
                return view('frontend.page.show-'.$short_lang, compact('transTitle','page'));
            }
                    
        }
        $language = Language::where('priority', 0)->first();   
        $transTitle = $language->pages()->pluck('title'); 
        return view('frontend.page.show', compact('transTitle', 'page')); 
        
        
    }
    
    
}
