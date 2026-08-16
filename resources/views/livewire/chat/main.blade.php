
    @extends('Dashboard.layouts.master')
    @section('css')
    @endsection
    @section('page-header')
        <div class="breadcrumb-header justify-content-between">
            <div class="my-auto">
                <div class="d-flex">
                    <h4 class="content-title mb-0 my-auto">المحادثات</h4>
                    <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ المحادثات الأخيرة</span>
                </div>
            </div>
        </div>
    @endsection
    @section('content')
        <div class="row row-sm hms-chat-app mb-4">
            <div class="col-xl-4 col-lg-5">
                <div class="card hms-table-card hms-chat-card hms-chat-card--list">
                    <div class="card-header hms-chat-card__header">
                        <h6 class="mb-0"><i class="far fa-comment-alt ml-1"></i> المحادثات الأخيرة</h6>
                    </div>
                    <div class="main-content-left main-content-left-chat">
                        @livewire('chat.chatlist')
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="card hms-table-card hms-chat-card hms-chat-card--thread">
                    <a class="main-header-arrow d-lg-none" href="" id="ChatBodyHide"><i class="icon ion-md-arrow-back"></i></a>
                    @livewire('chat.chatbox')
                    @livewire('chat.send-message')
                </div>
            </div>
        </div>
    @endsection
    @section('js')
        <script src="{{URL::asset('Dashboard/plugins/lightslider/js/lightslider.min.js')}}"></script>
        <script src="{{URL::asset('Dashboard/js/chat.js')}}?v=chat2"></script>
    @endsection

