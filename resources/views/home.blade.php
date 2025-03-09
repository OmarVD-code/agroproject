@extends('layouts.app')

@section('content')
<div id="app" class="d-flex justify-content-start">
   <sidebar-component class="flex-shrink-0"></sidebar-component>

   <div class="content flex-grow-1 p-4">
      <router-view></router-view>
   </div>
</div>
@endsection
