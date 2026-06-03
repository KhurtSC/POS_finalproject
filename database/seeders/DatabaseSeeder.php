<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────────────────

        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@pointsale.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Cashier',
            'email'    => 'cashier@pointsale.com',
            'password' => Hash::make('password'),
            'role'     => 'cashier',
        ]);

        // ── Copy demo images from public/assets into the storage disk ─────────
        //
        // The controller stores uploaded images at:  storage/app/public/products/
        // and references them via Storage::url('products/filename.jpg').
        //
        // All images live in public/assets/images/products/ — no external
        // downloads needed. The seeder copies them into the storage disk once.

        $sourcePath  = public_path('assets/images/products');
        $storagePath = Storage::disk('public')->path('products');

        File::ensureDirectoryExists($storagePath);

        $demoImages = [
            // Original 6
            'iced-coffee.jpg',
            'white-mocha.jpg',
            'club-sandwich.jpg',
            'truffle-pasta.jpg',
            'chocolate-mousse.jpg',
            'cafe-dessert.jpg',
            // New images — one per product that previously had null or a reused image
            'matcha-latte-new.jpg',
            'caramel-macchiato-new.jpg',
            'chamomile-tea-new.jpg',
            'spanish-latte-new.jpg',
            'caesar-salad-new.jpg',
            'beef-burger-new.jpg',
            'margherita-pizza-new.jpg',
            'mushroom-risotto-new.jpg',
            'tiramisu-new.jpg',
            'cheesecake-slice-new.jpg',
            'lava-cake-new.jpg',
            'garlic-bread-new.jpg',
            'nachos-dip-new.jpg',
            'cheese-platter-new.jpg',
            'french-fries-new.jpg',
        ];

        foreach ($demoImages as $filename) {
            $src = $sourcePath . DIRECTORY_SEPARATOR . $filename;
            $dst = $storagePath . DIRECTORY_SEPARATOR . $filename;

            if (File::exists($src) && ! File::exists($dst)) {
                File::copy($src, $dst);
            }
        }

        // Helper: return storage-relative path the model expects, or null if
        // the source image was missing (so the product still seeds cleanly).
        $img = fn (string $filename): ?string =>
            File::exists($sourcePath . DIRECTORY_SEPARATOR . $filename)
                ? 'products/' . $filename
                : null;

        // ── Categories ────────────────────────────────────────────────────────

        $beverages = Category::create(['name' => 'Beverages', 'slug' => 'beverages']);
        $food      = Category::create(['name' => 'Food',      'slug' => 'food']);
        $desserts  = Category::create(['name' => 'Desserts',  'slug' => 'desserts']);
        $snacks    = Category::create(['name' => 'Snacks',    'slug' => 'snacks']);

        // ── Products ──────────────────────────────────────────────────────────

        $products = [

            // ── Beverages ────────────────────────────────────────────────────
            [
                'name'                => 'Iced Coffee',
                'sku'                 => 'BEV-001',
                'description'         => 'Classic cold brew poured over ice with a creamy finish.',
                'price'               => 150,
                'cost'                => 60,
                'stock'               => 50,
                'low_stock_threshold' => 10,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('iced-coffee.jpg'),
            ],
            [
                'name'                => 'White Mocha',
                'sku'                 => 'BEV-002',
                'description'         => 'Espresso with white chocolate sauce and steamed milk.',
                'price'               => 175,
                'cost'                => 70,
                'stock'               => 50,
                'low_stock_threshold' => 10,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('white-mocha.jpg'),
            ],
            [
                'name'                => 'Matcha Latte',
                'sku'                 => 'BEV-003',
                'description'         => 'Ceremonial-grade matcha whisked with oat milk over ice.',
                'price'               => 165,
                'cost'                => 65,
                'stock'               => 40,
                'low_stock_threshold' => 8,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('matcha-latte-new.jpg'),
            ],
            [
                'name'                => 'Caramel Macchiato',
                'sku'                 => 'BEV-004',
                'description'         => 'Layered espresso, vanilla syrup, steamed milk and caramel drizzle.',
                'price'               => 180,
                'cost'                => 72,
                'stock'               => 35,
                'low_stock_threshold' => 8,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('caramel-macchiato-new.jpg'),
            ],
            [
                'name'                => 'Chamomile Tea',
                'sku'                 => 'BEV-005',
                'description'         => 'Soothing whole-flower chamomile steeped in hot water, served with honey.',
                'price'               => 120,
                'cost'                => 40,
                'stock'               => 45,
                'low_stock_threshold' => 10,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('chamomile-tea-new.jpg'),
            ],
            [
                'name'                => 'Spanish Latte',
                'sku'                 => 'BEV-006',
                'description'         => 'Strong espresso balanced with sweetened condensed milk and fresh milk.',
                'price'               => 170,
                'cost'                => 68,
                'stock'               => 40,
                'low_stock_threshold' => 8,
                'is_available'        => true,
                'category_id'         => $beverages->id,
                'image'               => $img('spanish-latte-new.jpg'),
            ],

            // ── Food ─────────────────────────────────────────────────────────
            [
                'name'                => 'Club Sandwich',
                'sku'                 => 'FOD-001',
                'description'         => 'Triple-decker with chicken, bacon, egg, lettuce and tomato on toasted bread.',
                'price'               => 220,
                'cost'                => 100,
                'stock'               => 30,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('club-sandwich.jpg'),
            ],
            [
                'name'                => 'Truffle Pasta',
                'sku'                 => 'FOD-002',
                'description'         => 'Al-dente fettuccine tossed in black truffle cream sauce with parmesan.',
                'price'               => 260,
                'cost'                => 120,
                'stock'               => 20,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('truffle-pasta.jpg'),
            ],
            [
                'name'                => 'Caesar Salad',
                'sku'                 => 'FOD-003',
                'description'         => 'Crisp romaine, house-made Caesar dressing, croutons and shaved parmesan.',
                'price'               => 195,
                'cost'                => 80,
                'stock'               => 25,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('caesar-salad-new.jpg'),
            ],
            [
                'name'                => 'Beef Burger',
                'sku'                 => 'FOD-004',
                'description'         => '180g beef patty, cheddar, caramelised onions and house burger sauce.',
                'price'               => 280,
                'cost'                => 125,
                'stock'               => 20,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('beef-burger-new.jpg'),
            ],
            [
                'name'                => 'Margherita Pizza',
                'sku'                 => 'FOD-005',
                'description'         => 'Thin-crust pizza with San Marzano tomato, fresh mozzarella and basil.',
                'price'               => 340,
                'cost'                => 150,
                'stock'               => 15,
                'low_stock_threshold' => 3,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('margherita-pizza-new.jpg'),
            ],
            [
                'name'                => 'Mushroom Risotto',
                'sku'                 => 'FOD-006',
                'description'         => 'Creamy arborio rice with wild mushrooms, white wine and parmesan.',
                'price'               => 250,
                'cost'                => 110,
                'stock'               => 18,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $food->id,
                'image'               => $img('mushroom-risotto-new.jpg'),
            ],

            // ── Desserts ─────────────────────────────────────────────────────
            [
                'name'                => 'Chocolate Mousse',
                'sku'                 => 'DES-001',
                'description'         => 'Airy dark-chocolate mousse topped with whipped cream and cocoa dust.',
                'price'               => 145,
                'cost'                => 55,
                'stock'               => 30,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $desserts->id,
                'image'               => $img('chocolate-mousse.jpg'),
            ],
            [
                'name'                => 'Cafe Dessert Cup',
                'sku'                 => 'DES-002',
                'description'         => 'Coffee-soaked sponge layered with mascarpone cream in a glass.',
                'price'               => 155,
                'cost'                => 60,
                'stock'               => 25,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $desserts->id,
                'image'               => $img('cafe-dessert.jpg'),
            ],
            [
                'name'                => 'Tiramisu',
                'sku'                 => 'DES-003',
                'description'         => 'Classic Italian ladyfinger and mascarpone dessert dusted with cocoa.',
                'price'               => 160,
                'cost'                => 62,
                'stock'               => 20,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $desserts->id,
                'image'               => $img('tiramisu-new.jpg'),
            ],
            [
                'name'                => 'Cheesecake Slice',
                'sku'                 => 'DES-004',
                'description'         => 'New York-style baked cheesecake on a graham cracker crust with berry compote.',
                'price'               => 150,
                'cost'                => 58,
                'stock'               => 18,
                'low_stock_threshold' => 5,
                'is_available'        => true,
                'category_id'         => $desserts->id,
                'image'               => $img('cheesecake-slice-new.jpg'),
            ],
            [
                'name'                => 'Lava Cake',
                'sku'                 => 'DES-005',
                'description'         => 'Warm chocolate fondant with a molten centre, served with vanilla ice cream.',
                'price'               => 175,
                'cost'                => 70,
                'stock'               => 15,
                'low_stock_threshold' => 3,
                'is_available'        => true,
                'category_id'         => $desserts->id,
                'image'               => $img('lava-cake-new.jpg'),
            ],

            // ── Snacks ───────────────────────────────────────────────────────
            [
                'name'                => 'Garlic Bread',
                'sku'                 => 'SNK-001',
                'description'         => 'Toasted baguette slices with herb butter and roasted garlic, served warm.',
                'price'               => 90,
                'cost'                => 35,
                'stock'               => 40,
                'low_stock_threshold' => 8,
                'is_available'        => true,
                'category_id'         => $snacks->id,
                'image'               => $img('garlic-bread-new.jpg'),
            ],
            [
                'name'                => 'Nachos & Dip',
                'sku'                 => 'SNK-002',
                'description'         => 'Tortilla chips with cheese sauce, jalapeños, salsa and sour cream.',
                'price'               => 130,
                'cost'                => 50,
                'stock'               => 30,
                'low_stock_threshold' => 8,
                'is_available'        => true,
                'category_id'         => $snacks->id,
                'image'               => $img('nachos-dip-new.jpg'),
            ],
            [
                'name'                => 'Cheese Platter',
                'sku'                 => 'SNK-003',
                'description'         => 'Selection of three cheeses with crackers, grapes and honey.',
                'price'               => 185,
                'cost'                => 80,
                'stock'               => 15,
                'low_stock_threshold' => 3,
                'is_available'        => true,
                'category_id'         => $snacks->id,
                'image'               => $img('cheese-platter-new.jpg'),
            ],
            [
                'name'                => 'French Fries',
                'sku'                 => 'SNK-004',
                'description'         => 'Crispy golden fries seasoned with sea salt, served with house ketchup.',
                'price'               => 95,
                'cost'                => 35,
                'stock'               => 50,
                'low_stock_threshold' => 10,
                'is_available'        => true,
                'category_id'         => $snacks->id,
                'image'               => $img('french-fries-new.jpg'),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}