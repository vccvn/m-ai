@if($data->show)

    <div class="container about-container">
        <div class="title title-2 text-center">
            <h2>Chúng tôi có gì?</h2>
        </div>
        <div class="row">
            {!! $children !!}
        </div>
    </div>
@endif
