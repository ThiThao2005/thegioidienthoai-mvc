<?php
require_once __DIR__ . '/app/config/database.php';

$db = (new Database())->getConnection();

$products = [
    ['iPhone 15 Pro Max 256GB Blue Titanium', 'Dien thoai cao cap voi chip A17 Pro, camera 48MP, man hinh Super Retina XDR 6.7 inch va khung titanium ben nhe.', 29990000, '1778937043_15-pro-max-xanh-.jpg', 1],
    ['Samsung Galaxy S26 Ultra 512GB', 'Flagship Android voi man hinh AMOLED lon, camera zoom xa, hieu nang manh va pin dung luong cao cho ca ngay su dung.', 32990000, '1778556865_OIP.jpg', 1],
    ['Xiaomi 17 5G 256GB Green', 'Smartphone 5G thiet ke tre trung, man hinh tan so quet cao, sac nhanh va camera ro net trong tam gia tot.', 15990000, '1779075308_xiaomi-17-5g-xanh-la-1-639088210870268655-750x500.jpg', 1],
    ['OPPO Reno 13 Pro 5G 256GB', 'Dien thoai chup anh chan dung dep, sac nhanh, thiet ke mong nhe va bo nho lon cho nguoi dung nang dong.', 13990000, '1778556952_OIP.jpg', 1],
    ['Vivo V40 5G 12GB 256GB', 'May co man hinh AMOLED sac net, camera selfie chat luong cao, pin tot va hieu nang on dinh.', 11990000, '1778556961_OIP.jpg', 1],
    ['Realme GT Neo 7 5G 256GB', 'Dien thoai hieu nang cao cho game, tan nhiet tot, sac nhanh va man hinh muot.', 10990000, '1778557104_OIP.jpg', 1],
    ['iPhone 14 128GB Midnight', 'iPhone pho thong cao cap voi camera dep, chip manh, iOS on dinh va ho tro cap nhat lau dai.', 16990000, '1778937043_15-pro-max-xanh-.jpg', 1],
    ['Samsung Galaxy A56 5G 128GB', 'Lua chon tam trung voi man hinh dep, pin ben, camera da dung va ket noi 5G.', 9490000, '1778556865_OIP.jpg', 1],
    ['Xiaomi Redmi Note 15 Pro 5G', 'May tam trung gia tot, sac nhanh, pin lon, man hinh AMOLED va camera do phan giai cao.', 7490000, '1779075308_xiaomi-17-5g-xanh-la-1-639088210870268655-750x500.jpg', 1],
    ['Nokia G60 5G 128GB', 'Dien thoai ben bi, pin tot, giao dien Android gon nhe phu hop nhu cau hoc tap va lam viec.', 4990000, '1778556952_OIP.jpg', 1],

    ['MacBook Neo 13 inch A18 Pro 256GB Silver', 'Laptop mong nhe, pin lau, hieu nang tot cho van phong, hoc tap, lap trinh va xu ly hinh anh co ban.', 25990000, '1778937650_macbook-neo-13-inch-a18-pro-8gb-256gb-bac-2-639083037660759265-750x500.jpg', 2],
    ['Acer Swift X 14 2024 OLED', 'Laptop sang tao noi dung voi man hinh OLED dep, CPU doi moi, GPU roi va than may gon nhe.', 27990000, '1778557560_acer-swift-x-14-2024-front-angled-e1715278668968.jpg', 2],
    ['Dell Inspiron 15 DC15250 i5', 'Laptop 15.6 inch phu hop hoc tap va van phong, ban phim rong, hieu nang on dinh va de nang cap.', 14990000, '1778937815_dell-15-dc15250-i5-1334u-dc5i5897w1-1-639044164677913392-750x500.jpg', 2],
    ['ASUS Vivobook 15 OLED i5 16GB', 'Man hinh OLED ruc ro, RAM 16GB, SSD nhanh, thiet ke tre trung cho sinh vien va nhan vien van phong.', 16990000, '1778939580_asus-vy249hgr-23-8-inch-fhd-ips-120hz-1ms-2-638808263746920802-750x500.jpg', 2],
    ['Lenovo IdeaPad Slim 5 Ryzen 7', 'Laptop mong nhe voi CPU Ryzen tiet kiem dien, pin tot, ban phim thoai mai va vo may chac chan.', 18490000, '1778937815_dell-15-dc15250-i5-1334u-dc5i5897w1-1-639044164677913392-750x500.jpg', 2],
    ['HP Pavilion 14 i5 512GB', 'May tinh xach tay gon nhe, cau hinh can bang, phu hop lam viec linh hoat va hoc online.', 15990000, '1778557560_acer-swift-x-14-2024-front-angled-e1715278668968.jpg', 2],
    ['MSI Modern 14 Core i7', 'Laptop hieu nang tot trong than hinh nhe, phu hop xu ly cong viec da nhiem va di chuyen nhieu.', 19990000, '1778937650_macbook-neo-13-inch-a18-pro-8gb-256gb-bac-2-639083037660759265-750x500.jpg', 2],
    ['ASUS TUF Gaming F15 RTX 4050', 'Laptop gaming co GPU RTX, man hinh tan so quet cao, tan nhiet tot va ban phim RGB.', 23990000, '1778939580_asus-vy249hgr-23-8-inch-fhd-ips-120hz-1ms-2-638808263746920802-750x500.jpg', 2],

    ['iPad Mini 6 WiFi 64GB Purple', 'May tinh bang nho gon, chip manh, ho tro Apple Pencil, phu hop ghi chu, doc sach va giai tri.', 11990000, '1778937225_thiet-ke-ipad-mini-6-vs-ipad-air-5-didongviet.jpg', 3],
    ['Xiaomi Pad 8 256GB Gray', 'Tablet Android man hinh lon, loa hay, pin ben va hieu nang tot cho hoc tap giai tri.', 8990000, '1778937736_xiaomi-pad-8-gray-1-639088381751465149-750x500.jpg', 3],
    ['Samsung Galaxy Tab S10 FE', 'May tinh bang Samsung co but S Pen, man hinh lon, da nhiem tot va phu hop hoc tap.', 12990000, '1778937225_thiet-ke-ipad-mini-6-vs-ipad-air-5-didongviet.jpg', 3],
    ['Lenovo Tab Plus 11.5 inch', 'Tablet giai tri voi loa lon, man hinh sac net, pin lau va gia de tiep can.', 6990000, '1778937736_xiaomi-pad-8-gray-1-639088381751465149-750x500.jpg', 3],
    ['iPad Air M3 11 inch WiFi 128GB', 'Tablet manh me cho ve, ghi chu, chinh anh va lam viec nhe voi chip M series.', 16990000, '1778937225_thiet-ke-ipad-mini-6-vs-ipad-air-5-didongviet.jpg', 3],
    ['Huawei MatePad 11.5 PaperMatte', 'Man hinh chong loi mat, phu hop doc tai lieu, ghi chu va hoc tap hang ngay.', 9990000, '1778937736_xiaomi-pad-8-gray-1-639088381751465149-750x500.jpg', 3],

    ['Chuot Bluetooth Akko Cat Theme Pink', 'Chuot khong day thiet ke de thuong, ket noi Bluetooth on dinh, cam nam thoai mai cho van phong.', 490000, '1778937566_chuot-bluetooth-akko-cat-theme-mouse-pink-angie-1-638913000869081581-750x500.jpg', 4],
    ['Tai nghe Bluetooth Sony WH-CH520 Blue', 'Tai nghe chup tai khong day, pin lau, am thanh ro va thiet ke nhe de deo ca ngay.', 1290000, '1778937883_tai-nghe-bluetooth-chup-tai-sony-wh-ch520-xanh-1-750x500.jpg', 4],
    ['Quat cam tay AVA JF-412', 'Quat cam tay nho gon, pin sac lai, nhieu muc gio, tien loi khi di hoc, di lam va du lich.', 190000, '1778937939_quat-cam-tay-ava-jf-412-1-639095877634713782-750x500.jpg', 4],
    ['Man hinh ASUS VY249HGR 23.8 inch', 'Man hinh FHD IPS 120Hz, bao ve mat, mau sac tot cho hoc tap, lam viec va giai tri.', 2890000, '1778939580_asus-vy249hgr-23-8-inch-fhd-ips-120hz-1ms-2-638808263746920802-750x500.jpg', 4],
    ['Sac nhanh USB-C 30W', 'Cu sac nhanh nho gon, ho tro dien thoai va may tinh bang, co bao ve qua nhiet va qua dong.', 290000, '1779067852_quat-cam-tay-ava-jf-412-1-639095877634713782-750x500.jpg', 4],
    ['Cap USB-C to USB-C 1m', 'Cap sac va truyen du lieu toc do cao, dau cap chac chan, phu hop sac nhanh hang ngay.', 150000, '1779067824_Screenshot 2026-05-03 134606.png', 4],
    ['Op lung iPhone trong suot MagSafe', 'Op lung trong suot chong soc, ho tro sac MagSafe va giu thiet ke may gon dep.', 250000, '1778556865_OIP.jpg', 4],
    ['Pin sac du phong 20000mAh 22.5W', 'Pin sac du phong dung luong lon, sac nhanh nhieu thiet bi, phu hop di chuyen xa.', 690000, '1778557104_OIP.jpg', 4],
];

$check = $db->prepare('SELECT id FROM product WHERE name = :name LIMIT 1');
$insert = $db->prepare('INSERT INTO product (name, description, price, image, category_id)
                        VALUES (:name, :description, :price, :image, :category_id)');

$added = 0;
$skipped = 0;

foreach ($products as $product) {
    [$name, $description, $price, $image, $categoryId] = $product;
    $check->execute([':name' => $name]);
    if ($check->fetch(PDO::FETCH_OBJ)) {
        $skipped++;
        continue;
    }

    $insert->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':image' => $image,
        ':category_id' => $categoryId,
    ]);
    $added++;
}

echo "Added: {$added}\nSkipped: {$skipped}\n";
