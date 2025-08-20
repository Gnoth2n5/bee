<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Gói Dịch Vụ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($activeSubscription)
                        <div class="mb-6 p-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-green-800 mb-2">
                                        Gói hiện tại: {{ ucfirst($activeSubscription->subscription_type) }}
                                    </h3>
                                    <p class="text-green-700 mb-1">
                                        <strong>Trạng thái:</strong> 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($activeSubscription->status) }}
                                        </span>
                                    </p>
                                    <p class="text-green-700 mb-1">
                                        <strong>Ngày bắt đầu:</strong> {{ $activeSubscription->start_date->format('d/m/Y') }}
                                    </p>
                                    <p class="text-green-700 mb-1">
                                        <strong>Ngày kết thúc:</strong> {{ $activeSubscription->end_date->format('d/m/Y') }}
                                    </p>
                                    <p class="text-green-700 mb-3">
                                        <strong>Còn lại:</strong> 
                                        <span class="font-bold text-lg">{{ $activeSubscription->getRemainingDays() }} ngày</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-green-800">
                                        {{ number_format($activeSubscription->amount) }} VNĐ
                                    </p>
                                    <p class="text-sm text-green-600">Đã thanh toán</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-6 bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-lg text-center">
                            <h3 class="text-xl font-bold text-orange-800 mb-2">Bạn chưa có gói dịch vụ nào</h3>
                            <p class="text-orange-700 mb-4">Nâng cấp lên VIP để tận hưởng những tính năng đặc biệt!</p>
                            <a href="{{ route('subscriptions.packages') }}" 
                               class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Xem các gói dịch vụ
                            </a>
                        </div>
                    @endif

                    @if($subscriptions->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử gói dịch vụ</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Gói dịch vụ
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Trạng thái
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ngày bắt đầu
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ngày kết thúc
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Số tiền
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($subscriptions as $subscription)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-white">
                                                                    {{ strtoupper(substr($subscription->subscription_type, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ ucfirst($subscription->subscription_type) }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $subscription->payment_method ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($subscription->status === 'active')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ ucfirst($subscription->status) }}
                                                        </span>
                                                    @elseif($subscription->status === 'pending')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ ucfirst($subscription->status) }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ ucfirst($subscription->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $subscription->start_date->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $subscription->end_date->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($subscription->amount) }} VNĐ
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-center">
                        <a href="{{ route('subscriptions.packages') }}" 
                           class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quản lý gói dịch vụ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
