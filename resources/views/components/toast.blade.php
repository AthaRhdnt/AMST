<div class="toast show position-fixed border border-1 border-{{ $type }} p-2" role="alert" aria-live="assertive"
    aria-atomic="true" style="bottom: 2rem; right: 2rem; z-index: 100;">
    <div class="toast-header d-flex justify-content-between">
        <img src="{{asset('media/logo/'.config('setting.general.app_logo')) }}" class="rounded me-2" alt="logo"
            style="height:1rem; width: auto">
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        <h5>
            {{ $message }}
        </h5>
    </div>
</div>
