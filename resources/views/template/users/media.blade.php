@extends('layouts.app')
@section('title', 'Media | CarNext.Autos - Auto News, Press Releases, and Auto Media Resources')

@section('meta_description', 'Explore the CarNext Autos Media page for the latest news, press releases, and media resources. Stay informed about our company updates, industry insights, and more. Access our media assets today!')
@section('content')
<section class="position-relative section_padding blog_list" id="subpage_mt">
    <div class="container">
        <div class="row">
            <div class="col col-12">
                <div class="info_page_heading section_heading text-center">
                    <h2>Press Release</h2>
                </div>
            </div>

            @foreach($media as $m)

            <div class="col col-lg-4 col-md-6 col-12 mb-3">
                <div class="card">
                    @php $file = '#'; $bg = asset('assets/images/active.jpeg'); @endphp
                    @if($m->mediaurl)
                        @php $file = $m->mediaurl; @endphp
                    @else
                        @php $file = $m->file; @endphp
                    @endif
                    <a href="{{$file}}" class="member_card blog_card" target="_blank">
                    
                        @if($file)

                            @if(pathinfo($file, PATHINFO_EXTENSION) == 'jpg' || pathinfo($file, PATHINFO_EXTENSION) == 'jpeg' || 
                            pathinfo($file, PATHINFO_EXTENSION) == 'png') 

                                @php $bg = asset('assets/images/active.jpeg'); @endphp

                            @endif
                            @if(pathinfo($file, PATHINFO_EXTENSION) == 'pdf') 
                                @php $bg = asset('assets/images/active.jpeg'); @endphp
                            @endif

                        @else
                            @php $file = asset('assets/images/active.jpeg'); @endphp
                        @endif
                        <div class="h-200px rounded-top" style="background-image:url({{$bg}}); 
                            background-position: center; background-size: cover; background-repeat: no-repeat;">
                        </div>
                    </a>
                    <div class="card-body pt-3">
                        <div class="text-start">
                            <div class="position-relative">
                                
                                <a href="{{$file}}"  target="_blank" class="member_card blog_card">                              
                                    @php
                                        $title_without_tags = strip_tags($m->title); 
                                        $title = substr($title_without_tags, 0, 35); 
                                    @endphp
                                    <h1 class="mb-1 h5 fw-bold">{{$m->title}}</h1>		
                                    <p class="text-secondary mb-2"><small><i class="fa-solid fa-calendar-days me-2"></i> {{$m->created_at->format('F j, Y')}}</small></p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>
@endsection

