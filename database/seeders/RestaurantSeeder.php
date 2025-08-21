<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Services\GoogleMapsService;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $googleMapsService = app(GoogleMapsService::class);

        // Dữ liệu mẫu cho các nhà hàng nổi tiếng ở Hà Nội và Hà Nam
        $sampleRestaurants = [
            [
                'place_id' => 'ChIJN1t_tDeuNTERKxE8d61aX_E',
                'name' => 'Phở Gia Truyền Bát Đàn',
                'formatted_address' => '49 Bát Đàn, Cửa Đông, Hoàn Kiếm, Hà Nội, Việt Nam',
                'latitude' => 21.0368,
                'longitude' => 105.8342,
                'rating' => 4.5,
                'user_ratings_total' => 1250,
                'formatted_phone_number' => '+84 24 3828 5953',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 10:00 PM',
                        'Thứ 3: 6:00 AM – 10:00 PM',
                        'Thứ 4: 6:00 AM – 10:00 PM',
                        'Thứ 5: 6:00 AM – 10:00 PM',
                        'Thứ 6: 6:00 AM – 10:00 PM',
                        'Thứ 7: 6:00 AM – 10:00 PM',
                        'Chủ nhật: 6:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=sample1&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJN1t_tDeuNTERKxE8d61aX_F',
                'name' => 'Bún Chả Hương Liên',
                'formatted_address' => '24 Lê Văn Hưu, Hai Bà Trưng, Hà Nội, Việt Nam',
                'latitude' => 21.0245,
                'longitude' => 105.8412,
                'rating' => 4.3,
                'user_ratings_total' => 890,
                'formatted_phone_number' => '+84 24 3943 5953',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 7:00 AM – 9:00 PM',
                        'Thứ 3: 7:00 AM – 9:00 PM',
                        'Thứ 4: 7:00 AM – 9:00 PM',
                        'Thứ 5: 7:00 AM – 9:00 PM',
                        'Thứ 6: 7:00 AM – 9:00 PM',
                        'Thứ 7: 7:00 AM – 9:00 PM',
                        'Chủ nhật: 7:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=sample2&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJN1t_tDeuNTERKxE8d61aX_G',
                'name' => 'Nhà hàng Sen',
                'formatted_address' => '60 Nguyễn Thị Minh Khai, Đống Đa, Hà Nội, Việt Nam',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
                'rating' => 4.7,
                'user_ratings_total' => 2100,
                'formatted_phone_number' => '+84 24 3943 5954',
                'website' => 'https://nhahangsen.com',
                'price_level' => 3,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 10:00 AM – 10:00 PM',
                        'Thứ 3: 10:00 AM – 10:00 PM',
                        'Thứ 4: 10:00 AM – 10:00 PM',
                        'Thứ 5: 10:00 AM – 10:00 PM',
                        'Thứ 6: 10:00 AM – 10:00 PM',
                        'Thứ 7: 10:00 AM – 10:00 PM',
                        'Chủ nhật: 10:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=sample3&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJN1t_tDeuNTERKxE8d61aX_H',
                'name' => 'Cơm Tấm Sài Gòn',
                'formatted_address' => '123 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội, Việt Nam',
                'latitude' => 21.0245,
                'longitude' => 105.8412,
                'rating' => 4.1,
                'user_ratings_total' => 650,
                'formatted_phone_number' => '+84 24 3943 5955',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 9:00 PM',
                        'Thứ 3: 6:00 AM – 9:00 PM',
                        'Thứ 4: 6:00 AM – 9:00 PM',
                        'Thứ 5: 6:00 AM – 9:00 PM',
                        'Thứ 6: 6:00 AM – 9:00 PM',
                        'Thứ 7: 6:00 AM – 9:00 PM',
                        'Chủ nhật: 6:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=sample4&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJN1t_tDeuNTERKxE8d61aX_I',
                'name' => 'Pizza Hut',
                'formatted_address' => '456 Lê Duẩn, Đống Đa, Hà Nội, Việt Nam',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
                'rating' => 4.2,
                'user_ratings_total' => 1200,
                'formatted_phone_number' => '+84 24 3943 5956',
                'website' => 'https://pizzahut.vn',
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 10:00 AM – 11:00 PM',
                        'Thứ 3: 10:00 AM – 11:00 PM',
                        'Thứ 4: 10:00 AM – 11:00 PM',
                        'Thứ 5: 10:00 AM – 11:00 PM',
                        'Thứ 6: 10:00 AM – 11:00 PM',
                        'Thứ 7: 10:00 AM – 11:00 PM',
                        'Chủ nhật: 10:00 AM – 11:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=sample5&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            // Nhà hàng ở Hà Nam
            [
                'place_id' => 'ChIJHaNam_restaurant_001',
                'name' => 'Nhà hàng Phú Lý',
                'formatted_address' => '123 Đường Trần Hưng Đạo, Phú Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5411,
                'longitude' => 105.9138,
                'rating' => 4.4,
                'user_ratings_total' => 320,
                'formatted_phone_number' => '+84 226 385 1234',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 7:00 AM – 10:00 PM',
                        'Thứ 3: 7:00 AM – 10:00 PM',
                        'Thứ 4: 7:00 AM – 10:00 PM',
                        'Thứ 5: 7:00 AM – 10:00 PM',
                        'Thứ 6: 7:00 AM – 10:00 PM',
                        'Thứ 7: 7:00 AM – 10:00 PM',
                        'Chủ nhật: 7:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam1&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_002',
                'name' => 'Quán Cơm Bình Dân Hà Nam',
                'formatted_address' => '456 Đường Lý Thường Kiệt, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5456,
                'longitude' => 105.9189,
                'rating' => 4.1,
                'user_ratings_total' => 180,
                'formatted_phone_number' => '+84 226 385 5678',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 9:00 PM',
                        'Thứ 3: 6:00 AM – 9:00 PM',
                        'Thứ 4: 6:00 AM – 9:00 PM',
                        'Thứ 5: 6:00 AM – 9:00 PM',
                        'Thứ 6: 6:00 AM – 9:00 PM',
                        'Thứ 7: 6:00 AM – 9:00 PM',
                        'Chủ nhật: 6:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam2&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_003',
                'name' => 'Nhà hàng Hải Sản Hà Nam',
                'formatted_address' => '789 Đường Nguyễn Du, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5489,
                'longitude' => 105.9223,
                'rating' => 4.6,
                'user_ratings_total' => 450,
                'formatted_phone_number' => '+84 226 385 9012',
                'website' => 'https://haisanhanam.com',
                'price_level' => 3,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 10:00 AM – 11:00 PM',
                        'Thứ 3: 10:00 AM – 11:00 PM',
                        'Thứ 4: 10:00 AM – 11:00 PM',
                        'Thứ 5: 10:00 AM – 11:00 PM',
                        'Thứ 6: 10:00 AM – 11:00 PM',
                        'Thứ 7: 10:00 AM – 11:00 PM',
                        'Chủ nhật: 10:00 AM – 11:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam3&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_004',
                'name' => 'Quán Phở Hà Nam',
                'formatted_address' => '321 Đường Lê Lợi, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5423,
                'longitude' => 105.9167,
                'rating' => 4.3,
                'user_ratings_total' => 280,
                'formatted_phone_number' => '+84 226 385 3456',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 10:00 PM',
                        'Thứ 3: 6:00 AM – 10:00 PM',
                        'Thứ 4: 6:00 AM – 10:00 PM',
                        'Thứ 5: 6:00 AM – 10:00 PM',
                        'Thứ 6: 6:00 AM – 10:00 PM',
                        'Thứ 7: 6:00 AM – 10:00 PM',
                        'Chủ nhật: 6:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam4&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_005',
                'name' => 'Nhà hàng Chay Hà Nam',
                'formatted_address' => '654 Đường Trần Phú, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5467,
                'longitude' => 105.9201,
                'rating' => 4.5,
                'user_ratings_total' => 190,
                'formatted_phone_number' => '+84 226 385 7890',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 7:00 AM – 9:00 PM',
                        'Thứ 3: 7:00 AM – 9:00 PM',
                        'Thứ 4: 7:00 AM – 9:00 PM',
                        'Thứ 5: 7:00 AM – 9:00 PM',
                        'Thứ 6: 7:00 AM – 9:00 PM',
                        'Thứ 7: 7:00 AM – 9:00 PM',
                        'Chủ nhật: 7:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam5&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_006',
                'name' => 'Quán Bún Bò Hà Nam',
                'formatted_address' => '987 Đường Hùng Vương, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5445,
                'longitude' => 105.9178,
                'rating' => 4.2,
                'user_ratings_total' => 220,
                'formatted_phone_number' => '+84 226 385 2345',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 9:00 PM',
                        'Thứ 3: 6:00 AM – 9:00 PM',
                        'Thứ 4: 6:00 AM – 9:00 PM',
                        'Thứ 5: 6:00 AM – 9:00 PM',
                        'Thứ 6: 6:00 AM – 9:00 PM',
                        'Thứ 7: 6:00 AM – 9:00 PM',
                        'Chủ nhật: 6:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam6&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_007',
                'name' => 'Nhà hàng Gà Nướng Hà Nam',
                'formatted_address' => '147 Đường Lê Hồng Phong, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5492,
                'longitude' => 105.9215,
                'rating' => 4.4,
                'user_ratings_total' => 310,
                'formatted_phone_number' => '+84 226 385 6789',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 10:00 AM – 10:00 PM',
                        'Thứ 3: 10:00 AM – 10:00 PM',
                        'Thứ 4: 10:00 AM – 10:00 PM',
                        'Thứ 5: 10:00 AM – 10:00 PM',
                        'Thứ 6: 10:00 AM – 10:00 PM',
                        'Thứ 7: 10:00 AM – 10:00 PM',
                        'Chủ nhật: 10:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam7&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_008',
                'name' => 'Quán Lẩu Hà Nam',
                'formatted_address' => '258 Đường Nguyễn Trãi, Phủ Lý, Hà Nam, Việt Nam',
                'latitude' => 20.5438,
                'longitude' => 105.9192,
                'rating' => 4.3,
                'user_ratings_total' => 270,
                'formatted_phone_number' => '+84 226 385 4567',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 11:00 AM – 10:00 PM',
                        'Thứ 3: 11:00 AM – 10:00 PM',
                        'Thứ 4: 11:00 AM – 10:00 PM',
                        'Thứ 5: 11:00 AM – 10:00 PM',
                        'Thứ 6: 11:00 AM – 10:00 PM',
                        'Thứ 7: 11:00 AM – 10:00 PM',
                        'Chủ nhật: 11:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam8&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            // Nhà hàng ở các huyện khác của Hà Nam
            [
                'place_id' => 'ChIJHaNam_restaurant_009',
                'name' => 'Nhà hàng Kim Bảng',
                'formatted_address' => '123 Đường Quốc lộ 21A, Kim Bảng, Hà Nam, Việt Nam',
                'latitude' => 20.5800,
                'longitude' => 105.8500,
                'rating' => 4.2,
                'user_ratings_total' => 150,
                'formatted_phone_number' => '+84 226 385 1111',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 7:00 AM – 10:00 PM',
                        'Thứ 3: 7:00 AM – 10:00 PM',
                        'Thứ 4: 7:00 AM – 10:00 PM',
                        'Thứ 5: 7:00 AM – 10:00 PM',
                        'Thứ 6: 7:00 AM – 10:00 PM',
                        'Thứ 7: 7:00 AM – 10:00 PM',
                        'Chủ nhật: 7:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam9&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_010',
                'name' => 'Quán Cơm Thanh Liêm',
                'formatted_address' => '456 Đường ĐT971, Thanh Liêm, Hà Nam, Việt Nam',
                'latitude' => 20.5200,
                'longitude' => 105.9500,
                'rating' => 4.0,
                'user_ratings_total' => 120,
                'formatted_phone_number' => '+84 226 385 2222',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 9:00 PM',
                        'Thứ 3: 6:00 AM – 9:00 PM',
                        'Thứ 4: 6:00 AM – 9:00 PM',
                        'Thứ 5: 6:00 AM – 9:00 PM',
                        'Thứ 6: 6:00 AM – 9:00 PM',
                        'Thứ 7: 6:00 AM – 9:00 PM',
                        'Chủ nhật: 6:00 AM – 9:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam10&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_011',
                'name' => 'Nhà hàng Bình Lục',
                'formatted_address' => '789 Đường ĐT9710, Bình Lục, Hà Nam, Việt Nam',
                'latitude' => 20.5000,
                'longitude' => 106.0000,
                'rating' => 4.1,
                'user_ratings_total' => 180,
                'formatted_phone_number' => '+84 226 385 3333',
                'website' => null,
                'price_level' => 2,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 8:00 AM – 10:00 PM',
                        'Thứ 3: 8:00 AM – 10:00 PM',
                        'Thứ 4: 8:00 AM – 10:00 PM',
                        'Thứ 5: 8:00 AM – 10:00 PM',
                        'Thứ 6: 8:00 AM – 10:00 PM',
                        'Thứ 7: 8:00 AM – 10:00 PM',
                        'Chủ nhật: 8:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam11&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ],
            [
                'place_id' => 'ChIJHaNam_restaurant_012',
                'name' => 'Quán Phở Lý Nhân',
                'formatted_address' => '321 Đường ĐT491, Lý Nhân, Hà Nam, Việt Nam',
                'latitude' => 20.5600,
                'longitude' => 106.0500,
                'rating' => 4.3,
                'user_ratings_total' => 200,
                'formatted_phone_number' => '+84 226 385 4444',
                'website' => null,
                'price_level' => 1,
                'types' => ['restaurant', 'food', 'establishment'],
                'opening_hours' => [
                    'open_now' => true,
                    'weekday_text' => [
                        'Thứ 2: 6:00 AM – 10:00 PM',
                        'Thứ 3: 6:00 AM – 10:00 PM',
                        'Thứ 4: 6:00 AM – 10:00 PM',
                        'Thứ 5: 6:00 AM – 10:00 PM',
                        'Thứ 6: 6:00 AM – 10:00 PM',
                        'Thứ 7: 6:00 AM – 10:00 PM',
                        'Chủ nhật: 6:00 AM – 10:00 PM'
                    ]
                ],
                'photos' => [
                    'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&maxheight=300&photo_reference=hanam12&key=' . config('services.google_maps.places_api_key')
                ],
                'reviews' => [],
                'is_active' => true
            ]
        ];

        foreach ($sampleRestaurants as $restaurantData) {
            Restaurant::updateOrCreate(
                ['place_id' => $restaurantData['place_id']],
                $restaurantData
            );
        }

        $this->command->info('Restaurant data seeded successfully!');
    }
}
