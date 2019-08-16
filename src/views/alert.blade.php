@if (Session::has('sweet_alert.alert'))
    <script>
        @if (Session::has('sweet_alert.content'))
            let config = {!! Session::pull('sweet_alert.alert') !!}
            let content = document.createElement('div');
            content.insertAdjacentHTML('afterbegin', config.content);
            config.content = content;
            Swal.fire(config);
        @else
            Swal.fire({!! Session::pull('sweet_alert.alert') !!});
        @endif
    </script>
@endif
