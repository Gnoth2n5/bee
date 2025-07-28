<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $posts = [
            [
                'title' => '10 Mẹo Nấu Ăn Cơ Bản Cho Người Mới Bắt Đầu',
                'excerpt' => 'Khám phá những mẹo nấu ăn cơ bản giúp bạn trở thành đầu bếp tại nhà trong thời gian ngắn nhất.',
                'content' => '<h2>1. Chuẩn bị nguyên liệu trước khi nấu</h2>
<p>Việc chuẩn bị tất cả nguyên liệu trước khi bắt đầu nấu sẽ giúp bạn tiết kiệm thời gian và tránh được những tình huống vội vàng.</p>

<h2>2. Sử dụng đúng loại dầu ăn</h2>
<p>Mỗi loại dầu có điểm bốc khói khác nhau. Dầu olive extra virgin phù hợp cho salad, dầu hạt cải phù hợp cho chiên xào.</p>

<h2>3. Nêm nếm từng chút một</h2>
<p>Luôn nêm nếm từ từ và nếm thử để đảm bảo hương vị phù hợp. Bạn có thể thêm gia vị nhưng khó lấy ra.</p>

<h2>4. Sử dụng nhiệt độ phù hợp</h2>
<p>Nhiệt độ cao phù hợp cho việc sear thịt, nhiệt độ trung bình cho xào rau, nhiệt độ thấp cho hầm.</p>

<h2>5. Không đảo thức ăn quá nhiều</h2>
<p>Để thức ăn có màu sắc đẹp và hương vị tốt, hãy để yên một chút trước khi đảo.</p>

<h2>6. Sử dụng nước dùng chất lượng</h2>
<p>Nước dùng tự làm sẽ tạo ra hương vị đậm đà hơn nhiều so với nước dùng đóng hộp.</p>

<h2>7. Để thịt nghỉ sau khi nấu</h2>
<p>Sau khi nấu thịt, hãy để nghỉ 5-10 phút để nước thịt phân bố đều và thịt mềm hơn.</p>

<h2>8. Sử dụng muối đúng cách</h2>
<p>Muối không chỉ tạo vị mặn mà còn làm nổi bật các hương vị khác. Nêm muối từ đầu quá trình nấu.</p>

<h2>9. Không sợ thử nghiệm</h2>
<p>Nấu ăn là nghệ thuật, hãy dũng cảm thử nghiệm với các nguyên liệu và phương pháp mới.</p>

<h2>10. Dọn dẹp trong khi nấu</h2>
<p>Dọn dẹp bếp trong khi nấu sẽ giúp bạn có không gian thoải mái và dễ dàng hoàn thành món ăn.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'view_count' => 1250,
            ],
            [
                'title' => 'Cách Làm Bánh Mì Việt Nam Truyền Thống',
                'excerpt' => 'Hướng dẫn chi tiết cách làm bánh mì Việt Nam với lớp vỏ giòn rụm và ruột mềm xốp đặc trưng.',
                'content' => '<h2>Nguyên liệu cần thiết</h2>
<ul>
<li>500g bột mì đa dụng</li>
<li>7g men nở khô</li>
<li>10g muối</li>
<li>30g đường</li>
<li>300ml nước ấm</li>
<li>30ml dầu ăn</li>
</ul>

<h2>Các bước thực hiện</h2>

<h3>Bước 1: Kích hoạt men</h3>
<p>Hòa men với 50ml nước ấm và 1 thìa đường, để 10 phút cho men nở.</p>

<h3>Bước 2: Trộn bột</h3>
<p>Trộn bột mì, muối, đường còn lại. Thêm nước men và nước ấm còn lại, nhào bột đến khi mịn.</p>

<h3>Bước 3: Ủ bột</h3>
<p>Để bột nghỉ 1-2 giờ ở nơi ấm, bột sẽ nở gấp đôi.</p>

<h3>Bước 4: Tạo hình</h3>
<p>Chia bột thành 8 phần, tạo hình bánh mì dài.</p>

<h3>Bước 5: Nướng bánh</h3>
<p>Nướng ở 200°C trong 20-25 phút cho đến khi bánh vàng giòn.</p>

<h2>Mẹo thành công</h2>
<p>Để bánh mì có vỏ giòn, hãy phun nước vào lò nướng trong 5 phút đầu. Nhiệt độ cao sẽ tạo ra lớp vỏ giòn đặc trưng của bánh mì Việt Nam.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'view_count' => 890,
            ],
            [
                'title' => 'Top 5 Loại Gia Vị Không Thể Thiếu Trong Bếp Việt',
                'excerpt' => 'Khám phá những loại gia vị cơ bản tạo nên hương vị đặc trưng của ẩm thực Việt Nam.',
                'content' => '<h2>1. Nước mắm</h2>
<p>Nước mắm là linh hồn của ẩm thực Việt Nam. Chọn nước mắm có độ đạm cao (40-50°N) để có hương vị đậm đà nhất.</p>

<h2>2. Bột ngọt (MSG)</h2>
<p>Mặc dù có nhiều tranh cãi, bột ngọt vẫn được sử dụng rộng rãi trong ẩm thực Việt để tăng vị umami.</p>

<h2>3. Hành tỏi</h2>
<p>Hành tỏi là bộ đôi không thể thiếu trong hầu hết các món ăn Việt, tạo hương thơm và vị đậm đà.</p>

<h2>4. Tiêu</h2>
<p>Tiêu đen và tiêu trắng được sử dụng để tạo vị cay nồng và hương thơm cho các món ăn.</p>

<h2>5. Ớt</h2>
<p>Ớt tươi, ớt khô và tương ớt là những gia vị không thể thiếu để tạo vị cay đặc trưng.</p>

<h2>Cách bảo quản gia vị</h2>
<p>Bảo quản gia vị trong hộp kín, tránh ánh nắng trực tiếp và độ ẩm cao để giữ được hương vị lâu nhất.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'view_count' => 567,
            ],
            [
                'title' => 'Cách Chọn Và Bảo Quản Rau Củ Quả Tươi',
                'excerpt' => 'Hướng dẫn chi tiết cách chọn lựa và bảo quản rau củ quả để luôn có nguyên liệu tươi ngon.',
                'content' => '<h2>Cách chọn rau củ quả tươi</h2>

<h3>Rau lá xanh</h3>
<p>Chọn rau có lá xanh tươi, không bị héo úa, không có đốm đen hoặc sâu bệnh.</p>

<h3>Củ quả</h3>
<p>Chọn củ quả cầm chắc tay, không bị mềm nhũn, không có vết thâm đen.</p>

<h3>Trái cây</h3>
<p>Chọn trái cây có mùi thơm tự nhiên, không bị dập nát, màu sắc tươi sáng.</p>

<h2>Cách bảo quản</h2>

<h3>Rau lá xanh</h3>
<p>Bọc trong khăn ẩm và để trong ngăn mát tủ lạnh, sử dụng trong 3-5 ngày.</p>

<h3>Củ quả</h3>
<p>Để ở nhiệt độ phòng hoặc ngăn mát tủ lạnh tùy loại, tránh ánh nắng trực tiếp.</p>

<h3>Trái cây</h3>
<p>Để ở nhiệt độ phòng để chín tự nhiên, sau đó chuyển vào tủ lạnh để bảo quản.</p>

<h2>Mẹo hữu ích</h2>
<p>Không rửa rau củ trước khi bảo quản, chỉ rửa khi sử dụng để tránh bị thối rữa.</p>',
                'status' => 'published',
                'published_at' => now()->subHours(12),
                'view_count' => 234,
            ],
            [
                'title' => 'Công Thức Làm Nước Mắm Pha Sẵn Chuẩn Vị',
                'excerpt' => 'Học cách pha nước mắm với tỷ lệ hoàn hảo để có được hương vị đậm đà và cân bằng.',
                'content' => '<h2>Nguyên liệu cần thiết</h2>
<ul>
<li>Nước mắm cốt (40-50°N)</li>
<li>Nước lọc</li>
<li>Đường</li>
<li>Chanh</li>
<li>Ớt tươi</li>
<li>Tỏi</li>
</ul>

<h2>Công thức cơ bản</h2>

<h3>Tỷ lệ chuẩn</h3>
<p>2 phần nước mắm : 1 phần nước : 1 phần đường : 1 phần chanh</p>

<h3>Cách pha</h3>
<ol>
<li>Hòa đường với nước ấm cho tan hoàn toàn</li>
<li>Thêm nước mắm và khuấy đều</li>
<li>Vắt chanh và điều chỉnh vị chua</li>
<li>Thêm ớt và tỏi băm nhỏ</li>
</ol>

<h2>Biến thể khác nhau</h2>

<h3>Nước mắm chấm gỏi</h3>
<p>Thêm ớt xanh, tỏi, chanh và một chút đường để có vị chua ngọt.</p>

<h3>Nước mắm pha sẵn</h3>
<p>Pha loãng hơn với tỷ lệ 1:1:1:1 để dùng cho các món ăn.</p>

<h2>Mẹo thành công</h2>
<p>Luôn nếm thử và điều chỉnh theo khẩu vị. Nước mắm pha sẵn có thể bảo quản trong tủ lạnh 1-2 tuần.</p>',
                'status' => 'published',
                'published_at' => now()->subHours(6),
                'view_count' => 123,
            ],
        ];

        foreach ($posts as $postData) {
            $post = new Post();
            $post->title = $postData['title'];
            $post->slug = Str::slug($postData['title']);
            $post->excerpt = $postData['excerpt'];
            $post->content = $postData['content'];
            $post->status = $postData['status'];
            $post->published_at = $postData['published_at'];
            $post->view_count = $postData['view_count'];
            $post->user_id = $users->random()->id;
            $post->save();
        }

        $this->command->info('Posts seeded successfully!');
    }
}