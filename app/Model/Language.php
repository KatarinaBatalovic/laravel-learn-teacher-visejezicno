<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function pages(){
        return $this->belongsToMany(Page::class, 'pages_content')->withPivot('title', 'description', 'content')->withTimestamps();
    }
    public function scopeNotbanned($query){
         return $query->where('ban', 0);
    }
    
        public static function nextLang(Language $language=NULL){    
                   
//f-ja vraca id narednog jezika po prioritetu
        if(!isset($language)){
            $language = Language::notbanned()
                    ->where('priority', 0)
                    ->first(); 
        }else{
            $language = Language::notbanned()
                    ->where('priority', $language->priority+1)
                    ->first(); 
        }
        if(isset($language)){
            return $language->id;  
        }
                
    }
}
