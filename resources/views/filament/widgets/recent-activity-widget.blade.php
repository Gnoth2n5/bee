<div class="p-6 bg-white rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hoạt động gần đây</h3>
    
    <div class="space-y-4">
        @foreach($this->getRecentActivities() as $activity)
            <div class="flex items-start space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-shadow">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <x-heroicon-o-{{ str_replace('heroicon-o-', '', $activity['icon']) }} 
                            class="w-4 h-4 {{ $activity['color'] }}" />
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $activity['title'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $activity['description'] }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $activity['time']->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endforeach
        
        @if($this->getRecentActivities()->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <x-heroicon-o-information-circle class="w-12 h-12 mx-auto text-gray-300 mb-4" />
                <p>Chưa có hoạt động nào</p>
            </div>
        @endif
    </div>
</div> 