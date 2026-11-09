<?php

namespace App\Function;

/**
 * الولايات الـ 69 وأقاليمها (1 الشمال، 2 الهضاب العليا، 3 الجنوب) من resources/data/wilayas.json
 */
class Wilayas
{
    private static ?array $data = null;

    /** رمز الولاية حسب اسمها الموحّد */
    private static ?array $byName = null;

    private static function data(): array
    {
        return self::$data ??= json_decode(
            file_get_contents(resource_path('data/wilayas.json')),
            true
        );
    }

    public static function all(): array
    {
        return self::data()['wilayas'];
    }

    public static function regions(): array
    {
        return self::data()['regions'];
    }

    public static function find(?string $code): ?array
    {
        foreach (self::all() as $wilaya) {
            if ($wilaya['code'] === $code) {
                return $wilaya;
            }
        }
        return null;
    }

    /**
     * رمز الولاية ("39") من اسمها كما هو محفوظ في الحساب ("الوادي")،
     * أو من رقمها؛ null إذا لم تُعرف
     */
    public static function codeOf(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (ctype_digit($value)) {
            $code = str_pad((string) (int) $value, 2, '0', STR_PAD_LEFT);
            return self::find($code) ? $code : null;
        }

        if (self::$byName === null) {
            self::$byName = [];
            foreach (self::all() as $wilaya) {
                $names = array_merge(
                    [$wilaya['name_ar'], $wilaya['name_fr']],
                    $wilaya['aliases'] ?? []
                );
                foreach ($names as $name) {
                    self::$byName[self::normalize($name)] = $wilaya['code'];
                }
            }
        }

        return self::$byName[self::normalize($value)] ?? null;
    }

    /** توحيد الكتابة: الهمزات، التاء المربوطة، الألف المقصورة، "ولاية" */
    private static function normalize(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/^(ولاية|wilaya de|wilaya d\')\s*/u', '', $name);
        $name = str_replace(['أ', 'إ', 'آ', 'ٱ', 'ة', 'ى', 'ئ', 'ؤ'], ['ا', 'ا', 'ا', 'ا', 'ه', 'ي', 'ي', 'و'], $name);
        return preg_replace('/\s+/u', ' ', $name);
    }
}
