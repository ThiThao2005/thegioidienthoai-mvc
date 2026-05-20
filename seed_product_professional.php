<?php
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/ProductModel.php';

$db = (new Database())->getConnection();
$model = new ProductModel($db);

$brandNames = ['Apple', 'Samsung', 'Xiaomi', 'OPPO', 'Vivo', 'Realme', 'Nokia', 'Acer', 'Dell', 'ASUS', 'Lenovo', 'HP', 'MSI', 'Sony', 'Akko', 'AVA', 'Huawei'];
$brandIds = [];
foreach ($brandNames as $brand) {
    $brandIds[$brand] = $model->addBrandIfNotExists($brand);
}

$products = $db->query('SELECT id, name, category_id, price, image FROM product')->fetchAll(PDO::FETCH_OBJ);
$updated = 0;

foreach ($products as $product) {
    $brandId = null;
    if (preg_match('/iphone|ipad|macbook/i', $product->name)) {
        $brandId = $brandIds['Apple'];
    }
    foreach ($brandIds as $brand => $id) {
        if ($brandId) break;
        if (stripos($product->name, $brand) !== false) {
            $brandId = $id;
            break;
        }
    }
    if (!$brandId) {
        $brandId = $brandIds['Samsung'];
    }

    $sale = ((int)$product->id % 5 === 0) ? 10 : (((int)$product->id % 7 === 0) ? 15 : 0);
    $featured = ((int)$product->id % 4 === 0) ? 1 : 0;
    $stmt = $db->prepare('UPDATE product SET brand_id = :brand_id, warranty_months = :warranty, sale_percent = :sale, featured = :featured WHERE id = :id');
    $stmt->execute([
        ':brand_id' => $brandId,
        ':warranty' => in_array((int)$product->category_id, [1, 2, 3], true) ? 12 : 6,
        ':sale' => $sale,
        ':featured' => $featured,
        ':id' => $product->id,
    ]);

    $specKeys = [];
    $specValues = [];
    if ((int)$product->category_id === 1) {
        $specKeys = ['Man hinh', 'Chip', 'RAM', 'Dung luong', 'Pin'];
        $specValues = ['AMOLED 6.7 inch 120Hz', 'Snapdragon/Apple A series', '8GB - 12GB', '128GB - 512GB', '5000mAh, sac nhanh'];
    } elseif ((int)$product->category_id === 2) {
        $specKeys = ['CPU', 'RAM', 'SSD', 'Man hinh', 'He dieu hanh'];
        $specValues = ['Intel Core/Ryzen/Apple Silicon', '8GB - 16GB', '512GB NVMe', '14-15.6 inch FHD/OLED', 'Windows/macOS'];
    } elseif ((int)$product->category_id === 3) {
        $specKeys = ['Man hinh', 'Chip', 'Bo nho', 'Pin', 'But cam ung'];
        $specValues = ['11 inch do phan giai cao', 'Chip tiet kiem dien', '64GB - 256GB', 'Su dung ca ngay', 'Ho tro tuy dong may'];
    } else {
        $specKeys = ['Ket noi', 'Bao hanh', 'Chat lieu', 'Tuong thich', 'Tinh nang'];
        $specValues = ['Bluetooth/USB-C tuy san pham', '6 thang', 'Hoan thien ben bi', 'Nhieu thiet bi pho bien', 'Phu hop su dung hang ngay'];
    }
    $model->replaceProductSpecs($product->id, $specKeys, $specValues);

    $colors = ['Den', 'Bac', 'Xanh'];
    $rams = ['8GB', '12GB', '16GB'];
    $storages = ['128GB', '256GB', '512GB'];
    $priceDeltas = [0, 1000000, 2500000];
    $stocks = [8 + ($product->id % 5), 5 + ($product->id % 4), 3 + ($product->id % 3)];
    $skus = [
        'P' . $product->id . '-STD',
        'P' . $product->id . '-PLUS',
        'P' . $product->id . '-PRO',
    ];
    $model->replaceProductVariants($product->id, $colors, $rams, $storages, $priceDeltas, $stocks, $skus);
    $updated++;
}

echo "Professional product data updated: {$updated}\n";
