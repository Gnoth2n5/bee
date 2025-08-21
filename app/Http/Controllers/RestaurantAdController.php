<?php

namespace App\Http\Controllers;

use App\Models\RestaurantAd;
use App\Models\Restaurant;
use App\Models\VietqrInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RestaurantAdController extends Controller
{
    public function __construct()
    {
        // Middleware được định nghĩa trong routes thay vì controller
    }

    public function index()
    {
        $user = Auth::user();
        $ads = $user->restaurantAds()->with('restaurant')->orderBy('created_at', 'desc')->get();

        return view('restaurant-ads.index', compact('ads'));
    }

    public function create()
    {
        $user = Auth::user();
        $restaurants = $user->restaurants;

        return view('restaurant-ads.create', compact('restaurants'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:50000',
            'payment_method' => 'required|in:vietqr',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Kiểm tra quyền sở hữu nhà hàng
        $restaurant = $user->restaurants()->find($request->restaurant_id);
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quảng cáo cho nhà hàng này'
            ], 403);
        }

        // Xử lý upload hình ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurant-ads', 'public');
        }

        // Tạo quảng cáo
        $ad = RestaurantAd::create([
            'restaurant_id' => $request->restaurant_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => 'pending',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => 'AD_' . Str::random(10),
        ]);

        // Tạo QR code thanh toán
        $vietqrAccount = VietqrInformation::where('status', 1)->first();

        if (!$vietqrAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Không có tài khoản thanh toán'
            ], 500);
        }

        $qrData = $vietqrAccount->generatePaymentCodeFromArray([
            'transaction_amount' => $request->amount,
            'message' => "Quang cao nha hang - {$restaurant->name}",
            'transaction_id' => $ad->transaction_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo quảng cáo thành công. Vui lòng quét mã QR để thanh toán.',
            'ad' => $ad,
            'qr_code' => $qrData['qr'],
            'amount' => $request->amount,
            'transaction_id' => $ad->transaction_id
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $ad = $user->restaurantAds()->with('restaurant')->findOrFail($id);

        return view('restaurant-ads.show', compact('ad'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $ad = $user->restaurantAds()->findOrFail($id);
        $restaurants = $user->restaurants;

        return view('restaurant-ads.edit', compact('ad', 'restaurants'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $ad = $user->restaurantAds()->findOrFail($id);

        // Kiểm tra quyền sở hữu nhà hàng
        $restaurant = $user->restaurants()->find($request->restaurant_id);
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quảng cáo cho nhà hàng này'
            ], 403);
        }

        // Xử lý upload hình ảnh mới
        if ($request->hasFile('image')) {
            // Xóa hình ảnh cũ
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }

            $imagePath = $request->file('image')->store('restaurant-ads', 'public');
            $ad->image = $imagePath;
        }

        $ad->update([
            'restaurant_id' => $request->restaurant_id,
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật quảng cáo thành công',
            'ad' => $ad
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $ad = $user->restaurantAds()->findOrFail($id);

        // Xóa hình ảnh
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa quảng cáo thành công'
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $ad = $user->restaurantAds()
            ->where('transaction_id', $request->transaction_id)
            ->where('status', 'pending')
            ->first();

        if (!$ad) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quảng cáo'
            ], 404);
        }

        // Cập nhật trạng thái thành công
        $ad->update([
            'status' => 'active',
            'payment_details' => json_encode([
                'verified_at' => now(),
                'method' => 'manual_verification'
            ])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công',
            'ad' => $ad
        ]);
    }

    public function getActiveAds()
    {
        $ads = RestaurantAd::with('restaurant', 'user')
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'ads' => $ads
        ]);
    }

    public function incrementViews($id)
    {
        $ad = RestaurantAd::findOrFail($id);
        $ad->incrementViews();

        return response()->json([
            'success' => true,
            'views' => $ad->views
        ]);
    }

    public function incrementClicks($id)
    {
        $ad = RestaurantAd::findOrFail($id);
        $ad->incrementClicks();

        return response()->json([
            'success' => true,
            'clicks' => $ad->clicks
        ]);
    }
}
