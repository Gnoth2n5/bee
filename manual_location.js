
// Add this to browser console or page
function setManualLocation(cityName, lat, lng) {
    const locationData = {
        latitude: lat,
        longitude: lng,
        timestamp: new Date().getTime(),
        manual_override: true,
        city_name: cityName
    };
    
    localStorage.setItem('user_location', JSON.stringify(locationData));
    console.log('✅ Manual location set:', locationData);
    
    // Also send to Livewire if available
    if (window.Livewire) {
        Livewire.first().call('setUserLocation', lat, lng);
    }
    
    location.reload();
}

// Usage:
// setManualLocation('Ninh Bình', 20.2506, 105.9744);
