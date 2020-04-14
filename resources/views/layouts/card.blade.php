@extends('layouts.app')


@section('content')
    <div class="h3 mt-2 mb-4"><u>
      @yield('view_title')
    </u></div>
    <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs">
            @foreach($cards as $card)
              @php
                $url = $card['url'];
                $label = $card['label'];
                $active = ($card['active'] ?? null) ? 'active' : '';
                $disabled = ($card['disabled'] ?? null) ? 'disabled' : '';
              @endphp
              <li class='nav-item'>
                <a class='nav-link {{$active}} {{$disabled}}' href='{{$url}}'>{{$label}}</a>
              </li>
            @endforeach
          </ul>
        </div>
        <div class="card-body">
          @yield('card_content')
        </div>
      </div>
@endsection