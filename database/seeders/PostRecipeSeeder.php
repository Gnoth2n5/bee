<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $recipes = Recipe::where('status', 'approved')->get();

        if ($users->isEmpty()) {
            $this->command->error('Cần có users trước khi chạy seeder này!');
            return;
        }

        $posts = [
            [
                'title' => 'Top 5 Món Phở Nổi Tiếng Việt Nam',
                'excerpt' => 'Khám phá những món phở đặc trưng của các vùng miền Việt Nam, từ phở bò Hà Nội đến phở gà Nam Định.',
                'content' => '<h2>Phở - Món ăn quốc hồn quốc túy</h2>
                
                <p>Phở không chỉ là một món ăn đơn thuần mà còn là biểu tượng văn hóa ẩm thực Việt Nam. Mỗi vùng miền lại có cách chế biến phở riêng, tạo nên những hương vị độc đáo không thể lẫn lộn.</p>

                <h3>1. Phở Bò Hà Nội</h3>
                <p>Phở bò Hà Nội được coi là "gốc" của món phở Việt Nam. Nước dùng trong veo, bánh phở mỏng và dai, thịt bò tươi ngon. Đặc biệt là cách ăn phở Hà Nội rất tinh tế với rau thơm, chanh, ớt và nước mắm.</p>

                <h3>2. Phở Gà Nam Định</h3>
                <p>Phở gà Nam Định có nét đặc trưng riêng với nước dùng trong, thịt gà mềm và bánh phở sợi nhỏ. Món này thường được ăn kèm với dưa góp và nước mắm pha.</p>

                <h3>3. Phở Bò Huế</h3>
                <p>Phở bò Huế có vị cay nồng đặc trưng với sả, mắm ruốc và ớt hiểm. Bún sợi to, thịt bò mềm và giò heo tạo nên hương vị đậm đà khó quên.</p>

                <h3>4. Phở Gà Sài Gòn</h3>
                <p>Phở gà Sài Gòn có nước dùng đậm đà hơn, thịt gà được luộc kỹ và thường ăn kèm với giá đỗ, rau thơm và nước mắm pha.</p>

                <h3>5. Phở Bò Bắc Ninh</h3>
                <p>Phở bò Bắc Ninh nổi tiếng với nước dùng ngọt tự nhiên, thịt bò tươi và bánh phở mỏng. Món này thường được ăn kèm với quẩy và nước mắm.</p>

                <h2>Bí quyết nấu phở ngon</h2>
                <ul>
                    <li>Nước dùng phải nấu từ xương tươi</li>
                    <li>Gia vị phải được nướng thơm trước khi nấu</li>
                    <li>Thịt phải được thái mỏng và trần qua nước sôi</li>
                    <li>Bánh phở phải được trụng vừa chín tới</li>
                </ul>

                <p>Phở không chỉ là món ăn mà còn là văn hóa, là tình yêu của người Việt Nam với ẩm thực truyền thống.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(500, 3000),
                'meta_title' => 'Top 5 Món Phở Nổi Tiếng Việt Nam - Ẩm Thực Truyền Thống',
                'meta_description' => 'Khám phá 5 món phở đặc trưng của các vùng miền Việt Nam, từ phở bò Hà Nội đến phở gà Nam Định với bí quyết nấu phở ngon.',
                'meta_keywords' => 'phở, phở bò, phở gà, ẩm thực việt nam, món ăn truyền thống'
            ],
            [
                'title' => 'Cách Nấu Bún Chả Hà Nội Chuẩn Vị',
                'excerpt' => 'Hướng dẫn chi tiết cách nấu bún chả Hà Nội với thịt nướng thơm lừng và nước mắm pha chua ngọt đúng chuẩn.',
                'content' => '<h2>Bún Chả Hà Nội - Món ăn đặc trưng thủ đô</h2>
                
                <p>Bún chả Hà Nội là món ăn không thể thiếu khi nhắc đến ẩm thực thủ đô. Với thịt nướng thơm lừng, nước mắm pha chua ngọt và bún tươi, món ăn này đã chinh phục biết bao thực khách.</p>

                <h3>Nguyên liệu cần thiết</h3>
                <ul>
                    <li>Thịt ba chỉ: 300g</li>
                    <li>Bún tươi: 200g</li>
                    <li>Nước mắm: 3 muỗng</li>
                    <li>Đường: 2 muỗng</li>
                    <li>Chanh: 1 quả</li>
                    <li>Tỏi: 3 tép</li>
                    <li>Ớt: 2 quả</li>
                    <li>Rau sống: 1 bó</li>
                </ul>

                <h3>Cách chế biến</h3>
                <h4>Bước 1: Ướp thịt</h4>
                <p>Thái thịt ba chỉ thành miếng vừa ăn, ướp với nước mắm, đường, tỏi băm nhỏ. Để thịt ướp trong 30 phút để ngấm gia vị.</p>

                <h4>Bước 2: Nướng thịt</h4>
                <p>Nướng thịt trên than hoa hoặc bếp gas với lửa vừa. Thịt phải có độ cháy xém bên ngoài, bên trong vẫn mềm và thơm.</p>

                <h4>Bước 3: Pha nước mắm</h4>
                <p>Pha nước mắm theo tỷ lệ 1:1:1 (nước mắm:đường:chanh). Thêm tỏi băm và ớt thái nhỏ để tăng hương vị.</p>

                <h4>Bước 4: Trụng bún</h4>
                <p>Trụng bún trong nước sôi khoảng 30 giây, vớt ra để ráo nước.</p>

                <h4>Bước 5: Bày trí</h4>
                <p>Xếp rau sống, bún, thịt nướng lên đĩa. Chan nước mắm pha và thưởng thức.</p>

                <h3>Bí quyết nấu ngon</h3>
                <ul>
                    <li>Thịt nướng phải có độ cháy xém bên ngoài</li>
                    <li>Nước mắm pha theo tỷ lệ chuẩn</li>
                    <li>Rau sống phải tươi và sạch</li>
                    <li>Bún phải được trụng vừa chín tới</li>
                </ul>

                <p>Bún chả Hà Nội không chỉ ngon mà còn rất bổ dưỡng với protein từ thịt và vitamin từ rau sống.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(500, 3000),
                'meta_title' => 'Cách Nấu Bún Chả Hà Nội Chuẩn Vị - Hướng Dẫn Chi Tiết',
                'meta_description' => 'Hướng dẫn chi tiết cách nấu bún chả Hà Nội với thịt nướng thơm lừng và nước mắm pha chua ngọt đúng chuẩn.',
                'meta_keywords' => 'bún chả, bún chả hà nội, thịt nướng, nước mắm pha, ẩm thực hà nội'
            ],
            [
                'title' => 'Bí Quyết Nấu Cơm Tấm Sài Gòn Ngon Như Ngoài Tiệm',
                'excerpt' => 'Chia sẻ bí quyết nấu cơm tấm Sài Gòn với sườn nướng thơm lừng, chả trứng và nước mắm pha đặc biệt.',
                'content' => '<h2>Cơm Tấm Sài Gòn - Món ăn sáng phổ biến</h2>
                
                <p>Cơm tấm Sài Gòn là món ăn sáng không thể thiếu của người dân thành phố Hồ Chí Minh. Với sườn nướng thơm lừng, chả trứng và nước mắm pha đặc biệt, món ăn này đã trở thành biểu tượng ẩm thực miền Nam.</p>

                <h3>Nguyên liệu chính</h3>
                <ul>
                    <li>Gạo tấm: 200g</li>
                    <li>Sườn heo: 300g</li>
                    <li>Trứng gà: 2 quả</li>
                    <li>Nước mắm: 2 muỗng</li>
                    <li>Đường: 1 muỗng</li>
                    <li>Tỏi: 4 tép</li>
                    <li>Dưa leo: 1 quả</li>
                    <li>Cà chua: 2 quả</li>
                </ul>

                <h3>Cách chế biến</h3>
                <h4>Bước 1: Nấu cơm tấm</h4>
                <p>Vo gạo tấm sạch, ngâm trong nước 30 phút. Nấu cơm với tỷ lệ nước 1:1.2. Cơm tấm phải nấu vừa chín tới, không được nhão.</p>

                <h4>Bước 2: Ướp sườn</h4>
                <p>Ướp sườn với nước mắm, đường, tỏi băm nhỏ. Để sườn ướp trong 1 giờ để ngấm gia vị.</p>

                <h4>Bước 3: Nướng sườn</h4>
                <p>Nướng sườn trên than hoa với lửa vừa. Sườn phải có độ cháy xém và thơm. Thời gian nướng khoảng 15-20 phút.</p>

                <h4>Bước 4: Làm chả trứng</h4>
                <p>Đánh trứng với chút muối và tiêu. Chiên trứng thành hình tròn, vàng đều hai mặt.</p>

                <h4>Bước 5: Thái rau củ</h4>
                <p>Thái dưa leo và cà chua thành lát mỏng để ăn kèm.</p>

                <h4>Bước 6: Bày trí</h4>
                <p>Xếp cơm tấm, sườn nướng, chả trứng và rau củ lên đĩa. Chan nước mắm pha và thưởng thức.</p>

                <h3>Bí quyết nấu ngon</h3>
                <ul>
                    <li>Gạo tấm phải nấu vừa chín tới</li>
                    <li>Sườn nướng phải có độ cháy xém</li>
                    <li>Chả trứng phải vàng đều</li>
                    <li>Nước mắm pha phải đậm đà</li>
                </ul>

                <p>Cơm tấm Sài Gòn không chỉ ngon mà còn rất bổ dưỡng với protein từ thịt và trứng.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(500, 3000),
                'meta_title' => 'Bí Quyết Nấu Cơm Tấm Sài Gòn Ngon Như Ngoài Tiệm',
                'meta_description' => 'Chia sẻ bí quyết nấu cơm tấm Sài Gòn với sườn nướng thơm lừng, chả trứng và nước mắm pha đặc biệt.',
                'meta_keywords' => 'cơm tấm, cơm tấm sài gòn, sườn nướng, chả trứng, ẩm thực miền nam'
            ],
            [
                'title' => 'Hướng Dẫn Làm Bánh Xèo Miền Tây Giòn Rụm',
                'excerpt' => 'Cách làm bánh xèo miền Tây với bột giòn, nhân tôm thịt và rau sống tươi ngon.',
                'content' => '<h2>Bánh Xèo Miền Tây - Món ăn đặc trưng vùng sông nước</h2>
                
                <p>Bánh xèo miền Tây là món ăn không thể thiếu khi nhắc đến ẩm thực vùng sông nước. Với bột giòn, nhân tôm thịt và rau sống tươi ngon, món ăn này đã chinh phục biết bao thực khách.</p>

                <h3>Nguyên liệu cần thiết</h3>
                <ul>
                    <li>Bột bánh xèo: 300g</li>
                    <li>Tôm: 200g</li>
                    <li>Thịt ba chỉ: 200g</li>
                    <li>Giá đỗ: 100g</li>
                    <li>Hành lá: 1 bó</li>
                    <li>Nước mắm: vừa đủ</li>
                    <li>Rau sống: 1 bó</li>
                    <li>Dầu ăn: vừa đủ</li>
                </ul>

                <h3>Cách chế biến</h3>
                <h4>Bước 1: Pha bột</h4>
                <p>Pha bột bánh xèo với nước theo tỷ lệ 1:1.5. Thêm chút muối và để bột nghỉ 30 phút.</p>

                <h4>Bước 2: Chuẩn bị nhân</h4>
                <p>Thái thịt ba chỉ thành miếng nhỏ. Tôm bóc vỏ, để nguyên con. Thái hành lá nhỏ.</p>

                <h4>Bước 3: Đổ bánh</h4>
                <p>Làm nóng chảo với dầu ăn. Đổ bột vào chảo, xếp tôm, thịt, giá đỗ lên bánh. Đậy nắp và nướng đến khi bánh giòn.</p>

                <h4>Bước 4: Gập bánh</h4>
                <p>Khi bánh chín, gập bánh lại thành hình bán nguyệt và nướng thêm 2-3 phút cho giòn.</p>

                <h4>Bước 5: Bày trí</h4>
                <p>Bày bánh xèo ra đĩa cùng rau sống. Chấm nước mắm pha chua ngọt và thưởng thức.</p>

                <h3>Bí quyết làm ngon</h3>
                <ul>
                    <li>Bột phải pha đúng tỷ lệ</li>
                    <li>Chảo phải nóng đều</li>
                    <li>Bánh phải giòn bên ngoài, mềm bên trong</li>
                    <li>Nước mắm pha chua ngọt vừa ăn</li>
                </ul>

                <h3>Nước mắm pha</h3>
                <p>Pha nước mắm theo tỷ lệ: 3 muỗng nước mắm + 2 muỗng đường + 1 muỗng chanh + tỏi băm + ớt thái.</p>

                <p>Bánh xèo miền Tây không chỉ ngon mà còn rất bổ dưỡng với protein từ tôm thịt và vitamin từ rau sống.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(500, 3000),
                'meta_title' => 'Hướng Dẫn Làm Bánh Xèo Miền Tây Giòn Rụm - Công Thức Chi Tiết',
                'meta_description' => 'Cách làm bánh xèo miền Tây với bột giòn, nhân tôm thịt và rau sống tươi ngon.',
                'meta_keywords' => 'bánh xèo, bánh xèo miền tây, tôm thịt, rau sống, ẩm thực miền tây'
            ],
            [
                'title' => 'Cách Nấu Chè Ba Màu Truyền Thống Mát Lạnh',
                'excerpt' => 'Hướng dẫn nấu chè ba màu truyền thống với đậu xanh, bột lọc và nước cốt dừa thơm ngon.',
                'content' => '<h2>Chè Ba Màu - Món tráng miệng mát lạnh</h2>
                
                <p>Chè ba màu là món tráng miệng truyền thống của Việt Nam với ba lớp màu sắc đẹp mắt: xanh (đậu xanh), trắng (bột lọc) và đỏ (rau câu). Món chè này không chỉ ngon mà còn rất bổ dưỡng.</p>

                <h3>Nguyên liệu cần thiết</h3>
                <ul>
                    <li>Đậu xanh: 200g</li>
                    <li>Bột năng: 100g</li>
                    <li>Nước cốt dừa: 400ml</li>
                    <li>Đường: 150g</li>
                    <li>Lá dứa: 2 lá</li>
                    <li>Muối: 1/2 muỗng</li>
                    <li>Đá viên: vừa đủ</li>
                    <li>Rau câu: 50g</li>
                </ul>

                <h3>Cách chế biến</h3>
                <h4>Bước 1: Nấu đậu xanh</h4>
                <p>Ngâm đậu xanh trong nước 2 giờ. Nấu đậu xanh với đường và lá dứa đến khi mềm. Đậu xanh phải mềm nhưng không nát.</p>

                <h4>Bước 2: Làm bột lọc</h4>
                <p>Pha bột năng với nước và màu xanh lá dứa. Đổ bột vào khuôn và hấp chín. Bột lọc phải dai và trong.</p>

                <h4>Bước 3: Làm rau câu</h4>
                <p>Nấu rau câu với đường và màu đỏ. Đổ vào khuôn và để nguội. Rau câu phải mềm và có màu đỏ đẹp.</p>

                <h4>Bước 4: Nấu nước cốt dừa</h4>
                <p>Nấu nước cốt dừa với đường và chút muối. Nước cốt dừa phải thơm và béo.</p>

                <h4>Bước 5: Xếp từng lớp</h4>
                <p>Xếp từng lớp vào ly: đậu xanh ở dưới, bột lọc ở giữa, rau câu ở trên. Chan nước cốt dừa lên trên.</p>

                <h4>Bước 6: Thưởng thức</h4>
                <p>Thêm đá viên và thưởng thức. Chè ba màu phải mát lạnh và có vị ngọt vừa phải.</p>

                <h3>Bí quyết nấu ngon</h3>
                <ul>
                    <li>Đậu xanh phải mềm nhưng không nát</li>
                    <li>Bột lọc phải dai và trong</li>
                    <li>Rau câu phải mềm và có màu đẹp</li>
                    <li>Nước cốt dừa phải thơm và béo</li>
                </ul>

                <h3>Lưu ý khi nấu</h3>
                <ul>
                    <li>Ngâm đậu xanh đủ thời gian</li>
                    <li>Nấu từng lớp riêng biệt</li>
                    <li>Để nguội trước khi xếp lớp</li>
                    <li>Thêm đá khi thưởng thức</li>
                </ul>

                <p>Chè ba màu không chỉ ngon mà còn rất bổ dưỡng với protein từ đậu xanh và chất béo từ nước cốt dừa.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(500, 3000),
                'meta_title' => 'Cách Nấu Chè Ba Màu Truyền Thống Mát Lạnh - Công Thức Chi Tiết',
                'meta_description' => 'Hướng dẫn nấu chè ba màu truyền thống với đậu xanh, bột lọc và nước cốt dừa thơm ngon.',
                'meta_keywords' => 'chè ba màu, đậu xanh, bột lọc, nước cốt dừa, món tráng miệng'
            ]
        ];

        foreach ($posts as $postData) {
            $post = Post::create([
                'user_id' => $users->random()->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'content' => $postData['content'],
                'excerpt' => $postData['excerpt'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'view_count' => $postData['view_count'],
                'meta_title' => $postData['meta_title'],
                'meta_description' => $postData['meta_description'],
                'meta_keywords' => $postData['meta_keywords']
            ]);
        }

        $this->command->info('Đã tạo thành công 5 bài viết về công thức nấu ăn:');
        $this->command->info('- Top 5 Món Phở Nổi Tiếng Việt Nam');
        $this->command->info('- Cách Nấu Bún Chả Hà Nội Chuẩn Vị');
        $this->command->info('- Bí Quyết Nấu Cơm Tấm Sài Gòn Ngon Như Ngoài Tiệm');
        $this->command->info('- Hướng Dẫn Làm Bánh Xèo Miền Tây Giòn Rụm');
        $this->command->info('- Cách Nấu Chè Ba Màu Truyền Thống Mát Lạnh');
    }
} 