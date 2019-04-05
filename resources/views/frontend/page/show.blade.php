@extends('frontend.layout.main')

@section('seo-title')
    <title>{{ $page->title }}</title>
    <meta content="" name="description" value="{{ $page->description }}"/>
@endsection

@section('custom-css')

@endsection

@section('page-title')
    {{ $page->title }}
@endsection

@section('content')
<div class="c-content-blog-post-1-view">
    <div class="c-content-blog-post-1">
        @if(isset($page->image) && !empty($page->image))
        <div class="c-media">
            <div class="c-content-media-2-slider" data-slider="owl">
                <div class="owl-theme c-theme owl-single" data-single-item="true" data-auto-play="4000" data-rtl="false">
                    <div class="item">
                        <div class="c-content-media-2" style="background-image: url('{{ $page->image }}'); min-height: 460px;"> </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="c-title c-font-bold c-font-uppercase">
            <a href="#">{{ $page->title }}</a>
        </div>
        <div class="c-desc">
            {!! $page->description !!}
             <hr>
        </div>
        <div class="c-desc">
            {!! $page->content !!}
        </div>
        
        @if($page->contact_form == 1)
        PRIKAZI KONTAKT FORMU
        @endif
    </div>
</div>
@endsection

@section('custom-js')

@endsection