@extends('layouts.app')

@section('title', 'Tìm kiếm nhà hàng - Bee Recipe')

@section('content')
    <livewire:restaurants.restaurant-map />
@endsection

@push('scripts')
<script>
    // Thêm meta tags cho SEO
    document.head.innerHTML += `
        <meta name="description" content="Tìm kiếm và khám phá các nhà hàng ngon nhất gần bạn với bản đồ tương tác. Đánh giá, đặt bàn và chia sẻ trải nghiệm ẩm thực.">
        <meta name="keywords" content="nhà hàng, ẩm thực, đặt bàn, đánh giá nhà hàng, bản đồ nhà hàng, ẩm thực Việt Nam">
        <meta property="og:title" content="Tìm kiếm nhà hàng - Bee Recipe">
        <meta property="og:description" content="Tìm kiếm và khám phá các nhà hàng ngon nhất gần bạn với bản đồ tương tác.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    `;
</script>
@endpush
