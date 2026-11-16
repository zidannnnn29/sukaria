<?php
/**
 * Controller Katalog Pembeli (akses publik, tanpa login).
 */
final class CatalogController
{
    public static function index(): never
    {
        $products = Product::forCatalog();

        // Susun payload JSON untuk rendering sisi klien (pencarian real-time)
        $items = array_map(function (array $p) {
            return [
                'id'          => (int) $p['id'],
                'nama'        => (string) $p['name'],
                'kategori'    => (string) $p['category_name'],
                'kategoriSlug'=> (string) $p['category_slug'],
                'toko'        => (string) ($p['seller_name'] ?: 'Toko Sukaria'),
                'harga'       => (int) $p['price'],
                'stok'        => (int) $p['stock'],
                'deskripsi'   => (string) ($p['description'] ?? ''),
                'unggulan'    => (int) $p['is_featured'] === 1,
                'gambar'      => product_image_url($p),
                'waNumber'    => (string) $p['wa_number'],
            ];
        }, $products);

        render('public', 'catalog/index', [
            'pageTitle'  => 'Katalog Oleh-Oleh Banyuwangi — ' . SUKARIA_APP_NAME,
            'items'      => $items,
            'categories' => array_values(array_map(fn($c) => [
                'name' => $c['name'],
                'slug' => $c['slug'],
            ], Category::allActive())),
        ]);
    }
}
