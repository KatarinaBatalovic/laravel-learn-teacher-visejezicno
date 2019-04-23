<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Page;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Model\Language;

class PagesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Page $page = NULL)
    {
        // level 0 - topLevel
        if(is_null($page)){
            $pageId = 0;
        } else {
            // subpages
            $pageId = $page->id;
        }
//        $languages = Language::where('ban', 0)
//                ->orderBy('priority', 'asc')
//                ->get();
        $rows = Page::notdeleted()
                ->where('page_id', $pageId)
                ->orderBy('order_number', 'ASC')
                ->get();
        $languages = Language::where('ban', 0)
                ->orderBy('priority', 'asc')
                ->get();
        //dd($languages);
        return view('admin.pages.index', compact(['rows', 'page', 'languages']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $pagesTopLevel = Page::topLevel()
                ->notdeleted()
                ->get();
        $language = Language::where('ban',0)
                    ->orderBy('priority', 'asc')
                ->first();
        foreach($pagesTopLevel as $page){
            $data[] = $page->languages()->where('language_id', $language->id)->first();
        }
      //  dd($data);
        return view('admin.pages.create', compact( 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pagesIds = Page::pluck('id')->all();
        $pagesIds[] = 0;
        $pagesIds = implode(",", $pagesIds);
        $data = request()->validate([
            'page_id' => 'required|integer|in:'.$pagesIds,
            
            'image' => 'required|image|mimes:jpeg,bmp,png,jpg',
            
            'layout' => 'required|string|in:fullwidth,leftaside,rightaside',
            'contact_form' => 'required|boolean',
            'header' => 'required|boolean',
            'aside' => 'required|boolean',
            'footer' => 'required|boolean',
            'active' => 'required|boolean',
        ]);
        $data2 = request()->validate([
            'title' => 'required|string|min:3|max:191',
            'description' => 'required|string|max:191',
            
            'content' => 'required|string|min:3|max:65000',
        ]);
        $languages = Language::where('ban',0)
                    ->orderBy('priority', 'asc')
                ->get();
        $language = $languages[0];
       // dd($language); 
        $row = new Page();
        
        unset($data['image']);
        foreach ($data as $key => $value) {
            $row->$key = $value;
        }
        
        $row->image = "";
        // provera da li uopste dolazi 'image' kroz request
        if(request()->has('image')){
            $file = request()->image;
            $fileExtension = $file->getClientOriginalExtension();
            
            $timeStamp = Str::slug(now(), '-');
            
            $fileName = $file->getClientOriginalName();
            $fileName = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '.' . $fileExtension;
            
            //echo public_path('/upload/pages/');
            $file->move(public_path('/upload/pages/'), $fileName);
            
            $row->image = '/upload/pages/' . $fileName;
            
            // intervetion
            // xl velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(1140, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameXL = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-xl.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameXL));
            
            // m velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameM = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-m.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameM));
            
            // s velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameS = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-s.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameS));
        }
        
        $row->order_number = Page::getMyOrderNUmber(request()->page_id);
        
        $row->save();
        $row->languages()->attach($language->id, ['title'=>$data2['title'], 'description'=>$data2['description'], 'content'=>$data2['content']]);
        $first = TRUE;
        foreach ($languages as $value) {
            if (!$first) {
                $row->languages()->attach($value->id, ['title' => NULL, 'description' => NULL, 'content'=>NULL]);
            } else {
               $first = FALSE;
            }
        }
        session()->flash('message-type', 'success');
        session()->flash('message-text', 'Successfully created page' . $row->title . '!!!');
        switch (request()->input('action')) {
            case 'save':
                return redirect(route('pages.index'));
            case 'save-and-add-next':

                return redirect()->route('pages.edit', [ 'page_id' => $row->id, 'language_id' => Language::nextLang($language)]);

        }
        
        
    }
    
    public function neworder()
    {        
        // validacija obavezna
        
        $pageId = request()->page_id;
        $newOrder = request()->neworder;
        
        // string u niz
        $newOrder = explode(',', $newOrder);
        // [4,3,1]
        $i = 0;
        foreach ($newOrder as $value) {
            $page = Page::findOrFail($value);
            $page->order_number = $i;
            $page->save();
            $i++;
        }
        
        $html = '
                <div class="card mb-4 py-3 border-left-success">
                    <div class="card-body">
                        Successfully changed order
                    </div>
                </div>
            ';
                
        
        
        return $html;
        
//        session()->flash('message-type', 'success');
//        session()->flash('message-text', 'Successfully changed order');
//        
//        
//        if($pageId == 0){
//            return redirect()->route('pages.index');
//        } else{
//            return redirect()->route('pages.index', ['page' => $pageId]);
//        }
        
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page, Language $language)
    {
        $pagesTopLevel = Page::topLevel()
                ->notdeleted()
                ->where('id', '!=', $page->id)
                ->get();
       
        foreach($pagesTopLevel as $value){
            $data[] = $value->languages()->where('language_id', $language->id)->first();
        }
       //dd($data);
         $lastpriority = Language::notbanned()
                   ->orderBy('priority', 'desc')
                   ->first();
        return view('admin.pages.edit', compact(['data', 'page', 'language', 'lastpriority']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page, Language $language)
    {
        $pagesIds = Page::pluck('id')->all();
        $pagesIds[] = 0;
        $pagesIds = implode(",", $pagesIds);
        $data = request()->validate([
            'page_id' => 'required|integer|in:'.$pagesIds,
          
            'image' => 'nullable|mimes:jpeg,bmp,png,jpg',
      
            'layout' => 'required|string|in:fullwidth,leftaside,rightaside',
            'contact_form' => 'required|boolean',
            'header' => 'required|boolean',
            'aside' => 'required|boolean',
            'footer' => 'required|boolean',
            'active' => 'required|boolean',
        ]);
         $data2 = request()->validate([
        'title' => 'required|string|min:3|max:191',
            'description' => 'required|string|max:191',
          
            'content' => 'required|string|min:3|max:65000',
             ]);
        
        $row = $page;
        
        unset($data['image']);
        foreach ($data as $key => $value) {
            $row->$key = $value;
        }
        
        $row->image = $page->image;
        // provera da li uopste dolazi 'image' kroz request
        if(request()->has('image')){
            $file = request()->image;
            $fileExtension = $file->getClientOriginalExtension();
            
            $timeStamp = Str::slug(now(), '-');
            
            $fileName = $file->getClientOriginalName();
            $fileName = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '.' . $fileExtension;
            
            //echo public_path('/upload/pages/');
            $file->move(public_path('/upload/pages/'), $fileName);
            
            $row->image = '/upload/pages/' . $fileName;
            
            // intervetion
            // xl velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(1140, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameXL = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-xl.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameXL));
            
            // m velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameM = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-m.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameM));
            
            // s velicina
            $intervetionImage = Image::make(public_path('/upload/pages/').$fileName);
            $intervetionImage->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $fileNameS = '/upload/pages/' . config('app.seo-image-prefiks') . Str::slug(request('title'), '-') . '-' . $timeStamp . '-s.' . $fileExtension;
            $intervetionImage->save(public_path($fileNameS));
        }
        
         $row->save();
         $translation =  $page->languages()->where('language_id', $language->id)->exists();
         
         if($translation){
         $page->languages()->updateExistingPivot($language->id, ['title'=>$data2['title'], 'description'=>$data2['description'], 'content'=>$data2['content']]);
         }else{
           $page->languages()->attach($language->id, ['title'=>$data2['title'], 'description'=>$data2['description'], 'content'=>$data2['content']]); 
         }
        
        session()->flash('message-type', 'success');
        session()->flash('message-text', 'Successfully edited page' . $row->title . '!!!');
        
         switch (request()->input('action')) {
            case 'save':
                return redirect(route('pages.index'));
            case 'save-and-add-next':

               $lang_id = Language::nextLang($language);
               
               if (!$lang_id) {
                    return redirect()->route('pages.index');
                } else {

                return redirect()->route('pages.edit', ['page_id' => $page->id, 'language_id' => $lang_id]);
                }
        }
        
    }

    public function changestatus(Page $page){
        if($page->active == 1){
            $page->active = 0;
        } else {
            $page->active = 1;
        }
        
        $page->save();
        
        session()->flash('message-type', 'success');
        session()->flash('message-text', 'Successfully changed status for page ' . $page->title . '!!!');
        
        return redirect()->route('pages.index');
        
    }
    
    public function delete(Page $page){
        
        // hard delete
        //$user->delete();
        
        // soft delete
        $page->deleted = 1;
        $page->deleted_by = auth()->user()->id;
        $page->deleted_at = now();
        $page->save();
        
        session()->flash('message-type', 'success');
        session()->flash('message-text', 'Successfully deleted page ' . $page->title . '!!!');
        
        return redirect()->route('pages.index');
    }
}
