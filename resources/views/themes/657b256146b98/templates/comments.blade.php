{{-- <div class="blog-profile box-center mb-lg-5 mb-4">
    <div class="image-profile">
        <img src="assets/images/inner-page/review-image/2.jpg"
            class="img-fluid blur-up lazyload" alt="">
    </div>

    <div class="image-name text-weight">
        <h3>John wike</h3>
        <h6>15 Aug 2021</h6>
    </div>
</div> --}}

<?php
$commentList = isset($comments) && is_countable($comments) ? $comments : [];
$refname = isset($ref) ? $ref : null;
$refid = isset($ref_id) ? $ref_id : 0;
$link = isset($url) ? $url : null;
// $t = count($commentList);
if ($user = $helper->getCurrentAccount()) {
    $name = $user->name;
    $email = $user->email;
} else {
    $name = null;
    $email = null;
}
?>

{{-- @if ($t)

<div class="comments-area">
    <div class="comments-section">
        <h3 class="single-post-tittle">Bình luận <span></span></h3>
        @include($_template.'comment-list', [
            'comments' => $comments,
            'level' => 0
        ])



    </div>
</div> <!--/.comments-area -->


@endif --}}

<form method="post" action="{{ route('web.comments.post') }}" data-ajax-url="{{ route('web.comments.ajax') }}" id="commentform" class="comment-form {{ parse_classname('comment-form') }}">
    @csrf
    <div class="row g-2">
        <div class="col-12">
            <div class="minus-spacing mb-2">
                <h3>Để lại ý kiến của bạn</h3>
            </div>
        </div>
        <div class="col-lg-4">
            <label for="author_name" class="form-label">Tên *</label>
            <input type="text" class="form-control inp" id="author_name" name="author_name" placeholder="Tên *" required>
        </div>

        <div class="col-lg-4">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control inp" id="email" name="author_email" placeholder="Email *" required>
        </div>
        <div class="col-lg-4">
            <label for="author_phone" class="form-label">SĐT (Tùy chọn)</label>
            <input type="text" class="form-control inp" id="author_phone" name="author_phone" placeholder="Số ĐT (không bắt buộc)">
        </div>

        <div class="col-12">
            <label for="tcomment-message" class="form-label">Nội dung bình luận</label>
            <span class="crazy-message-content">
                <textarea name="message" id="comment-message" class="comment-message-content message inp form-control" placeholder="Viết nội dung bình luận..." required>{{ old('message') }}</textarea>
            </span>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-solid-default btn-spacing mt-2">Đăng bình luận</button>
        </div>
    </div>
</form>
