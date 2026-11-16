<?php
/**
 * Model StoreSetting (nomor WhatsApp per seller).
 */
final class StoreSetting
{
    /** Pastikan baris pengaturan ada; kembalikan nomor WA aktif. */
    public static function ensure(int $sellerId, string $defaultWa): string
    {
        $stmt = Database::pdo()->prepare(
            "SELECT whatsapp_number FROM store_settings WHERE seller_id = :id"
        );
        $stmt->execute(['id' => $sellerId]);
        $existing = $stmt->fetchColumn();

        if ($existing === false || $existing === null) {
            $stmt = Database::pdo()->prepare(
                "INSERT INTO store_settings (seller_id, whatsapp_number) VALUES (:id, :wa)"
            );
            $stmt->execute(['id' => $sellerId, 'wa' => $defaultWa]);
            return $defaultWa;
        }
        return (string) $existing;
    }

    public static function get(int $sellerId): string
    {
        return self::ensure($sellerId, SUKARIA_DEFAULT_WA);
    }

    public static function save(int $sellerId, string $normalizedWa): bool
    {
        self::ensure($sellerId, SUKARIA_DEFAULT_WA);
        $stmt = Database::pdo()->prepare(
            "UPDATE store_settings SET whatsapp_number = :wa WHERE seller_id = :id"
        );
        return $stmt->execute(['wa' => $normalizedWa, 'id' => $sellerId]);
    }
}
