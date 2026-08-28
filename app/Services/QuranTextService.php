<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Reads Quran text from a free, no-key public API (alquran.cloud) and caches
 * it forever — the text never changes, so once a surah is fetched it's read
 * from the local cache (DB-backed) from then on, no network needed.
 */
class QuranTextService
{
    private const BASE_URL = 'https://api.alquran.cloud/v1';

    /**
     * All 114 surahs with their basic metadata (name, ayah count, ...).
     *
     * @return array<int, array{number:int, name:string, englishName:string, englishNameTranslation:string, numberOfAyahs:int, revelationType:string}>
     */
    public function surahList(): array
    {
        return Cache::rememberForever('quran.surah_list', function () {
            $response = Http::timeout(15)->get(self::BASE_URL.'/surah');

            if (! $response->successful()) {
                throw new \RuntimeException('تعذّر جلب قايمة السور دلوقتي.');
            }

            return $response->json('data');
        });
    }

    /**
     * A single surah's full text.
     *
     * @return array{number:int, name:string, englishName:string, englishNameTranslation:string, revelationType:string, ayahs: array<int, array{number:int, text:string, page:int}>}
     */
    public function surah(int $number): array
    {
        if ($number < 1 || $number > 114) {
            throw new \InvalidArgumentException('رقم سورة غير صحيح.');
        }

        return Cache::rememberForever("quran.surah.{$number}", function () use ($number) {
            $response = Http::timeout(15)->get(self::BASE_URL."/surah/{$number}");

            if (! $response->successful()) {
                throw new \RuntimeException('تعذّر جلب السورة دلوقتي.');
            }

            $data = $response->json('data');

            return [
                'number' => $data['number'],
                'name' => $data['name'],
                'englishName' => $data['englishName'],
                'englishNameTranslation' => $data['englishNameTranslation'],
                'revelationType' => $data['revelationType'],
                'ayahs' => array_map(fn ($a) => ['number' => $a['numberInSurah'], 'text' => $a['text'], 'page' => $a['page']], $data['ayahs']),
            ];
        });
    }
}
