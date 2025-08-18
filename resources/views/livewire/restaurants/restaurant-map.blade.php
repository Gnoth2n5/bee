<div class="min-h-screen bg-gray-50">
	<!-- Header -->
	<div class="bg-white shadow-sm border-b">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
			<div class="flex justify-between items-center">
				<h1 class="text-2xl font-bold text-gray-900">Tìm kiếm nhà hàng</h1>
				
				<!-- Button lấy vị trí hiện tại -->
				<button type="button" 
					class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 flex items-center space-x-2"
					onclick="getCurrentLocation()">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
					</svg>
					<span>Vị trí của tôi</span>
				</button>
			</div>
			
			<!-- Filter Buttons -->
			<div class="mt-4 flex space-x-2">
				<button type="button" 
					class="px-4 py-2 text-sm font-medium rounded-md {{ $selectedArea === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
					wire:click="filterByArea('all')">
					Tất cả ({{ count($restaurants) }})
				</button>
				<button type="button" 
					class="px-4 py-2 text-sm font-medium rounded-md {{ $selectedArea === 'hanoi' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
					wire:click="filterByArea('hanoi')">
					Hà Nội ({{ count($restaurants->filter(function($r) { return str_contains(strtolower($r->formatted_address), 'hà nội') || str_contains(strtolower($r->formatted_address), 'ha noi'); })) }})
				</button>
				<button type="button" 
					class="px-4 py-2 text-sm font-medium rounded-md {{ $selectedArea === 'hanam' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
					wire:click="filterByArea('hanam')">
					Hà Nam ({{ count($restaurants->filter(function($r) { return str_contains(strtolower($r->formatted_address), 'hà nam') || str_contains(strtolower($r->formatted_address), 'ha nam'); })) }})
				</button>
			</div>
		</div>
	</div>

	<!-- Main -->
	<div class="flex h-[calc(100vh-200px)]">
		<!-- Map -->
		<div class="flex-1 relative">
			<div id="map" class="w-full h-full" wire:ignore></div>
		</div>

		<!-- List -->
		<div class="w-96 bg-white border-l border-gray-200 overflow-y-auto">
			<div class="p-4">
				<h3 class="text-lg font-medium text-gray-900 mb-4">
					@if($selectedArea === 'hanoi')
						Nhà hàng Hà Nội ({{ count($this->filteredRestaurants) }})
					@elseif($selectedArea === 'hanam')
						Nhà hàng Hà Nam ({{ count($this->filteredRestaurants) }})
					@else
						Tất cả nhà hàng ({{ count($this->filteredRestaurants) }})
					@endif
				</h3>
				<div class="space-y-3">
					@foreach($this->filteredRestaurants as $restaurant)
						<div class="border rounded p-3 hover:bg-gray-50">
							<div class="flex justify-between items-start">
								<button type="button" class="flex-1 text-left" wire:click="selectRestaurant('{{ $restaurant->place_id }}')">
									<div class="font-medium">{{ $restaurant->name }}</div>
									<div class="text-sm text-gray-600">{{ $restaurant->short_address }}</div>
									<div class="text-xs text-gray-500 mt-1">
										⭐ {{ $restaurant->rating }} ({{ $restaurant->user_ratings_total }} đánh giá)
									</div>
								</button>
								<button type="button" class="ml-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600" 
									onclick="showDirections('{{ $restaurant->latitude }}', '{{ $restaurant->longitude }}', '{{ $restaurant->name }}')">
									Chỉ đường
								</button>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
	let map; let infoWindow; let markers = []; let directionsService; let directionsRenderer; let markerClusterer;
	let currentLocationMarker = null; let currentLocation = null;
	
	function initMap() {
		map = new google.maps.Map(document.getElementById('map'), { 
			center: { lat: {{ $latitude }}, lng: {{ $longitude }} }, 
			zoom: {{ $zoom }} 
		});
		
		infoWindow = new google.maps.InfoWindow();
		directionsService = new google.maps.DirectionsService();
		directionsRenderer = new google.maps.DirectionsRenderer();
		directionsRenderer.setMap(map);
		
		// Thêm markers từ dữ liệu đã lọc
		@foreach($this->filteredRestaurants as $restaurant) 
		addMarker(@json($restaurant)); 
		@endforeach
		
		// Tạo marker clusterer
		markerClusterer = new markerClusterer.MarkerClusterer({
			map,
			markers: markers.map(m => m.marker),
			algorithm: new markerClusterer.SuperClusterAlgorithm({
				radius: 100,
				maxZoom: 15
			})
		});
	}
	
	function addMarker(r) {
		if (!r.latitude || !r.longitude) return;
		const marker = new google.maps.Marker({ 
			map, 
			position: { lat: parseFloat(r.latitude), lng: parseFloat(r.longitude) }, 
			title: r.name,
			icon: {
				url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
					<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
						<circle cx="10" cy="10" r="8" fill="#4285F4" stroke="#FFFFFF" stroke-width="2"/>
						<circle cx="10" cy="10" r="3" fill="#FFFFFF"/>
					</svg>
				`),
				scaledSize: new google.maps.Size(20, 20),
				anchor: new google.maps.Point(10, 10)
			}
		});
		marker.addListener('click', () => { 
			infoWindow.setContent(`<div><strong>${r.name}</strong><br>${r.formatted_address || r.short_address || ''}</div>`); 
			infoWindow.open(map, marker); 
		});
		markers.push({ place_id: r.place_id, marker, restaurant: r });
	}
	
	function showDirections(lat, lng, name) {
		// Lấy vị trí hiện tại
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				(position) => {
					const origin = { lat: position.coords.latitude, lng: position.coords.longitude };
					const destination = { lat: parseFloat(lat), lng: parseFloat(lng) };
					
					// Tạo marker đánh dấu điểm đến
					const destinationMarker = new google.maps.Marker({
						position: destination,
						map: map,
						title: name,
						icon: {
							url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
								<svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
									<circle cx="16" cy="16" r="12" fill="#FF0000" stroke="#FFFFFF" stroke-width="2"/>
									<circle cx="16" cy="16" r="6" fill="#FFFFFF"/>
								</svg>
							`),
							scaledSize: new google.maps.Size(32, 32),
							anchor: new google.maps.Point(16, 16)
						}
					});
					
					const request = {
						origin: origin,
						destination: destination,
						travelMode: google.maps.TravelMode.DRIVING
					};
					
					directionsService.route(request, (result, status) => {
						if (status === 'OK') {
							// Vẽ đường đi trên bản đồ
							directionsRenderer.setDirections(result);
							
							// Hiển thị thông tin đường đi
							const route = result.routes[0].legs[0];
							const distance = route.distance.text;
							const duration = route.duration.text;
							
							infoWindow.setContent(`
								<div class="p-3">
									<h3 class="font-bold text-lg">${name}</h3>
									<p class="text-sm text-gray-600">${route.end_address}</p>
									<div class="mt-2 text-sm">
										<p><strong>Khoảng cách:</strong> ${distance}</p>
										<p><strong>Thời gian:</strong> ${duration}</p>
									</div>
									<button onclick="clearDirections()" class="mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
										Xóa đường đi
									</button>
								</div>
							`);
							infoWindow.setPosition(destination);
							infoWindow.open(map);
							
							// Lưu marker để có thể xóa sau
							window.destinationMarker = destinationMarker;
						} else {
							// Nếu Directions API không hoạt động, vẽ đường thẳng
							const path = new google.maps.Polyline({
								path: [origin, destination],
								geodesic: true,
								strokeColor: '#FF0000',
								strokeOpacity: 1.0,
								strokeWeight: 3,
								map: map
							});
							
							// Tính khoảng cách
							const distance = google.maps.geometry.spherical.computeDistanceBetween(
								new google.maps.LatLng(origin),
								new google.maps.LatLng(destination)
							);
							const distanceKm = (distance / 1000).toFixed(1);
							
							infoWindow.setContent(`
								<div class="p-3">
									<h3 class="font-bold text-lg">${name}</h3>
									<p class="text-sm text-gray-600">Khoảng cách: ${distanceKm} km</p>
									<p class="text-xs text-gray-500 mt-1">(Đường thẳng - không phải đường thực tế)</p>
									<button onclick="clearDirections()" class="mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
										Xóa đường đi
									</button>
								</div>
							`);
							infoWindow.setPosition(destination);
							infoWindow.open(map);
							
							// Lưu polyline và marker để có thể xóa sau
							window.currentPolyline = path;
							window.destinationMarker = destinationMarker;
						}
					});
				},
				(error) => {
					alert('Không thể lấy vị trí hiện tại. Vui lòng cho phép truy cập vị trí.');
				}
			);
		} else {
			alert('Trình duyệt không hỗ trợ định vị.');
		}
	}
	
	function clearDirections() {
		// Xóa đường đi từ Directions API
		directionsRenderer.setDirections({routes: []});
		
		// Xóa polyline nếu có
		if (window.currentPolyline) {
			window.currentPolyline.setMap(null);
			window.currentPolyline = null;
		}
		
		// Xóa marker đánh dấu điểm đến nếu có
		if (window.destinationMarker) {
			window.destinationMarker.setMap(null);
			window.destinationMarker = null;
		}
		
		infoWindow.close();
	}

	function getCurrentLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				(position) => {
					const latitude = position.coords.latitude;
					const longitude = position.coords.longitude;
					currentLocation = { lat: latitude, lng: longitude };
					
					// Xóa marker cũ nếu có
					if (currentLocationMarker) {
						currentLocationMarker.setMap(null);
					}
					
					// Tạo marker mới cho vị trí hiện tại
					currentLocationMarker = new google.maps.Marker({
						position: currentLocation,
						map: map,
						title: 'Vị trí của bạn',
						icon: {
							url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
								<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
									<circle cx="12" cy="12" r="10" fill="#4CAF50" stroke="#FFFFFF" stroke-width="2"/>
									<circle cx="12" cy="12" r="4" fill="#FFFFFF"/>
									<circle cx="12" cy="12" r="1" fill="#4CAF50"/>
								</svg>
							`),
							scaledSize: new google.maps.Size(24, 24),
							anchor: new google.maps.Point(12, 12)
						}
					});
					
					// Thêm event listener cho marker vị trí hiện tại
					currentLocationMarker.addListener('click', () => {
						showCurrentLocationInfo();
					});
					
					// Di chuyển map đến vị trí hiện tại
					map.panTo(currentLocation);
					map.setZoom(16);
					
					// Gửi thông tin vị trí đến Livewire
					Livewire.dispatch('locationUpdated', [latitude, longitude]);
				},
				(error) => {
					alert('Không thể lấy vị trí hiện tại. Vui lòng cho phép truy cập vị trí.');
				}
			);
		} else {
			alert('Trình duyệt không hỗ trợ định vị.');
		}
	}
	
	function showCurrentLocationInfo() {
		if (!currentLocation) return;
		
		// Tìm 3 nhà hàng gần nhất
		let nearbyRestaurants = [];
		
		markers.forEach(markerData => {
			const restaurant = markerData.restaurant;
			if (restaurant && restaurant.latitude && restaurant.longitude) {
				const distance = google.maps.geometry.spherical.computeDistanceBetween(
					new google.maps.LatLng(currentLocation),
					new google.maps.LatLng(restaurant.latitude, restaurant.longitude)
				);
				
				nearbyRestaurants.push({
					...restaurant,
					distance: distance
				});
			}
		});
		
		// Sắp xếp theo khoảng cách và lấy 3 nhà hàng gần nhất
		nearbyRestaurants.sort((a, b) => a.distance - b.distance);
		nearbyRestaurants = nearbyRestaurants.slice(0, 3);
		
		let content = `
			<div class="p-3 max-w-sm">
				<h3 class="font-bold text-lg text-green-600 mb-2">📍 Vị trí của bạn</h3>
				<p class="text-sm text-gray-600 mb-3">Tọa độ: ${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}</p>
		`;
		
		if (nearbyRestaurants.length > 0) {
			content += `
				<div class="space-y-2">
					<p class="text-sm font-medium text-blue-600">🍽️ Nhà hàng gần nhất:</p>
			`;
			
			nearbyRestaurants.forEach((restaurant, index) => {
				const distanceKm = (restaurant.distance / 1000).toFixed(1);
				const icon = index === 0 ? '🥇' : index === 1 ? '🥈' : '🥉';
				
				content += `
					<div class="p-2 bg-gray-50 rounded border-l-4 border-blue-400">
						<div class="flex justify-between items-start">
							<div class="flex-1">
								<p class="text-sm font-medium">${icon} ${restaurant.name}</p>
								<p class="text-xs text-gray-500">${restaurant.formatted_address || restaurant.short_address || ''}</p>
								<p class="text-xs text-blue-600 font-medium">⭐ ${restaurant.rating} (${restaurant.user_ratings_total} đánh giá)</p>
								<p class="text-xs text-gray-600">Cách ${distanceKm} km</p>
							</div>
							<button onclick="showDirections('${restaurant.latitude}', '${restaurant.longitude}', '${restaurant.name}')" 
								class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
								Chỉ đường
							</button>
						</div>
					</div>
				`;
			});
			
			content += `</div>`;
		} else {
			content += `
				<div class="p-2 bg-yellow-50 rounded border-l-4 border-yellow-400">
					<p class="text-sm text-yellow-700">Không tìm thấy nhà hàng nào gần đây</p>
				</div>
			`;
		}
		
		content += `
				<div class="mt-3 pt-2 border-t">
					<button onclick="infoWindow.close()" class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
						Đóng
					</button>
				</div>
			</div>
		`;
		
		infoWindow.setContent(content);
		infoWindow.setPosition(currentLocation);
		infoWindow.open(map);
	}
	
	document.addEventListener('livewire:init', () => {
		Livewire.on('focusMapTo', (payload) => {
			const { latitude, longitude, name, address, place_id } = payload;
			const position = { lat: parseFloat(latitude), lng: parseFloat(longitude) };
			map.panTo(position); 
			map.setZoom(16);
			
			const found = markers.find(m => m.place_id === place_id);
			if (found) { 
				infoWindow.setContent(`<div><strong>${name}</strong><br>${address || ''}</div>`); 
				infoWindow.open(map, found.marker); 
				return; 
			}
			const tempMarker = new google.maps.Marker({ map, position, title: name });
			infoWindow.setContent(`<div><strong>${name}</strong><br>${address || ''}</div>`);
			infoWindow.open(map, tempMarker);
		});

		Livewire.on('updateMapCenter', (payload) => {
			const { latitude, longitude, zoom } = payload;
			const position = { lat: parseFloat(latitude), lng: parseFloat(longitude) };
			map.setCenter(position);
			map.setZoom(parseInt(zoom));
		});

		Livewire.on('updateMapMarkers', (payload) => {
			// Xóa tất cả markers cũ
			markers.forEach(m => m.marker.setMap(null));
			markers = [];
			
			// Thêm markers mới
			payload.restaurants.forEach(restaurant => {
				addMarker(restaurant);
			});

			// Cập nhật clusterer với markers mới
			if (markerClusterer) {
				markerClusterer.clear();
				markerClusterer.addMarkers(markers.map(m => m.marker));
			}
		});
		
		Livewire.on('locationUpdated', (payload) => {
			// Xử lý khi vị trí được cập nhật từ Livewire
			console.log('Location updated:', payload);
		});
	});
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&libraries=places,geometry&callback=initMap"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
@endpush
