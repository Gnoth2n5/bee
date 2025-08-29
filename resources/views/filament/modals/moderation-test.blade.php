<div class="space-y-4">
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Công thức: {{ $recipe->title }}
        </h3>
        <p class="text-sm text-gray-600">
            Trạng thái hiện tại: 
            <span class="font-medium">
                @switch($recipe->status)
                    @case('draft')
                        <span class="text-gray-600">Bản nháp</span>
                        @break
                    @case('pending')
                        <span class="text-yellow-600">Chờ phê duyệt</span>
                        @break
                    @case('approved')
                        <span class="text-green-600">Đã phê duyệt</span>
                        @break
                    @case('rejected')
                        <span class="text-red-600">Bị từ chối</span>
                        @break
                    @default
                        <span class="text-gray-600">{{ $recipe->status }}</span>
                @endswitch
            </span>
        </p>
    </div>

    @if($hasViolations)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <h4 class="text-lg font-medium text-red-800">
                    Phát hiện vi phạm quy tắc kiểm duyệt ({{ count($violations) }} vi phạm)
                </h4>
            </div>
            
            <div class="mt-4 space-y-3">
                @foreach($violations as $result)
                    @php 
                        $rule = $result['rule']; 
                        $ruleName = $rule ? $rule->name : ($result['name'] ?? 'Kiểm tra hệ thống');
                    @endphp
                    <div class="bg-white border border-red-200 rounded p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-red-800">{{ $rule ? $rule->name : ($result['name'] ?? 'Kiểm tra') }}</h5>
                            @if($rule)
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($rule->action === 'reject') bg-red-100 text-red-800
                                    @elseif($rule->action === 'flag') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @switch($rule->action)
                                        @case('reject')
                                            Từ chối
                                            @break
                                        @case('flag')
                                            Đánh dấu
                                            @break
                                        @case('auto_approve')
                                            Tự động phê duyệt
                                            @break
                                    @endswitch
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    Kiểm tra hệ thống
                                </span>
                            @endif
                        </div>
                        
                        @if(isset($result['violation_details']['field']))
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Trường vi phạm:</strong> 
                                @switch($result['violation_details']['field'])
                                    @case('title')
                                        Tiêu đề
                                        @break
                                    @case('description')
                                        Mô tả
                                        @break
                                    @case('summary')
                                        Tóm tắt
                                        @break
                                    @case('ingredients')
                                        Nguyên liệu
                                        @break
                                    @case('instructions')
                                        Hướng dẫn
                                        @break
                                    @case('tips')
                                        Mẹo
                                        @break
                                    @case('notes')
                                        Ghi chú
                                        @break
                                    @default
                                        {{ $result['violation_details']['field'] }}
                                @endswitch
                            </p>
                        @endif
                        
                        @if($rule)
                            <p class="text-sm text-gray-600">
                                <strong>Từ khóa cấm:</strong> 
                                <span class="font-mono text-xs bg-red-100 px-2 py-1 rounded">
                                    {{ $rule->keywords }}
                                </span>
                            </p>
                            
                            @if(isset($result['violation_details']['found_keywords']))
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Từ khóa vi phạm:</strong>
                                    @foreach($result['violation_details']['found_keywords'] as $keyword)
                                        <span class="inline-block bg-red-200 text-red-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                            {{ $keyword }}
                                        </span>
                                    @endforeach
                                </p>
                            @endif
                            
                            @if(isset($result['violation_details']['content']))
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Nội dung vi phạm:</strong>
                                    <span class="font-mono text-xs bg-yellow-100 px-2 py-1 rounded">
                                        {{ Str::limit($result['violation_details']['content'], 100) }}
                                    </span>
                                </p>
                            @endif
                            
                            @if($rule->description)
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Mô tả quy tắc:</strong> {{ $rule->description }}
                                </p>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <h4 class="text-lg font-medium text-green-800">
                    Không phát hiện vi phạm quy tắc kiểm duyệt
                </h4>
            </div>
            <p class="text-sm text-green-700 mt-2">
                Công thức này đã vượt qua tất cả các quy tắc kiểm duyệt và có thể được phê duyệt.
            </p>
        </div>
    @endif

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-800 mb-2">Thông tin kiểm tra</h4>
        <div class="text-sm text-blue-700 space-y-1">
            <p><strong>Tổng số quy tắc:</strong> {{ count($results) }}</p>
            <p><strong>Quy tắc đang hoạt động:</strong> {{ count(array_filter($results, fn($r) => $r['rule'] && $r['rule']->is_active)) }}</p>
            <p><strong>Quy tắc vi phạm:</strong> {{ count($violations ?? []) }}</p>
        </div>
    </div>
</div> 