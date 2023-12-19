@extends($_layout.'blog')
@section('title', $page_title)
@include($_lib.'register-meta')

@section('page.header.show', 'breadcrumb')
@section('content')
<h1 class="page-title mb-12">{{$page_title}}</h1>
<div class="col-12">
    <div class="alert alert-warning text-center">
        Không tìm thấy kết quả phù hợp!
    </div>
</div>
@endsection