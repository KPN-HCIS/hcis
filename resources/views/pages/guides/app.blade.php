@extends('layouts_.vertical', ['page_title' => 'Guides'])

@section('css')
<style>
        .pop {
            transform: translateX(30%);
            opacity: 0;
            position: fixed;
            display: none;
        }
        .pop.show {
            transition: transform 0.2s ease, opacity 0.2s ease;
            transform: translateX(0);
            opacity: 1;
            position: relative;
            display: inline;
        }
        .pop.hide {
            transition: transform 0.2s ease, opacity 0.2s ease;
            transform: translateX(30%);
            opacity: 0;
            position: relative;
            display: inline;
        }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        
    </div>
@endsection
{{-- @push('scripts')
    <script src="{{ asset('js/guide.js') }}"></script>
@endpush --}}
