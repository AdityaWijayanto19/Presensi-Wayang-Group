{{-- jQuery --}}
<script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>

{{-- Bootstrap --}}
<script src="{{ asset('assets/js/lib/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/bootstrap.min.js') }}"></script>

{{-- Ionicons --}}
<script
    type="module"
    src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js">
</script>

<script
    nomodule
    src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js">
</script>

{{-- Owl Carousel --}}
<script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }}"></script>

{{-- jQuery Circle Progress --}}
<script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>

{{-- Webcam.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Base JS --}}
<script src="{{ asset('assets/js/base.js') }}"></script>

{{-- Custom Script --}}
@stack('myscript')
